<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class BinMemcached
 *
 * This class represents the Memcached service module in the Bearsampp application.
 * It handles the configuration, initialization, and management of the Memcached service.
 */
class BinMemcached extends Module
{
    const SERVICE_NAME = 'bearsamppmemcached';
    const SERVICE_PARAMS = '-m %d -p %d -U 0 -vv';

    const ROOT_CFG_ENABLE = 'memcachedEnable';
    const ROOT_CFG_VERSION = 'memcachedVersion';

    const LOCAL_CFG_EXE = 'memcachedExe';
    const LOCAL_CFG_MEMORY = 'memcachedMemory';
    const LOCAL_CFG_PORT = 'memcachedPort';

    private $service;
    private $log;

    private $exe;
    private $memory;
    private $port;

    /**
     * Constructs a BinMemcached object and initializes the Memcached service.
     *
     * @param string $id The ID of the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration and settings for the Memcached service.
     *
     * @param string|null $id The ID of the module. If null, the current ID is used.
     * @param string|null $type The type of the module. If null, the current type is used.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::MEMCACHED);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->enable = $this->enable && $bearsamppConfig->getRaw(self::ROOT_CFG_ENABLE);
        $this->service = new Win32Service(self::SERVICE_NAME);
        $this->log = $bearsamppRoot->getLogsPath() . '/memcached.log';

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->memory = intval($this->bearsamppConfRaw[self::LOCAL_CFG_MEMORY]);
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
        if (empty($this->memory)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_MEMORY, $this->memory));
            return;
        }
        if (empty($this->port)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_PORT, $this->port));
            return;
        }

        $nssm = new Nssm(self::SERVICE_NAME);
        $nssm->setDisplayName(APP_TITLE . ' ' . $this->getName());
        $nssm->setBinPath($this->exe);
        $nssm->setParams(sprintf(self::SERVICE_PARAMS, $this->memory, $this->port));
        $nssm->setStart(Nssm::SERVICE_DEMAND_START);
        $nssm->setStdout($bearsamppRoot->getLogsPath() . '/memcached.out.log');
        $nssm->setStderr($bearsamppRoot->getLogsPath() . '/memcached.err.log');

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
                case self::LOCAL_CFG_MEMORY:
                    $this->memory = intval($value);
                    break;
                case self::LOCAL_CFG_PORT:
                    $this->port = intval($value);
                    break;
            }
        }

        file_put_contents($this->bearsamppConf, $content);
    }

    /**
     * Rebuilds the configuration for the Memcached service in the Windows Registry.
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
                sprintf(self::SERVICE_PARAMS, $this->memory, $this->port)
            );
        }

        return false;
    }

    /**
     * Changes the port for the Memcached service.
     *
     * @param int $port The new port number.
     * @param bool $checkUsed Whether to check if the port is already in use.
     * @param mixed $wbProgressBar The progress bar object for UI updates.
     * @return bool|int True if the port was successfully changed, false if the port is invalid, or the process using the port.
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
     * Checks if the specified port is in use by the Memcached service.
     *
     * @param int $port The port number to check.
     * @param bool $showWindow Whether to show a message box with the result.
     * @return bool True if the port is in use by Memcached, false otherwise.
     */
    public function checkPort($port, $showWindow = false) {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::CHECK_PORT_TITLE), $this->getName(), $port);

        if (!Util::isValidPort($port)) {
            Util::logError($this->getName() . ' port not valid: ' . $port);
            return false;
        }

        if (function_exists('memcache_connect')) {
            $memcache = @memcache_connect('127.0.0.1', $port);
            if ($memcache) {
                $memcacheVersion = memcache_get_version($memcache);
                Util::logDebug($this->getName() . ' port ' . $port . ' is used by: ' . $this->getName() . ' ' . $memcacheVersion);
                memcache_close($memcache);
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY), $port, $this->getName() . ' ' . $memcacheVersion),
                        $boxTitle
                    );
                }
                return true;
            }
        } else {
            $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 3);
            if (!$fp) {
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
                fclose($fp);
            }
        }

        return false;
    }

    /**
     * Switches the version of the Memcached service.
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
     * Updates the configuration for the Memcached service.
     *
     * @param string|null $version The version to update to. If null, the current version is used.
     * @param int $sub The sub-level for logging indentation.
     * @param bool $showWindow Whether to show a message box with the result.
     * @return bool True if the configuration was successfully updated, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppLang, $bearsamppApps, $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->getName(), $version);

        $bearsamppConf = str_replace('memcached' . $this->getVersion(), 'memcached' . $version, $this->bearsamppConf);
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
     * Sets the version of the Memcached service.
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
     * Retrieves the service object for the Memcached service.
     *
     * @return Win32Service The service object.
     */
    public function getService() {
        return $this->service;
    }

    /**
     * Enables or disables the Memcached service.
     *
     * @param bool $enabled Whether to enable or disable the service.
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
     * Retrieves the log file path for the Memcached service.
     *
     * @return string The log file path.
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * Retrieves the executable file path for the Memcached service.
     *
     * @return string The executable file path.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Retrieves the memory allocation for the Memcached service.
     *
     * @return int The memory allocation in MB.
     */
    public function getMemory() {
        return $this->memory;
    }

    /**
     * Sets the memory allocation for the Memcached service.
     *
     * @param int $memory The memory allocation in MB.
     */
    public function setMemory($memory) {
        $this->replace(self::LOCAL_CFG_MEMORY, $memory);
    }

    /**
     * Retrieves the port number for the Memcached service.
     *
     * @return int The port number.
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Sets the port number for the Memcached service.
     *
     * @param int $port The port number.
     */
    public function setPort($port) {
        $this->replace(self::LOCAL_CFG_PORT, $port);
    }
}
