<?php
/*
 *
 *  * Copyright (c) 2021-2024 Bearsampp
 *  * License:  GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class ToolBruno
 *
 * This class represents the Bruno tool module in the Bearsampp application.
 * It extends the abstract Module class and provides specific functionalities
 * for managing the Bruno IDE tool, including setting versions,
 * and retrieving the executable path.
 */
class ToolBruno extends Module
{
    /**
     * Configuration key for the Bruno version in the root configuration.
     */
    const ROOT_CFG_VERSION = 'brunoVersion';

    /**
     * Configuration key for the Bruno executable in the local configuration.
     */
    const LOCAL_CFG_EXE = 'brunoExe';

    /**
     * Path to the Bruno executable.
     *
     * @var string
     */
    private $exe;

    /**
     * Constructor for the ToolBruno class.
     *
     * Initializes the ToolBruno instance by logging the initialization and reloading
     * the module configuration with the provided ID and type.
     *
     * @param string $id The ID of the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the module configuration based on the provided ID and type.
     *
     * This method overrides the parent reload method to include additional
     * configurations specific to the Bruno tool. It sets the name, version, and
     * executable path, and logs errors if the module is not properly configured.
     *
     * @param string|null $id The ID of the module. If null, the current ID is used.
     * @param string|null $type The type of the module. If null, the current type is used.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::BRUNO);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
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
    }

    /**
     * Sets the version of the Bruno IDE tool.
     *
     * This method updates the version in the configuration and reloads the module
     * to apply the new version.
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
     * Gets the path to the Bruno executable.
     *
     * @return string The path to the Bruno executable.
     */
    public function getExe() {
        return $this->exe;
    }
}
