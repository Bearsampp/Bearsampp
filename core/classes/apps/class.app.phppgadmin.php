<?php

class AppPhppgadmin extends Module
{
    const ROOT_CFG_VERSION = 'phppgadminVersion';

    const LOCAL_CFG_CONF = 'phppgadminConf';

    private $conf;

    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

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

    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    public function getConf() {
        return $this->conf;
    }
}
