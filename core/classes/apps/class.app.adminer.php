<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class AppAdminer
 *
 * This class represents the Adminer application module in the Bearsampp application.
 * It extends the Module class and provides functionalities specific to the Adminer module,
 * such as configuration management and version control.
 */
class AppAdminer extends Module
{
    /**
     * Configuration key for the Adminer version in the root configuration.
     */
    const ROOT_CFG_VERSION = 'adminerVersion';

    /**
     * Configuration key for the Adminer configuration file in the local configuration.
     */
    const LOCAL_CFG_CONF = 'adminerConf';

    /**
     * @var string The path to the Adminer configuration file.
     */
    private $conf;

    /**
     * Constructor for the AppAdminer class.
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

        $this->name = $bearsamppLang->getValue(Lang::ADMINER);
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
     * Updates the module configuration with a specific version.
     *
     * @param string|null $version The version to update to. If null, the current version is used.
     * @param int $sub The sub-level for logging indentation.
     * @param bool $showWindow Whether to show a window during the update process.
     * @return bool True if the update was successful, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppRoot, $bearsamppBins;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $alias = $bearsamppRoot->getAliasPath() . '/adminer.conf';
        if (is_file($alias)) {
            Util::replaceInFile($alias, array(
                '/^Alias\s\/adminer\s.*/' => 'Alias /adminer "' . $this->getSymlinkPath() . '/"',
                '/^<Directory\s.*/' => '<Directory "' . $this->getSymlinkPath() . '/">',
            ));
        } else {
            Util::logError($this->getName() . ' alias not found : ' . $alias);
        }

        if ($bearsamppBins->getMysql()->isEnable()) {
            Util::replaceInFile($this->getConf(), array(
                '/^\$mysqlPort\s=\s(\d+)/' => '$mysqlPort = ' . $bearsamppBins->getMysql()->getPort() . ';',
                '/^\$mysqlRootUser\s=\s/' => '$mysqlRootUser = \'' . $bearsamppBins->getMysql()->getRootUser() . '\';',
                '/^\$mysqlRootPwd\s=\s/' => '$mysqlRootPwd = \'' . $bearsamppBins->getMysql()->getRootPwd() . '\';'
            ));
        }
        if ($bearsamppBins->getMariadb()->isEnable()) {
            Util::replaceInFile($this->getConf(), array(
                '/^\$mariadbPort\s=\s(\d+)/' => '$mariadbPort = ' . $bearsamppBins->getMariadb()->getPort() . ';',
                '/^\$mariadbRootUser\s=\s/' => '$mariadbRootUser = \'' . $bearsamppBins->getMariadb()->getRootUser() . '\';',
                '/^\$mariadbRootPwd\s=\s/' => '$mariadbRootPwd = \'' . $bearsamppBins->getMariadb()->getRootPwd() . '\';'
            ));
        }
        if ($bearsamppBins->getPostgresql()->isEnable()) {
            Util::replaceInFile($this->getConf(), array(
                '/^\$postgresqlPort\s=\s(\d+)/' => '$postgresqlPort = ' . $bearsamppBins->getPostgresql()->getPort() . ';',
                '/^\$postgresqlRootUser\s=\s/' => '$postgresqlRootUser = \'' . $bearsamppBins->getPostgresql()->getRootUser() . '\';',
                '/^\$postgresqlRootPwd\s=\s/' => '$postgresqlRootPwd = \'' . $bearsamppBins->getPostgresql()->getRootPwd() . '\';'
            ));
        }

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
     * Gets the path to the Adminer configuration file.
     *
     * @return string The path to the Adminer configuration file.
     */
    public function getConf() {
        return $this->conf;
    }
}
