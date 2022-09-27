<?php

class Root
{
    const ERROR_HANDLER = 'errorHandler';

    public $path;
    private $procs;
    private $isRoot;

    public function __construct($rootPath)
    {
        $this->path = str_replace('\\', '/', rtrim($rootPath, '/\\'));
        $this->isRoot = $_SERVER['PHP_SELF'] == 'root.php';
    }

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

    public function initErrorHandling()
    {
        error_reporting(-1);
        ini_set('error_log', $this->getErrorLogFilePath());
        ini_set('display_errors', '1');
        set_error_handler(array($this, self::ERROR_HANDLER));
    }

    public function removeErrorHandling()
    {
        error_reporting(0);
        ini_set('error_log', null);
        ini_set('display_errors', '0');
        restore_error_handler();
    }

    public function getProcs()
    {
        return $this->procs;
    }

    public function isRoot()
    {
        return $this->isRoot;
    }

    public function getRootPath($aetrayPath = false)
    {
        $path = dirname($this->path);
        return $aetrayPath ? $this->aetrayPath($path) : $path;
    }

    private function aetrayPath($path)
    {
        $path = str_replace($this->getRootPath(), '', $path);
        return '%AeTrayMenuPath%' . substr($path, 1, strlen($path));
    }

    public function getAliasPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/alias';
    }

    public function getAppsPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/apps';
    }

    public function getBinPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/bin';
    }

    public function getCorePath($aetrayPath = false)
    {
        return $aetrayPath ? $this->aetrayPath($this->path) : $this->path;
    }

    public function getLogsPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/logs';
    }

    public function getSslPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/ssl';
    }

    public function getTmpPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/tmp';
    }

    public function getToolsPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/tools';
    }

    public function getVhostsPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/vhosts';
    }

    public function getWwwPath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/www';
    }

    public function getExeFilePath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/bearsampp.exe';
    }

    public function getConfigFilePath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/bearsampp.conf';
    }

    public function getIniFilePath($aetrayPath = false)
    {
        return $this->getRootPath($aetrayPath) . '/bearsampp.ini';
    }

    public function getSslConfPath($aetrayPath = false)
    {
        return $this->getSslPath($aetrayPath) . '/openssl.cnf';
    }

    public function getLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp.log';
    }

    public function getErrorLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-error.log';
    }

    public function getHomepageLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-homepage.log';
    }

    public function getServicesLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-services.log';
    }

    public function getRegistryLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-registry.log';
    }

    public function getStartupLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-startup.log';
    }

    public function getBatchLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-batch.log';
    }

    public function getVbsLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-vbs.log';
    }

    public function getWinbinderLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-winbinder.log';
    }

    public function getNssmLogFilePath($aetrayPath = false)
    {
        return $this->getLogsPath($aetrayPath) . '/bearsampp-nssm.log';
    }

    public function getHomepageFilePath($aetrayPath = false)
    {
        return $this->getWwwPath($aetrayPath) . '/index.php';
    }

    public function getProcessName()
    {
        return 'bearsampp';
    }

    public function getLocalUrl($request = null)
    {
        global $bearsamppBins;
        return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .
            (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost') .
            ($bearsamppBins->getApache()->getPort() != 80 && !isset($_SERVER['HTTPS']) ? ':' . $bearsamppBins->getApache()->getPort() : '') .
            (!empty($request) ? '/' . $request : '');
    }

    public static function loadCore()
    {
        global $bearsamppCore;
        $bearsamppCore = new Core();
    }

    public static function loadConfig()
    {
        global $bearsamppConfig;
        $bearsamppConfig = new Config();
    }

    public static function loadLang()
    {
        global $bearsamppLang;
        $bearsamppLang = new LangProc();
    }

    public static function loadOpenSsl()
    {
        global $bearsamppOpenSsl;
        $bearsamppOpenSsl = new OpenSsl();
    }

    public static function loadBins()
    {
        global $bearsamppBins;
        $bearsamppBins = new Bins();
    }

    public static function loadTools()
    {
        global $bearsamppTools;
        $bearsamppTools = new Tools();
    }

    public static function loadApps()
    {
        global $bearsamppApps;
        $bearsamppApps = new Apps();
    }

    public static function loadWinbinder()
    {
        global $bearsamppWinbinder;
        if (extension_loaded('winbinder')) {
            $bearsamppWinbinder = new WinBinder();
        }
    }

    public static function loadRegistry()
    {
        global $bearsamppRegistry;
        $bearsamppRegistry = new Registry();
    }

    public static function loadHomepage()
    {
        global $bearsamppHomepage;
        $bearsamppHomepage = new Homepage();
    }

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

function debugStringPregReplace($match)
{
    return '  #' . ($match[1] - 1);
}
