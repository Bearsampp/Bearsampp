<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Autoloader
 *
 * This class handles the autoloading of classes within the Bearsampp application.
 * It registers itself with the SPL autoload stack and loads classes based on naming conventions.
 *
 * Performance optimizations:
 * - Implements class path caching to avoid repeated file_exists() calls
 * - Caches both successful and failed lookups to prevent redundant checks
 * - Uses static cache that persists for the entire request lifecycle
 */
class Autoloader
{
    /**
     * Cache for resolved class paths
     * @var array
     */
    private static $classMap = [];

    /**
     * Cache for failed class lookups to avoid repeated file_exists() calls
     * @var array
     */
    private static $failedLookups = [];

    /**
     * Statistics for monitoring cache effectiveness
     * @var array
     */
    private static $stats = [
        'hits' => 0,
        'misses' => 0,
        'failed_hits' => 0
    ];

    /**
     * Autoloader constructor.
     *
     * Initializes the Autoloader object.
     */
    public function __construct()
    {
    }

    /**
     * Loads the specified class file based on the class name.
     * Implements caching to improve performance on repeated class loads.
     *
     * @param string $class The name of the class to load.
     * @return bool True if the class file was successfully loaded, false otherwise.
     */
    public function load($class)
    {
        global $bearsamppRoot;

        $originalClass = $class;
        $class = strtolower($class);

        // Check if we've already successfully loaded this class
        if (isset(self::$classMap[$class])) {
            self::$stats['hits']++;
            require_once self::$classMap[$class];
            return true;
        }

        // Check if we've already determined this class doesn't exist
        if (isset(self::$failedLookups[$class])) {
            self::$stats['failed_hits']++;
            return false;
        }

        self::$stats['misses']++;

        $rootPath = $bearsamppRoot->getCorePath();
        $file = $this->resolveClassPath($class, $rootPath);

        if (!file_exists($file)) {
            // Cache the failed lookup
            self::$failedLookups[$class] = true;
            return false;
        }

        // Cache the successful path resolution
        self::$classMap[$class] = $file;
        require_once $file;
        return true;
    }

    /**
     * Resolves the file path for a given class name based on naming conventions.
     * Extracted into separate method for better maintainability and testability.
     *
     * @param string $class The lowercase class name
     * @param string $rootPath The root path to the classes directory
     * @return string The resolved file path
     */
    private function resolveClassPath($class, $rootPath)
    {
        $file = $rootPath . '/classes/class.' . $class . '.php';

        if (Util::startWith($class, 'bin')) {
            $class = $class != 'bins' ? substr_replace($class, '.', 3, 0) : $class;
            $file = $rootPath . '/classes/bins/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'tool')) {
            $class = $class != 'tools' ? substr_replace($class, '.', 4, 0) : $class;
            $file = $rootPath . '/classes/tools/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'app')) {
            $class = $class != 'apps' ? substr_replace($class, '.', 3, 0) : $class;
            $file = $rootPath . '/classes/apps/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'action')) {
            $class = $class != 'action' ? substr_replace($class, '.', 6, 0) : $class;
            $file = $rootPath . '/classes/actions/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'tplapp') && $class != 'tplapp') {
            $class = substr_replace(substr_replace($class, '.', 3, 0), '.', 7, 0);
            $file = $rootPath . '/classes/tpls/app/class.' . $class . '.php';
        } elseif (Util::startWith($class, 'tpl')) {
            $class = $class != 'tpls' ? substr_replace($class, '.', 3, 0) : $class;
            $file = $rootPath . '/classes/tpls/class.' . $class . '.php';
        }

        return $file;
    }

    /**
     * Gets the current cache statistics.
     * Useful for monitoring and debugging cache effectiveness.
     *
     * @return array Array containing hits, misses, and failed_hits counts
     */
    public static function getStats()
    {
        return self::$stats;
    }

    /**
     * Clears the autoloader cache.
     * Useful for testing or when class files are modified during runtime.
     *
     * @return void
     */
    public static function clearCache()
    {
        self::$classMap = [];
        self::$failedLookups = [];
        self::$stats = [
            'hits' => 0,
            'misses' => 0,
            'failed_hits' => 0
        ];
    }

    /**
     * Gets the size of the class map cache.
     *
     * @return int Number of cached class paths
     */
    public static function getCacheSize()
    {
        return count(self::$classMap);
    }

    /**
     * Registers the autoloader with the SPL autoload stack.
     *
     * @return bool True on success, false on failure.
     */
    public function register()
    {
        return spl_autoload_register(array($this, 'load'));
    }

    /**
     * Unregisters the autoloader from the SPL autoload stack.
     *
     * @return bool True on success, false on failure.
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'load'));
    }
}
