<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Represents a Yarn tool module in the Bearsampp environment.
 * This class extends the Module class and manages the specific functionalities for the Yarn tool.
 */
class ToolYarn extends Module
{
    /**
     * Configuration key for the root version of Yarn.
     */
    const ROOT_CFG_VERSION = 'yarnVersion';

    /**
     * Configuration key for the local executable of Yarn.
     */
    const LOCAL_CFG_EXE = 'yarnExe';

    /**
     * Path to the Yarn executable.
     *
     * @var string
     */
    private $exe;

    /**
     * Constructs a new instance of the ToolYarn class.
     * Initializes the module by loading its configuration and setting up the environment.
     *
     * @param   string  $id    The identifier of the module.
     * @param   string  $type  The type of the module.
     */
    public function __construct($id, $type)
    {
        Util::logInitClass( $this );
        $this->reload( $id, $type );
    }

    /**
     * Reloads the configuration and updates the paths and executable information for the Yarn tool.
     * This method overrides the parent class's reload method to add specific functionalities.
     *
     * @param   string|null  $id    Optional. The new identifier of the module. If not provided, the current ID is used.
     * @param   string|null  $type  Optional. The new type of the module. If not provided, the current type is used.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::YARN );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
        }

        $this->checkModuleStatus();
    }

    /**
     * Sets the version of the Yarn tool and updates the configuration.
     * This method also triggers a reload to refresh the module state.
     *
     * @param   string  $version  The new version to set for the Yarn tool.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    /**
     * Gets the path to the Yarn executable.
     *
     * @return string The path to the Yarn executable.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Checks the module status and logs appropriate messages based on the current state.
     * This method helps in identifying issues with the module setup.
     */
    private function checkModuleStatus()
    {
        if ( !$this->enable ) {
            Util::logInfo( $this->name . ' is not enabled!' );

            return;
        }
        $this->validatePaths();
    }

    /**
     * Validates the existence of necessary directories and files for the module.
     * Logs errors if any required component is missing.
     */
    private function validatePaths()
    {
        global $bearsamppLang;
        if ( !is_dir( $this->currentPath ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_FILE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->currentPath ) );
        }
        if ( !is_dir( $this->symlinkPath ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_FILE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->symlinkPath ) );

            return;
        }
        if ( !is_file( $this->bearsamppConf ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->bearsamppConf ) );
        }
        if ( !is_file( $this->exe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->exe ) );
        }
    }
}
