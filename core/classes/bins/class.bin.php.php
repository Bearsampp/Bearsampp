<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Manages PHP configurations and operations within the Bearsampp environment.
 * This class extends the Module class and provides functionalities specific to PHP,
 * such as switching PHP versions, managing PHP configurations, and handling PHP extensions.
 */
class BinPhp extends Module
{
    const ROOT_CFG_ENABLE = 'phpEnable';
    const ROOT_CFG_VERSION = 'phpVersion';

    const LOCAL_CFG_CLI_EXE = 'phpCliExe';
    const LOCAL_CFG_CLI_SILENT_EXE = 'phpCliSilentExe';
    const LOCAL_CFG_CONF = 'phpConf';
    const LOCAL_CFG_PEAR_EXE = 'phpPearExe';

    const INI_SHORT_OPEN_TAG = 'short_open_tag';
    const INI_ASP_TAGS = 'asp_tags';
    const INI_Y2K_COMPLIANCE = 'y2k_compliance';
    const INI_OUTPUT_BUFFERING = 'output_buffering';
    const INI_ZLIB_OUTPUT_COMPRESSION = 'zlib.output_compression';
    const INI_IMPLICIT_FLUSH = 'implicit_flush';
    const INI_ALLOW_CALL_TIME_PASS_REFERENCE = 'allow_call_time_pass_reference';
    const INI_SAFE_MODE = 'safe_mode';
    const INI_SAFE_MODE_GID = 'safe_mode_gid';
    const INI_EXPOSE_PHP = 'expose_php';
    const INI_DISPLAY_ERRORS = 'display_errors';
    const INI_DISPLAY_STARTUP_ERRORS = 'display_startup_errors';
    const INI_LOG_ERRORS = 'log_errors';
    const INI_IGNORE_REPEATED_ERRORS = 'ignore_repeated_errors';
    const INI_IGNORE_REPEATED_SOURCE = 'ignore_repeated_source';
    const INI_REPORT_MEMLEAKS = 'report_memleaks';
    const INI_TRACK_ERRORS = 'track_errors';
    const INI_HTML_ERRORS = 'html_errors';
    const INI_REGISTER_GLOBALS = 'register_globals';
    const INI_REGISTER_LONG_ARRAYS = 'register_long_arrays';
    const INI_REGISTER_ARGC_ARGV = 'register_argc_argv';
    const INI_AUTO_GLOBALS_JIT = 'auto_globals_jit';
    const INI_MAGIC_QUOTES_GPC = 'magic_quotes_gpc';
    const INI_MAGIC_QUOTES_RUNTIME = 'magic_quotes_runtime';
    const INI_MAGIC_QUOTES_SYBASE = 'magic_quotes_sybase';
    const INI_ENABLE_DL = 'enable_dl';
    const INI_CGI_FORCE_REDIRECT = 'cgi.force_redirect';
    const INI_CGI_FIX_PATHINFO = 'cgi.fix_pathinfo';
    const INI_FILE_UPLOADS = 'file_uploads';
    const INI_ALLOW_URL_FOPEN = 'allow_url_fopen';
    const INI_ALLOW_URL_INCLUDE = 'allow_url_include';
    const INI_PHAR_READONLY = 'phar.readonly';
    const INI_PHAR_REQUIRE_HASH = 'phar.require_hash';
    const INI_DEFINE_SYSLOG_VARIABLES = 'define_syslog_variables';
    const INI_MAIL_ADD_X_HEADER = 'mail.add_x_header';
    const INI_SQL_SAFE_MODE = 'sql.safe_mode';
    const INI_ODBC_ALLOW_PERSISTENT = 'odbc.allow_persistent';
    const INI_ODBC_CHECK_PERSISTENT = 'odbc.check_persistent';
    const INI_MYSQL_ALLOW_LOCAL_INFILE = 'mysql.allow_local_infile';
    const INI_MYSQL_ALLOW_PERSISTENT = 'mysql.allow_persistent';
    const INI_MYSQL_TRACE_MODE = 'mysql.trace_mode';
    const INI_MYSQLI_ALLOW_PERSISTENT = 'mysqli.allow_persistent';
    const INI_MYSQLI_RECONNECT = 'mysqli.reconnect';
    const INI_MYSQLND_COLLECT_STATISTICS = 'mysqlnd.collect_statistics';
    const INI_MYSQLND_COLLECT_MEMORY_STATISTICS = 'mysqlnd.collect_memory_statistics';
    const INI_PGSQL_ALLOW_PERSISTENT = 'pgsql.allow_persistent';
    const INI_PGSQL_AUTO_RESET_PERSISTENT = 'pgsql.auto_reset_persistent';
    const INI_SYBCT_ALLOW_PERSISTENT = 'sybct.allow_persistent';
    const INI_SESSION_USE_COOKIES = 'session.use_cookies';
    const INI_SESSION_USE_ONLY_COOKIES = 'session.use_only_cookies';
    const INI_SESSION_AUTO_START = 'session.auto_start';
    const INI_SESSION_COOKIE_HTTPONLY = 'session.cookie_httponly';
    const INI_SESSION_BUG_COMPAT_42 = 'session.bug_compat_42';
    const INI_SESSION_BUG_COMPAT_WARN = 'session.bug_compat_warn';
    const INI_SESSION_USE_TRANS_SID = 'session.use_trans_sid';
    const INI_MSSQL_ALLOW_PERSISTENT = 'mssql.allow_persistent';
    const INI_MSSQL_COMPATIBILITY_MODE = 'mssql.compatability_mode';
    const INI_MSSQL_SECURE_CONNECTION = 'mssql.secure_connection';
    const INI_TIDY_CLEAN_OUTPUT = 'tidy.clean_output';
    const INI_SOAP_WSDL_CACHE_ENABLED = 'soap.wsdl_cache_enabled';
    const INI_XDEBUG_REMOTE_ENABLE = 'xdebug.remote_enable';
    const INI_XDEBUG_PROFILER_ENABLE = 'xdebug.profiler_enable';
    const INI_XDEBUG_PROFILER_ENABLE_TRIGGER = 'xdebug.profiler_enable_trigger';
    const INI_APC_ENABLED = 'apc.enabled';
    const INI_APC_INCLUDE_ONCE_OVERRIDE = 'apc.include_once_override';
    const INI_APC_CANONICALIZE = 'apc.canonicalize';
    const INI_APC_STAT = 'apc.stat';

