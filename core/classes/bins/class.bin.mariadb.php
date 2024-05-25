<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class BinMariadb
 * Extends the Module class to manage the MariaDB service within the Bearsampp environment.
 * This class handles the configuration, management, and operations specific to the MariaDB service.
 *
 * Constants:
 * - SERVICE_NAME: Defines the service name for MariaDB.
 * - ROOT_CFG_ENABLE: Configuration key for enabling MariaDB.
 * - ROOT_CFG_VERSION: Configuration key for the MariaDB version.
 * - LOCAL_CFG_EXE: Configuration key for the executable path of MariaDB.
 * - LOCAL_CFG_CLI_EXE: Configuration key for the command line interface executable path of MariaDB.
 * - LOCAL_CFG_ADMIN: Configuration key for the admin executable path of MariaDB.
 * - LOCAL_CFG_CONF: Configuration key for the configuration file path of MariaDB.
 * - LOCAL_CFG_PORT: Configuration key for the port on which MariaDB runs.
 * - LOCAL_CFG_ROOT_USER: Configuration key for the root username of MariaDB.
 * - LOCAL_CFG_ROOT_PWD: Configuration key for the root password of MariaDB.
 * - CMD_VERSION: Command line argument to get the version of MariaDB.
 * - CMD_VARIABLES: Command line argument to get the variables from MariaDB.
 * - CMD_SYNTAX_CHECK: Command line argument to check the syntax of the MariaDB configuration.
 *
 * Properties:
 * - $service: Holds the instance of the service management utility.
 * - $errorLog: Path to the error log file.
 * - $exe: Path to the MariaDB executable.
 * - $conf: Path to the MariaDB configuration file.
 * - $port: Port number on which MariaDB is configured to run.
 * - $rootUser: Username for the root user of MariaDB.
 * - $rootPwd: Password for the root user of MariaDB.
 * - $cliExe: Path to the command line interface executable of MariaDB.
 * - $admin: Path to the admin executable of MariaDB.
 */
class BinMariadb extends Module
{
    const SERVICE_NAME = 'bearsamppmariadb';

    const ROOT_CFG_ENABLE = 'mariadbEnable';
    const ROOT_CFG_VERSION = 'mariadbVersion';

    const LOCAL_CFG_EXE = 'mariadbExe';
    const LOCAL_CFG_CLI_EXE = 'mariadbCliExe';
    const LOCAL_CFG_ADMIN = 'mariadbAdmin';
    const LOCAL_CFG_CONF = 'mariadbConf';
    const LOCAL_CFG_PORT = 'mariadbPort';
    const LOCAL_CFG_ROOT_USER = 'mariadbRootUser';
    const LOCAL_CFG_ROOT_PWD = 'mariadbRootPwd';

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

    /**
     * Constructor for the BinMariadb class.
     * Initializes a new instance of the BinMariadb class, setting up the necessary properties and reloading configuration based on the provided identifiers.
     *
     * @param string $id The identifier for the specific instance of MariaDB.
     * @param string $type The type of module, used to categorize this instance within the system.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration and updates the properties of the MariaDB instance.
     * This method sets up the MariaDB service, checks the existence of necessary files and directories,
     * and initializes the service settings.
     *
     * @param string|null $id Optional. The identifier for the specific instance of MariaDB.
     * @param string|null $type Optional. The type of module, used to categorize this instance within the system.
     * @global Root $bearsamppRoot Global instance for accessing the application's root functionalities.
     * @global Config $bearsamppConfig Global configuration handler.
     * @global LangProc $bearsamppLang Language processor for retrieving language-specific values.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::MARIADB);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->enable = $this->enable && $bearsamppConfig->getRaw(self::ROOT_CFG_ENABLE);
        $this->service = new Win32Service(self::SERVICE_NAME);
        $this->errorLog = $bearsamppRoot->getLogsPath() . '/mariadb.log';

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
     * Replaces all specified parameters in the MariaDB configuration file.
     * Updates the internal configuration array after modifying the file.
     *
     * @param array $params Associative array of parameters where key is the configuration key and value is the new value to set.
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
     * Changes the port on which MariaDB is running. It checks if the new port is valid and not in use,
     * updates the configuration, and applies the changes.
     *
     * @param int $port The new port number to set for MariaDB.
     * @param bool $checkUsed Optional. Whether to check if the port is already in use. Defaults to false.
     * @param mixed $wbProgressBar Optional. Progress bar object to update the UI during the process.
     * @return bool True if the port was successfully changed, false otherwise.
     * @global WinBinder $bearsamppWinbinder Global instance for handling WinBinder operations.
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
     * Checks if the specified port is being used by MariaDB or another application.
     * Provides detailed information about the usage of the port.
     *
     * @param int $port The port number to check.
     * @param bool $showWindow Optional. Whether to show a message box with the result. Defaults to false.
     * @return bool True if the port is used by MariaDB, false if it's used by another application or not at all.
     * @global LangProc $bearsamppLang Language processor for retrieving language-specific values.
     * @global WinBinder $bearsamppWinbinder Global instance for handling WinBinder operations.
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
            $isMariadb = false;
            $version = false;

            if ($dbLink) {
                $result = mysqli_query($dbLink, 'SHOW VARIABLES');
                if ($result) {
                    while (false !== ($row = mysqli_fetch_array($result, MYSQLI_NUM))) {
                        if ($row[0] == 'version') {
                            $version = explode("-", $row[1]);
                            $version = count($version) > 1 ? $version[0] : $row[1];
                        }
                        if ($row[0] == 'version_comment' && Util::startWith(strtolower($row[1]), 'mariadb')) {
                            $isMariadb = true;
                        }
                        if ($isMariadb && $version !== false) {
                            break;
                        }
                    }
                    if (!$isMariadb) {
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
     * Changes the root password for MariaDB. Connects to the database to update the password,
     * and updates the configuration file if the password change is successful.
     *
     * @param string $currentPwd The current root password.
     * @param string $newPwd The new root password to set.
     * @param mixed $wbProgressBar Optional. Progress bar object to update the UI during the process.
     * @return mixed True if the password was successfully changed, an error message string otherwise.
     * @global WinBinder $bearsamppWinbinder Global instance for handling WinBinder operations.
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
     * Checks if the provided root password can connect to the MariaDB database.
     * If no password is provided, it uses the default root password from the class property.
     *
     * @param string|null $currentPwd The current root password to check. If null, uses the stored root password.
     * @param mixed $wbProgressBar Optional progress bar object to update UI progress.
     * @return mixed Returns true if the connection is successful, otherwise returns the error message.
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
     * Switches the MariaDB version to the specified version.
     * This method updates the configuration to reflect the new version and handles UI updates if specified.
     *
     * @param string $version The new version to switch to.
     * @param bool $showWindow Optional. If true, shows a window with the update process. Defaults to false.
     * @return bool Returns true if the switch is successful, false otherwise.
     */
    public function switchVersion($version, $showWindow = false) {
        Util::logDebug('Switch ' . $this->name . ' version to ' . $version);
        return $this->updateConfig($version, 0, $showWindow);
    }

