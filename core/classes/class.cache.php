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
 * Cache class for managing file scan caching with integrity verification.
 *
 * This class handles caching of file scan results with optional integrity checks
 * to prevent tampering. It supports both in-memory and file-based caching with
 * configurable cache duration.
 *
 * Usage Example:
 * ```
 * $cached = Cache::get('my_cache_key');
 * if ($cached === false) {
 *     $data = performExpensiveOperation();
 *     Cache::set('my_cache_key', $data);
 * }
 * ```
 */
class Cache
{
    /**
     * Cache for file scan results
     * @var array|null
     */
    private static $fileScanCache = null;

    /**
     * Cache validity duration in seconds (default: 1 hour)
     * @var int
     */
    private static $fileScanCacheDuration = 3600;

    /**
     * Statistics for monitoring file scan cache effectiveness
     * @var array
     */
    private static $fileScanStats = [
        'hits' => 0,
        'misses' => 0,
        'invalidations' => 0
    ];

    /**
     * Secret key for cache file integrity verification
     * Generated once per session to prevent cache tampering
     * @var string|null
     */
    private static $cacheIntegrityKey = null;

    /**
     * Gets cached file scan results if valid.
     * Includes integrity verification to prevent cache tampering.
     *
     * @param   string  $cacheKey  The cache key to retrieve.
     *
     * @return array|false Returns cached results or false if cache is invalid/missing.
     */
    public static function get($cacheKey)
    {
        global $bearsamppRoot;

        // Check if we have in-memory cache first
        if (self::$fileScanCache !== null && isset(self::$fileScanCache[$cacheKey])) {
            $cache = self::$fileScanCache[$cacheKey];

            // Check if cache is still valid
            if (time() - $cache['timestamp'] < self::$fileScanCacheDuration) {
                return $cache['data'];
            } else {
                self::$fileScanStats['invalidations']++;
                unset(self::$fileScanCache[$cacheKey]);
            }
        }

        // Try to load from file cache
        if (!isset($bearsamppRoot)) {
            return false;
        }

        $cacheFile = $bearsamppRoot->getTmpPath() . '/filescan_cache_' . $cacheKey . '.dat';

        if (file_exists($cacheFile)) {
            $fileContents = @file_get_contents($cacheFile);

            if ($fileContents === false) {
                return false;
            }

            // Verify HMAC over the raw file bytes BEFORE any parsing.
            // This prevents PHP object injection via magic methods (__wakeup /
            // __destruct) that would fire during unserialize() even when the
            // HMAC subsequently fails.
            if (!self::verifyCacheIntegrity($fileContents, $cacheKey)) {
                Log::warning('File scan cache integrity check failed for key: ' . $cacheKey . '. Possible tampering detected.');
                @unlink($cacheFile);
                return false;
            }

            // Integrity confirmed – now it is safe to decode.
            $cacheData = @json_decode($fileContents, true);

            if (is_array($cacheData)
                && isset($cacheData['timestamp']) && is_int($cacheData['timestamp'])
                && isset($cacheData['data'])      && is_array($cacheData['data'])
                && isset($cacheData['hmac'])      && is_string($cacheData['hmac'])
            ) {
                // Check if file cache is still valid
                if (time() - $cacheData['timestamp'] < self::$fileScanCacheDuration) {
                    // Store in memory cache for faster subsequent access
                    if (self::$fileScanCache === null) {
                        self::$fileScanCache = [];
                    }
                    self::$fileScanCache[$cacheKey] = $cacheData;

                    return $cacheData['data'];
                } else {
                    // Cache expired, delete file
                    self::$fileScanStats['invalidations']++;
                    @unlink($cacheFile);
                }
            } else {
                // Invalid cache structure, delete file
                Log::warning('Invalid cache structure detected for key: ' . $cacheKey);
                @unlink($cacheFile);
            }
        }

        return false;
    }

    /**
     * Stores file scan results in cache with integrity protection.
     *
     * @param   string  $cacheKey  The cache key to store under.
     * @param   array   $data      The scan results to cache.
     *
     * @return void
     */
    public static function set($cacheKey, $data)
    {
        global $bearsamppRoot;

        // Generate HMAC for integrity verification
        $hmac = self::generateCacheHMAC($data, $cacheKey);

        $cacheData = [
            'timestamp' => time(),
            'data' => $data,
            'hmac' => $hmac
        ];

        // Store in memory cache
        if (self::$fileScanCache === null) {
            self::$fileScanCache = [];
        }
        self::$fileScanCache[$cacheKey] = $cacheData;

        // Store in file cache
        if (isset($bearsamppRoot)) {
            $cacheFile = $bearsamppRoot->getTmpPath() . '/filescan_cache_' . $cacheKey . '.dat';
            // JSON is used instead of serialize() to eliminate the PHP object-injection
            // attack surface entirely – no magic methods can fire during json_decode().
            @file_put_contents($cacheFile, json_encode($cacheData), LOCK_EX);
            Log::debug('File scan results cached to: ' . $cacheFile);
        }
    }

