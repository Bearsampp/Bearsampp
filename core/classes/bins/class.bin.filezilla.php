<?php

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

    private $service;
    private $logsPath;
    private $log;

    private $exe;
    private $itfExe;
    private $conf;
    private $itfConf;
    private $localItfConf;
    private $port;
    private $sslPort;

    public function __construct($id, $type)
    {
        Util::logInitClass( $this );
        $this->reload( $id, $type );
    }

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

        if ( $this->bearsamppConfRaw !== false )
        {
            $this->exe          = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->itfExe       = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ITF_EXE];
            $this->conf         = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->itfConf      = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ITF_CONF];
            $this->localItfConf = Util::formatUnixPath( getenv( 'APPDATA' ) ) . '/FileZilla Server/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ITF_CONF];
            $this->port         = $this->bearsamppConfRaw[self::LOCAL_CFG_PORT];
            $this->sslPort      = $this->bearsamppConfRaw[self::LOCAL_CFG_SSL_PORT];
        }

        if ( !$this->enable )
        {
            Util::logInfo( $this->name . ' is not enabled!' );

            return;
        }
        if ( !is_dir( $this->currentPath ) )
        {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_FILE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->currentPath ) );

            return;
        }
        if ( !is_dir( $this->symlinkPath ) )
        {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_FILE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->symlinkPath ) );

            return;
        }
        if ( !is_file( $this->bearsamppConf ) )
        {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->bearsamppConf ) );

            return;
        }

        // Create log hard link
        $log = $this->logsPath . '/FileZilla Server.log';
        if ( !file_exists( $this->log ) && file_exists( $log ) )
        {
            @link( $log, $this->log );
        }

        if ( !is_file( $this->exe ) )
        {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->exe ) );

            return;
        }
        if ( !is_file( $this->conf ) )
        {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->conf ) );

            return;
        }
        if ( !is_numeric( $this->port ) || $this->port <= 0 )
        {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_PORT, $this->port ) );

            return;
        }
        if ( !is_numeric( $this->sslPort ) || $this->sslPort <= 0 )
        {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_SSL_PORT, $this->sslPort ) );

            return;
        }
        if ( !file_exists( $this->localItfConf ) )
        {
            if ( !is_dir( dirname( $this->localItfConf ) ) )
            {
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

    public function changePort($port, $checkUsed = false, $wbProgressBar = null)
    {
        global $bearsamppWinbinder;

        if ( !Util::isValidPort( $port ) )
        {
            Util::logError( $this->getName() . ' port not valid: ' . $port );

            return false;
        }

        $port = intval( $port );
        $bearsamppWinbinder->incrProgressBar( $wbProgressBar );

        $isPortInUse = Util::isPortInUse( $port );
        if ( !$checkUsed || $isPortInUse === false )
        {
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

    public function checkPort($port, $ssl = false, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::CHECK_PORT_TITLE ), $this->getName(), $port );

        if ( !Util::isValidPort( $port ) )
        {
            Util::logError( $this->getName() . ' port not valid: ' . $port );

            return false;
        }

        $headers = Util::getHeaders( '127.0.0.1', $port, $ssl );
        if ( !empty( $headers ) )
        {
            if ( $headers[0] == '220 ' . $this->getService()->getDisplayName() )
            {
                Util::logDebug( $this->getName() . ' port ' . $port . ' is used by: ' . str_replace( '220 ', '', $headers[0] ) );
                if ( $showWindow )
                {
                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf( $bearsamppLang->getValue( Lang::PORT_USED_BY ), $port, str_replace( '220 ', '', $headers[0] ) ),
                        $boxTitle
                    );
                }

                return true;
            }
            Util::logDebug( $this->getName() . ' port ' . $port . ' is used by another application' );
            if ( $showWindow )
            {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf( $bearsamppLang->getValue( Lang::PORT_NOT_USED_BY ), $port ),
                    $boxTitle
                );
            }
        }
        else
        {
            Util::logDebug( $this->getName() . ' port ' . $port . ' is not used' );
            if ( $showWindow )
            {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::PORT_NOT_USED ), $port ),
                    $boxTitle
                );
            }
        }

        return false;
    }

    public function getService()
    {
        return $this->service;
    }

    public function switchVersion($version, $showWindow = false)
    {
        Util::logDebug( 'Switch ' . $this->name . ' version to ' . $version );

        return $this->updateConfig( $version, 0, $showWindow );
    }

    protected function updateConfig($version = null, $sub = 0, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        if ( !$this->enable )
        {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug( ($sub > 0 ? str_repeat( ' ', 2 * $sub ) : '') . 'Update ' . $this->name . ' ' . $version . ' config...' );

        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::SWITCH_VERSION_TITLE ), $this->getName(), $version );

        $conf          = str_replace( 'filezilla' . $this->getVersion(), 'filezilla' . $version, $this->getConf() );
        $bearsamppConf = str_replace( 'filezilla' . $this->getVersion(), 'filezilla' . $version, $this->bearsamppConf );

        if ( !file_exists( $conf ) || !file_exists( $bearsamppConf ) )
        {
            Util::logError( 'bearsampp config files not found for ' . $this->getName() . ' ' . $version );
            if ( $showWindow )
            {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::BEARSAMPP_CONF_NOT_FOUND_ERROR ), $this->getName() . ' ' . $version ),
                    $boxTitle
                );
            }

            return false;
        }

        $bearsamppConfRaw = parse_ini_file( $bearsamppConf );
        if ( $bearsamppConfRaw === false || !isset( $bearsamppConfRaw[self::ROOT_CFG_VERSION] ) || $bearsamppConfRaw[self::ROOT_CFG_VERSION] != $version )
        {
            Util::logError( 'bearsampp config file malformed for ' . $this->getName() . ' ' . $version );
            if ( $showWindow )
            {
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

    public function getConf()
    {
        return $this->conf;
    }

    public function setConf($elts)
    {
        if ( !$this->enable )
        {
            return;
        }

        $conf = simplexml_load_file( $this->conf );
        foreach ( $elts as $key => $value )
        {
            $conf->Settings->Item[$key] = $value;
        }
        $conf->asXML( $this->conf );
    }

    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    public function rebuildConf()
    {
        if ( !$this->enable )
        {
            return;
        }

        $this->setConf( array(
                            self::CFG_SERVER_PORT          => $this->port,
                            self::CFG_SERVICE_NAME         => $this->service->getName(),
                            self::CFG_WELCOME_MSG          => $this->service->getDisplayName(),
                            self::CFG_SERVICE_DISPLAY_NAME => $this->service->getDisplayName()
                        ) );
    }

    public function setEnable($enabled, $showWindow = false)
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppWinbinder;

        if ( $enabled == Config::ENABLED && !is_dir( $this->currentPath ) )
        {
            Util::logDebug( $this->getName() . ' cannot be enabled because bundle ' . $this->getVersion() . ' does not exist in ' . $this->currentPath );
            if ( $showWindow )
            {
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
        if ( $this->enable )
        {
            Util::installService( $this, $this->port, null, $showWindow );
        }
        else
        {
            Util::removeService( $this->service, $this->name );
        }
    }

    public function getLogsPath()
    {
        return $this->logsPath;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function getExe()
    {
        return $this->exe;
    }

    public function getItfExe()
    {
        return $this->itfExe;
    }

    public function getItfConf()
    {
        return $this->itfConf;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port)
    {
        $this->replace( self::LOCAL_CFG_PORT, $port );
    }

    public function getSslPort()
    {
        return $this->sslPort;
    }

    public function setSslPort($sslPort)
    {
        $this->replace( self::LOCAL_CFG_SSL_PORT, $sslPort );
    }

    protected function replaceAll($params)
    {
        $content = file_get_contents( $this->bearsamppConf );

        foreach ( $params as $key => $value )
        {
            $content                      = preg_replace( '|' . $key . ' = .*|', $key . ' = ' . '"' . $value . '"', $content );
            $this->bearsamppConfRaw[$key] = $value;
            switch ( $key )
            {
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
