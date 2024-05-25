<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Represents the MySQL module in the Bearsampp application.
 * This class manages MySQL service operations including initialization, configuration updates,
 * version switching, and port management. It extends the Module class, inheriting its basic properties
 * and functionalities.
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

    /**
     * @var Win32Service The service handler for MySQL.
     */
    private $service;

    /**
     * @var string Path to the error log file.
     */
    private $errorLog;

    /**
     * @var string Path to the MySQL executable.
     */
    private $exe;

    /**
     * @var string Path to the MySQL configuration file.
     */
    private $conf;

    /**
     * @var int MySQL port number.
     */
    private $port;

    /**
     * @var string MySQL root username.
     */
    private $rootUser;

    /**
     * @var string MySQL root password.
     */
    private $rootPwd;

    /**
     * @var string Path to the MySQL CLI executable.
     */
    private $cliExe;

    /**
     * @var string Path to the MySQL admin executable.
     */
    private $admin;

    /**
     * Constructor for the MySQL module.
     * Initializes the module by loading its configuration and setting up the service.
     *
     * @param string $id The identifier for the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration and updates the service settings.
     * This method is typically called after changes in configuration to reinitialize the module.
     *
     * @param string|null $id Optional. The new identifier for the module.
     * @param string|null $type Optional. The new type of the module.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::MYSQL);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->enable = $this->enable && $bearsamppConfig->getRaw(self::ROOT_CFG_ENABLE);
        $this->service = new Win32Service(self::SERVICE_NAME);
        $this->errorLog = $bearsamppRoot->getLogsPath() . '/mysql.log';

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->conf = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->port = $this->bearsamppConfRaw[self::LOCAL_CFG_PORT];
            $this->rootUser = isset($this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_USER]) ? $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_USER] : 'root';
            $this->rootPwd = isset($this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_PWD]) ? $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_PWD] : '';
            $this->cliExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CLI_EXE];
            $this->admin = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ADMIN];
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
     * Replaces all specified configuration parameters in the MySQL configuration file.
     *
     * @param array $params Associative array of configuration parameters and their new values.
     */
    protected function replaceAll($params) {
        $content = file_get_contents($this->bearsamppConf);

        foreach ($params as $key => $value) {
            $content = preg_replace('|' . $key . ' = .*|', $key . ' = ' . '"' . $value.'"', $content);
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
     * Changes the MySQL port.
     * This method checks if the new port is valid and not in use before applying the change.
     *
     * @param int $port The new port number.
     * @param bool $checkUsed Optional. Whether to check if the port is already in use.
     * @param mixed $wbProgressBar Optional. Progress bar object from Winbinder.
     * @return mixed Returns true if the port was successfully changed, an error message if the port is in use, or false if the port is invalid.
     */
    public function changePort($port, $checkUsed = false, $wbProgressBar = null) {
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
     * Checks if a specific port is open and if it is being used by MySQL.
     * Optionally displays a message box with the check result.
     *
     * @param int $port The port to check.
     * @param bool $showWindow Optional. Whether to show a message box with the result.
     * @return bool Returns true if the port is used by MySQL, false otherwise.
     */
    public function checkPort($port, $showWindow = false) {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::CHECK_PORT_TITLE), $this->getName(), $port);

        if (!Util::isValidPort($port)) {
            Util::logError($this->getName() . ' port not valid: ' . $port);
            return false;
        }

        $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 5);
        if ($fp) {
            if (version_compare(phpversion(), '5.3') === -1) {
                $dbLink = mysqli_connect('127.0.0.1', $this->rootUser, $this->rootPwd, '', $port);
            } else {
                $dbLink = mysqli_connect('127.0.0.1:' . $port, $this->rootUser, $this->rootPwd);
            }
            $isMysql = false;
            $version = false;

            if ($dbLink) {
                $result = mysqli_query($dbLink, 'SHOW VARIABLES');
                if ($result) {
                    while (false !== ($row = mysqli_fetch_array($result, MYSQLI_NUM))) {
                        if ($row[0] == 'version') {
                            $version = explode("-", $row[1]);
                            $version = count($version) > 1 ? $version[0] : $row[1];
                        }
                        if ($row[0] == 'version_comment' && Util::startWith(strtolower($row[1]), 'mysql')) {
                            $isMysql = true;
                        }
                        if ($isMysql && $version !== false) {
                            break;
                        }
                    }
                    if (!$isMysql) {
                        Util::logDebug($this->getName() . ' port used by another DBMS: ' . $port);
                        if ($showWindow) {
                            $bearsamppWinbinder->messageBoxWarning(
                                sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY_ANOTHER_DBMS), $port),
                                $boxTitle
                            );
                        }
                    } else {
                        Util::logDebug($this->getName() . ' port ' . $port . ' is used by: ' . $this->getName() . ' ' . $version);
                        if ($showWindow) {
                            $bearsamppWinbinder->messageBoxInfo(
                                sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY), $port, $this->getName() . ' ' . $version),
                                $boxTitle
                            );
                        }
                        return true;
                    }
                }
                mysqli_close($dbLink);
            } else {
                Util::logDebug($this->getName() . ' port ' . $port . ' is used by another application');
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxWarning(
                        sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED_BY), $port),
                        $boxTitle
                    );
                }
            }
        } else {
            Util::logDebug($this->getName() . ' port ' . $port . ' is not used');
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED), $port),
                    $boxTitle
                );
            }
        }

        return false;
    }

    /**
     * Changes the root password for MySQL.
     * This method connects to the MySQL server, updates the password, and flushes privileges.
     *
     * @param string $currentPwd The current root password.
     * @param string $newPwd The new root password to set.
     * @param mixed $wbProgressBar Optional. Progress bar object from Winbinder.
     * @return mixed Returns true on success, or an error message on failure.
     */
    public function changeRootPassword($currentPwd, $newPwd, $wbProgressBar = null) {
        global $bearsamppWinbinder;
        $error = null;

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        if (version_compare(phpversion(), '5.3') === -1) {
            $dbLink = @mysqli_connect('127.0.0.1', $this->rootUser, $currentPwd, '', $this->port);
        } else {
            $dbLink = @mysqli_connect('127.0.0.1:' . $this->port, $this->rootUser, $currentPwd);
        }
        if (!$dbLink) {
            $error = mysqli_connect_error();
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        $stmt = @mysqli_prepare($dbLink, 'UPDATE mysql.user SET Password=PASSWORD(?) WHERE User=?');
        if (empty($error) && $stmt === false) {
            $error = mysqli_error($dbLink);
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        if (empty($error) && !@mysqli_stmt_bind_param($stmt, 'ss', $newPwd, $this->rootUser)) {
            $error = mysqli_stmt_error($stmt);
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        if (empty($error) && !@mysqli_stmt_execute($stmt)) {
            $error = mysqli_stmt_error($stmt);
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        if ($stmt !== false) {
            mysqli_stmt_close($stmt);
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        if (empty($error) && @mysqli_query($dbLink, "FLUSH PRIVILEGES") === false) {
            $error = mysqli_error($dbLink);
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        if ($dbLink) {
            mysqli_close($dbLink);
        }

        if (!empty($error)) {
            return $error;
        }

        // bearsampp.conf
        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        $this->setRootPwd($newPwd);

        // conf
        $this->update();
        $bearsamppWinbinder->incrProgressBar($wbProgressBar);

        return true;
    }

    /**
     * Checks if the specified root password is correct by attempting to connect to the MySQL server.
     *
     * @param string|null $currentPwd Optional. The root password to check. If not provided, uses the stored root password.
     * @param mixed $wbProgressBar Optional. Progress bar object from Winbinder.
     * @return mixed Returns true if the password is correct, or an error message if incorrect.
     */
    public function checkRootPassword($currentPwd = null, $wbProgressBar = null) {
        global $bearsamppWinbinder;
        $currentPwd = $currentPwd == null ? $this->rootPwd : $currentPwd;
        $error = null;

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        if (version_compare(phpversion(), '5.3') === -1) {
            $dbLink = @mysqli_connect('127.0.0.1', $this->rootUser, $currentPwd, '', $this->port);
        } else {
            $dbLink = @mysqli_connect('127.0.0.1:' . $this->port, $this->rootUser, $currentPwd);
        }
        if (!$dbLink) {
            $error = mysqli_connect_error();
        }

        $bearsamppWinbinder->incrProgressBar($wbProgressBar);
        if ($dbLink) {
            mysqli_close($dbLink);
        }

        if (!empty($error)) {
            return $error;
        }

        return true;
    }

    /**
     * Switches the MySQL version by updating the configuration to point to the binaries of the specified version.
     * Optionally displays a message box with the result.
     *
     * @param string $version The version to switch to.
     * @param bool $showWindow Optional. Whether to show a message box with the result.
     * @return bool Returns true if the switch was successful, false otherwise.
     */
    public function switchVersion($version, $showWindow = false) {
        Util::logDebug('Switch ' . $this->name . ' version to ' . $version);
        return $this->updateConfig($version, 0, $showWindow);
    }

    /**
     * Updates the MySQL configuration based on the specified version.
     * This method is called internally by switchVersion().
     *
     * @param string|null $version Optional. The version to update the configuration for. If not provided, uses the current version.
     * @param int $sub Optional. Sub-level for logging purposes.
     * @param bool $showWindow Optional. Whether to show a message box with the result.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppLang, $bearsamppBins, $bearsamppApps, $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->getName(), $version);

        $currentPath = str_replace('mysql' . $this->getVersion(), 'mysql' . $version, $this->getCurrentPath());
        $conf = str_replace('mysql' . $this->getVersion(), 'mysql' . $version, $this->getConf());
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

        // adminer
        $bearsamppApps->getAdminer()->update($sub + 1);

        // php
        $bearsamppBins->getPhp()->update($sub + 1);

        return true;
    }

    /**
     * Initializes data directories for MySQL if they do not exist, especially for versions 5.7.0 and above.
     *
     * @param string|null $path Optional. The path to initialize data in. If not provided, uses the current path.
     * @param string|null $version Optional. The version to consider for initialization. If not provided, uses the current version.
     */
    public function initData($path = null, $version = null) {
        $path = $path != null ? $path : $this->getCurrentPath();
        $version = $version != null ? $version : $this->getVersion();

        if (version_compare($version, '5.7.0', '<')) {
            return;
        }

        if (file_exists($path . '/data')) {
            return;
        }

        Batch::initializeMysql($path);
    }

    /**
     * Executes a MySQL command line and returns the output.
     *
     * @param string $cmd The command to execute.
     * @return array An array containing 'syntaxOk' (boolean) and 'content' (string) keys.
     */
    public function getCmdLineOutput($cmd) {
        $result = array(
            'syntaxOk' => false,
            'content'  => null,
        );

        $bin = $this->getExe();
        $removeLines = 0;
        $outputFrom = '';
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
     * Sets the MySQL version in the configuration.
     *
     * @param string $version The version to set.
     */
    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    /**
     * Retrieves the service handler for MySQL.
     *
     * This method returns the Win32Service object that manages the MySQL service operations.
     * It provides access to the service handler which can be used to control the MySQL service,
     * such as starting, stopping, and managing service properties.
     *
     * @return Win32Service The service handler for MySQL.
     */
    public function getService() {
        return $this->service;
    }

    /**
     * Enables or disables the MySQL module.
     * This method updates the configuration and manages the service based on the enabled state.
     *
     * @param int $enabled Whether to enable (1) or disable (0) the module.
     * @param bool $showWindow Optional. Whether to show a message box with the result.
     */
    public function setEnable($enabled, $showWindow = false) {
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
     * Retrieves the path to the error log file.
     *
     * This method returns the full path to the error log file used by the MySQL module.
     * The error log file is used to store error messages and other logging information
     * related to MySQL operations.
     *
     * @return string The full path to the error log file.
     */
    public function getErrorLog() {
        return $this->errorLog;
    }

    /**
     * Retrieves the path to the MySQL executable.
     *
     * This method returns the full path to the MySQL executable file. This executable
     * is used to start the MySQL server and perform other MySQL operations.
     *
     * @return string The full path to the MySQL executable.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Retrieves the path to the MySQL configuration file.
     *
     * This method returns the full path to the MySQL configuration file. This configuration
     * file contains settings and parameters that configure the MySQL server behavior.
     *
     * @return string The full path to the MySQL configuration file.
     */
    public function getConf() {
        return $this->conf;
    }

    /**
     * Retrieves the MySQL server port number.
     *
     * This method returns the port number on which the MySQL server is configured to listen.
     * The port number is used by clients to connect to the MySQL server.
     *
     * @return int The port number used by the MySQL server.
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Sets the MySQL server port number.
     *
     * This method updates the MySQL configuration to set a new port number for the server.
     * It ensures that the new port number is updated in the configuration file and reflects
     * in the server settings.
     *
     * @param int $port The new port number to set for the MySQL server.
     */
    public function setPort($port) {
        $this->replace(self::LOCAL_CFG_PORT, $port);
    }

    /**
     * Retrieves the MySQL root username.
     *
     * This method returns the username of the MySQL root user. The root user has full
     * administrative privileges over the MySQL server.
     *
     * @return string The username of the MySQL root user.
     */
    public function getRootUser() {
        return $this->rootUser;
    }

    /**
     * Sets the MySQL root username.
     *
     * This method updates the MySQL configuration to set a new username for the root user.
     * It ensures that the new username is updated in the configuration file and reflects
     * in the server settings.
     *
     * @param string $rootUser The new username to set for the MySQL root user.
     */
    public function setRootUser($rootUser) {
        $this->replace(self::LOCAL_CFG_ROOT_USER, $rootUser);
    }

    /**
     * Retrieves the MySQL root password.
     *
     * This method returns the password of the MySQL root user. The root password is used
     * to authenticate the root user who has full administrative privileges over the MySQL server.
     *
     * @return string The password of the MySQL root user.
     */
    public function getRootPwd() {
        return $this->rootPwd;
    }

    /**
     * Sets the MySQL root password.
     *
     * This method updates the MySQL configuration to set a new password for the root user.
     * It ensures that the new password is updated in the configuration file and reflects
     * in the server settings.
     *
     * @param string $rootPwd The new password to set for the MySQL root user.
     */
    public function setRootPwd($rootPwd) {
        $this->replace(self::LOCAL_CFG_ROOT_PWD, $rootPwd);
    }

    /**
     * Retrieves the path to the MySQL CLI executable.
     *
     * This method returns the full path to the MySQL Command Line Interface (CLI) executable.
     * The CLI executable is used to perform command-line operations on the MySQL server.
     *
     * @return string The full path to the MySQL CLI executable.
     */
    public function getCliExe() {
        return $this->cliExe;
    }

    /**
     * Retrieves the path to the MySQL admin executable.
     *
     * This method returns the full path to the MySQL admin executable. This executable
     * is used for administrative tasks on the MySQL server.
     *
     * @return string The full path to the MySQL admin executable.
     */
    public function getAdmin() {
        return $this->admin;
    }
}
