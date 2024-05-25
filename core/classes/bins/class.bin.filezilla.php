<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Manages the Filezilla server module within the Bearsampp environment.
 *
 * This class handles the configuration, management, and operation of the Filezilla server as a module within the Bearsampp stack.
 * It extends the generic Module class, adding specific functionality for handling Filezilla server operations such as starting,
 * stopping, and configuring the server, managing ports, and handling service interactions.
 */
class BinFilezilla extends Module
{
    const SERVICE_NAME = 'bearsamppfilezilla';

    const ROOT_CFG_ENABLE = 'filezillaEnable';
    const ROOT_CFG_VERSION = 'filezillaVersion';

    const LOCAL_CFG_EXE = 'filezillaExe';
    const LOCAL_CFG_ITF_EXE = 'filezillaItfExe';
    const LOCAL_CFG_CONF = 'filezillaConf';
    const LOCAL_CFG_ITF_CONF = 'filezillaItfConf';
    const LOCAL_CFG_PORT = 'filezillaPort';
    const LOCAL_CFG_SSL_PORT = 'filezillaSslPort';

    const CFG_SERVER_PORT = 0;
    const CFG_WELCOME_MSG = 15;
    const CFG_IP_FILTER_ALLOWED = 39;
    const CFG_IP_FILTER_DISALLOWED = 40;
    const CFG_SERVICE_NAME = 58;
    const CFG_SERVICE_DISPLAY_NAME = 59;

    /**
     * @var Win32Service The service handler for the Filezilla server.
     */
    private $service;

    /**
     * @var string Path to the logs directory for Filezilla.
     */
    private $logsPath;

    /**
     * @var string Path to the main log file for Filezilla.
     */
    private $log;

    /**
     * @var string Executable path for Filezilla server.
     */
    private $exe;

    /**
     * @var string Executable path for Filezilla interface.
     */
    private $itfExe;

    /**
     * @var string Configuration file path for Filezilla server.
     */
    private $conf;

    /**
     * @var string Configuration file path for Filezilla interface.
     */
    private $itfConf;

    /**
     * @var string Local interface configuration file path.
     */
    private $localItfConf;

    /**
     * @var int Port number for Filezilla server.
     */
    private $port;

    /**
     * @var int SSL port number for Filezilla server.
     */
    private $sslPort;

    /**
     * Constructor for the Filezilla module.
     *
     * @param   string  $id    The module identifier.
     * @param   string  $type  The type of module.
     */
    public function __construct($id, $type)
    {
        Util::logInitClass( $this );
        $this->reload( $id, $type );
    }

