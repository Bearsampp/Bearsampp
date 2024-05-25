<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolNgrok
 * Extends the Module class to manage the Ngrok tool configuration and operations.
 */
class ToolNgrok extends Module
{
    /**
     * @var string Configuration key for the Ngrok version.
     */
    const ROOT_CFG_VERSION = 'ngrokVersion';

    /**
     * @var string Configuration key for the Ngrok executable path.
     */
    const LOCAL_CFG_EXE = 'ngrokExe';

    /**
     * @var string Path to the Ngrok executable.
     */
    private $exe;

    /**
     * Constructor for the ToolNgrok class.
     * Initializes the class and reloads the configuration based on the provided ID and type.
     *
     * @param   string  $id    The identifier for the Ngrok module.
     * @param   string  $type  The type of the module.
     */
    public function __construct($id, $type)
    {
        Util::logInitClass( $this );
        $this->reload( $id, $type );
    }

    /**
     * Reloads the configuration for the Ngrok tool.
     * Sets the name, version, and executable path based on the configuration.
     * Logs various states and errors during the reload process.
     *
     * @param   string|null  $id    Optional. The identifier for the Ngrok module. Uses current ID if null.
     * @param   string|null  $type  Optional. The type of the module. Uses current type if null.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::NGROK );
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
     * Sets a new version for the Ngrok tool and updates the configuration.
     * Reloads the configuration after updating.
     *
     * @param   string  $version  The new version to set for Ngrok.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    /**
     * Gets the path to the Ngrok executable.
     *
     * @return string Path to the Ngrok executable.
     */
    public function getExe()
    {
        return $this->exe;
    }
}
