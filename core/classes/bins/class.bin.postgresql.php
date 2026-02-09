<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class BinPostgresql
 *
 * This class represents the PostgreSQL module in the Bearsampp application.
 * It handles the configuration, management, and operations related to PostgreSQL.
 */
class BinPostgresql extends Module
{
    const SERVICE_NAME = 'bearsampppostgresql';

    const ROOT_CFG_ENABLE = 'postgresqlEnable';
    const ROOT_CFG_VERSION = 'postgresqlVersion';

    const LOCAL_CFG_CTL_EXE = 'postgresqlCtlExe';
    const LOCAL_CFG_CLI_EXE = 'postgresqlCliExe';
    const LOCAL_CFG_DUMP_EXE = 'postgresqlDumpExe';
    const LOCAL_CFG_DUMP_ALL_EXE = 'postgresqlDumpAllExe';
    const LOCAL_CFG_CONF = 'postgresqlConf';
    const LOCAL_CFG_HBA_CONF = 'postgresqlUserConf';
    const LOCAL_CFG_ALT_CONF = 'postgresqlAltConf';
    const LOCAL_CFG_ALT_HBA_CONF = 'postgresqlAltUserConf';
    const LOCAL_CFG_PORT = 'postgresqlPort';
    const LOCAL_CFG_ROOT_USER = 'postgresqlRootUser';
    const LOCAL_CFG_ROOT_PWD = 'postgresqlRootPwd';

    const CMD_VERSION = '--version';

    private $service;
    private $errorLog;
    private $ctlExe;
    private $cliExe;
    private $dumpExe;
    private $dumpAllExe;
    private $conf;
    private $hbaConf;
    private $altConf;
    private $altHbaConf;
    private $port;
    private $rootUser;
    private $rootPwd;

    /**
     * Constructor for the BinPostgresql class.
     *
     * @param   string  $id    The ID of the module.
     * @param   string  $type  The type of the module.
     */
    public function __construct($id, $type)
    {
        Util::logInitClass( $this );
        $this->reload( $id, $type );
    }

