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
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::PHPMYADMIN);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $versions = array();
        if ($this->bearsamppConfRaw !== false) {
            $versions[self::LOCAL_CFG_PHP52] = $this->bearsamppConfRaw[self::LOCAL_CFG_PHP52];
            $versions[self::LOCAL_CFG_PHP53] = $this->bearsamppConfRaw[self::LOCAL_CFG_PHP53];
            $versions[self::LOCAL_CFG_PHP55] = $this->bearsamppConfRaw[self::LOCAL_CFG_PHP55];
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

        foreach ($versions as $key => $versionSub) {
            $bearsamppConfSub = $this->symlinkPath . '/' . $versionSub . '/bearsampp.conf';
            if (!is_file($bearsamppConfSub)) {
                Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version . ' / ' . $versionSub, $bearsamppConfSub));
            }
            $bearsamppConfRawSub = parse_ini_file($bearsamppConfSub);
            if ($bearsamppConfRawSub !== false) {
                $confSub = $this->symlinkPath . '/' . $versionSub . '/' . $bearsamppConfRawSub[self::LOCAL_CFG_CONF];
                if (!is_file($confSub)) {
                    Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version . ' / ' . $confSub));
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
        global $bearsamppBs, $bearsamppBins;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config...');

        $alias = $bearsamppBs->getAliasPath() . '/phpmyadmin.conf';
        if (is_file($alias)) {
            $version = $this->getVersionCompatPhp();
            Util::replaceInFile($alias, array(
                '/^Alias\s\/phpmyadmin\s.*/' => 'Alias /phpmyadmin "' . $this->getSymlinkPath() . '/' . $version . '/"',
                '/^<Directory\s.*/' => '<Directory "' . $this->getSymlinkPath() . '/' . $version . '/">',
            ));
        } else {
            Util::logError($this->getName() . ' alias not found : ' . $alias);
        }

        foreach ($this->getConfs() as $pmaConf) {
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
        }

        return true;
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
        global $bearsamppBins;

        $phpVersion = empty($phpVersion) ? $bearsamppBins->getPhp()->getVersion() : $phpVersion;
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
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    public function getConfs() {
        $result = array();
        foreach ($this->versions as $version => $data) {
            $result[] = $data['conf'];
        }
        return $result;
    }
}
