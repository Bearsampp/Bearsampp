<?php

class AppPhpmyadmin extends Module
{
    const ROOT_CFG_VERSION = 'phpmyadminVersion';
    
    const LOCAL_CFG_PHP52 = 'php52';
    const LOCAL_CFG_PHP53 = 'php53';
    const LOCAL_CFG_PHP55 = 'php55';
    
    const LOCAL_CFG_CONF = 'phpmyadminConf';
    
    private $versions;

    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    public function reload($id = null, $type = null) {
        global $neardConfig, $neardLang;

        $this->name = $neardLang->getValue(Lang::PHPMYADMIN);
        $this->version = $neardConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);
        
        $versions = array();
        if ($this->neardConfRaw !== false) {
            $versions[self::LOCAL_CFG_PHP52] = $this->neardConfRaw[self::LOCAL_CFG_PHP52];
            $versions[self::LOCAL_CFG_PHP53] = $this->neardConfRaw[self::LOCAL_CFG_PHP53];
            $versions[self::LOCAL_CFG_PHP55] = $this->neardConfRaw[self::LOCAL_CFG_PHP55];
        }
        
        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        if (!is_dir($this->currentPath)) {
            Util::logError(sprintf($neardLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->currentPath));
        }
        if (!is_file($this->neardConf)) {
            Util::logError(sprintf($neardLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->neardConf));
        }
        
        foreach ($versions as $key => $versionSub) {
            $neardConfSub = $this->currentPath . '/' . $versionSub . '/neard.conf';
            if (!is_file($neardConfSub)) {
                Util::logError(sprintf($neardLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version . ' / ' . $versionSub, $neardConfSub));
            }
            $neardConfRawSub = parse_ini_file($neardConfSub);
            if ($neardConfRawSub !== false) {
                $confSub = $this->currentPath . '/' . $versionSub . '/' . $neardConfRawSub[self::LOCAL_CFG_CONF];
                if (!is_file($confSub)) {
                    Util::logError(sprintf($neardLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version . ' / ' . $confSub));
                } else {
                    $this->versions[$key] = array(
                        'version' => $versionSub,
                        'conf' => $confSub
                    );
                }
            }
        }
    }

    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $neardBs, $neardBins;
        
        if (!$this->enable) {
            return true;
        }
        
        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config...');
    
        $alias = $neardBs->getAliasPath() . '/phpmyadmin.conf';
        if (is_file($alias)) {
            $version = $this->getVersionCompatPhp();
            Util::replaceInFile($alias, array(
                '/^Alias\s\/phpmyadmin\s.*/' => 'Alias /phpmyadmin "' . $this->getCurrentPath() . '/' . $version . '/"',
                '/^<Directory\s.*/' => '<Directory "' . $this->getCurrentPath() . '/' . $version . '/">',
            ));
        } else {
            Util::logError($this->getName() . ' alias not found : ' . $alias);
        }
        
        foreach ($this->getConfs() as $pmaConf) {
            if ($neardBins->getMysql()->isEnable()) {
                Util::replaceInFile($pmaConf, array(
                    '/^\$mysqlPort\s=\s(\d+)/' => '$mysqlPort = ' . $neardBins->getMysql()->getPort() . ';',
                    '/^\$mysqlRootUser\s=\s/' => '$mysqlRootUser = \'' . $neardBins->getMysql()->getRootUser() . '\';',
                    '/^\$mysqlRootPwd\s=\s/' => '$mysqlRootPwd = \'' . $neardBins->getMysql()->getRootPwd() . '\';'
                ));
            }
            if ($neardBins->getMariadb()->isEnable()) {
                Util::replaceInFile($pmaConf, array(
                    '/^\$mariadbPort\s=\s(\d+)/' => '$mariadbPort = ' . $neardBins->getMariadb()->getPort() . ';',
                    '/^\$mariadbRootUser\s=\s/' => '$mariadbRootUser = \'' . $neardBins->getMariadb()->getRootUser() . '\';',
                    '/^\$mariadbRootPwd\s=\s/' => '$mariadbRootPwd = \'' . $neardBins->getMariadb()->getRootPwd() . '\';'
                ));
            }
        }
    }
    
    public function getVersions() {
        return $this->versions;
    }
    
    public function getVersionsStr() {
        $result = '';
        foreach ($this->versions as $version => $data) {
            if (!empty($result)) {
                $result .= ' / ';
            }
            $result .= $data['version'];
        }
        return $result;
    }
    
    public function getVersionCompatPhp($phpVersion = null) {
        global $neardBins;
        
        $phpVersion = empty($phpVersion) ? $neardBins->getPhp()->getVersion() : $phpVersion;
        $versions = $this->getVersions();
        $version = $versions[self::LOCAL_CFG_PHP52]['version'];
        if (version_compare($phpVersion, '5.5', '>=')) {
            $version = $versions[self::LOCAL_CFG_PHP55]['version'];
        } elseif (version_compare($phpVersion, '5.3.7', '>=')) {
            $version = $versions[self::LOCAL_CFG_PHP53]['version'];
        }
        
        return $version;
    }
    
    public function setVersion($version) {
        global $neardConfig;
        $this->version = $version;
        $neardConfig->replace(self::ROOT_CFG_VERSION, $version);
    }
    
    public function getConfs() {
        $result = array();
        foreach ($this->versions as $version => $data) {
            $result[] = $data['conf'];
        }
        return $result;
    }
}
