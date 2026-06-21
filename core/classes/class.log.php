<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Log class providing static logging methods with buffered and asynchronous I/O.
 *
 * Supports five log levels (TRACE, DEBUG, INFO, WARNING, ERROR), a separator
 * utility, class-lifecycle helpers, and a configurable write buffer to reduce
 * file-system pressure. Also supports asynchronous logging to decouple I/O
 * operations from the main process.
 *
 * Call Log::init() once during bootstrap (after globals are available) to register
 * the shutdown flush handler and initialize async logging.
 *
 * Usage:
 * ```
 * Log::init();
 * Log::info('Application started');
 * Log::error('Something went wrong');
 * ```
 */
class Log
{
    const ERROR   = 'ERROR';
    const WARNING = 'WARNING';
    const INFO    = 'INFO';
    const DEBUG   = 'DEBUG';
    const TRACE   = 'TRACE';

    /** @var array Log buffer for batching log writes */
    private static $logBuffer = [];

    /** @var int Maximum number of log entries to buffer before flushing */
    private static $logBufferSize = 50;

    /** @var bool Tracks whether the shutdown flush handler has been registered */
    private static $shutdownRegistered = false;

    /** @var array Statistics for monitoring log buffer effectiveness */
    private static $logStats = [
        'buffered' => 0,
        'flushed'  => 0,
        'writes'   => 0,
        'async'    => 0,
    ];

    /** @var string Directory for async log queues */
    private static $asyncQueueDir = null;

    /** @var bool Whether async logging is enabled */
    private static $asyncEnabled = true;

    /** @var int Maximum number of queue files to process in one batch */
    private static $maxAsyncQueueBatch = 5;

    /**
     * Registers the shutdown flush handler and initializes async logging.
     * Call once during bootstrap after globals are initialised.
     *
     * Automatically disables async logging when TRACE verbosity is enabled,
     * to ensure live log monitoring shows logs immediately.
     *
     * @return void
     */
    public static function init()
    {
        if (!self::$shutdownRegistered) {
            register_shutdown_function([__CLASS__, 'flush']);
            register_shutdown_function([__CLASS__, 'processAsyncQueue']);
            self::$shutdownRegistered = true;

            // Initialize async queue directory
            self::initializeAsyncQueue();

            // Auto-disable async when TRACE logging is enabled (for live log monitoring)
            self::checkVerbosityAndAdjustAsync();
        }
    }

    /**
     * Check logging verbosity and adjust async settings accordingly.
     * Disables async when TRACE level is enabled for real-time log visibility.
     * Also optimizes buffer size for better responsiveness during debugging.
     *
     * @return void
     */
    private static function checkVerbosityAndAdjustAsync()
    {
        global $bearsamppConfig;

        try {
            if (!isset($bearsamppConfig)) {
                return;
            }

            $verbosity = $bearsamppConfig->getLogsVerbose();

            // Disable async and reduce buffer size for DEBUG/TRACE logging
            // This ensures live log monitoring shows logs immediately
            if ($verbosity === Config::VERBOSE_TRACE) {
                self::$asyncEnabled = false;
                // Use smaller buffer for TRACE to flush more frequently (every 10 entries)
                self::$logBufferSize = 10;
            } elseif ($verbosity === Config::VERBOSE_DEBUG) {
                self::$asyncEnabled = false;
                // For DEBUG level, use moderate buffer size (25 entries)
                self::$logBufferSize = 25;
            }
            // For INFO and REPORT levels, use default buffer size (50) with async enabled
        } catch (Exception $e) {
            // Silently fail - this is just an optimization
        }
    }

    /**
     * Initialize async queue directory.
     *
     * @return void
     */
    private static function initializeAsyncQueue()
    {
        try {
            // Set up queue directory in tmp
            self::$asyncQueueDir = Path::getTmpPath() . '/log-queue';

            // Create queue directory if it doesn't exist
            if (!is_dir(self::$asyncQueueDir)) {
                @mkdir(self::$asyncQueueDir, 0755, true);
            }
        } catch (Exception $e) {
            self::$asyncEnabled = false;
        }
    }

    /**
     * Writes a message to a log file, using the buffer to reduce I/O.
     *
     * The file path is resolved as follows:
     *  - If $file is provided by the caller it is used as-is.
     *  - Otherwise the default path is chosen based on $type (error log vs. main log),
     *    then overridden with the homepage log when not running in root context.
     *
     * @param   mixed        $data  The message to log.
     * @param   string       $type  One of the Log level constants.
     * @param   string|null  $file  Explicit file path, or null to use the default.
     */
    private static function write($data, $type, $file = null)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppConfig;