    /**
     * Reloads the module configuration based on the provided ID and type.
     *
     * @param   string|null  $id    The ID of the module. If null, the current ID is used.
     * @param   string|null  $type  The type of the module. If null, the current type is used.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::POSTGRESQL );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        $this->enable   = $this->enable && $bearsamppConfig->getRaw( self::ROOT_CFG_ENABLE );
        $this->service  = new Win32Service( self::SERVICE_NAME );
        $this->errorLog = $bearsamppRoot->getLogsPath() . '/postgresql.log';

        if ( $this->bearsamppConfRaw !== false ) {
            $this->ctlExe     = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CTL_EXE];
            $this->cliExe     = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CLI_EXE];
            $this->dumpExe    = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_DUMP_EXE];
            $this->dumpAllExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_DUMP_ALL_EXE];
            $this->conf       = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->hbaConf    = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_HBA_CONF];
            $this->altConf    = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ALT_CONF];
            $this->altHbaConf = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ALT_HBA_CONF];
            $this->port       = $this->bearsamppConfRaw[self::LOCAL_CFG_PORT];
            $this->rootUser   = isset( $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_USER] ) ? $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_USER] : 'postgres';
            $this->rootPwd    = isset( $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_PWD] ) ? $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_PWD] : '';
        }

        if ( !$this->enable ) {
            Util::logInfo( $this->name . ' is not enabled!' );

            return;
        }
        if ( !is_dir( $this->currentPath ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_FILE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->currentPath ) );

            return;
        }
        if ( !is_dir( $this->symlinkPath ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_FILE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->symlinkPath ) );

            return;
        }
        if ( !is_file( $this->bearsamppConf ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->bearsamppConf ) );

            return;
        }
        if ( !file_exists( $this->conf ) ) {
            $this->conf = $this->altConf;
        }
        if ( !file_exists( $this->hbaConf ) ) {
            $this->hbaConf = $this->altHbaConf;
        }

        if ( !is_file( $this->ctlExe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->ctlExe ) );

            return;
        }
        if ( !is_file( $this->cliExe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->cliExe ) );

            return;
        }
        if ( !is_file( $this->dumpExe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->dumpExe ) );

            return;
        }
        if ( !is_file( $this->dumpAllExe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->dumpAllExe ) );

            return;
        }
        if ( !is_file( $this->conf ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->conf ) );

            return;
        }
        if ( !is_file( $this->hbaConf ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->hbaConf ) );

            return;
        }
        if ( !is_numeric( $this->port ) || $this->port <= 0 ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_PORT, $this->port ) );

            return;
        }
        if ( empty( $this->rootUser ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_ROOT_USER, $this->rootUser ) );

            return;
        }

        $this->service->setDisplayName( APP_TITLE . ' ' . $this->getName() );
        $this->service->setBinPath( $this->ctlExe );
        $this->service->setStartType( Win32Service::SERVICE_DEMAND_START );
        $this->service->setErrorControl( Win32Service::SERVER_ERROR_NORMAL );
    }

    /**
     * Replaces multiple key-value pairs in the configuration file.
     *
     * @param   array  $params  An associative array of key-value pairs to replace.
     */
    protected function replaceAll($params)
    {
        $content = file_get_contents( $this->bearsamppConf );

        foreach ( $params as $key => $value ) {
            $content                      = preg_replace( '|' . $key . ' = .*|', $key . ' = ' . '"' . $value . '"', $content );
            $this->bearsamppConfRaw[$key] = $value;
            switch ( $key ) {
                case self::LOCAL_CFG_PORT:
                    $this->port = $value;
                    break;
                case self::LOCAL_CFG_ROOT_USER:
                    $this->rootUser = $value;
                    break;
                case self::LOCAL_CFG_ROOT_PWD:
                    $this->rootPwd = $value;
                    break;
            }
        }

        file_put_contents( $this->bearsamppConf, $content );
    }

    /**
     * Changes the port used by PostgreSQL.
     *
     * @param   int    $port           The new port number.
     * @param   bool   $checkUsed      Whether to check if the port is already in use.
     * @param   mixed  $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|string True if the port was changed successfully, or the process using the port if it is in use.
     */
    public function changePort($port, $checkUsed = false, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;

        if ( !Util::isValidPort( $port ) ) {
            Util::logError( $this->getName() . ' port not valid: ' . $port );

            return false;
        }

        $port = intval( $port );
        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );

        $isPortInUse = Util::isPortInUse( $port );
        if ( !$checkUsed || $isPortInUse === false ) {
            // bearsampp.conf
            $this->setPort( $port );
            $bearsamppWinbinder->incrProgressBar( $wbProgressBar );

            // conf
            $this->update();
            $bearsamppWinbinder->incrProgressBar( $wbProgressBar );

            return true;
        }

        Util::logDebug( $this->getName() . ' port in used: ' . $port . ' - ' . $isPortInUse );