    /**
     * Updates the configuration settings for the MariaDB instance to a new version.
     * This involves updating paths in the configuration files and ensuring the new version's configuration is valid.
     *
     * @param string|null $version The new version to update the configuration to. If null, uses the current version.
     * @param int $sub Level of indentation for logging, used for better readability in nested updates.
     * @param bool $showWindow If true, displays a window with error messages during the update process.
     * @return bool Returns true if the configuration update is successful, false if there are errors.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppLang, $bearsamppApps, $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->getName(), $version);

        $conf = str_replace('mariadb' . $this->getVersion(), 'mariadb' . $version, $this->getConf());
        $bearsamppConf = str_replace('mariadb' . $this->getVersion(), 'mariadb' . $version, $this->bearsamppConf);

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

        return true;
    }
    /**
     * Executes a command line operation for MariaDB and captures the output.
     * This method is used to run MariaDB-specific commands and fetch their output for further processing.
     *
     * @param string $cmd The command to execute. This could be a version check, syntax check, or other MariaDB commands.
     * @return array Returns an array with 'syntaxOk' indicating if the command was successful, and 'content' containing the command output.
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
            $tmpResult = Batch::exec('mariadbGetCmdLineOutput', '"' . $bin . '" ' . $cmd . ' ' . $outputFrom, 5);
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
     * Sets the version of MariaDB to be used.
     * This method updates the configuration to reflect the specified version and reloads the module to apply changes.
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
     * Retrieves the service management instance for MariaDB.
     * This service instance can be used to control the MariaDB service, such as starting or stopping it.
     *
     * @return Win32Service Returns the service management instance.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Sets the enable status of the module and optionally displays an error message if the module cannot be enabled.
     *
     * @param int $enabled The desired state of the module, where Config::ENABLED indicates enabling the module.
     * @param bool $showWindow Whether to display an error message window if the module cannot be enabled.
     * @return void
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
     * Retrieves the error log path for the module.
     *
     * @return string The path to the error log file.
     */
    public function getErrorLog() {
        return $this->errorLog;
    }

    /**
     * Retrieves the executable path for the module.
     *
     * @return string The path to the executable file.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Retrieves the configuration file path for the module.
     *
     * @return string The path to the configuration file.
     */
    public function getConf() {
        return $this->conf;
    }

    /**
     * Retrieves the port number used by the module.
     *
     * @return int The port number.
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Sets the port number for the module.
     *
     * @param int $port The new port number to be set.
     * @return void
     */
    public function setPort($port) {
        $this->replace(self::LOCAL_CFG_PORT, $port);
    }

    /**
     * Retrieves the root user name for the module.
     *
     * @return string The root user name.
     */
    public function getRootUser() {
        return $this->rootUser;
    }

    /**
     * Sets the root user name for the module.
     *
     * @param string $rootUser The new root user name to be set.
     * @return void
     */
    public function setRootUser($rootUser) {
        $this->replace(self::LOCAL_CFG_ROOT_USER, $rootUser);
    }

    /**
     * Retrieves the root password for the module.
     *
     * @return string The root password.
     */
    public function getRootPwd() {
        return $this->rootPwd;
    }

    /**
     * Sets the root password for the module.
     *
     * @param string $rootPwd The new root password to be set.
     * @return void
     */
    public function setRootPwd($rootPwd) {
        $this->replace(self::LOCAL_CFG_ROOT_PWD, $rootPwd);
    }

    /**
     * Retrieves the command-line executable path for the module.
     *
     * @return string The path to the command-line executable.
     */
    public function getCliExe() {
        return $this->cliExe;
    }

    /**
     * Retrieves the administration settings for the module.
     *
     * @return mixed The administration settings.
     */
    public function getAdmin() {
        return $this->admin;
    }
}
