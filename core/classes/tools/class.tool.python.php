<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Represents a Python tool module in the Bearsampp environment.
 * This class extends the Module class and manages Python-specific configurations and executable paths.
 */
class ToolPython extends Module
{
    /**
     * Configuration key for the Python version.
     */
    const ROOT_CFG_VERSION = 'pythonVersion';

    /**
     * Configuration keys for Python executables.
     */
    const LOCAL_CFG_EXE = 'pythonExe';
    const LOCAL_CFG_CP_EXE = 'pythonCpExe';
    const LOCAL_CFG_IDLE_EXE = 'pythonIdleExe';

    /**
     * @var string Path to the main Python executable.
     */
    private $exe;

    /**
     * @var string Path to the Python command prompt executable.
     */
    private $cpExe;

    /**
     * @var string Path to the Python IDLE executable.
     */
    private $idleExe;

    /**
     * Initializes a new instance of the ToolPython class.
     *
     * @param   string  $id    The module identifier.
     * @param   string  $type  The module type.
     */
    public function __construct($id, $type)
    {
        Util::logInitClass( $this );
        $this->reload( $id, $type );
    }

    /**
     * Reloads the module configuration and updates paths based on the current settings.
     *
     * @param   string|null  $id    Optional. The module identifier to reload.
     * @param   string|null  $type  Optional. The module type to reload.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::PYTHON );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe     = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->cpExe   = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CP_EXE];
            $this->idleExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_IDLE_EXE];
        }

        $this->checkFiles();
    }

    /**
     * Sets the version of the Python module and updates the configuration.
     *
     * @param   string  $version  The new version to set.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    /**
     * Gets the path to the main Python executable.
     *
     * @return string Path to the Python executable.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Gets the path to the Python command prompt executable.
     *
     * @return string Path to the Python command prompt executable.
     */
    public function getCpExe()
    {
        return $this->cpExe;
    }

    /**
     * Gets the path to the Python IDLE executable.
     *
     * @return string Path to the Python IDLE executable.
     */
    public function getIdleExe()
    {
        return $this->idleExe;
    }

    /**
     * Checks the existence of configured files and logs errors if they are not found.
     */
    private function checkFiles()
    {
        if ( !$this->enable ) {
            Util::logInfo( $this->name . ' is not enabled!' );

            return;
        }
        $this->checkFile( $this->currentPath, Lang::ERROR_FILE_NOT_FOUND );
        $this->checkFile( $this->symlinkPath, Lang::ERROR_FILE_NOT_FOUND );
        $this->checkFile( $this->bearsamppConf, Lang::ERROR_CONF_NOT_FOUND );
        $this->checkFile( $this->exe, Lang::ERROR_EXE_NOT_FOUND );
        $this->checkFile( $this->cpExe, Lang::ERROR_EXE_NOT_FOUND );
        $this->checkFile( $this->idleExe, Lang::ERROR_EXE_NOT_FOUND );
    }

    /**
     * Helper method to check if a file exists and log an error if it does not.
     *
     * @param   string  $path           The file path to check.
     * @param   string  $errorConstant  The error constant from Lang class to use in the log message.
     */
    private function checkFile($path, $errorConstant)
    {
        global $bearsamppLang;
        if ( !is_file( $path ) && !is_dir( $path ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( $errorConstant ), $this->name . ' ' . $this->version, $path ) );
        }
    }
}
