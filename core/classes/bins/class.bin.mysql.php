<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class BinMysql
 *
 * This class represents the MySQL binary module in the Bearsampp application.
 * It handles the configuration, management, and operations related to MySQL.
 */
class BinMysql extends Module
{
    const SERVICE_NAME = 'bearsamppmysql';

    const ROOT_CFG_ENABLE = 'mysqlEnable';
    const ROOT_CFG_VERSION = 'mysqlVersion';

    const LOCAL_CFG_EXE = 'mysqlExe';
    const LOCAL_CFG_CLI_EXE = 'mysqlCliExe';
    const LOCAL_CFG_ADMIN = 'mysqlAdmin';
    const LOCAL_CFG_CONF = 'mysqlConf';
    const LOCAL_CFG_PORT = 'mysqlPort';
    const LOCAL_CFG_ROOT_USER = 'mysqlRootUser';
    const LOCAL_CFG_ROOT_PWD = 'mysqlRootPwd';

    const CMD_VERSION = '--version';
    const CMD_VARIABLES = 'variables';
    const CMD_SYNTAX_CHECK = '--help --verbose 1>NUL';

    private $service;
    private $errorLog;

    private $exe;
    private $conf;
    private $port;
    private $rootUser;
    private $rootPwd;
    private $cliExe;
    private $admin;

