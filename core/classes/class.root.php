<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Root
 *
 * This class represents the root of the Bearsampp application. It handles the initialization,
 * configuration, and management of various components and settings within the application.
 */
class Root
{
    const ERROR_HANDLER = 'errorHandler';

    public $path;
    private $procs;
    private $procsLoaded = false;
    private $isRoot;

    /**
     * Constructs a Root object with the specified root path.
     *
     * @param string $rootPath The root path of the application.
     */
    public function __construct($rootPath)
    {
        $this->path = str_replace('\\', '/', rtrim($rootPath, '/\\'));
        $this->isRoot = $_SERVER['PHP_SELF'] == 'root.php';
        Path::init(dirname($this->path), $this->path);
    }

    /**
     * Registers the application components and initializes error handling.
     */
    public function register()
    {
        // Params
        set_time_limit(0);
        clearstatcache();

        // Error log
        $this->initErrorHandling();

        // External classes
        require_once $this->path . '/classes/class.log.php';
        require_once $this->path . '/classes/class.util.php';
        require_once $this->path . '/classes/class.util.string.php';
        Log::init();

        // Autoloader
        require_once $this->path . '/classes/class.autoloader.php';
        $bearsamppAutoloader = new Autoloader();
        $bearsamppAutoloader->register();

        // Module loaders for async initialization
        require_once $this->path . '/classes/class.moduleloader.php';
        require_once $this->path . '/classes/class.cachemanager.php';
        require_once $this->path . '/classes/class.fibermoduleloader.php';

        // Initialize cache system (disk caching for warm starts)
        CacheManager::init(Path::getTmpPath() . '/cache');

        // Initialize fiber loader (true async for 8.1+)
        $useFibers = FiberModuleLoader::init();

        // Load critical modules synchronously (required for main functionality)
        self::loadCore();
        self::loadConfig();
        self::loadLang();
        self::loadOpenSsl();
        self::loadBins();
        Log::separator();

        // Load non-critical modules asynchronously (for UI/admin functions)
        // Uses Fibers if PHP 8.1+, fallback to deferred loading
        if ($useFibers) {
            self::loadToolsFiber();
            self::loadAppsFiber();
            self::loadRegistryFiber();
            self::loadHomepageFiber();
        } else {
            self::loadToolsAsync();
            self::loadAppsAsync();
            self::loadRegistryAsync();
            self::loadHomepageAsync();
        }

        self::loadWinbinder();
    }

    /**
     * Initializes error handling settings for the application.
     */
    public function initErrorHandling()
    {
        error_reporting(-1);
        ini_set('error_log', Path::getErrorLogFilePath());
        ini_set('display_errors', '1');
        set_error_handler(array($this, self::ERROR_HANDLER));
    }

    /**
     * Removes the custom error handling, reverting to the default PHP error handling.
     */
    public function removeErrorHandling()
    {
        error_reporting(0);
        ini_set('error_log', null);
        ini_set('display_errors', '0');
        restore_error_handler();
    }

    /**
     * Retrieves the list of processes.
     * Lazy loads process list only when accessed.
     *
     * @return array The list of processes.
     */
    public function getProcs()
    {
        if (!$this->procsLoaded && $this->isRoot()) {
            $this->procs = Win32Ps::getListProcs();
            $this->procsLoaded = true;
        }
        return $this->procs;
    }

    /**
     * Ensures a module is loaded, waiting if it's still loading asynchronously.
     * Safe to call for modules that may be loading in background.
     * Supports both fiber-based and deferred loading mechanisms.
     *
     * @param string $module The module name (use ModuleLoader constants)
     * @param int $timeout Maximum wait time in milliseconds
     * @return bool True if module loaded successfully
     */
    public static function ensureModuleLoaded($module, $timeout = 5000)
    {
        // Try Fiber loader first (if available on PHP 8.1+)
        if (FiberModuleLoader::isAvailable()) {
            return FiberModuleLoader::waitForModule($module, $timeout);
        }

        // Fallback to deferred loading
        return ModuleLoader::waitForModule($module, $timeout);
    }

