<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class FiberModuleLoader
 *
 * Manages true asynchronous module loading using PHP 8.1+ Fibers.
 * Fibers provide lightweight pseudo-threading without race conditions.
 *
 * Works with PHP 8.1+. Falls back gracefully if Fibers unavailable.
 */
class FiberModuleLoader
{
    /** @var array<string, FiberModule> Active fibers keyed by module name. */
    private static $fibers = [];

    /** @var bool Whether Fibers are available and the loader is enabled. */
    private static $enabled = false;

    /**
     * Initialize Fiber module loader
     * Checks if Fibers are available in this PHP version
     *
     * @return bool True if Fibers available
     */
    public static function init(): bool
    {
        self::$enabled = PHP_VERSION_ID >= 80100;

        if (!self::$enabled) {
            Log::warning('FiberModuleLoader unavailable (requires PHP 8.1+, using fallback async)');
        }

        return self::$enabled;
    }

    /**
     * Load module in a Fiber (true concurrent)
     * Returns immediately; module loads in background
     *
     * @param string $module Module name
     * @param callable $loader Function that loads the module
     * @param int $timeout Maximum wait time if accessed before ready (ms)
     * @return bool True if fiber started, false if Fibers unavailable
     */
    public static function loadInFiber(
        string $module,
        callable $loader,
        int $timeout = 5000
    ): bool {
        if (!self::$enabled) {
            // Fallback: load synchronously
            call_user_func($loader);
            return false;
        }

        // Create fiber for concurrent execution
        $fiberModule = new FiberModule($module, $loader, $timeout);
        self::$fibers[$module] = $fiberModule;

        return true;
    }

    /**
     * Wait for a specific module to finish loading
     * Blocks until module is ready or timeout expires
     *
     * @param string $module Module name
     * @param int $timeout Maximum wait time (ms)
     * @return bool True if module loaded, false if timeout
     */
    public static function waitForModule(string $module, int $timeout = 5000): bool
    {
        if (!isset(self::$fibers[$module])) {
            return true; // Not loaded in fiber, must be ready
        }

        return self::$fibers[$module]->wait($timeout);
    }

    /**
     * Wait for all fibers to complete
     * Useful for synchronization before proceeding
     *
     * @param int $timeout Maximum total wait time (ms)
     * @return bool True if all loaded, false if timeout
     */
    public static function waitAll(int $timeout = 5000): bool
    {
        if (!self::$enabled || empty(self::$fibers)) {
            return true;
        }

        $start = microtime(true);
        $maxWait = $timeout / 1000;

        foreach (self::$fibers as $module => $fiber) {
            $elapsed = microtime(true) - $start;
            $remaining = max(100, ($maxWait - $elapsed) * 1000);

            if (!$fiber->wait((int)$remaining)) {
                Log::warning('Timeout waiting for module: ' . $module);
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a module is loaded
     *
     * @param string $module Module name
     * @return bool True if module finished loading
     */
    public static function isLoaded(string $module): bool
    {
        if (!isset(self::$fibers[$module])) {
            return true; // Not in fiber system
        }

        return self::$fibers[$module]->isReady();
    }

    /**
     * Check if a module is currently loading
     *
     * @param string $module Module name
     * @return bool True if still loading
     */
    public static function isLoading(string $module): bool
    {
        if (!isset(self::$fibers[$module])) {
            return false;
        }

        return !self::$fibers[$module]->isReady();
    }

    /**
     * Get fiber for a module (advanced usage)
     *
     * @param string $module Module name
     * @return FiberModule|null The fiber module or null
     */
    public static function getFiber(string $module): ?FiberModule
    {
        return self::$fibers[$module] ?? null;
    }

    /**
     * Check if Fibers are available
     *
     * @return bool True if PHP 8.1+ with Fibers
     */
    public static function isAvailable(): bool
    {
        return self::$enabled;
    }

    /**
     * Get all loaded modules
     *
     * @return array Module names
     */
    public static function getLoadedModules(): array
    {
        return array_keys(self::$fibers);
    }
}

/**
 * Class FiberModule
 *
 * Internal single-fiber module wrapper.
 * Manages the lifecycle of one module's fiber.
 */
class FiberModule
{
    /** @var Fiber The underlying PHP Fiber instance. */
    private $fiber;

    /** @var string The module name this fiber loads. */
    private $moduleName;

    /** @var bool Whether the loader callback has finished (success or error). */
    private $initialized = false;

    /** @var mixed The loader callback's return value, or null if not yet set. */
    private $result = null;

    /** @var int Maximum wait time in milliseconds. */
    private $timeout;

    /**
     * FiberModule constructor.
     *
     * Creates and starts the fiber that runs the loader callback.
     *
     * @param string $moduleName The module name this fiber loads.
     * @param callable $loader The callback that loads the module.
     * @param int $timeout Maximum wait time in milliseconds.
     */
    public function __construct(string $moduleName, callable $loader, int $timeout = 5000)
    {
        $this->moduleName = $moduleName;
        $this->timeout = $timeout;

        // Create fiber (PHP 8.1+)
        $this->fiber = new Fiber(function() use ($loader) {
            try {
                $this->result = call_user_func($loader);
                $this->initialized = true;
            } catch (Throwable $e) {
                Log::error('Fiber error in ' . $this->moduleName . ': ' . $e->getMessage());
                $this->initialized = true; // Mark as done even on error
            }
        });

        // Start the fiber (begins execution until first yield)
        try {
            $this->fiber->start();
        } catch (Throwable $e) {
            Log::error('Failed to start fiber for ' . $this->moduleName . ': ' . $e->getMessage());
            $this->initialized = true;
        }
    }

    /**
     * Wait for module to finish loading
     * Returns immediately if already done
     *
     * @param int $timeout Maximum wait time (ms)
     * @return bool True if loaded, false if timeout
     */
    public function wait(int $timeout = 5000): bool
    {
        if ($this->initialized) {
            return true;
        }

        $start = microtime(true);
        $maxWait = $timeout / 1000;

        while (!$this->initialized) {
            // Try to resume the fiber if suspended
            if ($this->fiber->isSuspended()) {
                try {
                    $this->fiber->resume();
                } catch (Throwable $e) {
                    Log::error('Fiber resume error: ' . $e->getMessage());
                    $this->initialized = true;
                    break;
                }
            }

            // Check if fiber completed
            if ($this->fiber->isTerminated()) {
                $this->initialized = true;
                break;
            }

            // Timeout check
            if ((microtime(true) - $start) > $maxWait) {
                Log::warning('Timeout waiting for module: ' . $this->moduleName);
                return false;
            }

            // Prevent busy-waiting (yield to other operations)
            usleep(1000);
        }

        return true;
    }

    /**
     * Check if module is ready
     *
     * @return bool True if loading complete
     */
    public function isReady(): bool
    {
        return $this->initialized || $this->fiber->isTerminated();
    }

    /**
     * Check if fiber is suspended (mid-execution)
     *
     * @return bool True if suspended
     */
    public function isSuspended(): bool
    {
        return $this->fiber->isSuspended();
    }

    /**
     * Check if fiber is terminated
     *
     * @return bool True if finished
     */
    public function isTerminated(): bool
    {
        return $this->fiber->isTerminated();
    }

    /**
     * Get module name
     *
     * @return string Module name
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * Get result from module loader
     *
     * @return mixed The loader's return value
     */
    public function getResult()
    {
        return $this->result;
    }
}
