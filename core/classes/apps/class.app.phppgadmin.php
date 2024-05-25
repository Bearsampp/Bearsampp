<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Manages the configuration and operations specific to the phpPgAdmin application module.
 */
class AppPhppgadmin extends Module
{
    /**
     * Configuration key for the phpPgAdmin version.
     */
    const ROOT_CFG_VERSION = 'phppgadminVersion';

    /**
     * Configuration key for the phpPgAdmin configuration file.
     */
    const LOCAL_CFG_CONF = 'phppgadminConf';

    /**
     * Path to the phpPgAdmin configuration file.
     * @var string
     */
    private $conf;

    /**
     * Constructs an instance of the phpPgAdmin application module.
     *
     * @param string $id The identifier of the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration and updates the module properties based on the current settings.
     *
     * @param string|null $id Optional. The identifier of the module.
     * @param string|null $type Optional. The type of the module.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::PHPPGADMIN);
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
     * Updates the configuration file for phpPgAdmin based on the current settings.
     *
     * @param string|null $version Optional. The version to set for the module.
     * @param int $sub Optional. Sub-level for logging.
     * @param bool $showWindow Optional. Whether to show a window during the update.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppRoot, $bearsamppBins;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $alias = $bearsamppRoot->getAliasPath() . '/phppgadmin.conf';
        if (is_file($alias)) {
            Util::replaceInFile($alias, array(
                '/^Alias\s\/phppgadmin\s.*/' => 'Alias /phppgadmin "' . $this->getSymlinkPath() . '/"',
                '/^<Directory\s.*/' => '<Directory "' . $this->getSymlinkPath() . '/">',
            ));
        } else {
            Util::logError($this->getName() . ' alias not found : ' . $alias);
        }

        if ($bearsamppBins->getPostgresql()->isEnable()) {
            Util::replaceInFile($this->getConf(), array(
                '/^\$postgresqlPort\s=\s(\d+)/' => '$postgresqlPort = ' . $bearsamppBins->getPostgresql()->getPort() . ';',
                '/^\$postgresqlRootUser\s=\s/' => '$postgresqlRootUser = \'' . $bearsamppBins->getPostgresql()->getRootUser() . '\';',
                '/^\$postgresqlRootPwd\s=\s/' => '$postgresqlRootPwd = \'' . $bearsamppBins->getPostgresql()->getRootPwd() . '\';',
                '/^\$postgresqlDumpExe\s=\s/' => '$postgresqlDumpExe = \'' . $bearsamppBins->getPostgresql()->getDumpExe() . '\';',
                '/^\$postgresqlDumpAllExe\s=\s/' => '$postgresqlDumpAllExe = \'' . $bearsamppBins->getPostgresql()->getDumpAllExe() . '\';',
            ));
        }

        return true;
    }

    /**
     * Sets the version for the phpPgAdmin module and updates the configuration.
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
     * Gets the path to the phpPgAdmin configuration file.
     *
     * @return string The path to the configuration file.
     */
    public function getConf() {
        return $this->conf;
    }
}
