<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Represents the phpMyAdmin application module within the Bearsampp environment.
 * This class extends the Module class and manages the configuration and behavior
 * specific to the phpMyAdmin application.
 */
class AppPhpmyadmin extends Module
{
    /**
     * Constant for the configuration key that stores the phpMyAdmin version.
     */
    const ROOT_CFG_VERSION = 'phpmyadminVersion';

    /**
     * Constant for the configuration key that stores the phpMyAdmin configuration file location.
     */
    const LOCAL_CFG_CONF = 'phpmyadminConf';

    /**
     * @var string The path to the phpMyAdmin configuration file.
     */
    private $conf;

    /**
     * Constructs a new instance of the phpMyAdmin application module.
     * Initializes the module by loading its configuration and setting up the environment.
     *
     * @param string $id The identifier of the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration and environment for the phpMyAdmin module.
     * This method updates the module's properties based on the current configuration
     * and checks the availability and integrity of necessary files and directories.
     *
     * @param string|null $id Optional. The new identifier of the module.
     * @param string|null $type Optional. The new type of the module.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        // This makes name and version the values in bearsampp.conf
        $this->name = $bearsamppLang->getValue(Lang::PHPMYADMIN);
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
     * Updates the configuration for the phpMyAdmin module.
     * This method adjusts the configuration files to reflect the current settings,
     * such as the database connection details.
     *
     * @param string|null $version Optional. The version to set for the module.
     * @param int $sub Optional. Sub-level for logging depth.
     * @param bool $showWindow Optional. Whether to show any GUI window during the update.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppRoot, $bearsamppBins;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        $alias = $bearsamppRoot->getAliasPath() . '/phpmyadmin.conf';
        if (is_file($alias)) {
            Util::replaceInFile($alias, array(
                '/^Alias\s\/phpmyadmin\s.*/' => 'Alias /phpmyadmin "' . $this->getSymlinkPath() . '/"',
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


        return true;
    }

    /**
     * Sets the version of the phpMyAdmin module and updates the configuration accordingly.
     *
     * @param string $version The new version to set for the module.
     */
    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    /**
     * Retrieves the path to the phpMyAdmin configuration file.
     *
     * @return string The path to the configuration file.
     */
    public function getConf() {
        return $this->conf;
    }
}
