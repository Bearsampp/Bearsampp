<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Represents the Ghostscript tool module, extending the functionality of the Module class.
 * This class manages the Ghostscript configurations and executable paths.
 */
class ToolGhostscript extends Module
{
    /**
     * Configuration key for the Ghostscript version in the root configuration.
     */
    const ROOT_CFG_VERSION = 'ghostscriptVersion';

    /**
     * Configuration key for the Ghostscript executable in the local configuration.
     */
    const LOCAL_CFG_EXE = 'ghostscriptExe';

    /**
     * Configuration key for the Ghostscript console executable in the local configuration.
     */
    const LOCAL_CFG_EXE_CONSOLE = 'ghostscriptExeConsole';

    /**
     * Path to the Ghostscript executable.
     * @var string
     */
    private $exe;

    /**
     * Path to the Ghostscript console executable.
     * @var string
     */
    private $exeConsole;

    /**
     * Constructor for the ToolGhostscript class.
     * Initializes logging and reloads the module configuration.
     *
     * @param string $id The module identifier.
     * @param string $type The module type.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration for the Ghostscript module, setting up paths and checking file existence.
     *
     * @param string|null $id Optional module identifier, defaults to current ID if not provided.
     * @param string|null $type Optional module type, defaults to current type if not provided.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::GHOSTSCRIPT);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->exeConsole = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE_CONSOLE];
        }

        $this->checkFileExistence();
    }

    /**
     * Sets the version of the Ghostscript module and updates the configuration.
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
     * Gets the path to the Ghostscript executable.
     *
     * @return string Path to the executable.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Gets the path to the Ghostscript console executable.
     *
     * @return string Path to the console executable.
     */
    public function getExeConsole() {
        return $this->exeConsole;
    }

    /**
     * Checks the existence of critical files and logs errors if they are not found.
     */
    private function checkFileExistence() {
        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        $this->checkPath($this->currentPath, Lang::ERROR_FILE_NOT_FOUND);
        $this->checkPath($this->symlinkPath, Lang::ERROR_FILE_NOT_FOUND);
        $this->checkPath($this->bearsamppConf, Lang::ERROR_CONF_NOT_FOUND);
        $this->checkPath($this->exe, Lang::ERROR_EXE_NOT_FOUND);
        $this->checkPath($this->exeConsole, Lang::ERROR_EXE_NOT_FOUND);
    }

    /**
     * Helper method to check if a path exists and log an error if it does not.
     *
     * @param string $path The path to check.
     * @param string $errorConstant The error constant from Lang class to use in the error message.
     */
    private function checkPath($path, $errorConstant) {
        global $bearsamppLang;
        if (!is_file( $path) && !is_dir( $path)) {
            Util::logError(sprintf($bearsamppLang->getValue($errorConstant), $this->name . ' ' . $this->version, $path));
        }
    }
}