    /**
     * Reloads the configuration and reinitializes the module.
     *
     * @param   string|null  $id    Optional module identifier to reload.
     * @param   string|null  $type  Optional type of the module to reload.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::FILEZILLA );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        $this->enable   = $this->enable && $bearsamppConfig->getRaw( self::ROOT_CFG_ENABLE );
        $this->service  = new Win32Service( self::SERVICE_NAME );
        $this->logsPath = $this->symlinkPath . '/Logs';
        $this->log      = $bearsamppRoot->getLogsPath() . '/filezilla.log';

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe          = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->itfExe       = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ITF_EXE];
            $this->conf         = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->itfConf      = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ITF_CONF];
            $this->localItfConf = Util::formatUnixPath( getenv( 'APPDATA' ) ) . '/FileZilla Server/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ITF_CONF];
            $this->port         = $this->bearsamppConfRaw[self::LOCAL_CFG_PORT];
            $this->sslPort      = $this->bearsamppConfRaw[self::LOCAL_CFG_SSL_PORT];
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

        // Create log hard link
        $log = $this->logsPath . '/FileZilla Server.log';
        if ( !file_exists( $this->log ) && file_exists( $log ) ) {
            @link( $log, $this->log );
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
        if ( !is_numeric( $this->sslPort ) || $this->sslPort <= 0 ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_SSL_PORT, $this->sslPort ) );

            return;
        }
        if ( !file_exists( $this->localItfConf ) ) {
            if ( !is_dir( dirname( $this->localItfConf ) ) ) {
                Util::logDebug( 'Create folder ' . dirname( $this->localItfConf ) );
                @mkdir( dirname( $this->localItfConf ), 0777 );
            }
            Util::logDebug( 'Write ' . $this->bearsamppConfRaw[self::LOCAL_CFG_ITF_CONF] . ' to ' . $this->localItfConf );
            @copy( $this->itfConf, $this->localItfConf );
        }

        $this->service->setDisplayName( APP_TITLE . ' ' . $this->getName() );
        $this->service->setBinPath( $this->exe );
        $this->service->setStartType( Win32Service::SERVICE_DEMAND_START );
        $this->service->setErrorControl( Win32Service::SERVER_ERROR_NORMAL );
    }

    /**
     * Changes the port number for the Filezilla server.
     *
     * @param   int    $port           The new port number.
     * @param   bool   $checkUsed      Whether to check if the port is already in use.
     * @param   mixed  $wbProgressBar  Optional progress bar object for UI feedback.
     *
     * @return mixed Returns true on success, or the error state if the port is in use.
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
     * Checks if the specified port is available and being used by Filezilla.
     *
     * @param   int   $port        The port to check.
     * @param   bool  $ssl         Whether to check the SSL port.
     * @param   bool  $showWindow  Whether to show a message box with the result.
     *
     * @return bool Returns true if the port is used by Filezilla, false otherwise.
     */
    public function checkPort($port, $ssl = false, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::CHECK_PORT_TITLE ), $this->getName(), $port );

        if ( !Util::isValidPort( $port ) ) {
            Util::logError( $this->getName() . ' port not valid: ' . $port );

            return false;
        }

        $headers = Util::getHeaders( '127.0.0.1', $port, $ssl );
        if ( !empty( $headers ) ) {
            if ( $headers[0] == '220 ' . $this->getService()->getDisplayName() ) {
                Util::logDebug( $this->getName() . ' port ' . $port . ' is used by: ' . str_replace( '220 ', '', $headers[0] ) );
                if ( $showWindow ) {
                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf( $bearsamppLang->getValue( Lang::PORT_USED_BY ), $port, str_replace( '220 ', '', $headers[0] ) ),
                        $boxTitle
                    );
                }

                return true;
            }
            Util::logDebug( $this->getName() . ' port ' . $port . ' is used by another application' );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf( $bearsamppLang->getValue( Lang::PORT_NOT_USED_BY ), $port ),
                    $boxTitle
                );
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
     * Retrieves the service handler for the Filezilla server.
     *
     * @return Win32Service The service handler.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Switches the version of the Filezilla server.
     *
     * @param   string  $version     The version to switch to.
     * @param   bool    $showWindow  Whether to show a message box with the result.
     *
     * @return bool Returns true on successful switch, false on failure.
     */
    public function switchVersion($version, $showWindow = false)
    {
        Util::logDebug( 'Switch ' . $this->name . ' version to ' . $version );

        return $this->updateConfig( $version, 0, $showWindow );
    }

    /**
     * Updates the configuration file for the Filezilla server.
     *
     * @param   string|null  $version     The version to update the configuration for.
     * @param   int          $sub         Sub-level for logging indentation.
     * @param   bool         $showWindow  Whether to show a message box with the result.
     *
     * @return bool Returns true on successful update, false on failure.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        if ( !$this->enable ) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug( ($sub > 0 ? str_repeat( ' ', 2 * $sub ) : '') . 'Update ' . $this->name . ' ' . $version . ' config' );

        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::SWITCH_VERSION_TITLE ), $this->getName(), $version );

        $conf          = str_replace( 'filezilla' . $this->getVersion(), 'filezilla' . $version, $this->getConf() );
        $bearsamppConf = str_replace( 'filezilla' . $this->getVersion(), 'filezilla' . $version, $this->bearsamppConf );

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
        $this->rebuildConf();

        return true;
    }

    /**
     * Retrieves the current configuration file path.
     *
     * @return string The path to the current configuration file.
     */
    public function getConf()
    {
        return $this->conf;
    }

    /**
     * Sets configuration values in the XML configuration file.
     *
     * This method loads the XML configuration file, updates specified settings, and saves the changes back to the file.
     * It only performs these operations if the module is enabled.
     *
     * @param   array  $elts  An associative array where keys are the XML paths to the settings, and values are the new settings values.
     */
    public function setConf($elts)
    {
        if ( !$this->enable ) {
            return;
        }

        $conf = simplexml_load_file( $this->conf );
        foreach ( $elts as $key => $value ) {
            $conf->Settings->Item[$key] = $value;
        }
        $conf->asXML( $this->conf );
    }

    /**
     * Sets the version of the module and updates the configuration.
     *
     * This method updates the module's version and the corresponding configuration in the global configuration.
     * It then triggers a reload of the module to apply changes.
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
     * Rebuilds the configuration file with updated settings.
     *
     * This method updates the configuration file with new values for server port, service name, welcome message,
     * and service display name. It only performs these operations if the module is enabled.
     */
    public function rebuildConf()
    {
        if ( !$this->enable ) {
            return;
        }

        $this->setConf( array(
                            self::CFG_SERVER_PORT          => $this->port,
                            self::CFG_SERVICE_NAME         => $this->service->getName(),
                            self::CFG_WELCOME_MSG          => $this->service->getDisplayName(),
                            self::CFG_SERVICE_DISPLAY_NAME => $this->service->getDisplayName()
                        ) );
    }

    /**
     * Enables or disables the module and handles the service accordingly.
     *
     * This method sets the module's enabled state and updates the global configuration.
     * If enabling, it installs the service; if disabling, it removes the service.
     * It also handles UI feedback if specified.
     *
     * @param   bool  $enabled     True to enable the module, false to disable.
     * @param   bool  $showWindow  True to show error messages in a window, false to not show.
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
     * Retrieves the path to the logs directory for Filezilla.
     *
     * @return string The path to the logs directory.
     */
    public function getLogsPath()
    {
        return $this->logsPath;
    }

    /**
     * Retrieves the path to the main log file for Filezilla.
     *
     * @return string The path to the main log file.
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Retrieves the executable path for the Filezilla server.
     *
     * @return string The executable path for the Filezilla server.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Retrieves the executable path for the Filezilla interface.
     *
     * @return string The executable path for the Filezilla interface.
     */
    public function getItfExe()
    {
        return $this->itfExe;
    }

    /**
     * Retrieves the configuration file path for the Filezilla interface.
     *
     * @return string The configuration file path for the Filezilla interface.
     */
    public function getItfConf()
    {
        return $this->itfConf;
    }

    /**
     * Retrieves the port number used by the Filezilla server.
     *
     * @return int The port number.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the port number for the Filezilla server.
     *
     * @param   int  $port  The new port number to be set.
     */
    public function setPort($port)
    {
        $this->replace( self::LOCAL_CFG_PORT, $port );
    }

    /**
     * Retrieves the SSL port number used by the Filezilla server.
     *
     * @return int The SSL port number.
     */
    public function getSslPort()
    {
        return $this->sslPort;
    }

    public function setSslPort($sslPort)
    {
        $this->replace( self::LOCAL_CFG_SSL_PORT, $sslPort );
    }

    /**
     * Replaces all specified configuration parameters in the configuration file and updates the class properties.
     *
     * This method reads the current configuration from the configuration file, replaces each specified parameter
     * with its new value, and updates the configuration file. It also updates the class properties for specific
     * configuration keys related to port settings.
     *
     * @param   array  $params  An associative array where keys are the configuration keys to be updated, and values are the new values for these keys.
     *
     * @return void
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
                case self::LOCAL_CFG_SSL_PORT:
                    $this->sslPort = $value;
                    break;
            }
        }

        file_put_contents( $this->bearsamppConf, $content );
    }
}