    /**
     * Generates or retrieves the cache integrity key.
     * This key is unique per session to prevent cross-session cache tampering.
     *
     * @return string The cache integrity key
     */
    private static function getCacheIntegrityKey()
    {
        if (self::$cacheIntegrityKey === null) {
            global $bearsamppRoot;

            // Try to load existing key from session file
            if (isset($bearsamppRoot)) {
                $keyFile = $bearsamppRoot->getTmpPath() . '/cache_integrity.key';

                if (file_exists($keyFile)) {
                    $key = @file_get_contents($keyFile);
                    if ($key !== false && strlen($key) === 64) {
                        self::$cacheIntegrityKey = $key;
                        return self::$cacheIntegrityKey;
                    }
                }

                // Generate new key if none exists or invalid
                try {
                    self::$cacheIntegrityKey = bin2hex(random_bytes(32));
                    @file_put_contents($keyFile, self::$cacheIntegrityKey, LOCK_EX);
                } catch (Exception $e) {
                    Log::error('Failed to generate cache integrity key: ' . $e->getMessage());
                    // Fallback to a less secure but functional key
                    self::$cacheIntegrityKey = hash('sha256', uniqid('bearsampp_cache_', true));
                }
            } else {
                // Fallback if bearsamppRoot not available
                try {
                    self::$cacheIntegrityKey = bin2hex(random_bytes(32));
                } catch (Exception $e) {
                    self::$cacheIntegrityKey = hash('sha256', uniqid('bearsampp_cache_', true));
                }
            }
        }

        return self::$cacheIntegrityKey;
    }

    /**
     * Generates HMAC for cache data integrity verification.
     *
     * @param   array   $data      The data to generate HMAC for
     * @param   string  $cacheKey  The cache key
     *
     * @return string The HMAC hash
     */
    private static function generateCacheHMAC($data, $cacheKey)
    {
        $key = self::getCacheIntegrityKey();
        // Bind the HMAC to the cache key so a valid payload for key A cannot
        // be replayed under key B.
        $message = json_encode($data) . $cacheKey;
        return hash_hmac('sha256', $message, $key);
    }

    /**
     * Verifies cache file integrity using HMAC.
     *
     * @param   string  $fileContents  The JSON-encoded cache file contents
     * @param   string  $cacheKey      The cache key
     *
     * @return bool True if integrity check passes, false otherwise
     */
    private static function verifyCacheIntegrity($fileContents, $cacheKey)
    {
        // Decode with json_decode() – unlike unserialize(), this cannot
        // instantiate PHP objects or invoke any magic methods, so it is safe
        // to call before the HMAC is confirmed.
        $cacheData = @json_decode($fileContents, true);

        if (!is_array($cacheData)
            || !isset($cacheData['hmac']) || !is_string($cacheData['hmac'])
            || !isset($cacheData['data']) || !is_array($cacheData['data'])
        ) {
            return false;
        }

        $expectedHmac = self::generateCacheHMAC($cacheData['data'], $cacheKey);

        // Use hash_equals to prevent timing attacks
        return hash_equals($expectedHmac, $cacheData['hmac']);
    }

    /**
     * Clears all file scan caches.
     *
     * @return void
     */
    public static function clear()
    {
        global $bearsamppRoot;

        // Clear memory cache
        self::$fileScanCache = null;

        // Clear file caches
        if (isset($bearsamppRoot)) {
            $tmpPath = $bearsamppRoot->getTmpPath();
            $cacheFiles = glob($tmpPath . '/filescan_cache_*.dat');

            if ($cacheFiles !== false) {
                foreach ($cacheFiles as $cacheFile) {
                    @unlink($cacheFile);
                }
                Log::info('Cleared ' . count($cacheFiles) . ' file scan cache files');
            }
        }

        // Reset stats
        self::$fileScanStats = [
            'hits' => 0,
            'misses' => 0,
            'invalidations' => 0
        ];
    }

    /**
     * Gets file scan cache statistics.
     *
     * @return array Array containing hits, misses, and invalidations counts
     */
    public static function getStats()
    {
        return self::$fileScanStats;
    }

    /**
     * Sets the file scan cache duration.
     *
     * @param   int  $seconds  Cache duration in seconds (default: 3600 = 1 hour).
     *
     * @return void
     */
    public static function setDuration($seconds)
    {
        if ($seconds > 0 && $seconds <= 86400) { // Max 24 hours
            self::$fileScanCacheDuration = $seconds;
            Log::debug('File scan cache duration set to ' . $seconds . ' seconds');
        }
    }

    /**
     * Gets the current file scan cache duration.
     *
     * @return int Cache duration in seconds
     */
    public static function getDuration()
    {
        return self::$fileScanCacheDuration;
    }

    /**
     * Records a cache hit for statistics tracking.
     *
     * @return void
     */
    public static function recordHit()
    {
        self::$fileScanStats['hits']++;
    }

    /**
     * Records a cache miss for statistics tracking.
     *
     * @return void
     */
    public static function recordMiss()
    {
        self::$fileScanStats['misses']++;
    }
}
