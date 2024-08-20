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
        require_once $this->getCorePath() . '/classes/class.util.php';
        Util::logSeparator();

        // Autoloader
        require_once $this->getCorePath() . '/classes/class.autoloader.php';
        $bearsamppAutoloader = new Autoloader();
        $bearsamppAutoloader->register();

        // Load
        self::loadCore();
        self::loadConfig();
        self::loadLang();
        self::loadOpenSsl();
        self::loadBins();
        self::loadTools();
        self::loadApps();
        self::loadWinbinder();
        self::loadRegistry();
        self::loadHomepage();

        // Init
        if ($this->isRoot) {
            $this->procs = Win32Ps::getListProcs();
        }
    }

    /**
     * Initializes error handling settings for the application.
     */
    public function initErrorHandling()
    {
        error_reporting(-1);
        ini_set('error_log', $this->getErrorLogFilePath());
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
     *
     * @return array The list of processes.
     */
    public function getProcs()
    {
        return $this->procs;
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
     * Gets the root path, optionally formatted for AeTrayMenu.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The root path.
     */
    public function getRootPath($aetrayPath = false)
    {
        $path = dirname($this->path);
        return $aetrayPath ? $this->aetrayPath($path) : $path;
    }

    /**
     * Formats a path for AeTrayMenu.
     *
     * @param string $path The path to format.
     * @return string The formatted path.
     */
    private function aetrayPath($path)
    {
        $path = str_replace($this->getRootPath(), '', $path);
        return '%AeTrayMenuPath%' . substr($path, 1, strlen($path));
    }

    /**
     * Gets the path to the alias directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The alias path.
     */
    public function getAliasPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/alias';
    }

    /**
     * Gets the path to the apps directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The apps path.
     */
    public function getAppsPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/apps';
    }

    /**
     * Gets the path to the bin directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The bin path.
     */
    public function getBinPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/bin';
    }

    /**
     * Gets the path to the core directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The core path.
     */
    public function getCorePath($aetrayPath = false)
    {
        return $aetrayPath ? $this->aetrayPath($this->path) : $this->path;
    }

    /**
     * Gets the path to the logs directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The logs path.
     */
    public function getLogsPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/logs';
    }

    /**
     * Gets the path to the SSL directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The SSL path.
     */
    public function getSslPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/ssl';
    }

    /**
     * Gets the path to the temporary directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The temporary path.
     */
    public function getTmpPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/tmp';
    }

    /**
     * Gets the path to the tools directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The tools path.
     */
    public function getToolsPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/tools';
    }

    /**
     * Gets the path to the virtual hosts directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The virtual hosts path.
     */
    public function getVhostsPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/vhosts';
    }

    /**
     * Gets the path to the WWW directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The WWW path.
     */
    public function getWwwPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/www';
    }

    /**
     * Gets the path to the executable file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The executable file path.
     */
    public function getExeFilePath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/bearsampp.exe';
    }

    /**
     * Gets the path to the configuration file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The configuration file path.
     */
    public function getConfigFilePath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/bearsampp.conf';
    }

    /**
     * Gets the path to the INI file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The INI file path.
     */
    public function getIniFilePath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/bearsampp.ini';
    }

    /**
     * Gets the path to the SSL configuration file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The SSL configuration file path.
     */
    public function getSslConfPath($aetrayPath = false)
    {
        return $this->getSslPath($aetrayPath) . '/openssl.cnf';
    }

    /**
     * Gets the path to the log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The log file path.
     */
    public function getLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp.log';
    }

    /**
     * Gets the path to the error log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The error log file path.
     */
    public function getErrorLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-error.log';
    }

    /**
     * Gets the path to the homepage log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The homepage log file path.
     */
    public function getHomepageLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-homepage.log';
    }

    /**
     * Gets the path to the services log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The services log file path.
     */
    public function getServicesLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-services.log';
    }

    /**
     * Gets the path to the registry log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The registry log file path.
     */
    public function getRegistryLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-registry.log';
    }

    /**
     * Gets the path to the startup log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The startup log file path.
     */
    public function getStartupLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-startup.log';
    }

    /**
     * Gets the path to the batch log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The batch log file path.
     */
    public function getBatchLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-batch.log';
    }

    /**
     * Gets the path to the VBS log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The VBS log file path.
     */
    public function getVbsLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-vbs.log';
    }

    /**
     * Gets the path to the Winbinder log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The Winbinder log file path.
     */
    public function getWinbinderLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-winbinder.log';
    }

    /**
     * Gets the path to the NSSM log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The NSSM log file path.
     */
    public function getNssmLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-nssm.log';
    }

    /**
     * Gets the path to the homepage file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The homepage file path.
     */
    public function getHomepageFilePath($aetrayPath = false)
    {
        return $this->getWwwPath($aetrayPath) . '/index.php';
    }

    /**
     * Gets the name of the process.
     *
     * @return string The process name.
     */
    public function getProcessName()
    {
        return 'bearsampp';
    }

    /**
     * Constructs a local URL with the specified request.
     *
     * @param string|null $request The specific request to append to the URL.
     * @return string The constructed local URL.
     */
    public function getLocalUrl($request = null)
    {
        global $bearsamppBins;
        return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .
            (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost') .
            ($bearsamppBins->getApache()->getPort() != 80 && !isset($_SERVER['HTTPS']) ? ':' . $bearsamppBins->getApache()->getPort() : '') .
            (!empty($request) ? '/' . $request : '');
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
        $bearsamppTools = new Tools();
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
     * Handles errors and logs them to the error log file.
     *
     * @param int $errno The level of the error raised.
     * @param string $errstr The error message.
     * @param string $errfile The filename that the error was raised in.
     * @param int $errline The line number the error was raised at.
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() === 0) {
            return;
        }

        $errfile = Util::formatUnixPath($errfile);
        $errfile = str_replace($this->getRootPath(), '', $errfile);

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
            E_STRICT            => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED        => 'E_DEPRECATED',
        );

        $content = '[' . date('Y-m-d H:i:s', time()) . '] ';
        $content .= $errNames[$errno] . ' ';
        $content .= $errstr . ' in ' .  $errfile;
        $content .= ' on line ' . $errline . PHP_EOL;
        $content .= self::debugStringBacktrace() . PHP_EOL;

        file_put_contents($this->getErrorLogFilePath(), $content, FILE_APPEND);
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