    /**
     * Get cache manager statistics
     * Useful for monitoring warm start performance
     *
     * @return array Cache statistics
     */
    public static function getCacheStats(): array
    {
        return CacheManager::getStats();
    }

    /**
     * Clear all configuration caches
     * Used on version upgrade or manual reset
     *
     * @return int Number of cache files deleted
     */
    public static function clearCaches(): int
    {
        return CacheManager::clearAll();
    }

    /**
     * Checks if the current script is executed from the root path.
     *
     * @return bool True if executed from the root, false otherwise.
     */
    public function isRoot()
    {
        return $this->isRoot;
    }

    /**
     * Loads the core components of the application.
     */
    public static function loadCore()
    {
        global $bearsamppCore;
        $bearsamppCore = new Core();
    }

    /**
     * Loads the configuration settings of the application.
     */
    public static function loadConfig()
    {
        global $bearsamppConfig;
        $bearsamppConfig = new Config();
    }

    /**
     * Loads the language settings of the application.
     */
    public static function loadLang()
    {
        global $bearsamppLang;
        $bearsamppLang = new LangProc();
    }

    /**
     * Loads the OpenSSL settings of the application.
     */
    public static function loadOpenSsl()
    {
        global $bearsamppOpenSsl;
        $bearsamppOpenSsl = new OpenSsl();
    }

    /**
     * Loads the binary components of the application.
     */
    public static function loadBins()
    {
        global $bearsamppBins;
        $bearsamppBins = new Bins();
    }

    /**
     * Loads the tools components of the application.
     */
    public static function loadTools()
    {
        global $bearsamppTools;
        if (!isset($bearsamppTools)) {
            $bearsamppTools = new Tools();
        }
    }

    /**
     * Loads the apps components of the application.
     */
    public static function loadApps()
    {
        global $bearsamppApps;
        $bearsamppApps = new Apps();
    }

    /**
     * Loads the Winbinder extension if available.
     */
    public static function loadWinbinder()
    {
        global $bearsamppWinbinder;
        if (extension_loaded('winbinder')) {
            $bearsamppWinbinder = new WinBinder();
        }
    }

    /**
     * Loads the registry settings of the application.
     */
    public static function loadRegistry()
    {
        global $bearsamppRegistry;
        $bearsamppRegistry = new Registry();
    }

    /**
     * Loads the homepage settings of the application.
     */
    public static function loadHomepage()
    {
        global $bearsamppHomepage;
        $bearsamppHomepage = new Homepage();
    }

    /**
     * Loads the tools components asynchronously.
     * Tools module loads in background without blocking main thread.
     */
    public static function loadToolsAsync()
    {
        ModuleLoader::loadAsync(ModuleLoader::TOOLS, function() {
            global $bearsamppTools;
            $bearsamppTools = new Tools();
        });
    }

    /**
     * Loads the apps components asynchronously.
     * Apps module loads in background without blocking main thread.
     */
    public static function loadAppsAsync()
    {
        ModuleLoader::loadAsync(ModuleLoader::APPS, function() {
            global $bearsamppApps;
            $bearsamppApps = new Apps();
        });
    }

    /**
     * Loads the registry settings asynchronously.
     * Registry operations are slow (COM), loads in background.
     */
    public static function loadRegistryAsync()
    {
        ModuleLoader::loadAsync(ModuleLoader::REGISTRY, function() {
            global $bearsamppRegistry;
            $bearsamppRegistry = new Registry();
        });
    }

    /**
     * Loads the homepage settings asynchronously.
     * Homepage is for UI display, can load in background.
     */
    public static function loadHomepageAsync()
    {
        ModuleLoader::loadAsync(ModuleLoader::HOMEPAGE, function() {
            global $bearsamppHomepage;
            $bearsamppHomepage = new Homepage();
        });
    }

