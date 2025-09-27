<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class BinMysql
 *
 * This class represents the MySQL binary module in the Bearsampp application.
 * It handles the configuration, management, and operations related to MySQL.
 */
class BinMysql extends Module
{
    const SERVICE_NAME = 'bearsamppmysql';

    const ROOT_CFG_ENABLE = 'mysqlEnable';
    const ROOT_CFG_VERSION = 'mysqlVersion';

    const LOCAL_CFG_EXE = 'mysqlExe';
    const LOCAL_CFG_CLI_EXE = 'mysqlCliExe';
    const LOCAL_CFG_ADMIN = 'mysqlAdmin';
    const LOCAL_CFG_CONF = 'mysqlConf';
    const LOCAL_CFG_PORT = 'mysqlPort';
    const LOCAL_CFG_ROOT_USER = 'mysqlRootUser';
    const LOCAL_CFG_ROOT_PWD = 'mysqlRootPwd';

    const CMD_VERSION = '--version';
    const CMD_VARIABLES = 'variables';
    const CMD_SYNTAX_CHECK = '--help --verbose 1>NUL';

    private $service;
    private $errorLog;

    private $exe;
    private $conf;
    private $port;
    private $rootUser;
    private $rootPwd;
    private $cliExe;
    private $admin;
    private $dataDir;

