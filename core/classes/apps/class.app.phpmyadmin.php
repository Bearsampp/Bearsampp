<?php

class AppPhpmyadmin extends Module
{
    const ROOT_CFG_VERSION = 'phpmyadminVersion';

    const LOCAL_CFG_CONF = 'phpmyadminConf';

    private $versions;

    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        // This makes name and version the values in bearsampp.conf
        $this->name = $bearsamppLang->getValue(Lang::PHPMYADMIN);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $versions = array();

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
    }

    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppBs, $bearsamppBins;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config...');

        $alias = $bearsamppBs->getAliasPath() . '/phpmyadmin.conf';
        if (is_file($alias)) {
            $version = '';
            Util::replaceInFile($alias, array(
                '/^Alias\s\/phpmyadmin\s.*/' => 'Alias /phpmyadmin "' . $this->getSymlinkPath() . '/' . $version . '/"',
                '/^<Directory\s.*/' => '<Directory "' . $this->getSymlinkPath() . '/' . $version . '/">',
            ));
        } else {
            Util::logError($this->getName() . ' alias not found : ' . $alias);
        }

        $pmaConf = $this->getConfs();
            if ($bearsamppBins->getMysql()->isEnable()) {
                Util::replaceInFile($pmaConf, array(
                    '/^\$mysqlPort\s=\s(\d+)/' => '$mysqlPort = ' . $bearsamppBins->getMysql()->getPort() . ';',
                    '/^\$mysqlRootUser\s=\s/' => '$mysqlRootUser = \'' . $bearsamppBins->getMysql()->getRootUser() . '\';',
                    '/^\$mysqlRootPwd\s=\s/' => '$mysqlRootPwd = \'' . $bearsamppBins->getMysql()->getRootPwd() . '\';'
                ));
            }
            if ($bearsamppBins->getMariadb()->isEnable()) {
                Util::replaceInFile($pmaConf, array(
                    '/^\$mariadbPort\s=\s(\d+)/' => '$mariadbPort = ' . $bearsamppBins->getMariadb()->getPort() . ';',
                    '/^\$mariadbRootUser\s=\s/' => '$mariadbRootUser = \'' . $bearsamppBins->getMariadb()->getRootUser() . '\';',
                    '/^\$mariadbRootPwd\s=\s/' => '$mariadbRootPwd = \'' . $bearsamppBins->getMariadb()->getRootPwd() . '\';'
                ));
            }


        return true;
    }

    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    // TODO How do we replace foreach with static value?
    public function getConfs() {
        $result = array();
        foreach ($this->versions as $version => $data) {
            $result[] = $data['conf'];
        }
        return $result;
    }
}