    // Constants for configuration keys and other identifiers
    // ...

    /**
     * @var string Path to the Apache configuration file.
     */
    private $apacheConf;

    /**
     * @var string Path to the PHP error log file.
     */
    private $errorLog;

    /**
     * @var string Path to the PHP CLI executable.
     */
    private $cliExe;

    /**
     * @var string Path to the PHP CLI silent executable.
     */
    private $cliSilentExe;

    /**
     * @var string Path to the PHP configuration file.
     */
    private $conf;

    /**
     * @var string Path to the PHP PEAR executable.
     */
    private $pearExe;

    /**
     * Constructor for the BinPhp class.
     * Initializes the PHP module by loading its configuration and setting up paths.
     *
     * @param string $id The identifier for the PHP module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the PHP module configuration and updates paths based on the current settings.
     * This method is typically called when PHP settings or versions are changed.
     *
     * @param string|null $id Optional identifier for the PHP module, used when reloading.
     * @param string|null $type Optional type of the module, used when reloading.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppBins, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::PHP);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->enable = $this->enable && $bearsamppConfig->getRaw(self::ROOT_CFG_ENABLE);
        $this->apacheConf = $bearsamppBins->getApache()->getCurrentPath() . '/' . $this->apacheConf; //FIXME: Useful ?
        $this->errorLog = $bearsamppRoot->getLogsPath() . '/php_error.log';

        if ($this->bearsamppConfRaw !== false) {
            $this->cliExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CLI_EXE];
            $this->cliSilentExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CLI_SILENT_EXE];
            $this->conf = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->pearExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_PEAR_EXE];
        }

        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        if (!is_dir($this->currentPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->currentPath));
            return;
        }
        if (!is_dir($this->symlinkPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->symlinkPath));
            return;
        }
        if (!is_file($this->bearsamppConf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->bearsamppConf));
            return;
        }
        if (!is_file($this->cliExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->cliExe));
        }
        if (!is_file($this->cliSilentExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->cliSilentExe));
        }
        if (!is_file($this->conf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->conf));
        }
        if (!is_file($this->pearExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->pearExe));
        }
    }

    /**
     * Switches the PHP version to the specified version.
     * This involves updating configuration files and notifying other components of the change.
     *
     * @param string $version The target PHP version to switch to.
     * @param bool $showWindow Whether to show a window with the progress or status.
     * @return bool Returns true if the switch was successful, false otherwise.
     */
    public function switchVersion($version, $showWindow = false) {
        Util::logDebug('Switch ' . $this->name . ' version to ' . $version);
        return $this->updateConfig($version, 0, $showWindow);
    }