    /**
     * Constructs a BinMysql object and initializes the MySQL module.
     *
     * @param   string  $id    The ID of the module.
     * @param   string  $type  The type of the module.
     */
    public function __construct($id, $type)
    {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the MySQL module configuration based on the provided ID and type.
     *
     * @param   string|null  $id    The ID of the module. If null, the current ID is used.
     * @param   string|null  $type  The type of the module. If null, the current type is used.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name    = $bearsamppLang->getValue(Lang::MYSQL);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->enable   = $this->enable && $bearsamppConfig->getRaw(self::ROOT_CFG_ENABLE);
        $this->service  = new Win32Service(self::SERVICE_NAME);
        $this->errorLog = $bearsamppRoot->getLogsPath() . '/mysql.log';

        if ($this->bearsamppConfRaw !== false) {
            $this->exe      = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->conf     = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->port     = $this->bearsamppConfRaw[self::LOCAL_CFG_PORT];
            $this->rootUser = isset($this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_USER]) ? $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_USER] : 'root';
            $this->rootPwd  = isset($this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_PWD]) ? $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_PWD] : '';
            $this->cliExe   = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CLI_EXE];
            $this->admin    = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ADMIN];
            $this->dataDir  = $this->symlinkPath . '/data';
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
        if (!is_file($this->exe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->exe));

            return;
        }
        if (!is_file($this->conf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->conf));

            return;
        }
        if (!is_numeric($this->port) || $this->port <= 0) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_PORT, $this->port));

            return;
        }
        if (empty($this->rootUser)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_ROOT_USER, $this->rootUser));

            return;
        }
        if (!is_file($this->cliExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->cliExe));

            return;
        }
        if (!is_file($this->admin)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->admin));

            return;
        }

        $this->service->setDisplayName(APP_TITLE . ' ' . $this->getName());
        $this->service->setBinPath($this->exe);
        $this->service->setParams(self::SERVICE_NAME);
        $this->service->setStartType(Win32Service::SERVICE_DEMAND_START);
        $this->service->setErrorControl(Win32Service::SERVER_ERROR_NORMAL);
    }

    /**
     * Replaces multiple key-value pairs in the configuration file.
     *
     * @param   array  $params  An associative array of key-value pairs to replace.
     */
    protected function replaceAll($params)
    {
        $content = file_get_contents($this->bearsamppConf);

        foreach ($params as $key => $value) {
            $content                      = preg_replace('|' . $key . ' = .*|', $key . ' = ' . '"' . $value . '"', $content);
            $this->bearsamppConfRaw[$key] = $value;
            switch ($key) {
                case self::LOCAL_CFG_PORT:
                    $this->port = $value;
                    break;
                case self::LOCAL_CFG_ROOT_USER:
                    $this->rootUser = $value;
                    break;
                case self::LOCAL_CFG_ROOT_PWD:
                    $this->rootPwd = $value;
                    break;
            }
        }

        file_put_contents($this->bearsamppConf, $content);
    }

    /**
     * Changes the MySQL port and updates the configuration.
     *
     * @param   int    $port           The new port number.
     * @param   bool   $checkUsed      Whether to check if the port is already in use.
     * @param   mixed  $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|string True if the port was changed successfully, or an error message if the port is in use.
     */
    public function changePort($port, $checkUsed = false, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;

        if (!Util::isValidPort($port)) {
            Util::logError($this->getName() . ' port not valid: ' . $port);

            return false;
        }

        $port = intval($port);
        $bearsamppWinbinder->incrProgressBar($wbProgressBar);

        $isPortInUse = Util::isPortInUse($port);
        if (!$checkUsed || $isPortInUse === false) {
            // bearsampp.conf
            $this->setPort($port);
            $bearsamppWinbinder->incrProgressBar($wbProgressBar);

            // conf
            $this->update();
            $bearsamppWinbinder->incrProgressBar($wbProgressBar);

            return true;
        }

        Util::logDebug($this->getName() . ' port in used: ' . $port . ' - ' . $isPortInUse);

        return $isPortInUse;
    }

    /**
     * Checks if the specified port is in use by MySQL.
     *
     * @param   int   $port        The port number to check.
     * @param   bool  $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the port is in use by MySQL, false otherwise.
     */
    public function checkPort($port, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle  = sprintf($bearsamppLang->getValue(Lang::CHECK_PORT_TITLE), $this->getName(), $port);
        $startTime = microtime(true);

        if (!Util::isValidPort($port)) {
            Util::logError($this->getName() . ' port not valid: ' . $port);
            return false;
        }

        // Quick socket check first - much faster than PDO connection
        $timeout = 1; // Reduced timeout for better performance
        $fp      = @fsockopen('127.0.0.1', $port, $errno, $errstr, $timeout);
        if (!$fp) {
            Util::logDebug($this->getName() . ' port ' . $port . ' is not used');
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED), $port),
                    $boxTitle
                );
            }
            return false;
        }
        fclose($fp);

        // Use cached connection if available for better performance
        static $cachedConnection = null;
        static $lastPort = null;

        if ($cachedConnection === null || $lastPort !== $port) {
            try {
                $options = [
                    \PDO::ATTR_TIMEOUT => $timeout,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode=''"
                ];

                $dsn = 'mysql:host=127.0.0.1;port=' . $port;
                $cachedConnection = new \PDO($dsn, $this->rootUser, $this->rootPwd, $options);
                $lastPort = $port;
            } catch (\PDOException $e) {
                Util::logDebug($this->getName() . ' port ' . $port . ' connection failed: ' . $e->getMessage());
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxWarning(
                        sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED_BY), $port),
                        $boxTitle
                    );
                }
                return false;
            }
        }

        try {
            // Single optimized query to get both version and type
            $stmt = $cachedConnection->query("SELECT @@version, @@version_comment");
            $row = $stmt->fetch(\PDO::FETCH_NUM);

            if (!$row) {
                return false;
            }

            $version = explode('-', $row[0]);
            $version = count($version) > 1 ? $version[0] : $row[0];
            $isMysql = Util::startWith(strtolower($row[1]), 'mysql');

            if (!$isMysql) {
                Util::logDebug($this->getName() . ' port used by another DBMS: ' . $port);
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxWarning(
                        sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY_ANOTHER_DBMS), $port),
                        $boxTitle
                    );
                }
                return false;
            }

            Util::logDebug($this->getName() . ' port ' . $port . ' is used by: ' . $this->getName() . ' ' . $version);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxInfo(
                    sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY), $port, $this->getName() . ' ' . $version),
                    $boxTitle
                );
            }

            $totalTime = round(microtime(true) - $startTime, 2);
            Util::logTrace("MySQL port check completed in {$totalTime}s");
            return true;

        } catch (\PDOException $e) {
            Util::logDebug($this->getName() . ' port ' . $port . ' validation error: ' . $e->getMessage());
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED_BY), $port),
                    $boxTitle
                );
            }
            return false;
        }
    }

    /**
     * Changes the MySQL root password.
     *
     * @param   string  $currentPwd     The current root password.
     * @param   string  $newPwd         The new root password.
     * @param   mixed   $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|string True if the password was changed successfully, or an error message if the operation failed.
     */
    public function changeRootPassword($currentPwd, $newPwd, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;
        $startTime = microtime(true);
        $error     = null;
        $timeout   = 5; // 5 seconds timeout

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);

        try {
            // Connect using PDO
            $options = [
                \PDO::ATTR_TIMEOUT => $timeout,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ];

            $dsn    = 'mysql:host=127.0.0.1;port=' . $this->port;
            $dbLink = new \PDO($dsn, $this->rootUser, $currentPwd, $options);

            $bearsamppWinbinder->incrProgressBar($wbProgressBar);

            // Determine MySQL version to use appropriate password update syntax
            $stmt    = $dbLink->query('SELECT VERSION()');
            $version = $stmt->fetchColumn();

            $bearsamppWinbinder->incrProgressBar($wbProgressBar);

            // Use appropriate SQL syntax based on MySQL version
            if (version_compare($version, '5.7.6', '>=')) {
                // MySQL 5.7.6 and newer uses ALTER USER
                $sql  = "ALTER USER '{$this->rootUser}'@'localhost' IDENTIFIED BY :password";
                $stmt = $dbLink->prepare($sql);
                $stmt->bindParam(':password', $newPwd);
            } else {
                // Older versions use SET PASSWORD
                $sql  = "SET PASSWORD FOR '{$this->rootUser}'@'localhost' = PASSWORD(:password)";
                $stmt = $dbLink->prepare($sql);
                $stmt->bindParam(':password', $newPwd);
            }

            $bearsamppWinbinder->incrProgressBar($wbProgressBar);
            $stmt->execute();

            $bearsamppWinbinder->incrProgressBar($wbProgressBar);
            $dbLink->query('FLUSH PRIVILEGES');

            $bearsamppWinbinder->incrProgressBar($wbProgressBar);
            $dbLink = null; // Close connection properly

        } catch (\PDOException $e) {
            $error = $e->getMessage();
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);

        if (!empty($error)) {
            $totalTime = round(microtime(true) - $startTime, 2);
            Util::logTrace("MySQL password change failed in {$totalTime}s: " . $error);

            return $error;
        }

        // bearsampp.conf
        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        $this->setRootPwd($newPwd);

        // conf
        $this->update();
        $bearsamppWinbinder->incrProgressBar($wbProgressBar);

        $totalTime = round(microtime(true) - $startTime, 2);
        Util::logTrace("MySQL password change completed in {$totalTime}s");

        return true;
    }

    /**
     * Checks if the provided root password is correct.
     *
     * @param   string|null  $currentPwd     The current root password. If null, the stored root password is used.
     * @param   mixed        $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|string True if the password is correct, or an error message if the operation failed.
     */
    public function checkRootPassword($currentPwd = null, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;
        $startTime  = microtime(true);
        $currentPwd = $currentPwd == null ? $this->rootPwd : $currentPwd;
        $error      = null;
        $timeout    = 2; // Reduced timeout for faster validation

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);

        // Use cached connection for password validation if available
        static $passwordCache = [];
        $cacheKey = md5($this->rootUser . ':' . $currentPwd . ':' . $this->port);

        if (isset($passwordCache[$cacheKey]) && (time() - $passwordCache[$cacheKey]['time']) < 30) {
            $bearsamppWinbinder->incrProgressBar($wbProgressBar);
            $totalTime = round(microtime(true) - $startTime, 2);
            Util::logTrace("MySQL password check completed from cache in {$totalTime}s");
            return $passwordCache[$cacheKey]['result'];
        }

        try {
            $options = [
                \PDO::ATTR_TIMEOUT => $timeout,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode=''"
            ];

            $dsn    = 'mysql:host=127.0.0.1;port=' . $this->port;
            $dbLink = new \PDO($dsn, $this->rootUser, $currentPwd, $options);

            // Quick validation query
            $dbLink->query('SELECT 1');
            $dbLink = null; // Close connection properly

            // Cache successful result
            $passwordCache[$cacheKey] = [
                'result' => true,
                'time' => time()
            ];

        } catch (\PDOException $e) {
            $error = $e->getMessage();

            // Cache failed result for shorter time
            $passwordCache[$cacheKey] = [
                'result' => $error,
                'time' => time() - 25 // Cache for only 5 seconds
            ];
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);

        if (!empty($error)) {
            $totalTime = round(microtime(true) - $startTime, 2);
            Util::logTrace("MySQL password check failed in {$totalTime}s: " . $error);
            return $error;
        }

        $totalTime = round(microtime(true) - $startTime, 2);
        Util::logTrace("MySQL password check completed in {$totalTime}s");
        return true;
    }

    /**
     * Switches the MySQL version and updates the configuration.
     *
     * @param   string  $version     The new MySQL version.
     * @param   bool    $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the version was switched successfully, false otherwise.
     */
    public function switchVersion($version, $showWindow = false)
    {
        Util::logDebug('Switch ' . $this->name . ' version to ' . $version);

        return $this->updateConfig($version, 0, $showWindow);
    }

    /**
     * Updates the MySQL configuration with a specific version.
     *
     * @param   string|null  $version     The version to update to. If null, the current version is used.
     * @param   int          $sub         The sub-level for logging indentation.
     * @param   bool         $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the configuration was updated successfully, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppApps, $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->getName(), $version);

        $currentPath   = str_replace('mysql' . $this->getVersion(), 'mysql' . $version, $this->getCurrentPath());
        $conf          = str_replace('mysql' . $this->getVersion(), 'mysql' . $version, $this->getConf());
        $bearsamppConf = str_replace('mysql' . $this->getVersion(), 'mysql' . $version, $this->bearsamppConf);

        if ($this->version != $version) {
            $this->initData($currentPath, $version);
        }

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

        // bearsampp.conf
        $this->setVersion($version);

        // conf
        Util::replaceInFile($this->getConf(), array(
            '/^port(.*?)=(.*?)(\d+)/' => 'port = ' . $this->port
        ));

        // phpmyadmin
        $bearsamppApps->getPhpmyadmin()->update($sub + 1);

        // php
        $bearsamppBins->getPhp()->update($sub + 1);

        return true;
    }

    /**
     * Initializes the MySQL data directory if needed.
     * Triggers reinitialization when the directory exists but is incomplete (e.g., missing performance_schema).
     *
     * @param   string|null  $path     The path to the MySQL installation. If null, the current path is used.
     * @param   string|null  $version  The version of MySQL. If null, the current version is used.
     *
     * @return  bool         True if initialization was successful or not needed
     */
    public function initData($path = null, $version = null)
    {
        Util::logTrace('Starting MySQL data initialization');
        $startTime = microtime(true);

        $path          = $path != null ? $path : $this->getCurrentPath();
        $version       = $version != null ? $version : $this->getVersion();
        $dataDir       = $path . '/data';
        $perfSchemaDir = $dataDir . '/performance_schema';

        if (version_compare($version, '5.7.0', '<')) {
            Util::logTrace('MySQL version below 5.7.0, skipping initialization');

            return true;
        }

        $needsInit = false;

        if (!is_dir($dataDir)) {
            Util::logTrace('MySQL data directory does not exist; initialization required');
            $needsInit = true;
        } else {
            if (!is_dir($perfSchemaDir)) {
                Util::logTrace('performance_schema directory missing; reinitialization required');
                $needsInit = true;
            }
        }

        if (!$needsInit) {
            Util::logTrace('MySQL data directory already initialized');

            return true;
        }

        // Prepare a clean data directory (mysqld --initialize-insecure requires an empty/nonexistent directory)
        if (is_dir($dataDir)) {
            $backupDir = $dataDir . '_bak_' . date('Ymd_His');
            if (@rename($dataDir, $backupDir)) {
                Util::logTrace('Backed up existing data directory to: ' . $backupDir);
            } else {
                Util::logTrace('Failed to backup existing data directory; attempting to clear it');
                try {
                    $it    = new \RecursiveDirectoryIterator($dataDir, \FilesystemIterator::SKIP_DOTS);
                    $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
                    foreach ($files as $file) {
                        if ($file->isDir()) {
                            @rmdir($file->getPathname());
                        } else {
                            @unlink($file->getPathname());
                        }
                    }
                    @rmdir($dataDir);
                } catch (\Throwable $t) {
                    Util::logTrace('Error clearing data directory: ' . $t->getMessage());
                }
            }
        }

        if (!is_dir($dataDir)) {
            @mkdir($dataDir, 0777, true);
            Util::logTrace('Created clean MySQL data directory');
        }

        // Use Bearsampp built-in initialization (init.bat via Batch)
        try {
            Batch::initializeMysql($path);
        } catch (\Throwable $e) {
            Util::logTrace('Error during MySQL initialization via Batch: ' . $e->getMessage());

            return false;
        }

        // Verify initialization by checking performance_schema existence
        if (!is_dir($perfSchemaDir)) {
            Util::logTrace('MySQL initialization appears to have failed: performance_schema still missing');

            return false;
        }

        $totalTime = round(microtime(true) - $startTime, 2);
        Util::logTrace("MySQL initialization completed in {$totalTime}s");

        return true;
    }

    /**
     * Executes a MySQL command and retrieves the output.
     *
     * @param   string  $cmd  The command to execute.
     *
     * @return array An associative array containing 'syntaxOk' (boolean) and 'content' (string|null).
     */
    public function getCmdLineOutput($cmd)
    {
        $result = array(
            'syntaxOk' => false,
            'content'  => null,
        );

        $bin         = $this->getExe();
        $removeLines = 0;
        $outputFrom  = '';
        if ($cmd == self::CMD_SYNTAX_CHECK) {
            $outputFrom = '2';
        } elseif ($cmd == self::CMD_VARIABLES) {
            $bin = $this->getAdmin();
            $cmd .= ' --user=' . $this->getRootUser();
            if ($this->getRootPwd()) {
                $cmd .= ' --password=' . $this->getRootPwd();
            }
            $removeLines = 2;
        }

        if (file_exists($bin)) {
            $tmpResult = Batch::exec('mysqlGetCmdLineOutput', '"' . $bin . '" ' . $cmd . ' ' . $outputFrom, 5);
            if ($tmpResult !== false && is_array($tmpResult)) {
                $result['syntaxOk'] = empty($tmpResult) || !Util::contains(trim($tmpResult[count($tmpResult) - 1]), '[ERROR]');
                for ($i = 0; $i < $removeLines; $i++) {
                    unset($tmpResult[$i]);
                }
                $result['content'] = trim(str_replace($bin, '', implode(PHP_EOL, $tmpResult)));
            }
        }

        return $result;
    }

    /**
     * Sets the MySQL version and reloads the configuration.
     *
     * @param   string  $version  The version to set.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    /**
     * Retrieves the MySQL service object.
     *
     * @return Win32Service The MySQL service object.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Enables or disables the MySQL module and updates the configuration.
     *
     * @param   bool  $enabled     Whether to enable or disable the module.
     * @param   bool  $showWindow  Whether to show a message box with the result.
     */
    public function setEnable($enabled, $showWindow = false)
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppWinbinder;

        if ($enabled == Config::ENABLED && !is_dir($this->currentPath)) {
            Util::logDebug($this->getName() . ' cannot be enabled because bundle ' . $this->getVersion() . ' does not exist in ' . $this->currentPath);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::ENABLE_BUNDLE_NOT_EXIST), $this->getName(), $this->getVersion(), $this->currentPath),
                    sprintf($bearsamppLang->getValue(Lang::ENABLE_TITLE), $this->getName())
                );
            }
            $enabled = Config::DISABLED;
        }

        Util::logInfo($this->getName() . ' switched to ' . ($enabled == Config::ENABLED ? 'enabled' : 'disabled'));
        $this->enable = $enabled == Config::ENABLED;
        $bearsamppConfig->replace(self::ROOT_CFG_ENABLE, $enabled);

        $this->reload();
        if ($this->enable) {
            Util::installService($this, $this->port, self::CMD_SYNTAX_CHECK, $showWindow);
        } else {
            Util::removeService($this->service, $this->name);
        }
    }

    /**
     * Retrieves the path to the MySQL error log.
     *
     * @return string The path to the error log.
     */
    public function getErrorLog()
    {
        return $this->errorLog;
    }

    /**
     * Retrieves the path to the MySQL executable.
     *
     * @return string The path to the MySQL executable.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Retrieves the path to the MySQL configuration file.
     *
     * @return string The path to the configuration file.
     */
    public function getConf()
    {
        return $this->conf;
    }

    /**
     * Retrieves the MySQL port number.
     *
     * @return int The port number.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the MySQL port number and updates the configuration.
     *
     * @param   int  $port  The port number to set.
     */
    public function setPort($port)
    {
        $this->replace(self::LOCAL_CFG_PORT, $port);
    }

    /**
     * Retrieves the MySQL root username.
     *
     * @return string The root username.
     */
    public function getRootUser()
    {
        return $this->rootUser;
    }

    /**
     * Sets the MySQL root username and updates the configuration.
     *
     * @param   string  $rootUser  The root username to set.
     */
    public function setRootUser($rootUser)
    {
        $this->replace(self::LOCAL_CFG_ROOT_USER, $rootUser);
    }

    /**
     * Retrieves the MySQL root password.
     *
     * @return string The root password.
     */
    public function getRootPwd()
    {
        return $this->rootPwd;
    }

    /**
     * Sets the MySQL root password and updates the configuration.
     *
     * @param   string  $rootPwd  The root password to set.
     */
    public function setRootPwd($rootPwd)
    {
        $this->replace(self::LOCAL_CFG_ROOT_PWD, $rootPwd);
    }

    /**
     * Retrieves the path to the MySQL CLI executable.
     *
     * @return string The path to the CLI executable.
     */
    public function getCliExe()
    {
        return $this->cliExe;
    }

    /**
     * Retrieves the path to the MySQL admin executable.
     *
     * @return string The path to the admin executable.
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Retrieves the path to the MySQL data directory.
     *
     * @return string The path to the data directory.
     */
    public function getDataDir()
    {
        return $this->dataDir;
    }
}