        // Safety check: if globals aren't initialised, fall back to error_log
        if (!isset($bearsamppRoot) || !isset($bearsamppCore) || !isset($bearsamppConfig)) {
            error_log('[' . $type . '] ' . $data);
            return;
        }

        // Lazily register the shutdown handler if init() was not called explicitly
        self::init();

        // Resolve default file path only when the caller did not supply one
        if ($file === null) {
            $file = $type === self::ERROR
                ? Path::getErrorLogFilePath()
                : Path::getLogFilePath();

            if (!$bearsamppRoot->isRoot()) {
                $file = Path::getHomepageLogFilePath();
            }
        }

        $verbose                         = [];
        $verbose[Config::VERBOSE_SIMPLE] = $type === self::ERROR || $type === self::WARNING;
        $verbose[Config::VERBOSE_REPORT] = $verbose[Config::VERBOSE_SIMPLE] || $type === self::INFO;
        $verbose[Config::VERBOSE_DEBUG]  = $verbose[Config::VERBOSE_REPORT] || $type === self::DEBUG;
        $verbose[Config::VERBOSE_TRACE]  = $verbose[Config::VERBOSE_DEBUG] || $type === self::TRACE;

        $writeLog = false;
        if ($bearsamppConfig->getLogsVerbose() === Config::VERBOSE_SIMPLE && $verbose[Config::VERBOSE_SIMPLE]) {
            $writeLog = true;
        } elseif ($bearsamppConfig->getLogsVerbose() === Config::VERBOSE_REPORT && $verbose[Config::VERBOSE_REPORT]) {
            $writeLog = true;
        } elseif ($bearsamppConfig->getLogsVerbose() === Config::VERBOSE_DEBUG && $verbose[Config::VERBOSE_DEBUG]) {
            $writeLog = true;
        } elseif ($bearsamppConfig->getLogsVerbose() === Config::VERBOSE_TRACE && $verbose[Config::VERBOSE_TRACE]) {
            $writeLog = true;
        }