    /**
     * Updates the PHP configuration to reflect the specified version.
     * This method is called internally by switchVersion.
     *
     * @param string|null $version The PHP version to update the configuration for.
     * @param int $sub Sub-level for logging depth, used for nested calls.
     * @param bool $showWindow Whether to show a window with the progress or status.
     * @return bool Returns true if the configuration update was successful, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppLang, $bearsamppBins, $bearsamppApps, $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->getName(), $version);

        //$phpPath = str_replace('php' . $this->getVersion(), 'php' . $version, $this->getCurrentPath());
        $conf = str_replace('php' . $this->getVersion(), 'php' . $version, $this->getConf());
        $bearsamppConf = str_replace('php' . $this->getVersion(), 'php' . $version, $this->bearsamppConf);

        $tsDll = $this->getTsDll($version);
        //$apacheShortVersion = substr(str_replace('.', '', $bearsamppBins->getApache()->getVersion()), 0, 2);
        //$apachePhpModuleName = $tsDll !== false ? substr($tsDll, 0, 4) . '_module' : null;
        $apachePhpModulePath = $this->getApacheModule($bearsamppBins->getApache()->getVersion(), $version);

        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'PHP TsDll found: ' . $tsDll);
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'PHP Apache module found: ' . $apachePhpModulePath);

        if (!file_exists($conf) || !file_exists($bearsamppConf)) {
            Util::logError('bearsampp config files not found for ' . $this->getName() . ' ' . $version);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::BEARSAMPP_CONF_NOT_FOUND_ERROR), $this->getName() . ' ' . $version),
                    $boxTitle
                );
            }
            return false;
        }

        $bearsamppConfRaw = parse_ini_file($bearsamppConf);
        if ($bearsamppConfRaw === false || !isset($bearsamppConfRaw[self::ROOT_CFG_VERSION]) || $bearsamppConfRaw[self::ROOT_CFG_VERSION] != $version) {
            Util::logError('bearsampp config file malformed for ' . $this->getName() . ' ' . $version);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::BEARSAMPP_CONF_MALFORMED_ERROR), $this->getName() . ' ' . $version),
                    $boxTitle
                );
            }
            return false;
        }

        if ($tsDll === false || $apachePhpModulePath === false) {
            Util::logDebug($this->getName() . ' ' . $version . ' does not seem to be compatible with Apache ' . $bearsamppBins->getApache()->getVersion());
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::PHP_INCPT), $version, $bearsamppBins->getApache()->getVersion()),
                    $boxTitle
                );
            }
            return false;
        }

        // bearsampp.conf
        $this->setVersion($version);

        // conf
        Util::replaceInFile($this->getConf(), array(
            '/^mysql.default_port\s=\s(\d+)/' => 'mysql.default_port = ' . $bearsamppBins->getMysql()->getPort(),
            '/^mysqli.default_port\s=\s(\d+)/' => 'mysqli.default_port = ' . $bearsamppBins->getMysql()->getPort()
        ));

        // apache
        $bearsamppBins->getApache()->update($sub + 1);

        // phpmyadmin
        $bearsamppApps->getPhpmyadmin()->update($sub + 1);

        return true;
    }

    /**
     * Retrieves the settings for the PHP module.
     * This method returns a structured array of PHP settings categories and their respective settings.
     *
     * @return array An associative array of PHP settings organized by categories.
     */
    public function getSettings() {
        return array(
            'Language options' => array(
                'Short open tag' => self::INI_SHORT_OPEN_TAG,
                'ASP-style tags' => self::INI_ASP_TAGS,
                'Year 2000 compliance' => self::INI_Y2K_COMPLIANCE,
                'Output buffering' => self::INI_OUTPUT_BUFFERING,
                'Zlib output compression' => self::INI_ZLIB_OUTPUT_COMPRESSION,
                'Implicit flush' => self::INI_IMPLICIT_FLUSH,
                'Allow call time pass reference' => self::INI_ALLOW_CALL_TIME_PASS_REFERENCE,
                'Safe mode' => self::INI_SAFE_MODE,
                'Safe mode GID' => self::INI_SAFE_MODE_GID,
            ),
            'Miscellaneous' => array(
                'Expose PHP' => self::INI_EXPOSE_PHP,
            ),
            'Error handling and logging' => array(
                'Display errors' => self::INI_DISPLAY_ERRORS,
                'Display startup errors' => self::INI_DISPLAY_STARTUP_ERRORS,
                'Log errors' => self::INI_LOG_ERRORS,
                'Ignore repeated errors' => self::INI_IGNORE_REPEATED_ERRORS,
                'Ignore repeated source' => self::INI_IGNORE_REPEATED_SOURCE,
                'Report memory leaks' => self::INI_REPORT_MEMLEAKS,
                'Track errors' => self::INI_TRACK_ERRORS,
                'HTML errors' => self::INI_HTML_ERRORS,
            ),
            'Data Handling' => array(
                'Register globals' => self::INI_REGISTER_GLOBALS,
                'Register long arrays' => self::INI_REGISTER_LONG_ARRAYS,
                'Register argc argv' => self::INI_REGISTER_ARGC_ARGV,
                'Auto globals just in time' => self::INI_AUTO_GLOBALS_JIT,
                'Magic quotes gpc' => self::INI_MAGIC_QUOTES_GPC,
                'Magic quotes runtime' => self::INI_MAGIC_QUOTES_RUNTIME,
                'Magic quotes Sybase' => self::INI_MAGIC_QUOTES_SYBASE,
            ),
            'Paths and Directories' => array(
                'Enable dynamic loading' => self::INI_ENABLE_DL,
                'CGI force redirect' => self::INI_CGI_FORCE_REDIRECT,
                'CGI fix path info' => self::INI_CGI_FIX_PATHINFO,
            ),
            'File uploads' => array(
                'File uploads' => self::INI_FILE_UPLOADS,
            ),
            'Fopen wrappers' => array(
                'Allow url fopen' => self::INI_ALLOW_URL_FOPEN,
                'Allow url include' => self::INI_ALLOW_URL_INCLUDE,
            ),
            'Module settings' => array(
                'Phar' => array(
                    'Read only' => self::INI_PHAR_READONLY,
                    'Require hash' => self::INI_PHAR_REQUIRE_HASH,
                ),
                'Syslog' => array(
                    'Define syslog variables' => self::INI_DEFINE_SYSLOG_VARIABLES,
                ),
                'Mail' => array(
                    'Add X-PHP-Originating-Script' => self::INI_MAIL_ADD_X_HEADER,
                ),
                'SQL' => array(
                    'Safe mode' => self::INI_SQL_SAFE_MODE,
                ),
                'ODBC' => array(
                    'Allow persistent' => self::INI_ODBC_ALLOW_PERSISTENT,
                    'Check persistent' => self::INI_ODBC_CHECK_PERSISTENT,
                ),
                'MySQL' => array(
                    'Allow local infile' => self::INI_MYSQL_ALLOW_LOCAL_INFILE,
                    'Allow persistent' => self::INI_MYSQL_ALLOW_PERSISTENT,
                    'Trace mode' => self::INI_MYSQL_TRACE_MODE,
                ),
                'MySQLi' => array(
                    'Allow persistent' => self::INI_MYSQLI_ALLOW_PERSISTENT,
                    'Reconnect' => self::INI_MYSQLI_RECONNECT,
                ),
                'MySQL Native Driver' => array(
                    'Collect statistics' => self::INI_MYSQLND_COLLECT_STATISTICS,
                    'Collect memory statistics' => self::INI_MYSQLND_COLLECT_MEMORY_STATISTICS,
                ),
                'PostgresSQL' => array(
                    'Allow persistent' => self::INI_PGSQL_ALLOW_PERSISTENT,
                    'Auto reset persistent' => self::INI_PGSQL_AUTO_RESET_PERSISTENT,
                ),
                'Sybase-CT' => array(
                    'Allow persistent' => self::INI_SYBCT_ALLOW_PERSISTENT,
                ),
                'Session' => array(
                    'Use cookies' => self::INI_SESSION_USE_COOKIES,
                    'Use only cookies' => self::INI_SESSION_USE_ONLY_COOKIES,
                    'Auto start' => self::INI_SESSION_AUTO_START,
                    'Cookie HTTP only' => self::INI_SESSION_COOKIE_HTTPONLY,
                    'Bug compat 42' => self::INI_SESSION_BUG_COMPAT_42,
                    'Bug compat warning' => self::INI_SESSION_BUG_COMPAT_WARN,
                    'Use trans sid' => self::INI_SESSION_USE_TRANS_SID,
                ),
                'MSSQL' => array(
                    'Allow persistent' => self::INI_MSSQL_ALLOW_PERSISTENT,
                    'Compatibility mode' => self::INI_MSSQL_COMPATIBILITY_MODE,
                    'Secure connection' => self::INI_MSSQL_SECURE_CONNECTION,
                ),
                'Tidy' => array(
                    'Clean output' => self::INI_TIDY_CLEAN_OUTPUT,
                ),
                'SOAP' => array(
                    'WSDL cache enabled' => self::INI_SOAP_WSDL_CACHE_ENABLED,
                ),
                'XDebug' => array(
                    'Remote enable' => self::INI_XDEBUG_REMOTE_ENABLE,
                    'Profiler enable' => self::INI_XDEBUG_PROFILER_ENABLE,
                    'Profiler enable trigger' => self::INI_XDEBUG_PROFILER_ENABLE_TRIGGER,
                ),
                'APC' => array(
                    'Enabled' => self::INI_APC_ENABLED,
                    'Include once override' => self::INI_APC_INCLUDE_ONCE_OVERRIDE,
                    'Canonicalize' => self::INI_APC_CANONICALIZE,
                    'Stat' => self::INI_APC_STAT,
                ),
            ),
        );
    }

