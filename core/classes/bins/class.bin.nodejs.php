<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class BinNodejs
 *
 * The `BinNodejs` class extends the `Module` class and provides functionalities specific to managing
 * the Node.js module within the Bearsampp application. It includes methods for reloading the module
 * configuration, switching versions, enabling/disabling the module, and retrieving various configuration
 * paths such as the executable, configuration file, variables file, npm executable, and launch script.
 *
 * Constants:
 * - `ROOT_CFG_ENABLE`: Configuration key for enabling the Node.js module.
 * - `ROOT_CFG_VERSION`: Configuration key for the Node.js version.
 * - `LOCAL_CFG_EXE`: Configuration key for the Node.js executable.
 * - `LOCAL_CFG_VARS`: Configuration key for the Node.js variables.
 * - `LOCAL_CFG_NPM`: Configuration key for the npm executable.
 * - `LOCAL_CFG_LAUNCH`: Configuration key for the Node.js launch script.
 * - `LOCAL_CFG_CONF`: Configuration key for the Node.js configuration file.
 *
 * Properties:
 * - `exe`: Path to the Node.js executable.
 * - `conf`: Path to the Node.js configuration file.
 * - `vars`: Path to the Node.js variables file.
 * - `npm`: Path to the npm executable.
 * - `launch`: Path to the Node.js launch script.
 *
 * Methods:
 * - `__construct($id, $type)`: Constructs a `BinNodejs` object and initializes the module with the given ID and type.
 * - `reload($id = null, $type = null)`: Reloads the module configuration based on the provided ID and type.
 * - `switchVersion($version, $showWindow = false)`: Switches the Node.js version to the specified version.
 * - `updateConfig($version = null, $sub = 0, $showWindow = false)`: Updates the module configuration with a specific version.
 * - `setVersion($version)`: Sets the version of the module.
 * - `setEnable($enabled, $showWindow = false)`: Enables or disables the module.
 * - `getExe()`: Retrieves the executable path for Node.js.
 * - `getConf()`: Retrieves the configuration file path for Node.js.
 * - `getVars()`: Retrieves the variables file path for Node.js.
 * - `getNpm()`: Retrieves the npm executable path for Node.js.
 * - `getLaunch()`: Retrieves the launch script path for Node.js.
 */
class BinNodejs extends Module
{
    const ROOT_CFG_ENABLE = 'nodejsEnable';
    const ROOT_CFG_VERSION = 'nodejsVersion';

    const LOCAL_CFG_EXE = 'nodejsExe';
    const LOCAL_CFG_VARS = 'nodejsVars';
    const LOCAL_CFG_NPM = 'nodejsNpm';
    const LOCAL_CFG_LAUNCH = 'nodejsLaunch';
    const LOCAL_CFG_CONF = 'nodejsConf';

    private $exe;
    private $conf;
    private $vars;
    private $npm;
    private $launch;

    /**
     * Constructs a BinNodejs object and initializes the module with the given ID and type.
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
     * Switches the Node.js version to the specified version.
     *
     * @param string $version The version to switch to.
     * @param bool $showWindow Whether to show a window during the switch process.
     * @return bool True if the switch was successful, false otherwise.
     */
    public function switchVersion($version, $showWindow = false) {
        Util::logDebug('Switch ' . $this->name . ' version to ' . $version);
        return $this->updateConfig($version, 0, $showWindow);
    }

    /**
     * Updates the module configuration with a specific version.
     *
     * @param string|null $version The version to update to. If null, the current version is used.
     * @param int $sub The sub-level for logging indentation.
     * @param bool $showWindow Whether to show a window during the update process.
     * @return bool True if the update was successful, false otherwise.
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
     * Sets the version of the module.
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
     * Enables or disables the module.
     *
     * @param int $enabled The enable status (1 for enabled, 0 for disabled).
     * @param bool $showWindow Whether to show a window during the enable/disable process.
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
     * Retrieves the executable path for Node.js.
     *
     * @return string The executable path.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Retrieves the configuration file path for Node.js.
     *
     * @return string The configuration file path.
     */
    public function getConf() {
        return $this->conf;
    }

    /**
     * Retrieves the variables file path for Node.js.
     *
     * @return string The variables file path.
     */
    public function getVars() {
        return $this->vars;
    }

    /**
     * Retrieves the npm executable path for Node.js.
     *
     * @return string The npm executable path.
     */
    public function getNpm() {
        return $this->npm;
    }

    /**
     * Retrieves the launch script path for Node.js.
     *
     * @return string The launch script path.
     */
    public function getLaunch() {
        return $this->launch;
    }
}
