<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolRuby
 *
 * This class represents a Ruby tool module in the Bearsampp application.
 * It extends the Module class and provides functionalities specific to managing
 * Ruby tools, including loading configurations, setting versions, and retrieving
 * executable paths.
 */
class ToolRuby extends Module
{
    /**
     * Configuration key for the Ruby version in the root configuration.
     */
    const ROOT_CFG_VERSION = 'rubyVersion';

    /**
     * Configuration key for the Ruby executable in the local configuration.
     */
    const LOCAL_CFG_EXE = 'rubyExe';

    /**
     * Configuration key for the Ruby console executable in the local configuration.
     */
    const LOCAL_CFG_CONSOLE_EXE = 'rubyConsoleExe';

    /**
     * @var string Path to the Ruby executable.
     */
    private $exe;

    /**
     * @var string Path to the Ruby console executable.
     */
    private $consoleExe;

    /**
     * Constructor for the ToolRuby class.
     *
     * @param string $id The ID of the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the Ruby tool configuration based on the provided ID and type.
     *
     * @param string|null $id The ID of the module. If null, the current ID is used.
     * @param string|null $type The type of the module. If null, the current type is used.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::RUBY);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->consoleExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONSOLE_EXE];
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
        if (!is_file($this->consoleExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->consoleExe));
        }
    }

    /**
     * Sets the version of the Ruby tool and reloads the configuration.
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
     * Gets the path to the Ruby executable.
     *
     * @return string The path to the Ruby executable.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Gets the path to the Ruby console executable.
     *
     * @return string The path to the Ruby console executable.
     */
    public function getConsoleExe() {
        return $this->consoleExe;
    }
}