    /**
     * Retrieves the current values for PHP settings.
     * This method returns an array where each setting is associated with its possible and current values.
     *
     * @return array An associative array of PHP settings and their values.
     */
    public function getSettingsValues() {
        return array(
            self::INI_SHORT_OPEN_TAG => array('On', 'Off', 'On'),
            self::INI_ASP_TAGS => array('On', 'Off', 'Off'),
            self::INI_Y2K_COMPLIANCE => array('1', '0', '1'),
            self::INI_OUTPUT_BUFFERING => array('4096', 'Off', '4096'),
            self::INI_ZLIB_OUTPUT_COMPRESSION => array('On', 'Off', 'Off'),
            self::INI_IMPLICIT_FLUSH => array('On', 'Off', 'Off'),
            self::INI_ALLOW_CALL_TIME_PASS_REFERENCE => array('On', 'Off', 'On'),
            self::INI_SAFE_MODE => array('On', 'Off', 'Off'),
            self::INI_SAFE_MODE_GID => array('On', 'Off', 'Off'),
            self::INI_EXPOSE_PHP => array('On', 'Off', 'On'),
            self::INI_DISPLAY_ERRORS => array('On', 'Off', 'On'),
            self::INI_DISPLAY_STARTUP_ERRORS => array('On', 'Off', 'On'),
            self::INI_LOG_ERRORS => array('On', 'Off', 'On'),
            self::INI_IGNORE_REPEATED_ERRORS => array('On', 'Off', 'Off'),
            self::INI_IGNORE_REPEATED_SOURCE => array('On', 'Off', 'Off'),
            self::INI_REPORT_MEMLEAKS => array('On', 'Off', 'On'),
            self::INI_TRACK_ERRORS => array('On', 'Off', 'On'),
            self::INI_HTML_ERRORS => array('On', 'Off', 'On'),
            self::INI_REGISTER_GLOBALS => array('On', 'Off', 'Off'),
            self::INI_REGISTER_LONG_ARRAYS => array('On', 'Off', 'Off'),
            self::INI_REGISTER_ARGC_ARGV => array('On', 'Off', 'Off'),
            self::INI_AUTO_GLOBALS_JIT => array('On', 'Off', 'On'),
            self::INI_MAGIC_QUOTES_GPC => array('On', 'Off', 'Off'),
            self::INI_MAGIC_QUOTES_RUNTIME => array('On', 'Off', 'Off'),
            self::INI_MAGIC_QUOTES_SYBASE => array('On', 'Off', 'Off'),
            self::INI_ENABLE_DL => array('On', 'Off', 'Off'),
            self::INI_CGI_FORCE_REDIRECT => array('1', '0', '1'),
            self::INI_FILE_UPLOADS => array('On', 'Off', 'On'),
            self::INI_ALLOW_URL_FOPEN => array('On', 'Off', 'On'),
            self::INI_ALLOW_URL_INCLUDE => array('On', 'Off', 'Off'),
            self::INI_DEFINE_SYSLOG_VARIABLES => array('On', 'Off', 'Off'),
            self::INI_MAIL_ADD_X_HEADER => array('On', 'Off', 'On'),
            self::INI_SQL_SAFE_MODE => array('On', 'Off', 'Off'),
            self::INI_ODBC_ALLOW_PERSISTENT => array('On', 'Off', 'On'),
            self::INI_ODBC_CHECK_PERSISTENT => array('On', 'Off', 'On'),
            self::INI_MYSQL_ALLOW_LOCAL_INFILE => array('On', 'Off', 'Off'),
            self::INI_MYSQL_ALLOW_PERSISTENT => array('On', 'Off', 'On'),
            self::INI_MYSQL_TRACE_MODE => array('On', 'Off', 'Off'),
            self::INI_MYSQLI_ALLOW_PERSISTENT => array('On', 'Off', 'On'),
            self::INI_MYSQLI_RECONNECT => array('On', 'Off', 'Off'),
            self::INI_MYSQLND_COLLECT_STATISTICS => array('On', 'Off', 'On'),
            self::INI_MYSQLND_COLLECT_MEMORY_STATISTICS => array('On', 'Off', 'On'),
            self::INI_PGSQL_ALLOW_PERSISTENT => array('On', 'Off', 'On'),
            self::INI_PGSQL_AUTO_RESET_PERSISTENT => array('On', 'Off', 'Off'),
            self::INI_SYBCT_ALLOW_PERSISTENT => array('On', 'Off', 'On'),
            self::INI_SESSION_USE_COOKIES => array('1', '0', '1'),
            self::INI_SESSION_USE_ONLY_COOKIES => array('1', '0', '1'),
            self::INI_SESSION_AUTO_START => array('1', '0', '0'),
            self::INI_SESSION_COOKIE_HTTPONLY => array('1', '', ''),
            self::INI_SESSION_BUG_COMPAT_42 => array('On', 'Off', 'On'),
            self::INI_SESSION_BUG_COMPAT_WARN => array('On', 'Off', 'On'),
            self::INI_SESSION_USE_TRANS_SID => array('1', '0', '0'),
            self::INI_MSSQL_ALLOW_PERSISTENT => array('On', 'Off', 'On'),
            self::INI_MSSQL_COMPATIBILITY_MODE => array('On', 'Off', 'Off'),
            self::INI_MSSQL_SECURE_CONNECTION => array('On', 'Off', 'Off'),
            self::INI_TIDY_CLEAN_OUTPUT => array('On', 'Off', 'Off'),
            self::INI_SOAP_WSDL_CACHE_ENABLED => array('1', '0', '1'),
            self::INI_XDEBUG_REMOTE_ENABLE => array('On', 'Off', 'On'),
            self::INI_XDEBUG_PROFILER_ENABLE => array('On', 'Off', 'Off'),
            self::INI_XDEBUG_PROFILER_ENABLE_TRIGGER => array('On', 'Off', 'Off'),
            self::INI_APC_ENABLED => array('1', '0', '1'),
            self::INI_APC_INCLUDE_ONCE_OVERRIDE => array('1', '0', '1'),
            self::INI_APC_CANONICALIZE => array('1', '0', '1'),
            self::INI_APC_STAT => array('1', '0', '1'),
        );
    }

