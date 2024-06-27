<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolConsoleZ
 *
 * This class represents the ConsoleZ tool in the Bearsampp application.
 * It extends the Module class and provides functionalities specific to ConsoleZ.
 */
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

    /**
     * Constructor for the ToolConsoleZ class.
     *
     * @param string $id The ID of the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration for the ConsoleZ tool.
     *
     * @param string|null $id The ID of the module. If null, the current ID is used.
     * @param string|null $type The type of the module. If null, the current type is used.
     */
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

    /**
     * Sets the version of the ConsoleZ tool.
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
     * Gets the executable path for ConsoleZ.
     *
     * @return string The executable path.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Gets the launch executable path for ConsoleZ.
     *
     * @return string The launch executable path.
     */
    public function getLaunchExe() {
        return $this->launchExe;
    }

    /**
     * Gets the configuration file path for ConsoleZ.
     *
     * @return string The configuration file path.
     */
    public function getConf() {
        return $this->conf;
    }

    /**
     * Gets the number of rows for the ConsoleZ window.
     *
     * @return int The number of rows.
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * Gets the number of columns for the ConsoleZ window.
     *
     * @return int The number of columns.
     */
    public function getCols() {
        return $this->cols;
    }

    /**
     * Gets the shell command to launch ConsoleZ.
     *
     * @param string|null $args Additional arguments for the shell command.
     * @return string The shell command.
     */
    public function getShell($args = null) {
        if (empty($args)) {
            return 'cmd /k &quot;' . Util::formatWindowsPath($this->launchExe) . '&quot;';
        } else {
            return 'cmd /k &quot;&quot;' . Util::formatWindowsPath($this->getLaunchExe()) . '&quot; &amp; ' . Util::formatWindowsPath($args) . '&quot;';
        }
    }

    /**
     * Gets the default tab title for ConsoleZ.
     *
     * @return string The default tab title.
     */
    public function getTabTitleDefault() {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::CONSOLE);
    }

    /**
     * Gets the tab title for PowerShell.
     *
     * @return string The tab title for PowerShell.
     */
    public function getTabTitlePowershell() {
        return 'PowerShell';
    }

    /**
     * Gets the tab title for PEAR.
     *
     * @return string The tab title for PEAR.
     */
    public function getTabTitlePear() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::PEAR) . ' ' . $bearsamppBins->getPhp()->getPearVersion(true);
    }

    /**
     * Gets the tab title for MySQL.
     *
     * @return string The tab title for MySQL.
     */
    public function getTabTitleMysql() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::MYSQL) . ' ' . $bearsamppBins->getMysql()->getVersion();
    }

    /**
     * Gets the tab title for MariaDB.
     *
     * @return string The tab title for MariaDB.
     */
    public function getTabTitleMariadb() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::MARIADB) . ' ' . $bearsamppBins->getMariadb()->getVersion();
    }

    /**
     * Gets the tab title for PostgreSQL.
     *
     * @return string The tab title for PostgreSQL.
     */
    public function getTabTitlePostgresql() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::POSTGRESQL) . ' ' . $bearsamppBins->getPostgresql()->getVersion();
    }

    /**
     * Gets the tab title for Git.
     *
     * @param string|null $repoPath The repository path.
     * @return string The tab title for Git.
     */
    public function getTabTitleGit($repoPath = null) {
        global $bearsamppLang, $bearsamppTools;
        $result = $bearsamppLang->getValue(Lang::GIT) . ' ' . $bearsamppTools->getGit()->getVersion();
        if ($repoPath != null) {
            $result .= ' - ' . basename($repoPath);
        }
        return $result;
    }

    /**
     * Gets the tab title for Node.js.
     *
     * @return string The tab title for Node.js.
     */
    public function getTabTitleNodejs() {
        global $bearsamppLang, $bearsamppBins;
        return $bearsamppLang->getValue(Lang::NODEJS) . ' ' . $bearsamppBins->getNodejs()->getVersion();
    }

    /**
     * Gets the tab title for Composer.
     *
     * @return string The tab title for Composer.
     */
    public function getTabTitleComposer() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::COMPOSER) . ' ' . $bearsamppTools->getComposer()->getVersion();
    }

    /**
     * Gets the tab title for Python.
     *
     * @return string The tab title for Python.
     */
    public function getTabTitlePython() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::PYTHON) . ' ' . $bearsamppTools->getPython()->getVersion();
    }

    /**
     * Gets the tab title for Ruby.
     *
     * @return string The tab title for Ruby.
     */
    public function getTabTitleRuby() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::RUBY) . ' ' . $bearsamppTools->getRuby()->getVersion();
    }

    /**
     * Gets the tab title for Yarn.
     *
     * @return string The tab title for Yarn.
     */
    public function getTabTitleYarn() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::YARN) . ' ' . $bearsamppTools->getYarn()->getVersion();
    }

    /**
     * Gets the tab title for Perl.
     *
     * @return string The tab title for Perl.
     */
    public function getTabTitlePerl() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::PERL) . ' ' . $bearsamppTools->getPerl()->getVersion();
    }

    /**
     * Gets the tab title for Ghostscript.
     *
     * @return string The tab title for Ghostscript.
     */
    public function getTabTitleGhostscript() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::GHOSTSCRIPT) . ' ' . $bearsamppTools->getGhostscript()->getVersion();
    }

    /**
     * Gets the tab title for Ngrok.
     *
     * @return string The tab title for Ngrok.
     */
    public function getTabTitleNgrok() {
        global $bearsamppLang, $bearsamppTools;
        return $bearsamppLang->getValue(Lang::NGROK) . ' ' . $bearsamppTools->getNgrok()->getVersion();
    }
}
