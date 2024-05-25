<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolXdc represents a specific module type in the Bearsampp environment, handling configurations and operations specific to the XDC tool.
 */
class ToolXdc extends Module
{
    /**
     * Configuration key for the root version of XDC.
     */
    const ROOT_CFG_VERSION = 'xdcVersion';

    /**
     * Configuration key for the executable path of XDC.
     */
    const LOCAL_CFG_EXE = 'xdcExe';

    /**
     * Path to the executable file of XDC.
     *
     * @var string
     */
    private $exe;

    /**
     * Constructs a new instance of ToolXdc, initializing logging and reloading configurations.
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
     * Reloads the configuration and updates paths based on the module's current state.
     * This method performs several checks and updates:
     * - It logs the reloading action.
     * - Updates the module's name and version from global configuration.
     * - Calls the parent class's reload method to handle common reload operations.
     * - Updates the executable path if the configuration is valid.
     * - Logs an informational message if the module is not enabled.
     * - Checks for the existence of the current path directory and logs an error if not found.
     * - Checks for the existence of the symlink path directory and logs an error if not found.
     * - Checks for the existence of the configuration file and logs an error if not found.
     * - Checks for the existence of the executable file and logs an error if not found.
     *
     * @param   string|null  $id              Optional. The new identifier of the module. If not provided, the current ID is used.
     * @param   string|null  $type            Optional. The new type of the module. If not provided, the current type is used.
     *
     * @global object        $bearsamppConfig The global configuration object.
     * @global object        $bearsamppLang   The global language object for retrieving language-specific values.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::XDC );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
        }

        if ( !$this->enable ) {
            Util::logInfo( $this->name . ' is not enabled!' );

            return;
        }
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

    /**
     * Sets the version of the XDC tool and updates the configuration accordingly.
     *
     * @param   string  $version  The new version to set for the module.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    /**
     * Gets the path to the executable file of XDC.
     *
     * @return string Path to the executable file.
     */
    public function getExe()
    {
        return $this->exe;
    }

}
