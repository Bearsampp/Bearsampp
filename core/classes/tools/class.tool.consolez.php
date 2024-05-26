<?php

class ToolConsoleZ extends Module
{
    const ROOT_CFG_VERSION = 'consolezVersion';

    const LOCAL_CFG_EXE = 'consolezExe';
    const LOCAL_CFG_CONF = 'consolezConf';
    const LOCAL_CFG_LAUNCH_EXE = 'consolezLaunchExe';
    const LOCAL_CFG_ROWS = 'consolezRows';
    const LOCAL_CFG_COLS = 'consolezCols';

    private $exe;
    private $launchExe;
    private $conf;
    private $rows;
    private $cols;

    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::CONSOLEZ);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->launchExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_LAUNCH_EXE];
            $this->conf = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->rows = intval($this->bearsamppConfRaw[self::LOCAL_CFG_ROWS]);
            $this->cols = intval($this->bearsamppConfRaw[self::LOCAL_CFG_COLS]);
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
        if (!is_file($this->exe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->exe));
        }
        if (!is_file($this->launchExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->launchExe));
        }
        if (!is_file($this->conf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->conf));
        }
        if (!is_numeric($this->rows) || $this->rows <= 0) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_ROWS, $this->rows));
        }
        if (!is_numeric($this->cols) || $this->cols <= 0) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_COLS, $this->cols));
        }
    }

    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    public function getExe() {
        return $this->exe;
    }

    public function getLaunchExe() {
        return $this->launchExe;
    }

    public function getConf() {
        return $this->conf;
    }

    public function getRows() {
        return $this->rows;
    }

    public function getCols() {
        return $this->cols;
    }

    public function getShell($args = null) {
        if (empty($args)) {
            return 'cmd /k &quot;' . Util::formatWindowsPath($this->launchExe) . '&quot;';
        } else {
            return 'cmd /k &quot;&quot;' . Util::formatWindowsPath($this->getLaunchExe()) . '&quot; &amp; ' . Util::formatWindowsPath($args) . '&quot;';
        }
    }

    public function getTabTitleDefault() {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::CONSOLE);
    }

    public function getTabTitlePowershell() {
        return 'PowerShell';
    }

    public function getTabTitlePear() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::PEAR) . ' ' . $bearsamppBins->getPhp()->getPearVersion(true);
    }

    public function getTabTitleMysql() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::MYSQL) . ' ' . $bearsamppBins->getMysql()->getVersion();
    }

    public function getTabTitleMariadb() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::MARIADB) . ' ' . $bearsamppBins->getMariadb()->getVersion();
    }

    public function getTabTitlePostgresql() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::POSTGRESQL) . ' ' . $bearsamppBins->getPostgresql()->getVersion();
    }

    public function getTabTitleGit($repoPath = null) {
        global $bearsamppLang, $bearsamppTools;
        $result = $bearsamppLang->getValue(Lang::GIT) . ' ' . $bearsamppTools->getGit()->getVersion();
        if ($repoPath != null) {
            $result .= ' - ' . basename($repoPath);
        }
        return $result;
    }

    public function getTabTitleNodejs() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::NODEJS) . ' ' . $bearsamppBins->getNodejs()->getVersion();
    }

    public function getTabTitleComposer() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::COMPOSER) . ' ' . $bearsamppTools->getComposer()->getVersion();
    }

    public function getTabTitlePython() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::PYTHON) . ' ' . $bearsamppTools->getPython()->getVersion();
    }

    public function getTabTitleRuby() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::RUBY) . ' ' . $bearsamppTools->getRuby()->getVersion();
    }

    public function getTabTitleYarn() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::YARN) . ' ' . $bearsamppTools->getYarn()->getVersion();
    }

    public function getTabTitlePerl() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::PERL) . ' ' . $bearsamppTools->getPerl()->getVersion();
    }

    public function getTabTitleGhostscript() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::GHOSTSCRIPT) . ' ' . $bearsamppTools->getGhostscript()->getVersion();
    }

    public function getTabTitleNgrok() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::NGROK) . ' ' . $bearsamppTools->getNgrok()->getVersion();
    }
}
