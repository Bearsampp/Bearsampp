<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class AppWebgrind
 * Manages the Webgrind module, handling configuration and version management.
 */
class AppWebgrind extends Module
{
    /**
     * Configuration key for the Webgrind version.
     */
    const ROOT_CFG_VERSION = 'webgrindVersion';

    /**
     * Configuration key for the Webgrind configuration file.
     */
    const LOCAL_CFG_CONF = 'webgrindConf';

    /**
     * Path to the Webgrind configuration file.
     * @var string
     */
    private $conf;

    /**
     * Constructs the AppWebgrind object.
     *
     * @param string $id The module ID.
     * @param string $type The module type.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the module configuration and updates paths based on the current version.
     *
     * @param string|null $id Optional module ID to reload.
     * @param string|null $type Optional module type to reload.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::WEBGRIND);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        if ($this->bearsamppConfRaw !== false) {
            $this->conf = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
        }

        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        if (!is_dir($this->currentPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->currentPath));
        }
        if (!is_dir($this->symlinkPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->symlinkPath));
            return;
        }
        if (!is_file($this->bearsamppConf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->bearsamppConf));
        }
        if (!is_file($this->conf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->conf));
        }
    }

    /**
     * Updates the configuration for the specified version.
     *
     * @param string|null $version Optional version to update the configuration for.
     * @param int $sub Sub level for logging indentation.
     * @param bool $showWindow Whether to show a window during the update.
     * @return bool Returns true if the update is successful.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppRoot;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $alias = $bearsamppRoot->getAliasPath() . '/webgrind.conf';
        if (is_file($alias)) {
            Util::replaceInFile($alias, array(
                '/^Alias\s\/webgrind\s.*/' => 'Alias /webgrind "' . $this->getSymlinkPath() . '/"',
                '/^<Directory\s.*/' => '<Directory "' . $this->getSymlinkPath() . '/">',
            ));
        } else {
            Util::logError($this->getName() . ' alias not found : ' . $alias);
        }

        return true;
    }

    /**
     * Sets the version of the module and reloads the configuration.
     *
     * @param string $version The new version to set.
     */
    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    /**
     * Gets the path to the configuration file.
     *
     * @return string The path to the configuration file.
     */
    public function getConf() {
        return $this->conf;
    }
}
