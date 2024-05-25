<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolComposer extends the functionality of the Module class specifically for managing Composer tool.
 */
class ToolComposer extends Module
{
    /**
     * @var string Configuration key for the root Composer version.
     */
    const ROOT_CFG_VERSION = 'composerVersion';

    /**
     * @var string Configuration key for the local Composer executable path.
     */
    const LOCAL_CFG_EXE = 'composerExe';

    /**
     * @var string Path to the Composer executable.
     */
    private $exe;

    /**
     * Constructor for the ToolComposer class.
     * Initializes the class and reloads configuration based on provided ID and type.
     *
     * @param string $id The unique identifier for the Composer tool.
     * @param string $type The type of module, should be specific to Composer in this context.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the configuration and updates the properties of the class based on the current state and configuration.
     * It also checks if the necessary directories and files exist and logs appropriate messages.
     *
     * @param string|null $id Optional. The unique identifier for the Composer tool. If not provided, uses existing ID.
     * @param string|null $type Optional. The type of module. If not provided, uses existing type.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::COMPOSER);
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
     * Sets a new version for the Composer tool and updates the configuration accordingly.
     *
     * @param string $version The new version to set for Composer.
     */
    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    /**
     * Gets the path to the Composer executable.
     *
     * @return string Path to the Composer executable.
     */
    public function getExe() {
        return $this->exe;
    }
}