        if ($writeLog) {
            self::$logBuffer[] = [
                'file' => $file,
                'data' => $data,
                'type' => $type,
                'time' => time(),
            ];
            self::$logStats['buffered']++;

            // Flush immediately for:
            // 1. Errors (always)
            // 2. TRACE/DEBUG level logs (for real-time visibility during debugging)
            // 3. When buffer reaches the configured size limit
            $debugVerbosity = $bearsamppConfig->getLogsVerbose();
            $isDebugMode = ($debugVerbosity === Config::VERBOSE_TRACE || $debugVerbosity === Config::VERBOSE_DEBUG);
            $shouldFlush = $type === self::ERROR ||
                          $isDebugMode ||
                          count(self::$logBuffer) >= self::$logBufferSize;

            if ($shouldFlush) {
                self::flush();
            }
        }
    }

    /**
     * Flushes the log buffer to disk using async writes when available.
     * Groups entries by file to minimise file operations.
     * Uses async logging to avoid blocking the main process on I/O.
     * Falls back to synchronous writes or error_log() if needed.
     *
     * @return void
     */
    public static function flush()
    {
        if (empty(self::$logBuffer)) {
            return;
        }

        global $bearsamppCore, $bearsamppConfig;

        // If the core global is gone (e.g. during an abnormal shutdown), fall back to error_log
        if (!isset($bearsamppCore)) {
            foreach (self::$logBuffer as $log) {
                error_log('[' . date('Y-m-d H:i:s', $log['time']) . '] [' . $log['type'] . '] ' . $log['data']);
            }
            self::$logStats['flushed'] += count(self::$logBuffer);
            self::$logBuffer = [];
            return;
        }

        // Check if DEBUG or TRACE logging is active and force sync writes
        $forceSync = false;
        try {
            if (isset($bearsamppConfig)) {
                $verbosity = $bearsamppConfig->getLogsVerbose();
                if ($verbosity === Config::VERBOSE_TRACE || $verbosity === Config::VERBOSE_DEBUG) {
                    $forceSync = true;
                }
            }
        } catch (Exception $e) {
            // Ignore errors checking config
        }

        // Group logs by destination file
        $logsByFile = [];
        foreach (self::$logBuffer as $log) {
            $logsByFile[$log['file']][] = $log;
        }

        foreach ($logsByFile as $file => $logs) {
            $content = '';
            foreach ($logs as $log) {
                $content .= '[' . date('Y-m-d H:i:s', $log['time']) . '] # ' .
                            APP_TITLE . ' ' . $bearsamppCore->getAppVersion() . ' # ' .
                            $log['type'] . ': ' . $log['data'] . PHP_EOL;
            }

            // Use sync writes if TRACE is enabled or async is disabled
            if (!$forceSync && self::$asyncEnabled) {
                // Queue for async processing (non-blocking)
                $queued = self::queueAsyncWrite($file, $content);

                if ($queued) {
                    // Successfully queued, content will be written in background
                    self::$logStats['async']++;
                    self::$logStats['writes']++;
                    continue;
                }
                // If async queueing failed, fall through to sync write
            }

            // Synchronous write (immediate for TRACE, fallback for others)
            $written = @file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
            if ($written === false) {
                // File write failed â€” ensure entries are not silently lost
                foreach ($logs as $log) {
                    error_log('[' . $log['type'] . '] ' . $log['data'] . ' (target: ' . $file . ')');
                }
            }

            self::$logStats['writes']++;
        }

        self::$logStats['flushed'] += count(self::$logBuffer);
        self::$logBuffer = [];
    }

    /**
     * Queue a log entry for asynchronous writing.
     * Returns immediately without blocking on I/O.
     *
     * @param string $file Target log file path
     * @param string $content Log content to write
     * @return bool True if queued successfully
     */
    private static function queueAsyncWrite($file, $content)
    {
        if (!self::$asyncEnabled || !is_dir(self::$asyncQueueDir)) {
            return false;
        }

        try {
            // Create a unique queue file for this write
            $queueFile = self::$asyncQueueDir . '/' . uniqid('log_', true) . '.queue';

            // Queue entry contains the target file and content
            $queueEntry = [
                'file' => $file,
                'content' => $content,
                'timestamp' => time(),
            ];

            // Write queue entry (this is a small, fast operation)
            $serialized = serialize($queueEntry);
            $written = @file_put_contents($queueFile, $serialized, LOCK_EX);

            return ($written !== false);
        } catch (Exception $e) {
            // Silently fail - this is async so we don't want to interrupt main process
            return false;
        }
    }

    /**
     * Process all queued log entries immediately (public method for manual flushing).
     * Useful for live log monitoring or ensuring logs are written before critical operations.
     *
     * Can be called at any time to flush pending async log writes.
     *
     * @return int Number of queue files processed
     */
    public static function flushAsyncQueue()
    {
        return self::processAsyncQueue();
    }

    /**
     * Process all queued log entries at shutdown.
     * Ensures any remaining queued logs are written before exit.
     *
     * @return int Number of queue files processed
     */
    public static function processAsyncQueue()
    {
        if (!is_dir(self::$asyncQueueDir)) {
            return 0;
        }

        $processed = 0;

        try {
            $queueFiles = glob(self::$asyncQueueDir . '/*.queue');

            if (empty($queueFiles)) {
                return 0;
            }

            // Group entries by target file
            $entriesByFile = [];

            foreach ($queueFiles as $queueFile) {
                try {
                    // Read and deserialize queue entry
                    $serialized = @file_get_contents($queueFile);
                    if ($serialized === false) {
                        continue;
                    }

                    $entry = @unserialize($serialized, ['allowed_classes' => false]);
                    if ($entry === false || !isset($entry['file']) || !isset($entry['content'])) {
                        // Invalid queue entry, remove it
                        @unlink($queueFile);
                        continue;
                    }

                    // Group by target file
                    $targetFile = $entry['file'];
                    if (!isset($entriesByFile[$targetFile])) {
                        $entriesByFile[$targetFile] = [];
                    }

                    $entriesByFile[$targetFile][] = $entry['content'];
                    $processed++;

                    // Remove the processed queue file
                    @unlink($queueFile);

                } catch (Exception $e) {
                    // Skip bad entries
                    @unlink($queueFile);
                }
            }

            // Write all accumulated entries to their target files
            foreach ($entriesByFile as $targetFile => $contents) {
                try {
                    $combined = implode('', $contents);
                    @file_put_contents($targetFile, $combined, FILE_APPEND | LOCK_EX);
                } catch (Exception $e) {
                    // Log write failed - use error_log as fallback
                    error_log('Failed to write to ' . $targetFile);
                }
            }

            // Clean up any stale queue files
            self::cleanupStaleAsyncQueue(3600);

        } catch (Exception $e) {
            // Silently fail
        }

        return $processed;
    }

    /**
     * Clean up stale queue files that weren't processed.
     * Prevents queue directory from filling up with old entries.
     *
     * @param int $maxAge Maximum age in seconds
     * @return int Number of files removed
     */
    private static function cleanupStaleAsyncQueue($maxAge)
    {
        if (!is_dir(self::$asyncQueueDir)) {
            return 0;
        }

        $removed = 0;
        $now = time();

        try {
            $queueFiles = @glob(self::$asyncQueueDir . '/*.queue');

            if (!is_array($queueFiles)) {
                return 0;
            }

            foreach ($queueFiles as $queueFile) {
                try {
                    // Remove files older than maxAge
                    if ($now - @filemtime($queueFile) > $maxAge) {
                        @unlink($queueFile);
                        $removed++;
                    }
                } catch (Exception $e) {
                    // Skip
                }
            }
        } catch (Exception $e) {
            // Ignore cleanup errors
        }

        return $removed;
    }

    /**
     * Clears the buffer and resets statistics.
     * Useful in tests or when re-initialising the application.
     *
     * @return void
     */
    public static function reset()
    {
        self::$logBuffer         = [];
        self::$shutdownRegistered = false;
        self::$logStats          = [
            'buffered' => 0,
            'flushed'  => 0,
            'writes'   => 0,
            'async'    => 0,
        ];
    }

    /**
     * Enable or disable async logging.
     *
     * @param bool $enabled
     * @return void
     */
    public static function setAsyncEnabled($enabled)
    {
        self::$asyncEnabled = (bool)$enabled;
    }

    /**
     * Check if async logging is enabled.
     *
     * @return bool
     */
    public static function isAsyncEnabled()
    {
        return self::$asyncEnabled;
    }

    /**
     * Get the async queue directory path.
     *
     * @return string|null
     */
    public static function getAsyncQueueDir()
    {
        return self::$asyncQueueDir;
    }

    /**
     * Get current async queue size.
     *
     * @return int
     */
    public static function getAsyncQueueSize()
    {
        if (!is_dir(self::$asyncQueueDir)) {
            return 0;
        }

        $files = @glob(self::$asyncQueueDir . '/*.queue');
        return is_array($files) ? count($files) : 0;
    }

    /**
     * Returns the current log buffer statistics.
     *
     * @return array Array with keys 'buffered', 'flushed', and 'writes'.
     */
    public static function getStats()
    {
        return self::$logStats;
    }

    /**
     * Sets the log buffer size.
     *
     * @param   int  $size  New buffer size (1â€“1000).
     * @return void
     */
    public static function setBufferSize($size)
    {
        if ($size > 0 && $size <= 1000) {
            self::$logBufferSize = $size;
        }
    }

    /**
     * Returns the current log buffer size.
     *
     * @return int
     */
    public static function getBufferSize()
    {
        return self::$logBufferSize;
    }

    /**
     * Appends a separator line to each log file that does not already end with one.
     *
     * @global object $bearsamppRoot
     */
    public static function separator()
    {
        global $bearsamppRoot;

        $logs = [
            Path::getLogFilePath(),
            Path::getErrorLogFilePath(),
            Path::getServicesLogFilePath(),
            Path::getRegistryLogFilePath(),
            Path::getStartupLogFilePath(),
            Path::getBatchLogFilePath(),
            Path::getWinbinderLogFilePath(),
        ];

        $separator = '========================================================================================' . PHP_EOL;
        foreach ($logs as $log) {
            if (!file_exists($log)) {
                continue;
            }
            $logContent = @file_get_contents($log);
            if ($logContent !== false && !str_ends_with($logContent, $separator)) {
                file_put_contents($log, $separator, FILE_APPEND);
            }
        }
    }

    /**
     * Logs a TRACE-level message.
     *
     * @param   mixed        $data
     * @param   string|null  $file
     */
    public static function trace($data, $file = null)
    {
        self::write($data, self::TRACE, $file);
    }

    /**
     * Logs a DEBUG-level message.
     *
     * @param   mixed        $data
     * @param   string|null  $file
     */
    public static function debug($data, $file = null)
    {
        self::write($data, self::DEBUG, $file);
    }

    /**
     * Logs an INFO-level message.
     *
     * @param   mixed        $data
     * @param   string|null  $file
     */
    public static function info($data, $file = null)
    {
        self::write($data, self::INFO, $file);
    }

    /**
     * Logs a WARNING-level message.
     *
     * @param   mixed        $data
     * @param   string|null  $file
     */
    public static function warning($data, $file = null)
    {
        self::write($data, self::WARNING, $file);
    }

    /**
     * Logs an ERROR-level message.
     * Errors bypass the buffer and are written immediately.
     *
     * @param   mixed        $data
     * @param   string|null  $file
     */
    public static function error($data, $file = null)
    {
        self::write($data, self::ERROR, $file);
    }

    /**
     * Logs the initialisation of a class instance at TRACE level.
     *
     * @param   object  $classInstance
     */
    public static function initClass($classInstance)
    {
        self::trace('Init ' . get_class($classInstance));
    }

    /**
     * Logs the reloading of a class instance at TRACE level.
     *
     * @param   object  $classInstance
     */
    public static function reloadClass($classInstance)
    {
        self::trace('Reload ' . get_class($classInstance));
    }
}

