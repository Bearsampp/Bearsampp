<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolPerl
 * Manages the Perl tool configuration and operations within the Bearsampp environment.
 * Extends the Module class to utilize common module functionalities.
 */
class ToolPerl extends Module
{
    /**
     * Configuration key for the Perl version in the root configuration.
     */
    const ROOT_CFG_VERSION = 'perlVersion';

    /**
     * Configuration key for the Perl executable in the local configuration.
     */
    const LOCAL_CFG_EXE = 'perlExe';

    /**
     * Path to the Perl executable.
     * @var string
     */
    private $exe;

    /**
     * Constructor for the ToolPerl class.
     * Initializes the class and reloads the configuration based on the provided module ID and type.
     *
     * @param string $id The module ID.
     * @param string $type The module type.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration for the Perl tool.
     * Updates the module's properties and checks the existence of necessary files and directories.
     *
     * @param string|null $id Optional. The module ID to reload. If null, uses the current ID.
     * @param string|null $type Optional. The module type to reload. If null, uses the current type.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::PERL);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
        }

        $this->checkModuleStatus();
    }

    /**
     * Sets the version of the Perl tool and updates the configuration.
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
     * Gets the path to the Perl executable.
     *
     * @return string Path to the Perl executable.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Checks the status of the module and logs appropriate messages based on the existence of files and directories.
     */
    private function checkModuleStatus() {
        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        $this->checkPath($this->currentPath, Lang::ERROR_FILE_NOT_FOUND);
        $this->checkPath($this->symlinkPath, Lang::ERROR_FILE_NOT_FOUND);
        $this->checkPath($this->bearsamppConf, Lang::ERROR_CONF_NOT_FOUND);
        $this->checkPath($this->exe, Lang::ERROR_EXE_NOT_FOUND);
    }

    /**
     * Helper method to check if a path exists and log an error if it does not.
     *
     * @param string $path The path to check.
     * @param string $errorConstant The error constant from Lang class to use in the error message.
     */
    private function checkPath($path, $errorConstant) {
        global $bearsamppLang;
        if (!file_exists( $path)) {
            Util::logError(sprintf($bearsamppLang->getValue($errorConstant), $this->name . ' ' . $this->version, $path));
        }
    }
}
