<?php
/*
 *
 *  * Copyright (c) 2021-2024 Bearsampp
 *  * License:  GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class BinXlight
 *
 * This class represents the Xlight FTP server module in the Bearsampp application.
 * It handles the configuration, initialization, and management of the Xlight FTP server.
 */
class BinXlight extends Module
{
    const SERVICE_NAME = 'bearsamppxlight';
    const SERVICE_PARAMS = ' -startall';

    const ROOT_CFG_ENABLE = 'xlightEnable';
    const ROOT_CFG_VERSION = 'xlightVersion';

    const LOCAL_CFG_EXE = 'xlightExe';
    const LOCAL_CFG_SSL_PORT = 'xlightSslPort';
    const LOCAL_CFG_PORT = 'xlightPort';

    private $service;
    private $log;

    private $exe;
    private $port;
    private $SslPort;

    /**
     * Constructs a BinXlight object and initializes the module.
     *
     * @param string $id The ID of the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the module configuration based on the provided ID and type.
     *
     * @param string|null $id The ID of the module. If null, the current ID is used.
     * @param string|null $type The type of the module. If null, the current type is used.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::XLIGHT);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->enable = $this->enable && $bearsamppConfig->getRaw(self::ROOT_CFG_ENABLE);
        $this->service = new Win32Service(self::SERVICE_NAME);
        $this->log = $bearsamppRoot->getLogsPath() . '/xlight.log';

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->SslPort = intval($this->bearsamppConfRaw[self::LOCAL_CFG_SSL_PORT]);
            $this->port = intval($this->bearsamppConfRaw[self::LOCAL_CFG_PORT]);
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
        if (empty($this->SslPort)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_SSL_PORT, $this->SslPort));
            return;
        }
        if (empty($this->port)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_PORT, $this->port));
            return;
        }

        $nssm = new Nssm(self::SERVICE_NAME);
        $nssm->setDisplayName(APP_TITLE . ' ' . $this->getName());
        $nssm->setBinPath($this->exe);
        $nssm->setParams(sprintf(self::SERVICE_PARAMS, $this->SslPort, $this->port));
        $nssm->setStart(Nssm::SERVICE_DEMAND_START);
        $nssm->setStdout($bearsamppRoot->getLogsPath() . '/xlight.log');
        $nssm->setStderr($bearsamppRoot->getLogsPath() . '/xlight.error.log');

        $this->service->setNssm($nssm);
    }

    /**
     * Replaces multiple key-value pairs in the configuration file.
     *
     * @param array $params An associative array of key-value pairs to replace.
     */
    protected function replaceAll($params) {
        $content = file_get_contents($this->bearsamppConf);

        foreach ($params as $key => $value) {
            $content = preg_replace('|' . $key . ' = .*|', $key . ' = ' . '"' . $value.'"', $content);
            $this->bearsamppConfRaw[$key] = $value;
            switch ($key) {
                case self::LOCAL_CFG_SSL_PORT:
                    $this->SslPort = intval($value);
                    break;
                case self::LOCAL_CFG_PORT:
                    $this->port = intval($value);
                    break;
            }
        }

        file_put_contents($this->bearsamppConf, $content);
    }

    /**
     * Rebuilds the configuration in the Windows Registry.
     *
     * @return bool True if the configuration was successfully rebuilt, false otherwise.
     */
    public function rebuildConf() {
        global $bearsamppRegistry;

        $exists = $bearsamppRegistry->exists(
            Registry::HKEY_LOCAL_MACHINE,
            'SYSTEM\CurrentControlSet\Services\\' . self::SERVICE_NAME . '\Parameters',
            Nssm::INFO_APP_PARAMETERS
        );
        if ($exists) {
            return $bearsamppRegistry->setExpandStringValue(
                Registry::HKEY_LOCAL_MACHINE,
                'SYSTEM\CurrentControlSet\Services\\' . self::SERVICE_NAME . '\Parameters',
                Nssm::INFO_APP_PARAMETERS,
                sprintf(self::SERVICE_PARAMS, $this->SslPort, $this->port)
            );
        }

        return false;
    }