        return $isPortInUse;
    }

    /**
     * Checks if the specified port is being used by PostgreSQL
     * @param int $port The port number to check
     * @param bool $showWindow Whether to show a message box with the result
     * @return bool True if port is used by PostgreSQL, false otherwise
     */
    public function checkPort($port, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::CHECK_PORT_TITLE), $this->getName(), $port);

        if (!Util::isValidPort($port)) {
            Util::logError($this->getName() . ' port not valid: ' . $port);
            return false;
        }

        $dbLink = @pg_connect("host=127.0.0.1 port=$port user={$this->rootUser} password={$this->rootPwd}");
        if (!$dbLink) {
            Util::logDebug($this->getName() . ' connection failed: ' . pg_last_error());
            return $this->handleNonPostgresUsage($port, $showWindow, $boxTitle);
        }

        // Verify active PostgreSQL connection
        $connectionStatus = pg_connection_status($dbLink);
        $isPostgres = $connectionStatus === PGSQL_CONNECTION_OK;
        pg_close($dbLink);

        if ($isPostgres) {
            Util::logDebug($this->getName() . " port $port is used by PostgreSQL");
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxInfo(
                    sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY), $port, 'PostgreSQL'),
                    $boxTitle
                );
            }
            return true;
        }

        return $this->handleNonPostgresUsage($port, $showWindow, $boxTitle);
    }

    /**
     * Handles non-PostgreSQL port usage scenarios
     */
    private function handleNonPostgresUsage($port, $showWindow, $boxTitle)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        if (Util::isPortInUse($port)) {
            Util::logDebug($this->getName() . " port $port used by non-PostgreSQL service");
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY_ANOTHER_DBMS), $port),
                    $boxTitle
                );
            }
            return false;
        }

        Util::logDebug($this->getName() . " port $port not in use");
        if ($showWindow) {
            $bearsamppWinbinder->messageBoxError(
                sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED), $port),
                $boxTitle
            );
        }
        return false;
    }
    /**
     * Changes the root password for PostgreSQL.
     *
     * @param   string  $currentPwd     The current root password.
     * @param   string  $newPwd         The new root password.
     * @param   mixed   $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|string True if the password was changed successfully, or an error message if it failed.
     */
    public function changeRootPassword($currentPwd, $newPwd, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;
        $error = null;

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        $dbLink = pg_connect( 'host=127.0.0.1 port=' . $this->port . ' user=' . $this->rootUser . ' password=' . $currentPwd );

        if ( !$dbLink ) {
            $error = pg_last_error( $dbLink );
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        $pgr = pg_query_params( $dbLink, 'SELECT quote_ident($1)', array(pg_escape_string( $this->rootUser )) );
        list( $quoted_user ) = pg_fetch_array( $pgr );
        $password = pg_escape_string( $newPwd );
        $result   = pg_query( $dbLink, "ALTER USER $quoted_user WITH PASSWORD '$password'" );
        if ( empty( $error ) && !$result ) {
            $error = pg_last_error( $dbLink );
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( $dbLink ) {
            pg_close( $dbLink );
        }

        if ( !empty( $error ) ) {
            return $error;
        }

        // bearsampp.conf
        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        $this->setRootPwd( $newPwd );

        // conf
        $this->update();
        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );

        return true;
    }

    /**
     * Checks if the provided root password is correct.
     *
     * @param   string|null  $currentPwd     The current root password. If null, the stored root password is used.
     * @param   mixed        $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|string True if the password is correct, or an error message if it is incorrect.
     */
    public function checkRootPassword($currentPwd = null, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;
        $currentPwd = $currentPwd == null ? $this->rootPwd : $currentPwd;
        $error      = null;

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        $dbLink = pg_connect( 'host=127.0.0.1 port=' . $this->port . ' user=' . $this->rootUser . ' password=' . $currentPwd );
        if ( !$dbLink ) {
            $error = pg_last_error( $dbLink );
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( $dbLink ) {
            pg_close( $dbLink );
        }

        if ( !empty( $error ) ) {
            return $error;
        }

        return true;
    }

    /**
     * Switches the PostgreSQL version.
     *
     * @param   string  $version     The version to switch to.
     * @param   bool    $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the version was switched successfully, false otherwise.
     */
    public function switchVersion($version, $showWindow = false)
    {
        Util::logDebug( 'Switch ' . $this->name . ' version to ' . $version );

        return $this->updateConfig( $version, 0, $showWindow );
    }

    /**
     * Updates the PostgreSQL configuration.
     *
     * @param   string|null  $version     The version to update to. If null, the current version is used.
     * @param   int          $sub         The sub-level for logging indentation.
     * @param   bool         $showWindow  Whether to show a window during the update process.
     *
     * @return bool True if the configuration was updated successfully, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppApps, $bearsamppWinbinder;

        if ( !$this->enable ) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug( ($sub > 0 ? str_repeat( ' ', 2 * $sub ) : '') . 'Update ' . $this->name . ' ' . $version . ' config' );

        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::SWITCH_VERSION_TITLE ), $this->getName(), $version );

        $currentPath   = str_replace( 'postgresql' . $this->getVersion(), 'postgresql' . $version, $this->getCurrentPath() );
        $conf          = str_replace( 'postgresql' . $this->getVersion(), 'postgresql' . $version, $this->getConf() );
        $bearsamppConf = str_replace( 'postgresql' . $this->getVersion(), 'postgresql' . $version, $this->bearsamppConf );

        if ( $this->version != $version ) {
            $this->initData( $currentPath );
        }

        if ( !file_exists( $conf ) || !file_exists( $bearsamppConf ) ) {
            Util::logError( 'bearsampp config files not found for ' . $this->getName() . ' ' . $version );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::BEARSAMPP_CONF_NOT_FOUND_ERROR ), $this->getName() . ' ' . $version ),
                    $boxTitle
                );
            }

            return false;
        }

        $bearsamppConfRaw = parse_ini_file( $bearsamppConf );
        if ( $bearsamppConfRaw === false || !isset( $bearsamppConfRaw[self::ROOT_CFG_VERSION] ) || $bearsamppConfRaw[self::ROOT_CFG_VERSION] != $version ) {
            Util::logError( 'bearsampp config file malformed for ' . $this->getName() . ' ' . $version );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::BEARSAMPP_CONF_MALFORMED_ERROR ), $this->getName() . ' ' . $version ),
                    $boxTitle
                );
            }

            return false;
        }

        // bearsampp.conf
        $this->setVersion( $version );

        // conf
        Util::replaceInFile( $this->getConf(), array(
            '/^port(.*?)=(.*?)(\d+)/' => 'port = ' . $this->port
        ) );

        // phppgadmin
        $bearsamppApps->getPhppgadmin()->update( $sub + 1 );

        return true;
    }

    /**
     * Initializes the data directory for PostgreSQL if it does not already exist.
     *
     * @param   string|null  $path  The path to the PostgreSQL data directory. If null, the current path is used.
     */
    public function initData($path = null)
    {
        $path = $path != null ? $path : $this->getCurrentPath();

        if ( file_exists( $path . '/data' ) ) {
            return;
        }

        Batch::initializePostgresql( $path );
    }

    /**
     * Rebuilds the PostgreSQL configuration files by updating the port number.
     */
    public function rebuildConf()
    {
        Util::replaceInFile( $this->conf, array(
            '/^port(.*?)=(.*?)(\d+)/' => 'port = ' . $this->port
        ) );
        Util::replaceInFile( $this->altConf, array(
            '/^port(.*?)=(.*?)(\d+)/' => 'port = ' . $this->port
        ) );
    }

    /**
     * Executes a PostgreSQL command line instruction and returns the output.
     *
     * @param   string  $cmd  The command to execute.
     *
     * @return string|null The output of the command, or null if the command failed.
     */
    public function getCmdLineOutput($cmd)
    {
        $result = null;

        $bin = $this->getCliExe();
        if ( file_exists( $bin ) ) {
            $tmpResult = Batch::exec( 'postgresqlGetCmdLineOutput', '"' . $bin . '" ' . $cmd );
            if ( $tmpResult !== false && is_array( $tmpResult ) ) {
                $result = trim( str_replace( $bin, '', implode( PHP_EOL, $tmpResult ) ) );
            }
        }

        return $result;
    }

    /**
     * Sets the version of PostgreSQL and reloads the configuration.
     *
     * @param   string  $version  The version to set.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    /**
     * Gets the service object for PostgreSQL.
     *
     * @return Win32Service The service object.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Enables or disables the PostgreSQL module.
     *
     * @param   bool  $enabled     Whether to enable or disable the module.
     * @param   bool  $showWindow  Whether to show a message box with the result.
     */
    public function setEnable($enabled, $showWindow = false)
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppWinbinder;

        if ( $enabled == Config::ENABLED && !is_dir( $this->currentPath ) ) {
            Util::logDebug( $this->getName() . ' cannot be enabled because bundle ' . $this->getVersion() . ' does not exist in ' . $this->currentPath );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::ENABLE_BUNDLE_NOT_EXIST ), $this->getName(), $this->getVersion(), $this->currentPath ),
                    sprintf( $bearsamppLang->getValue( Lang::ENABLE_TITLE ), $this->getName() )
                );
            }
            $enabled = Config::DISABLED;
        }

        Util::logInfo( $this->getName() . ' switched to ' . ($enabled == Config::ENABLED ? 'enabled' : 'disabled') );
        $this->enable = $enabled == Config::ENABLED;
        $bearsamppConfig->replace( self::ROOT_CFG_ENABLE, $enabled );

        $this->reload();
        if ( $this->enable ) {
            Util::installService( $this, $this->port, null, $showWindow );
        }
        else {
            Util::removeService( $this->service, $this->name );
        }
    }

    /**
     * Gets the path to the PostgreSQL error log file.
     *
     * @return string The path to the error log file.
     */
    public function getErrorLog()
    {
        return $this->errorLog;
    }

    /**
     * Gets the path to the PostgreSQL control executable.
     *
     * @return string The path to the control executable.
     */
    public function getCtlExe()
    {
        return $this->ctlExe;
    }

    /**
     * Gets the path to the PostgreSQL command line interface executable.
     *
     * @return string The path to the CLI executable.
     */
    public function getCliExe()
    {
        return $this->cliExe;
    }

    /**
     * Gets the path to the PostgreSQL dump executable.
     *
     * @return string The path to the dump executable.
     */
    public function getDumpExe()
    {
        return $this->dumpExe;
    }

    /**
     * Gets the path to the PostgreSQL dump all executable.
     *
     * @return string The path to the dump all executable.
     */
    public function getDumpAllExe()
    {
        return $this->dumpAllExe;
    }

    /**
     * Gets the path to the PostgreSQL configuration file.
     *
     * @return string The path to the configuration file.
     */
    public function getConf()
    {
        return $this->conf;
    }

    /**
     * Gets the path to the PostgreSQL host-based authentication configuration file.
     *
     * @return string The path to the HBA configuration file.
     */
    public function getHbaConf()
    {
        return $this->hbaConf;
    }

    /**
     * Gets the port number used by PostgreSQL.
     *
     * @return int The port number.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the port number used by PostgreSQL.
     *
     * @param   int  $port  The port number to set.
     */
    public function setPort($port)
    {
        $this->replace( self::LOCAL_CFG_PORT, $port );
    }

    /**
     * Gets the root user for PostgreSQL.
     *
     * @return string The root user.
     */
    public function getRootUser()
    {
        return $this->rootUser;
    }

    /**
     * Sets the root user for PostgreSQL.
     *
     * @param   string  $rootUser  The root user to set.
     */
    public function setRootUser($rootUser)
    {
        $this->replace( self::LOCAL_CFG_ROOT_USER, $rootUser );
    }

    /**
     * Gets the root password for PostgreSQL.
     *
     * @return string The root password.
     */
    public function getRootPwd()
    {
        return $this->rootPwd;
    }

    /**
     * Sets the root password for PostgreSQL.
     *
     * @param   string  $rootPwd  The root password to set.
     */
    public function setRootPwd($rootPwd)
    {
        $this->replace( self::LOCAL_CFG_ROOT_PWD, $rootPwd );
    }
}
