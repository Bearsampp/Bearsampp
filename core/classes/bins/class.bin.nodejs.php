<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Manages the Node.js binary module within the Bearsampp environment.
 *
 * This class extends the Module class and provides specific functionalities
 * for managing Node.js, including configuration loading, version switching,
 * and enabling/disabling the module.
 */
class BinNodejs extends Module
{
    /**
     * Configuration keys for root settings specific to Node.js.
     */
    const ROOT_CFG_ENABLE = 'nodejsEnable';
    const ROOT_CFG_VERSION = 'nodejsVersion';

    /**
     * Configuration keys for local settings specific to Node.js.
     */
    const LOCAL_CFG_EXE = 'nodejsExe';
    const LOCAL_CFG_VARS = 'nodejsVars';
    const LOCAL_CFG_NPM = 'nodejsNpm';
    const LOCAL_CFG_LAUNCH = 'nodejsLaunch';
    const LOCAL_CFG_CONF = 'nodejsConf';

    /**
     * Path to the Node.js executable.
     */
    private $exe;

    /**
     * Path to the Node.js configuration file.
     */
    private $conf;

    /**
     * Path to the Node.js environment variables.
     */
    private $vars;

    /**
     * Path to the npm executable.
     */
    private $npm;

    /**
     * Path to the launch script for Node.js.
     */
    private $launch;

    /**
     * Constructor for the BinNodejs class.
     *
     * @param string $id The identifier for the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration and updates paths based on the current settings.
     *
     * @param string|null $id Optional. The new identifier for the module.
     * @param string|null $type Optional. The new type of the module.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::NODEJS);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->enable = $this->enable && $bearsamppConfig->getRaw(self::ROOT_CFG_ENABLE);

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->conf = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->vars = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_VARS];
            $this->npm = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_NPM];
            $this->launch = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_LAUNCH];
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
        }
        if (!is_file($this->conf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->conf));
        }
        if (!is_file($this->vars)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->vars));
        }
        if (!is_file($this->npm)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->npm));
        }
        if (!is_file($this->launch)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->launch));
        }
    }

    /**
     * Switches the version of Node.js being used.
     *
     * @param string $version The version to switch to.
     * @param bool $showWindow Whether to show a window with the result.
     * @return bool Returns true if the switch was successful, false otherwise.
     */
    public function switchVersion($version, $showWindow = false) {
        Util::logDebug('Switch ' . $this->name . ' version to ' . $version);
        return $this->updateConfig($version, 0, $showWindow);
    }

    /**
     * Updates the configuration for the specified version.
     *
     * @param string|null $version The version to update the configuration for.
     * @param int $sub Level of sub-processing.
     * @param bool $showWindow Whether to show a window with the result.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppLang, $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->getName(), $version);

        $conf = str_replace('nodejs' . $this->getVersion(), 'nodejs' . $version, $this->getConf());
        $bearsamppConf = str_replace('nodejs' . $this->getVersion(), 'nodejs' . $version, $this->bearsamppConf);

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

        return true;
    }

    /**
     * Sets the version of Node.js.
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
     * Enables or disables the Node.js module.
     *
     * @param bool $enabled Whether to enable or disable the module.
     * @param bool $showWindow Whether to show a window with the result.
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
    }

    /**
     * Gets the path to the Node.js executable.
     *
     * @return string The path to the executable.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Gets the path to the Node.js configuration file.
     *
     * @return string The path to the configuration file.
     */
    public function getConf() {
        return $this->conf;
    }

    /**
     * Gets the path to the Node.js environment variables.
     *
     * @return string The path to the environment variables.
     */
    public function getVars() {
        return $this->vars;
    }

    /**
     * Gets the path to the npm executable.
     *
     * @return string The path to the npm executable.
     */
    public function getNpm() {
        return $this->npm;
    }

    /**
     * Gets the path to the launch script for Node.js.
     *
     * @return string The path to the launch script.
     */
    public function getLaunch() {
        return $this->launch;
    }
}
