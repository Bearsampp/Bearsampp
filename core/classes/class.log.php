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
 * Log class providing static logging methods with buffered I/O.
 *
 * Supports five log levels (TRACE, DEBUG, INFO, WARNING, ERROR), a separator
 * utility, class-lifecycle helpers, and a configurable write buffer to reduce
 * file-system pressure.
 *
 * Call Log::init() once during bootstrap (after globals are available) to register
 * the shutdown flush handler.
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
    ];

    /**
     * Registers the shutdown flush handler.
     * Call once during bootstrap after globals are initialised.
     *
     * @return void
     */
    public static function init()
    {
        if (!self::$shutdownRegistered) {
            register_shutdown_function([__CLASS__, 'flush']);
            self::$shutdownRegistered = true;
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
                ? $bearsamppRoot->getErrorLogFilePath()
                : $bearsamppRoot->getLogFilePath();

            if (!$bearsamppRoot->isRoot()) {
                $file = $bearsamppRoot->getHomepageLogFilePath();
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

            // Flush immediately for errors, or when the buffer is full
            if ($type === self::ERROR || count(self::$logBuffer) >= self::$logBufferSize) {
                self::flush();
            }
        }
    }

    /**
     * Flushes the log buffer to disk.
     * Groups entries by file to minimise file operations.
     * Falls back to error_log() if globals are unavailable or a write fails.
     *
     * @return void
     */
    public static function flush()
    {
        if (empty(self::$logBuffer)) {
            return;
        }

        global $bearsamppCore;

        // If the core global is gone (e.g. during an abnormal shutdown), fall back to error_log
        if (!isset($bearsamppCore)) {
            foreach (self::$logBuffer as $log) {
                error_log('[' . date('Y-m-d H:i:s', $log['time']) . '] [' . $log['type'] . '] ' . $log['data']);
            }
            self::$logStats['flushed'] += count(self::$logBuffer);
            self::$logBuffer = [];
            return;
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

            $written = @file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
            if ($written === false) {
                // File write failed — ensure entries are not silently lost
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
        ];
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
     * @param   int  $size  New buffer size (1–1000).
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
            $bearsamppRoot->getLogFilePath(),
            $bearsamppRoot->getErrorLogFilePath(),
            $bearsamppRoot->getServicesLogFilePath(),
            $bearsamppRoot->getRegistryLogFilePath(),
            $bearsamppRoot->getStartupLogFilePath(),
            $bearsamppRoot->getBatchLogFilePath(),
            $bearsamppRoot->getWinbinderLogFilePath(),
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