    /**
     * Checks if a specific PHP setting is active based on the current configuration.
     *
     * @param string $name The name of the PHP setting to check.
     * @return bool Returns true if the setting is active, false otherwise.
     */
    public function isSettingActive($name) {
        $settingsValues = $this->getSettingsValues();

        $confContent = file($this->getConf());
        foreach ($confContent as $row) {
            $settingMatch = array();
            if (preg_match('/^' . $name . '\s*=\s*(.+)/i', $row, $settingMatch)) {
                return isset($settingMatch[1]) && isset($settingsValues[$name]) && $settingsValues[$name][0] == trim($settingMatch[1]);
            }
        }

        return false;
    }

    /**
     * Checks if a specific PHP setting exists in the configuration file.
     *
     * @param string $name The name of the PHP setting to check.
     * @return bool Returns true if the setting exists, false otherwise.
     */
    public function isSettingExists($name) {
        $confContent = file($this->getConf());
        foreach ($confContent as $row) {
            if (preg_match('/^\s*?;?\s*?' . $name . '\s*=\s*.*/i', $row)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves a list of PHP extensions from the configuration and the file system.
     *
     * @return array An associative array of PHP extensions and their statuses.
     */
    public function getExtensions() {
        $fromFolder = $this->getExtensionsFromConf();
        $fromConf = $this->getExtensionsFromFolder();
        $result = array_merge($fromConf, $fromFolder);
        ksort($result);
        return $result;
    }

    /**
     * Checks if a given extension should be excluded from processing.
     *
     * @param   string  $ext  The extension name to check.
     *
     * @return bool Returns true if the extension is in the exclusion list, false otherwise.
     */
    private function isExtensionExcluded($ext)
    {
        return in_array( $ext, array(
            'opcache',
            'xdebug'
        ) );
    }

    /**
     * Retrieves a list of PHP extensions from the configuration file.
     *
     * This method parses the configuration file to extract the status of PHP extensions,
     * filtering out excluded extensions and sorting them alphabetically.
     *
     * @return array Associative array of extensions with their enable status.
     */
    public function getExtensionsFromConf()
    {
        $result = array();

        $confContent = file( $this->getConf() );
        foreach ( $confContent as $row ) {
            $extMatch = array();
            if ( preg_match( '/^(;)?extension\s*=\s*"?(.+)"?/i', $row, $extMatch ) ) {
                $name = preg_replace( '/^php_/', '', preg_replace( '/\.dll$/', '', trim( $extMatch[2] ) ) );
                if ( $this->isExtensionExcluded( $name ) ) {
                    continue;
                }
                if ( $extMatch[1] == ';' ) {
                    $result[$name] = ActionSwitchPhpExtension::SWITCH_OFF;
                }
                else {
                    $result[$name] = ActionSwitchPhpExtension::SWITCH_ON;
                }
            }
        }

        ksort( $result );

        return $result;
    }

    /**
     * Retrieves a list of currently loaded PHP extensions.
     *
     * This method uses getExtensionsFromConf() to get all extensions and filters out
     * those that are not enabled.
     *
     * @return array List of enabled extensions.
     */
    public function getExtensionsLoaded()
    {
        $result = array();
        foreach ( $this->getExtensionsFromConf() as $name => $status ) {
            if ( $status == ActionSwitchPhpExtension::SWITCH_ON ) {
                $result[] = $name;
            }
        }

        return $result;
    }

    /**
     * Retrieves PHP extensions from the extensions directory.
     *
     * This method scans the extensions directory and lists all .dll files,
     * excluding the ones defined in isExtensionExcluded().
     *
     * @return array Associative array of extensions with their enable status set to SWITCH_OFF.
     */
    public function getExtensionsFromFolder()
    {
        $result = array();

        $handle = @opendir( $this->getCurrentPath() . '/ext' );
        if ( !$handle ) {
            return $result;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file != '.' && $file != '..' && Util::endWith( $file, '.dll' ) ) {
                $name = preg_replace( '/^php_/', '', preg_replace( '/\.dll$/', '', trim( $file ) ) );
                if ( $this->isExtensionExcluded( $name ) ) {
                    continue;
                }
                $result[$name] = ActionSwitchPhpExtension::SWITCH_OFF;
            }
        }

        closedir( $handle );
        ksort( $result );

        return $result;
    }

    /**
     * Retrieves the Apache module path for a specific PHP and Apache version.
     *
     * This method adjusts paths based on the provided PHP version and searches for
     * a compatible Apache module configuration in the bearsampp configuration file.
     *
     * @param   string       $apacheVersion  The Apache version.
     * @param   string|null  $phpVersion     The PHP version, defaults to current version if null.
     *
     * @return string|false The path to the Apache module if found, false otherwise.
     */
    public function getApacheModule($apacheVersion, $phpVersion = null)
    {
        $apacheVersion = substr( str_replace( '.', '', $apacheVersion ), 0, 2 );
        $phpVersion    = $phpVersion == null ? $this->getVersion() : $phpVersion;

        $currentPath   = str_replace( 'php' . $this->getVersion(), 'php' . $phpVersion, $this->getCurrentPath() );
        $bearsamppConf = str_replace( 'php' . $this->getVersion(), 'php' . $phpVersion, $this->bearsamppConf );

        if ( in_array( $phpVersion, $this->getVersionList() ) && file_exists( $bearsamppConf ) ) {
            $apacheCpt = parse_ini_file( $bearsamppConf );
            if ( $apacheCpt !== false ) {
                foreach ( $apacheCpt as $aVersion => $apacheModule ) {
                    $aVersion = str_replace( 'apache', '', $aVersion );
                    $aVersion = str_replace( '.', '', $aVersion );
                    if ( $apacheVersion == $aVersion && file_exists( $currentPath . '/' . $apacheModule ) ) {
                        return $currentPath . '/' . $apacheModule;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Retrieves the thread-safe DLL name for the specified PHP version.
     *
     * This method checks for the existence of 'php7ts.dll' or 'php8ts.dll' in the
     * current PHP path and returns the appropriate DLL name if found.
     *
     * @param   string|null  $phpVersion  The PHP version, defaults to current version if null.
     *
     * @return string|false The DLL name if found, false otherwise.
     */
    public function getTsDll($phpVersion = null)
    {
        $phpVersion  = $phpVersion == null ? $this->getVersion() : $phpVersion;
        $currentPath = str_replace( 'php' . $this->getVersion(), 'php' . $phpVersion, $this->getCurrentPath() );

        if ( file_exists( $currentPath . '/php7ts.dll' ) ) {
            return 'php7ts.dll';
        }
        elseif ( file_exists( $currentPath . '/php8ts.dll' ) ) {
            return 'php8ts.dll';
        }

        return false;
    }

    /**
     * Sets the version of the module and updates the configuration.
     *
     * This method updates the version property, writes the new version to the configuration file,
     * and reloads the module to apply changes.
     *
     * @param   string  $version  The new version to set.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    /**
     * Enables or disables the module and updates the system configuration.
     *
     * This method sets the enable status, updates the configuration, and optionally
     * restarts related services to apply changes.
     *
     * @param   int   $enabled     The enable status (1 for enabled, 0 for disabled).
     * @param   bool  $showWindow  Whether to show error messages in a window.
     */
    public function setEnable($enabled, $showWindow = false)
    {
        global $bearsamppConfig, $bearsamppBins, $bearsamppLang, $bearsamppWinbinder;

        if ( $enabled == Config::ENABLED && !is_dir( $this->currentPath ) ) {
            Util::logDebug( $this->getName() . ' cannot be enabled because bundle ' . $this->getVersion() . ' does not exist in ' . $this->currentPath );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::ENABLE_BUNDLE_NOT_EXIST ), $this->getName(), $this->getVersion(), $this->currentPath ),
                    sprintf( $bearsamppLang->getValue( Lang::ENABLE_TITLE ), $this->getName() )
                );
            }
            $enabled = Config::DISABLED;
        }

        Util::logInfo( $this->getName() . ' switched to ' . ($enabled == Config::ENABLED ? 'enabled' : 'disabled') );
        $this->enable = $enabled == Config::ENABLED;
        $bearsamppConfig->replace( self::ROOT_CFG_ENABLE, $enabled );

        $this->reload();
        $bearsamppBins->getApache()->update();
        if ( $bearsamppBins->getApache()->isEnable() && $bearsamppBins->getApache()->getService()->isRunning() ) {
            $bearsamppBins->getApache()->getService()->stop();
            Util::startService( $bearsamppBins->getApache(), BinApache::CMD_SYNTAX_CHECK, $showWindow );
        }
    }
    /**
     * Retrieves the path to the PHP error log file.
     *
     * This method returns the full path to the error log file used by PHP. This log file
     * captures all error-related output from PHP scripts, making it essential for debugging.
     *
     * @return string The full path to the PHP error log file.
     */
    public function getErrorLog() {
        return $this->errorLog;
    }

    /**
     * Retrieves the path to the PHP CLI executable.
     *
     * This method returns the full path to the command-line interface (CLI) executable for PHP.
     * This executable is used to run PHP scripts from the command line.
     *
     * @return string The full path to the PHP CLI executable.
     */
    public function getCliExe() {
        return $this->cliExe;
    }

    /**
     * Retrieves the path to the PHP CLI silent executable.
     *
     * This method returns the full path to the silent version of the PHP CLI executable.
     * This executable is similar to the standard CLI but is configured to suppress output,
     * useful in automated scripts or where output is not needed.
     *
     * @return string The full path to the PHP CLI silent executable.
     */
    public function getCliSilentExe() {
        return $this->cliSilentExe;
    }

    /**
     * Retrieves the path to the PHP configuration file.
     *
     * This method returns the full path to the main PHP configuration file (php.ini).
     * This file contains all the configuration directives that dictate how PHP functions.
     *
     * @return string The full path to the PHP configuration file.
     */
    public function getConf() {
        return $this->conf;
    }

    /**
     * Retrieves the path to the PHP PEAR executable.
     *
     * This method returns the full path to the PEAR executable for PHP. PEAR is a framework
     * and distribution system for reusable PHP components.
     *
     * @return string The full path to the PHP PEAR executable.
     */
    public function getPearExe() {
        return $this->pearExe;
    }

    /**
     * Retrieves the version of the PEAR installation.
     *
     * This method returns the version of PEAR installed. It can optionally use a cached version
     * to avoid recomputation. If caching is not requested or if the cache is not present,
     * it fetches the version directly using the PEAR executable.
     *
     * @param bool $cache Whether to use the cached version of the PEAR version.
     * @return string|null The version of PEAR if available, or null if not found.
     */
    public function getPearVersion($cache = false) {
        $cacheFile = $this->getCurrentPath() . '/pear/version';
        if (!$cache) {
            file_put_contents($cacheFile, Batch::getPearVersion());
        }
        return file_exists($cacheFile) ? file_get_contents($cacheFile) : null;
    }
}