    /**
     * Loads the tools components in a Fiber (true async, PHP 8.1+)
     * Tools module loads concurrently with main startup.
     */
    public static function loadToolsFiber()
    {
        FiberModuleLoader::loadInFiber(ModuleLoader::TOOLS, function() {
            global $bearsamppTools;
            $bearsamppTools = new Tools();
        });
    }

    /**
     * Loads the apps components in a Fiber (true async, PHP 8.1+)
     * Apps module loads concurrently with main startup.
     */
    public static function loadAppsFiber()
    {
        FiberModuleLoader::loadInFiber(ModuleLoader::APPS, function() {
            global $bearsamppApps;
            $bearsamppApps = new Apps();
        });
    }

    /**
     * Loads the registry settings in a Fiber (true async, PHP 8.1+)
     * Registry operations are slow (COM), loads concurrently.
     */
    public static function loadRegistryFiber()
    {
        FiberModuleLoader::loadInFiber(ModuleLoader::REGISTRY, function() {
            global $bearsamppRegistry;
            $bearsamppRegistry = new Registry();
        });
    }

    /**
     * Loads the homepage settings in a Fiber (true async, PHP 8.1+)
     * Homepage is for UI display, loads concurrently.
     */
    public static function loadHomepageFiber()
    {
        FiberModuleLoader::loadInFiber(ModuleLoader::HOMEPAGE, function() {
            global $bearsamppHomepage;
            $bearsamppHomepage = new Homepage();
        });
    }

    /**
     * Handles errors and logs them to the error log file.
     *
     * @param int $errno The level of the error raised.
     * @param string $errstr The error message.
     * @param string $errfile The filename that the error was raised in.
     * @param int $errline The line number the error was raised at.
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        $errfile = Path::formatUnixPath($errfile);
        $errfile = str_replace(Path::getRootPath(), '', $errfile);

        if (!defined('E_DEPRECATED')) {
            define('E_DEPRECATED', 8192);
        }

        $errNames = array(
            E_ERROR             => 'E_ERROR',
            E_WARNING           => 'E_WARNING',
            E_PARSE             => 'E_PARSE',
            E_NOTICE            => 'E_NOTICE',
            E_CORE_ERROR        => 'E_CORE_ERROR',
            E_CORE_WARNING      => 'E_CORE_WARNING',
            E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
            E_USER_ERROR        => 'E_USER_ERROR',
            E_USER_WARNING      => 'E_USER_WARNING',
            E_USER_NOTICE       => 'E_USER_NOTICE',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED        => 'E_DEPRECATED',
        );

        $content = '[' . date('Y-m-d H:i:s', time()) . '] ';
        $content .= $errNames[$errno] . ' ';
        $content .= $errstr . ' in ' .  $errfile;
        $content .= ' on line ' . $errline . PHP_EOL;
        $content .= self::debugStringBacktrace() . PHP_EOL;

        file_put_contents(Path::getErrorLogFilePath(), $content, FILE_APPEND);
    }

    /**
     * Generates a debug backtrace string.
     *
     * @return string The debug backtrace.
     */
    private static function debugStringBacktrace()
    {
        ob_start();
        debug_print_backtrace();
        $trace = ob_get_contents();
        ob_end_clean();

        $trace = preg_replace('/^#0\s+Root::debugStringBacktrace[^\n]*\n/', '', $trace, 1);
        $trace = preg_replace('/^#1\s+isRoot->errorHandler[^\n]*\n/', '', $trace, 1);
        $trace = preg_replace_callback('/^#(\d+)/m', 'debugStringPregReplace', $trace);
        return $trace;
    }
}

    /**
     * Adjusts the trace number in debug backtrace.
     *
     * @param array $match The matches from the regular expression.
     * @return string The adjusted trace number.
     */
    function debugStringPregReplace($match)
    {
        return '  #' . ($match[1] - 1);
    }
