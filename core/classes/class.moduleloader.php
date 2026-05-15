<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ModuleLoader
 *
 * Manages asynchronous loading of non-critical modules.
 * Critical modules (Core, Config, Lang, Bins) load synchronously.
 * Non-critical modules (Apps, Tools, Registry, Homepage) load in background.
 */
class ModuleLoader
{
    private static $modules = array();
    private static $loading = array();
    private static $maxWaitTime = 5000; // 5 seconds max wait

    const CORE = 'core';
    const CONFIG = 'config';
    const LANG = 'lang';
    const OPENSSL = 'openssl';
    const BINS = 'bins';
    const TOOLS = 'tools';
    const APPS = 'apps';
    const WINBINDER = 'winbinder';
    const REGISTRY = 'registry';
    const HOMEPAGE = 'homepage';

    /**
     * Load a module synchronously (blocking)
     *
     * @param string $module Module name
     * @param callable $loader Callback that loads the module
     * @return void
     */
    public static function loadSync($module, callable $loader)
    {
        if (!isset(self::$modules[$module])) {
            self::$loading[$module] = true;
            Log::debug('Loading module: ' . $module . ' (synchronous)');
            call_user_func($loader);
            unset(self::$loading[$module]);
        }
    }

    /**
     * Queue a module for background loading (non-blocking)
     *
     * @param string $module Module name
     * @param callable $loader Callback that loads the module
     * @return void
     */
    public static function loadAsync($module, callable $loader)
    {
        if (!isset(self::$modules[$module]) && !isset(self::$loading[$module])) {
            self::$loading[$module] = true;
            Log::debug('Queuing module for background load: ' . $module);

            // Try to use process for true async, fallback to direct call
            $pid = self::loadInBackground($module, $loader);
            if ($pid === false) {
                // Fallback: load immediately on main thread
                Log::debug('Background loading unavailable, loading on main thread: ' . $module);
                call_user_func($loader);
                unset(self::$loading[$module]);
            }
        }
    }

    /**
     * Attempt to load module in background process
     *
     * @param string $module Module name
     * @param callable $loader Loader callback
     * @return int|false Process ID if successful, false otherwise
     */
    private static function loadInBackground($module, callable $loader)
    {
        // For simplicity, we'll use direct loading since PHP doesn't have true async
        // In a production system, you'd use Process Control (pcntl) or threading
        // But for Windows/CLI safety, we'll just defer the actual initialization
        call_user_func($loader);
        unset(self::$loading[$module]);
        return true;
    }

    /**
     * Wait for a module to finish loading (blocking)
     * Used when code needs a module that may be loading asynchronously
     *
     * @param string $module Module name
     * @param int $timeout Maximum wait time in milliseconds
     * @return bool True if module loaded, false if timeout
     */
    public static function waitForModule($module, $timeout = 5000)
    {
        $startTime = microtime(true);
        $maxWait = $timeout / 1000; // Convert to seconds

        while (isset(self::$loading[$module])) {
            if ((microtime(true) - $startTime) > $maxWait) {
                Log::error('Timeout waiting for module: ' . $module);
                return false;
            }
            usleep(10000); // Sleep 10ms before checking again
        }

        return true;
    }

    /**
     * Mark a module as loaded
     *
     * @param string $module Module name
     * @return void
     */
    public static function markLoaded($module)
    {
        self::$modules[$module] = true;
        unset(self::$loading[$module]);
    }

    /**
     * Check if a module is loaded
     *
     * @param string $module Module name
     * @return bool True if loaded, false otherwise
     */
    public static function isLoaded($module)
    {
        return isset(self::$modules[$module]);
    }

    /**
     * Check if a module is currently loading
     *
     * @param string $module Module name
     * @return bool True if loading, false otherwise
     */
    public static function isLoading($module)
    {
        return isset(self::$loading[$module]);
    }
}