    /**
     * Changes the port used by the Xlight FTP server.
     *
     * @param int $port The new port number.
     * @param bool $checkUsed Whether to check if the port is already in use.
     * @param mixed|null $wbProgressBar The progress bar object for UI updates (optional).
     * @return bool|int True if the port was successfully changed, false if invalid, or the process using the port.
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
     * Checks if a port is used by the Xlight FTP server.
     *
     * @param int $port The port number to check.
     * @param bool $showWindow Whether to show a message box with the result.
     * @return bool True if the port is used by Xlight, false otherwise.
     */
    public function checkPort($port, $showWindow = false) {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::CHECK_PORT_TITLE), $this->getName(), $port);

        if (!Util::isValidPort($port)) {
            Util::logError($this->getName() . ' port not valid: ' . $port);
            return false;
        }

        $headers = Util::getHeaders('127.0.0.1', $port);
        if (!empty($headers)) {
            if (Util::contains($headers[0], 'Xlight')) {
                Util::logDebug($this->getName() . ' port ' . $port . ' is used by: ' . str_replace('220 ', '', $headers[0]));
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY), $port, str_replace('220 ', '', $headers[0])),
                        $boxTitle
                    );
                }
                return true;
            }
            Util::logDebug($this->getName() . ' port ' . $port . ' is used by another application');
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED_BY), $port),
                    $boxTitle
                );
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
     * Switches the version of the Xlight FTP server.
     *
     * @param string $version The version to switch to.
     * @param bool $showWindow Whether to show a message box with the result.
     * @return bool True if the version was successfully switched, false otherwise.
     */
    public function switchVersion($version, $showWindow = false) {
        Util::logDebug('Switch ' . $this->name . ' version to ' . $version);
        return $this->updateConfig($version, 0, $showWindow);
    }

    /**
     * Updates the configuration of the Xlight FTP server.
     *
     * @param string|null $version The version to update to. If null, the current version is used.
     * @param int $sub The sub-level for logging indentation.
     * @param bool $showWindow Whether to show a message box with the result.
     * @return bool True if the configuration was successfully updated, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppLang, $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->getName(), $version);

        $bearsamppConf = str_replace('xlight' . $this->getVersion(), 'xlight' . $version, $this->bearsamppConf);
        if (!file_exists($bearsamppConf)) {
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

        return true;
    }

    /**
     * Sets the version of the Xlight FTP server.
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
     * Gets the service object for the Xlight FTP server.
     *
     * @return Win32Service The service object.
     */
    public function getService() {
        return $this->service;
    }

    /**
     * Enables or disables the Xlight FTP server.
     *
     * @param bool $enabled Whether to enable or disable the server.
     * @param bool $showWindow Whether to show a message box with the result.
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
            Util::installService($this, $this->port, null, $showWindow);
        } else {
            Util::removeService($this->service, $this->name);
        }
    }

    /**
     * Gets the log file path for the Xlight FTP server.
     *
     * @return string The log file path.
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * Gets the executable file path for the Xlight FTP server.
     *
     * @return string The executable file path.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Gets the SSL port used by the Xlight FTP server.
     *
     * @return int The SSL port number.
     */
    public function getUiPort() {
        return $this->SslPort;
    }

    /**
     * Sets the SSL port for the Xlight FTP server.
     *
     * @param int $SslPort The SSL port number.
     */
    public function setSslPort($SslPort) {
        $this->replace(self::LOCAL_CFG_SSL_PORT, $SslPort);
    }

    /**
     * Gets the port used by the Xlight FTP server.
     *
     * @return int The port number.
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Sets the port for the Xlight FTP server.
     *
     * @param int $port The port number.
     */
    public function setPort($port) {
        $this->replace(self::LOCAL_CFG_PORT, $port);
    }
}
