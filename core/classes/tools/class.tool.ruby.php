<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolRuby extends the functionality of Module to manage Ruby installations.
 * It handles the configuration and management of Ruby executable paths.
 */
class ToolRuby extends Module
{
    /**
     * Configuration key for the Ruby version.
     */
    const ROOT_CFG_VERSION = 'rubyVersion';

    /**
     * Configuration key for the Ruby executable path.
     */
    const LOCAL_CFG_EXE = 'rubyExe';

    /**
     * Configuration key for the Ruby console executable path.
     */
    const LOCAL_CFG_CONSOLE_EXE = 'rubyConsoleExe';

    /**
     * Path to the Ruby executable.
     * @var string
     */
    private $exe;

    /**
     * Path to the Ruby console executable.
     * @var string
     */
    private $consoleExe;

    /**
     * Constructor for the ToolRuby class.
     * Initializes the class and reloads configuration based on provided ID and type.
     *
     * @param string $id The identifier for the Ruby tool instance.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration for the Ruby tool.
     * It updates the executable paths based on the current configuration.
     *
     * @param string|null $id Optional. The identifier for the Ruby tool instance.
     * @param string|null $type Optional. The type of the module.
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

        $this->checkPaths();
    }

    /**
     * Sets the version of Ruby and updates the configuration.
     *
     * @param string $version The new version to set.
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
     * @return string Path to the Ruby executable.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Gets the path to the Ruby console executable.
     *
     * @return string Path to the Ruby console executable.
     */
    public function getConsoleExe() {
        return $this->consoleExe;
    }

    /**
     * Checks and logs errors for the necessary paths.
     */
    private function checkPaths() {
        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        $this->checkPath($this->currentPath, Lang::ERROR_FILE_NOT_FOUND);
        $this->checkPath($this->symlinkPath, Lang::ERROR_FILE_NOT_FOUND);
        $this->checkPath($this->bearsamppConf, Lang::ERROR_CONF_NOT_FOUND);
        $this->checkPath($this->exe, Lang::ERROR_EXE_NOT_FOUND);
        $this->checkPath($this->consoleExe, Lang::ERROR_EXE_NOT_FOUND);
    }

    /**
     * Helper method to check if a path exists and log an error if it does not.
     *
     * @param string $path The path to check.
     * @param string $errorConstant The error constant from Lang class to use for logging.
     */
    private function checkPath($path, $errorConstant) {
        global $bearsamppLang;
        if (!is_file( $path) && !is_dir( $path)) {
            Util::logError(sprintf($bearsamppLang->getValue($errorConstant), $this->name . ' ' . $this->version, $path));
        }
    }
}