    /**
     * Constructs a BinMysql object and initializes the MySQL module.
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
     * Reloads the MySQL module configuration based on the provided ID and type.
     *
     * @param   string|null  $id    The ID of the module. If null, the current ID is used.
     * @param   string|null  $type  The type of the module. If null, the current type is used.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::MYSQL );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        $this->enable   = $this->enable && $bearsamppConfig->getRaw( self::ROOT_CFG_ENABLE );
        $this->service  = new Win32Service( self::SERVICE_NAME );
        $this->errorLog = $bearsamppRoot->getLogsPath() . '/mysql.log';

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe      = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->conf     = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->port     = $this->bearsamppConfRaw[self::LOCAL_CFG_PORT];
            $this->rootUser = isset( $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_USER] ) ? $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_USER] : 'root';
            $this->rootPwd  = isset( $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_PWD] ) ? $this->bearsamppConfRaw[self::LOCAL_CFG_ROOT_PWD] : '';
            $this->cliExe   = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CLI_EXE];
            $this->admin    = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ADMIN];
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
        if ( !is_file( $this->exe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->exe ) );

            return;
        }
        if ( !is_file( $this->conf ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->conf ) );

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
        if ( !is_file( $this->cliExe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->cliExe ) );

            return;
        }
        if ( !is_file( $this->admin ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->admin ) );

            return;
        }

        $this->service->setDisplayName( APP_TITLE . ' ' . $this->getName() );
        $this->service->setBinPath( $this->exe );
        $this->service->setParams( self::SERVICE_NAME );
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
     * Changes the MySQL port and updates the configuration.
     *
     * @param   int    $port           The new port number.
     * @param   bool   $checkUsed      Whether to check if the port is already in use.
     * @param   mixed  $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|string True if the port was changed successfully, or an error message if the port is in use.
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
     * Checks if the specified port is in use by MySQL.
     *
     * @param   int   $port        The port number to check.
     * @param   bool  $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the port is in use by MySQL, false otherwise.
     */
    public function checkPort($port, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::CHECK_PORT_TITLE ), $this->getName(), $port );

        if ( !Util::isValidPort( $port ) ) {
            Util::logError( $this->getName() . ' port not valid: ' . $port );

            return false;
        }

        $fp = @fsockopen( '127.0.0.1', $port, $errno, $errstr, 5 );
        if ( $fp ) {
            if ( version_compare( phpversion(), '5.3' ) === -1 ) {
                $dbLink = mysqli_connect( '127.0.0.1', $this->rootUser, $this->rootPwd, '', $port );
            }
            else {
                $dbLink = mysqli_connect( '127.0.0.1:' . $port, $this->rootUser, $this->rootPwd );
            }
            $isMysql = false;
            $version = false;

            if ( $dbLink ) {
                $result = mysqli_query( $dbLink, 'SHOW VARIABLES' );
                if ( $result ) {
                    while ( false !== ($row = mysqli_fetch_array( $result, MYSQLI_NUM )) ) {
                        if ( $row[0] == 'version' ) {
                            $version = explode( '-', $row[1] );
                            $version = count( $version ) > 1 ? $version[0] : $row[1];
                        }
                        if ( $row[0] == 'version_comment' && Util::startWith( strtolower( $row[1] ), 'mysql' ) ) {
                            $isMysql = true;
                        }
                        if ( $isMysql && $version !== false ) {
                            break;
                        }
                    }
                    if ( !$isMysql ) {
                        Util::logDebug( $this->getName() . ' port used by another DBMS: ' . $port );
                        if ( $showWindow ) {
                            $bearsamppWinbinder->messageBoxWarning(
                                sprintf( $bearsamppLang->getValue( Lang::PORT_USED_BY_ANOTHER_DBMS ), $port ),
                                $boxTitle
                            );
                        }
                    }
                    else {
                        Util::logDebug( $this->getName() . ' port ' . $port . ' is used by: ' . $this->getName() . ' ' . $version );
                        if ( $showWindow ) {
                            $bearsamppWinbinder->messageBoxInfo(
                                sprintf( $bearsamppLang->getValue( Lang::PORT_USED_BY ), $port, $this->getName() . ' ' . $version ),
                                $boxTitle
                            );
                        }

                        return true;
                    }
                }
                mysqli_close( $dbLink );
            }
            else {
                Util::logDebug( $this->getName() . ' port ' . $port . ' is used by another application' );
                if ( $showWindow ) {
                    $bearsamppWinbinder->messageBoxWarning(
                        sprintf( $bearsamppLang->getValue( Lang::PORT_NOT_USED_BY ), $port ),
                        $boxTitle
                    );
                }
            }
        }
        else {
            Util::logDebug( $this->getName() . ' port ' . $port . ' is not used' );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::PORT_NOT_USED ), $port ),
                    $boxTitle
                );
            }
        }

        return false;
    }

    /**
     * Changes the MySQL root password.
     *
     * @param   string  $currentPwd     The current root password.
     * @param   string  $newPwd         The new root password.
     * @param   mixed   $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|string True if the password was changed successfully, or an error message if the operation failed.
     */
    public function changeRootPassword($currentPwd, $newPwd, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;
        $error = null;

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( version_compare( phpversion(), '5.3' ) === -1 ) {
            $dbLink = @mysqli_connect( '127.0.0.1', $this->rootUser, $currentPwd, '', $this->port );
        }
        else {
            $dbLink = @mysqli_connect( '127.0.0.1:' . $this->port, $this->rootUser, $currentPwd );
        }
        if ( !$dbLink ) {
            $error = mysqli_connect_error();
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        $stmt = @mysqli_prepare( $dbLink, 'UPDATE mysql.user SET Password=PASSWORD(?) WHERE User=?' );
        if ( empty( $error ) && $stmt === false ) {
            $error = mysqli_error( $dbLink );
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( empty( $error ) && !@mysqli_stmt_bind_param( $stmt, 'ss', $newPwd, $this->rootUser ) ) {
            $error = mysqli_stmt_error( $stmt );
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( empty( $error ) && !@mysqli_stmt_execute( $stmt ) ) {
            $error = mysqli_stmt_error( $stmt );
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( $stmt !== false ) {
            mysqli_stmt_close( $stmt );
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( empty( $error ) && @mysqli_query( $dbLink, 'FLUSH PRIVILEGES' ) === false ) {
            $error = mysqli_error( $dbLink );
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( $dbLink ) {
            mysqli_close( $dbLink );
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
     * @return bool|string True if the password is correct, or an error message if the operation failed.
     */
    public function checkRootPassword($currentPwd = null, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;
        $currentPwd = $currentPwd == null ? $this->rootPwd : $currentPwd;
        $error      = null;

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( version_compare( phpversion(), '5.3' ) === -1 ) {
            $dbLink = @mysqli_connect( '127.0.0.1', $this->rootUser, $currentPwd, '', $this->port );
        }
        else {
            $dbLink = @mysqli_connect( '127.0.0.1:' . $this->port, $this->rootUser, $currentPwd );
        }
        if ( !$dbLink ) {
            $error = mysqli_connect_error();
        }

        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );
        if ( $dbLink ) {
            mysqli_close( $dbLink );
        }

        if ( !empty( $error ) ) {
            return $error;
        }

        return true;
    }

    /**
     * Switches the MySQL version and updates the configuration.
     *
     * @param   string  $version     The new MySQL version.
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
     * Updates the MySQL configuration with a specific version.
     *
     * @param   string|null  $version     The version to update to. If null, the current version is used.
     * @param   int          $sub         The sub-level for logging indentation.
     * @param   bool         $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the configuration was updated successfully, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppApps, $bearsamppWinbinder;

        if ( !$this->enable ) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug( ($sub > 0 ? str_repeat( ' ', 2 * $sub ) : '') . 'Update ' . $this->name . ' ' . $version . ' config' );

        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::SWITCH_VERSION_TITLE ), $this->getName(), $version );

        $currentPath   = str_replace( 'mysql' . $this->getVersion(), 'mysql' . $version, $this->getCurrentPath() );
        $conf          = str_replace( 'mysql' . $this->getVersion(), 'mysql' . $version, $this->getConf() );
        $bearsamppConf = str_replace( 'mysql' . $this->getVersion(), 'mysql' . $version, $this->bearsamppConf );

        if ( $this->version != $version ) {
            $this->initData( $currentPath, $version );
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

        // phpmyadmin
        $bearsamppApps->getPhpmyadmin()->update( $sub + 1 );

        // adminer
        $bearsamppApps->getAdminer()->update( $sub + 1 );

        // php
        $bearsamppBins->getPhp()->update( $sub + 1 );

        return true;
    }

    /**
     * Initializes the MySQL data directory if it does not exist.
     *
     * @param   string|null  $path     The path to the MySQL installation. If null, the current path is used.
     * @param   string|null  $version  The version of MySQL. If null, the current version is used.
     */
    public function initData($path = null, $version = null)
    {
        $path    = $path != null ? $path : $this->getCurrentPath();
        $version = $version != null ? $version : $this->getVersion();

        if ( version_compare( $version, '5.7.0', '<' ) ) {
            return;
        }

        if ( file_exists( $path . '/data' ) ) {
            return;
        }

        Batch::initializeMysql( $path );
    }

    /**
     * Executes a MySQL command and retrieves the output.
     *
     * @param   string  $cmd  The command to execute.
     *
     * @return array An associative array containing 'syntaxOk' (boolean) and 'content' (string|null).
     */
    public function getCmdLineOutput($cmd)
    {
        $result = array(
            'syntaxOk' => false,
            'content'  => null,
        );

        $bin         = $this->getExe();
        $removeLines = 0;
        $outputFrom  = '';
        if ( $cmd == self::CMD_SYNTAX_CHECK ) {
            $outputFrom = '2';
        }
        elseif ( $cmd == self::CMD_VARIABLES ) {
            $bin = $this->getAdmin();
            $cmd .= ' --user=' . $this->getRootUser();
            if ( $this->getRootPwd() ) {
                $cmd .= ' --password=' . $this->getRootPwd();
            }
            $removeLines = 2;
        }

        if ( file_exists( $bin ) ) {
            $tmpResult = Batch::exec( 'mysqlGetCmdLineOutput', '"' . $bin . '" ' . $cmd . ' ' . $outputFrom, 5 );
            if ( $tmpResult !== false && is_array( $tmpResult ) ) {
                $result['syntaxOk'] = empty( $tmpResult ) || !Util::contains( trim( $tmpResult[count( $tmpResult ) - 1] ), '[ERROR]' );
                for ( $i = 0; $i < $removeLines; $i++ ) {
                    unset( $tmpResult[$i] );
                }
                $result['content'] = trim( str_replace( $bin, '', implode( PHP_EOL, $tmpResult ) ) );
            }
        }

        return $result;
    }

    /**
     * Sets the MySQL version and reloads the configuration.
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
     * Retrieves the MySQL service object.
     *
     * @return Win32Service The MySQL service object.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Enables or disables the MySQL module and updates the configuration.
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
            Util::installService( $this, $this->port, self::CMD_SYNTAX_CHECK, $showWindow );
        }
        else {
            Util::removeService( $this->service, $this->name );
        }
    }

    /**
     * Retrieves the path to the MySQL error log.
     *
     * @return string The path to the error log.
     */
    public function getErrorLog()
    {
        return $this->errorLog;
    }

    /**
     * Retrieves the path to the MySQL executable.
     *
     * @return string The path to the MySQL executable.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Retrieves the path to the MySQL configuration file.
     *
     * @return string The path to the configuration file.
     */
    public function getConf()
    {
        return $this->conf;
    }

    /**
     * Retrieves the MySQL port number.
     *
     * @return int The port number.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the MySQL port number and updates the configuration.
     *
     * @param   int  $port  The port number to set.
     */
    public function setPort($port)
    {
        $this->replace( self::LOCAL_CFG_PORT, $port );
    }

    /**
     * Retrieves the MySQL root username.
     *
     * @return string The root username.
     */
    public function getRootUser()
    {
        return $this->rootUser;
    }

    /**
     * Sets the MySQL root username and updates the configuration.
     *
     * @param   string  $rootUser  The root username to set.
     */
    public function setRootUser($rootUser)
    {
        $this->replace( self::LOCAL_CFG_ROOT_USER, $rootUser );
    }

    /**
     * Retrieves the MySQL root password.
     *
     * @return string The root password.
     */
    public function getRootPwd()
    {
        return $this->rootPwd;
    }

    /**
     * Sets the MySQL root password and updates the configuration.
     *
     * @param   string  $rootPwd  The root password to set.
     */
    public function setRootPwd($rootPwd)
    {
        $this->replace( self::LOCAL_CFG_ROOT_PWD, $rootPwd );
    }

    /**
     * Retrieves the path to the MySQL CLI executable.
     *
     * @return string The path to the CLI executable.
     */
    public function getCliExe()
    {
        return $this->cliExe;
    }

    /**
     * Retrieves the path to the MySQL admin executable.
     *
     * @return string The path to the admin executable.
     */
    public function getAdmin()
    {
        return $this->admin;
    }
}
