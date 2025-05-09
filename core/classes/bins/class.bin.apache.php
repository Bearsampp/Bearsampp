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
 * Class BinApache
 *
 * This class represents the Apache module in the Bearsampp application.
 * It provides functionalities to manage Apache services, configurations, and modules.
 */
class BinApache extends Module
{
    const SERVICE_NAME = 'bearsamppapache';
    const SERVICE_PARAMS = '-k runservice';

    const ROOT_CFG_ENABLE = 'apacheEnable';
    const ROOT_CFG_VERSION = 'apacheVersion';

    const LOCAL_CFG_EXE = 'apacheExe';
    const LOCAL_CFG_CONF = 'apacheConf';
    const LOCAL_CFG_PORT = 'apachePort';
    const LOCAL_CFG_SSL_PORT = 'apacheSslPort';
    const LOCAL_CFG_OPENSSL_EXE = 'apacheOpensslExe';

    const CMD_VERSION_NUMBER = '-v';
    const CMD_COMPILE_SETTINGS = '-V';
    const CMD_COMPILED_MODULES = '-l';
    const CMD_CONFIG_DIRECTIVES = '-L';
    const CMD_VHOSTS_SETTINGS = '-S';
    const CMD_LOADED_MODULES = '-M';
    const CMD_SYNTAX_CHECK = '-t';

    const TAG_START_SWITCHONLINE = '# START switchOnline tag - Do not replace!';
    const TAG_END_SWITCHONLINE = '# END switchOnline tag - Do not replace!';

    private $service;
    private $modulesPath;
    private $sslConf;
    private $accessLog;
    private $rewriteLog;
    private $errorLog;

    private $exe;
    private $conf;
    private $port;
    private $sslPort;
    private $opensslExe;

    /**
     * Constructor for the BinApache class.
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

        $this->name    = $bearsamppLang->getValue( Lang::APACHE );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        $this->enable      = $this->enable && $bearsamppConfig->getRaw( self::ROOT_CFG_ENABLE );
        $this->service     = new Win32Service( self::SERVICE_NAME );
        $this->modulesPath = $this->symlinkPath . '/modules';
        $this->sslConf     = $this->symlinkPath . '/conf/extra/httpd-ssl.conf';
        $this->accessLog   = $bearsamppRoot->getLogsPath() . '/apache_access.log';
        $this->rewriteLog  = $bearsamppRoot->getLogsPath() . '/apache_rewrite.log';
        $this->errorLog    = $bearsamppRoot->getLogsPath() . '/apache_error.log';

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe        = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->conf       = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->port       = $this->bearsamppConfRaw[self::LOCAL_CFG_PORT];
            $this->sslPort    = $this->bearsamppConfRaw[self::LOCAL_CFG_SSL_PORT];
            $this->opensslExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_OPENSSL_EXE];
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
        if ( !is_file( $this->sslConf ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->sslConf ) );

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
        if ( !is_numeric( $this->sslPort ) || $this->sslPort <= 0 ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_SSL_PORT, $this->sslPort ) );

            return;
        }
        if ( !is_file( $this->opensslExe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->opensslExe ) );

            return;
        }

        $nssm = new Nssm( self::SERVICE_NAME );
        $nssm->setDisplayName( APP_TITLE . ' ' . $this->getName() );
        $nssm->setBinPath( $this->exe );
        $nssm->setStart( Nssm::SERVICE_DEMAND_START );

        $this->service->setNssm( $nssm );
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
                case self::LOCAL_CFG_SSL_PORT:
                    $this->sslPort = $value;
                    break;
            }
        }

        file_put_contents( $this->bearsamppConf, $content );
    }

    /**
     * Changes the port for the Apache service.
     *
     * @param   int    $port           The new port number.
     * @param   bool   $checkUsed      Whether to check if the port is already in use.
     * @param   mixed  $wbProgressBar  The progress bar object for updating progress.
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
     * Checks if a specific port is being used by the Apache service.
     *
     * @param   int   $port        The port number to check.
     * @param   bool  $ssl         Whether to check for SSL.
     * @param   bool  $showWindow  Whether to show a window with the result.
     *
     * @return bool True if the port is used by Apache, false otherwise.
     */
    public function checkPort($port, $ssl = false, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder, $bearsamppHomepage;
        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::CHECK_PORT_TITLE ), $this->getName(), $port );

        if ( !Util::isValidPort( $port ) ) {
            Util::logError( $this->getName() . ' port not valid: ' . $port );

            return false;
        }

        $headers = Util::getHttpHeaders( 'http' . ($ssl ? 's' : '') . '://localhost:' . $port . '/' . $bearsamppHomepage->getResourcesPath() . '/ping.php' );
        if ( !empty( $headers ) ) {
            foreach ( $headers as $row ) {
                if ( Util::startWith( $row, 'Server: ' ) || Util::startWith( $row, 'server: ' ) ) {
                    Util::logDebug( $this->getName() . ' port ' . $port . ' is used by: ' . $this->getName() . ' ' . str_replace( 'Server: ', '', str_replace( 'server: ', '', trim( $row ) ) ) );
                    if ( $showWindow ) {
                        $bearsamppWinbinder->messageBoxInfo(
                            sprintf( $bearsamppLang->getValue( Lang::PORT_USED_BY ), $port, str_replace( 'Server: ', '', str_replace( 'server: ', '', trim( $row ) ) ) ),
                            $boxTitle
                        );
                    }

                    return true;
                }
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
     * Switches the Apache version.
     *
     * @param   string  $version     The version to switch to.
     * @param   bool    $showWindow  Whether to show a window with the result.
     *
     * @return bool True if the version was switched successfully, false otherwise.
     */
    public function switchVersion($version, $showWindow = false)
    {
        Util::logDebug( 'Switch ' . $this->name . ' version to ' . $version );

        return $this->updateConfig( $version, 0, $showWindow );
    }

    /**
     * Updates the Apache configuration with a specific version.
     *
     * @param   string|null  $version     The version to update to. If null, the current version is used.
     * @param   int          $sub         The sub-level for logging indentation.
     * @param   bool         $showWindow  Whether to show a window during the update process.
     *
     * @return bool True if the configuration was updated successfully, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if ( !$this->enable ) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug( ($sub > 0 ? str_repeat( ' ', 2 * $sub ) : '') . 'Update ' . $this->name . ' ' . $version . ' config' );

        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::SWITCH_VERSION_TITLE ), $this->getName(), $version );

        $conf          = str_replace( 'apache' . $this->getVersion(), 'apache' . $version, $this->getConf() );
        $bearsamppConf = str_replace( 'apache' . $this->getVersion(), 'apache' . $version, $this->bearsamppConf );

        $tsDll = $bearsamppBins->getPhp()->getTsDll();

        $apachePhpModuleName = null;
        if ( $tsDll !== false ) {
            $apachemoduleNamePrefix = substr( $tsDll, 0, 4 );
            $apachePhpModuleName    = ($apachemoduleNamePrefix == 'php8' ? 'php' : $apachemoduleNamePrefix) . '_module';
        }
        $apachePhpModulePath = $bearsamppBins->getPhp()->getApacheModule( $version );
        $apachePhpModuleDll  = basename( $apachePhpModulePath );

        Util::logDebug( ($sub > 0 ? str_repeat( ' ', 2 * $sub ) : '') . 'PHP TsDll found: ' . $tsDll );
        Util::logDebug( ($sub > 0 ? str_repeat( ' ', 2 * $sub ) : '') . 'PHP Apache module found: ' . $apachePhpModulePath );

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

        if ( $tsDll === false || $apachePhpModulePath === false ) {
            Util::logDebug( $this->getName() . ' ' . $version . ' does not seem to be compatible with PHP ' . $bearsamppBins->getPhp()->getVersion() );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::APACHE_INCPT ), $version, $bearsamppBins->getPhp()->getVersion() ),
                    $boxTitle
                );
            }

            return false;
        }

        // httpd.conf
        $this->setVersion( $version );

        // conf
        Util::logDebug( 'httpd.conf = ' . $conf );
        Util::replaceInFile( $conf, array(
            // PHP module
            '/^#?PHPIniDir\s.*/'                          => ($bearsamppBins->getPhp()->isEnable() ? '' : '#') . 'PHPIniDir "' . $bearsamppBins->getPhp()->getSymlinkPath() . '"',
            '/^#?LoadFile\s.*php.ts\.dll.*/'              => ($bearsamppBins->getPhp()->isEnable() ? '' : '#') . (!file_exists( $bearsamppBins->getPhp()->getSymlinkPath() . '/' . $tsDll ) ? '#' : '') . 'LoadFile "' . $bearsamppBins->getPhp()->getSymlinkPath() . '/' . $tsDll . '"',
            '/^#?LoadModule\sphp.*/'                      => ($bearsamppBins->getPhp()->isEnable() ? '' : '#') . 'LoadModule ' . $apachePhpModuleName . ' "' . $bearsamppBins->getPhp()->getSymlinkPath() . '/' . $apachePhpModuleDll . '"',
            '/^#?LoadModule\sphp_*/'                      => ($bearsamppBins->getPhp()->isEnable() ? '' : '#') . 'LoadModule ' . $apachePhpModuleName . ' "' . $bearsamppBins->getPhp()->getSymlinkPath() . '/' . $apachePhpModuleDll . '"',


            // Port
            '/^Listen\s(\d+)/'                            => 'Listen ' . $this->port,
            '/^ServerName\s+([a-zA-Z0-9.]+):(\d+)/'       => 'ServerName {{1}}:' . $this->port,
            '/^NameVirtualHost\s+([a-zA-Z0-9.*]+):(\d+)/' => 'NameVirtualHost {{1}}:' . $this->port,
            '/^<VirtualHost\s+([a-zA-Z0-9.*]+):(\d+)>/'   => '<VirtualHost {{1}}:' . $this->port . '>'
        ) );

        // vhosts
        foreach ( $this->getVhosts() as $vhost ) {
            Util::replaceInFile( $bearsamppRoot->getVhostsPath() . '/' . $vhost . '.conf', array(
                '/^<VirtualHost\s+([a-zA-Z0-9.*]+):(\d+)>$/' => '<VirtualHost {{1}}:' . $this->port . '>$'
            ) );
        }

        // www .htaccess
        Util::replaceInFile( $bearsamppRoot->getWwwPath() . '/.htaccess', array(
            '/(.*)http:\/\/localhost(.*)/' => '{{1}}http://localhost' . ($this->port != 80 ? ':' . $this->port : '') . '/$1 [QSA,R=301,L]',
        ) );

        return true;
    }

    /**
     * Retrieves the list of modules by merging the modules from the folder and the configuration file.
     *
     * @return array The list of modules.
     */
    public function getModules()
    {
        $fromFolder = $this->getModulesFromFolder();
        $fromConf   = $this->getModulesFromConf();
        $result     = array_merge( $fromFolder, $fromConf );
        ksort( $result );

        return $result;
    }

    /**
     * Retrieves the list of modules from the configuration file.
     *
     * @return array The list of modules from the configuration file.
     */
    public function getModulesFromConf()
    {
        $result = array();

        if ( !$this->enable ) {
            return $result;
        }

        $confContent = file( $this->getConf() );
        foreach ( $confContent as $row ) {
            $modMatch = array();
            if ( preg_match( '/^(#)?LoadModule\s*([a-z0-9_-]+)\s*"?(.*)"?/i', $row, $modMatch ) ) {
                $name = $modMatch[2];
                //$path = $modMatch[3];
                if ( !Util::startWith( $name, 'php' ) ) {
                    if ( $modMatch[1] == '#' ) {
                        $result[$name] = ActionSwitchApacheModule::SWITCH_OFF;
                    }
                    else {
                        $result[$name] = ActionSwitchApacheModule::SWITCH_ON;
                    }
                }
            }
        }

        ksort( $result );

        return $result;
    }

    /**
     * Retrieves the list of loaded modules from the configuration file.
     *
     * @return array The list of loaded modules.
     */
    public function getModulesLoaded()
    {
        $result = array();
        foreach ( $this->getModulesFromConf() as $name => $status ) {
            if ( $status == ActionSwitchApacheModule::SWITCH_ON ) {
                $result[] = $name;
            }
        }

        return $result;
    }

    /**
     * Retrieves the list of modules from the modules folder.
     *
     * @return array The list of modules from the modules folder.
     */
    private function getModulesFromFolder()
    {
        $result = array();

        if ( !$this->enable ) {
            return $result;
        }

        $handle = @opendir( $this->getModulesPath() );
        if ( !$handle ) {
            return $result;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file != '.' && $file != '..' && Util::startWith( $file, 'mod_' ) && (Util::endWith( $file, '.so' ) || Util::endWith( $file, '.dll' )) ) {
                $name          = str_replace( array('mod_', '.so', '.dll'), '', $file ) . '_module';
                $result[$name] = ActionSwitchApacheModule::SWITCH_OFF;
            }
        }

        closedir( $handle );
        ksort( $result );

        return $result;
    }

    /**
     * Retrieves the list of alias configurations.
     *
     * @return array The list of alias configurations.
     */
    public function getAlias()
    {
        global $bearsamppRoot;
        $result = array();

        $handle = @opendir( $bearsamppRoot->getAliasPath() );
        if ( !$handle ) {
            return $result;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file != '.' && $file != '..' && Util::endWith( $file, '.conf' ) ) {
                $result[] = str_replace( '.conf', '', $file );
            }
        }

        closedir( $handle );
        ksort( $result );

        return $result;
    }

    /**
     * Retrieves the list of virtual hosts configurations.
     *
     * @return array The list of virtual hosts configurations.
     */
    public function getVhosts()
    {
        global $bearsamppRoot;
        $result = array();

        $handle = @opendir( $bearsamppRoot->getVhostsPath() );
        if ( !$handle ) {
            return $result;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file != '.' && $file != '..' && Util::endWith( $file, '.conf' ) ) {
                $result[] = str_replace( '.conf', '', $file );
            }
        }

        closedir( $handle );
        ksort( $result );

        return $result;
    }

    /**
     * Retrieves the URLs of the virtual hosts.
     *
     * @return array The list of virtual hosts URLs.
     */
    public function getVhostsUrl()
    {
        global $bearsamppRoot;
        $result = array();

        foreach ( $this->getVhosts() as $vhost ) {
            $vhostContent = file( $bearsamppRoot->getVhostsPath() . '/' . $vhost . '.conf' );
            foreach ( $vhostContent as $vhostLine ) {
                $vhostLine = trim( $vhostLine );
                $enabled   = !Util::startWith( $vhostLine, '#' );
                if ( preg_match_all( '/ServerName\s+(.*)/', $vhostLine, $matches ) ) {
                    foreach ( $matches as $match ) {
                        $found = isset( $match[1] ) ? trim( $match[1] ) : trim( $match[0] );
                        if ( filter_var( 'http://' . $found, FILTER_VALIDATE_URL ) !== false ) {
                            $result[$found] = $enabled;
                            break 2;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Retrieves the list of directories in the www folder.
     *
     * @return array The list of directories in the www folder.
     */
    public function getWwwDirectories()
    {
        global $bearsamppRoot;
        $result = array();

        $handle = @opendir( $bearsamppRoot->getWwwPath() );
        if ( !$handle ) {
            return $result;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file != '.' && $file != '..' && is_dir( $bearsamppRoot->getWwwPath() . '/' . $file ) ) {
                $result[] = $file;
            }
        }

        closedir( $handle );
        ksort( $result );

        return $result;
    }

    /**
     * Executes a command line and retrieves the output.
     *
     * @param   string  $cmd  The command to execute.
     *
     * @return array The output of the command.
     */
    public function getCmdLineOutput($cmd)
    {
        $result = array(
            'syntaxOk' => false,
            'content'  => null,
        );

        if ( file_exists( $this->getExe() ) ) {
            $tmpResult = Batch::exec( 'apacheGetCmdLineOutput', '"' . $this->getExe() . '" ' . $cmd );
            if ( $tmpResult !== false && is_array( $tmpResult ) ) {
                $result['syntaxOk'] = trim( $tmpResult[count( $tmpResult ) - 1] ) == 'Syntax OK';
                if ( $result['syntaxOk'] ) {
                    unset( $tmpResult[count( $tmpResult ) - 1] );
                }
                $result['content'] = implode( PHP_EOL, $tmpResult );
            }
        }

        return $result;
    }

    /**
     * Generates the online content for the configuration file.
     *
     * @param   string|null  $version  The version of the configuration.
     *
     * @return string The online content.
     */
    private function getOnlineContent($version = null)
    {
        $version = $version != null ? $version : $this->getVersion();
        $result  = self::TAG_START_SWITCHONLINE . PHP_EOL;

        if ( Util::startWith( $version, '2.4' ) ) {
            $result .= 'Require all granted' . PHP_EOL;
        }
        else {
            $result .= 'Order Allow,Deny' . PHP_EOL .
                'Allow from all' . PHP_EOL;
        }

        return $result . self::TAG_END_SWITCHONLINE;
    }

    /**
     * Generates the offline content for the configuration file.
     *
     * @param   string|null  $version  The version of the configuration.
     *
     * @return string The offline content.
     */
    private function getOfflineContent($version = null)
    {
        $version = $version != null ? $version : $this->getVersion();
        $result  = self::TAG_START_SWITCHONLINE . PHP_EOL;

        if ( Util::startWith( $version, '2.4' ) ) {
            $result .= 'Require local' . PHP_EOL;
        }
        else {
            $result .= 'Order Deny,Allow' . PHP_EOL .
                'Deny from all' . PHP_EOL .
                'Allow from 127.0.0.1 ::1' . PHP_EOL;
        }

        return $result . self::TAG_END_SWITCHONLINE;
    }

    /**
     * Generates the required content for the configuration file based on the online status.
     *
     * @param   string|null  $version  The version of the configuration.
     *
     * @return string The required content.
     */
    private function getRequiredContent($version = null)
    {
        global $bearsamppConfig;

        return $bearsamppConfig->isOnline() ? $this->getOnlineContent( $version ) : $this->getOfflineContent( $version );
    }

    /**
     * Generates the alias content for the configuration file.
     *
     * @param   string  $name  The name of the alias.
     * @param   string  $dest  The destination path of the alias.
     *
     * @return string The alias content.
     */
    public function getAliasContent($name, $dest)
    {
        $dest = Util::formatUnixPath( $dest );

        return 'Alias /' . $name . ' "' . $dest . '"' . PHP_EOL . PHP_EOL .
            '<Directory "' . $dest . '">' . PHP_EOL .
            '    Options Indexes FollowSymLinks MultiViews' . PHP_EOL .
            '    AllowOverride all' . PHP_EOL .
            $this->getRequiredContent() . PHP_EOL .
            '</Directory>' . PHP_EOL;
    }

    /**
     * Generates the virtual host content for the configuration file.
     *
     * @param   string  $serverName    The server name of the virtual host.
     * @param   string  $documentRoot  The document root of the virtual host.
     *
     * @return string The virtual host content.
     */
    public function getVhostContent($serverName, $documentRoot)
    {
        global $bearsamppRoot;

        $documentRoot = Util::formatUnixPath( $documentRoot );

        return '<VirtualHost *:' . $this->getPort() . '>' . PHP_EOL .
            '    ServerAdmin webmaster@' . $serverName . PHP_EOL .
            '    DocumentRoot "' . $documentRoot . '"' . PHP_EOL .
            '    ServerName ' . $serverName . PHP_EOL .
            '    ErrorLog "' . $bearsamppRoot->getLogsPath() . '/' . $serverName . '_error.log"' . PHP_EOL .
            '    CustomLog "' . $bearsamppRoot->getLogsPath() . '/' . $serverName . '_access.log" combined' . PHP_EOL . PHP_EOL .
            '    <Directory "' . $documentRoot . '">' . PHP_EOL .
            '        Options Indexes FollowSymLinks MultiViews' . PHP_EOL .
            '        AllowOverride all' . PHP_EOL .
            $this->getRequiredContent() . PHP_EOL .
            '    </Directory>' . PHP_EOL .
            '</VirtualHost>' . PHP_EOL . PHP_EOL .
            '<IfModule ssl_module>' . PHP_EOL .
            '<VirtualHost *:' . $this->getSslPort() . '> #SSL' . PHP_EOL .
            '    DocumentRoot "' . $documentRoot . '"' . PHP_EOL .
            '    ServerName ' . $serverName . PHP_EOL .
            '    ServerAdmin webmaster@' . $serverName . PHP_EOL .
            '    ErrorLog "' . $bearsamppRoot->getLogsPath() . '/' . $serverName . '_error.log"' . PHP_EOL .
            '    TransferLog "' . $bearsamppRoot->getLogsPath() . '/' . $serverName . '_access.log"' . PHP_EOL . PHP_EOL .
            '    SSLEngine on' . PHP_EOL .
            '    SSLProtocol all -SSLv2' . PHP_EOL .
            '    SSLCipherSuite HIGH:MEDIUM:!aNULL:!MD5' . PHP_EOL .
            '    SSLCertificateFile "' . $bearsamppRoot->getSslPath() . '/' . $serverName . '.crt"' . PHP_EOL .
            '    SSLCertificateKeyFile "' . $bearsamppRoot->getSslPath() . '/' . $serverName . '.pub"' . PHP_EOL .
            '    BrowserMatch "MSIE [2-5]" nokeepalive ssl-unclean-shutdown downgrade-1.0 force-response-1.0' . PHP_EOL .
            '    CustomLog "' . $bearsamppRoot->getLogsPath() . '/' . $serverName . '_sslreq.log" "%t %h %{SSL_PROTOCOL}x %{SSL_CIPHER}x \"%r\" %b"' . PHP_EOL . PHP_EOL .
            '    <Directory "' . $documentRoot . '">' . PHP_EOL .
            '        SSLOptions +StdEnvVars' . PHP_EOL .
            '        Options Indexes FollowSymLinks MultiViews' . PHP_EOL .
            '        AllowOverride all' . PHP_EOL .
            $this->getRequiredContent() . PHP_EOL .
            '    </Directory>' . PHP_EOL .
            '</VirtualHost>' . PHP_EOL .
            '</IfModule>' . PHP_EOL;
    }

    /**
     * Refreshes the configuration file to switch between online and offline modes.
     *
     * @param   bool  $putOnline  Whether to put the configuration online.
     */
    public function refreshConf($putOnline)
    {
        if ( !$this->enable ) {
            return;
        }

        $onlineContent  = $this->getOnlineContent();
        $offlineContent = $this->getOfflineContent();

        $conf = file_get_contents( $this->getConf() );
        Util::logTrace( 'refreshConf ' . $this->getConf() );
        preg_match( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $conf, $matches );
        Util::logTrace( isset( $matches[1] ) ? print_r( $matches[1], true ) : 'N/A' );

        if ( $putOnline ) {
            $conf = preg_replace( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $onlineContent, $conf, -1, $count );
        }
        else {
            $conf = preg_replace( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $offlineContent, $conf, -1, $count );
        }
        file_put_contents( $this->getConf(), $conf );
        Util::logDebug( 'Refresh ' . $this->getConf() . ': ' . $count . ' occurrence(s) replaced' );

        $sslConf = file_get_contents( $this->getSslConf() );
        Util::logTrace( 'refreshConf ' . $this->getSslConf() );
        preg_match( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $sslConf, $matches );
        Util::logTrace( isset( $matches[1] ) ? print_r( $matches[1], true ) : 'N/A' );

        if ( $putOnline ) {
            $sslConf = preg_replace( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $onlineContent, $sslConf, -1, $count );
        }
        else {
            $sslConf = preg_replace( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $offlineContent, $sslConf, -1, $count );
        }
        file_put_contents( $this->getSslConf(), $sslConf );
        Util::logDebug( 'Refresh ' . $this->getSslConf() . ': ' . $count . ' occurrence(s) replaced' );
    }

    /**
     * Refreshes the alias configurations to switch between online and offline modes.
     *
     * @param   bool  $putOnline  Whether to put the alias configurations online.
     */
    public function refreshAlias($putOnline)
    {
        global $bearsamppRoot, $bearsamppHomepage;

        if ( !$this->enable ) {
            return;
        }

        $onlineContent  = $this->getOnlineContent();
        $offlineContent = $this->getOfflineContent();

        foreach ( $this->getAlias() as $alias ) {
            $aliasConf = file_get_contents( $bearsamppRoot->getAliasPath() . '/' . $alias . '.conf' );
            Util::logTrace( 'refreshAlias ' . $bearsamppRoot->getAliasPath() . '/' . $alias . '.conf' );
            preg_match( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $aliasConf, $matches );
            Util::logTrace( isset( $matches[1] ) ? print_r( $matches[1], true ) : 'N/A' );

            if ( $putOnline ) {
                $aliasConf = preg_replace( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $onlineContent, $aliasConf, -1, $count );
            }
            else {
                $aliasConf = preg_replace( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $offlineContent, $aliasConf, -1, $count );
            }
            file_put_contents( $bearsamppRoot->getAliasPath() . '/' . $alias . '.conf', $aliasConf );
            Util::logDebug( 'Refresh ' . $bearsamppRoot->getAliasPath() . '/' . $alias . '.conf: ' . $count . ' occurrence(s) replaced' );
        }

        // Homepage
        $bearsamppHomepage->refreshAliasContent();
    }

    /**
     * Refreshes the virtual host configurations to switch between online and offline modes.
     *
     * @param   bool  $putOnline  Whether to put the virtual host configurations online.
     */
    public function refreshVhosts($putOnline)
    {
        global $bearsamppRoot;

        if ( !$this->enable ) {
            return;
        }

        $onlineContent  = $this->getOnlineContent();
        $offlineContent = $this->getOfflineContent();

        foreach ( $this->getVhosts() as $vhost ) {
            $vhostConf = file_get_contents( $bearsamppRoot->getVhostsPath() . '/' . $vhost . '.conf' );
            Util::logTrace( 'refreshVhost ' . $bearsamppRoot->getVhostsPath() . '/' . $vhost . '.conf' );
            preg_match( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $vhostConf, $matches );
            Util::logTrace( isset( $matches[1] ) ? print_r( $matches[1], true ) : 'N/A' );

            if ( $putOnline ) {
                $vhostConf = preg_replace( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $onlineContent, $vhostConf, -1, $count );
            }
            else {
                $vhostConf = preg_replace( '/' . self::TAG_START_SWITCHONLINE . '(.*?)' . self::TAG_END_SWITCHONLINE . '/s', $offlineContent, $vhostConf, -1, $count );
            }
            file_put_contents( $bearsamppRoot->getVhostsPath() . '/' . $vhost . '.conf', $vhostConf );
            Util::logDebug( 'Refresh ' . $bearsamppRoot->getVhostsPath() . '/' . $vhost . '.conf: ' . $count . ' occurrence(s) replaced' );
        }
    }

    /**
     * Sets the enable status of the module.
     *
     * This method enables or disables the module based on the provided parameter.
     * If enabling the module and the current path does not exist, it logs an error
     * and optionally shows an error message window.
     *
     * @param   bool  $enabled     Whether to enable or disable the module.
     * @param   bool  $showWindow  Whether to show an error message window if enabling fails.
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
     * Sets the version of the module.
     *
     * This method updates the version of the module and reloads its configuration.
     *
     * @param   string  $version  The version to set for the module.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    /**
     * Gets the service associated with the module.
     *
     * @return string The service associated with the module.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Gets the path to the modules.
     *
     * @return string The path to the modules.
     */
    public function getModulesPath()
    {
        return $this->modulesPath;
    }

    /**
     * Gets the SSL configuration file path.
     *
     * @return string The SSL configuration file path.
     */
    public function getSslConf()
    {
        return $this->sslConf;
    }

    /**
     * Gets the access log file path.
     *
     * @return string The access log file path.
     */
    public function getAccessLog()
    {
        return $this->accessLog;
    }

    /**
     * Gets the rewrite log file path.
     *
     * @return string The rewrite log file path.
     */
    public function getRewriteLog()
    {
        return $this->rewriteLog;
    }

    /**
     * Gets the error log file path.
     *
     * @return string The error log file path.
     */
    public function getErrorLog()
    {
        return $this->errorLog;
    }

    /**
     * Gets the executable file path.
     *
     * @return string The executable file path.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Gets the configuration file path.
     *
     * @return string The configuration file path.
     */
    public function getConf()
    {
        return $this->conf;
    }

    /**
     * Gets the port number.
     *
     * @return int The port number.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the port number.
     *
     * This method updates the port number in the configuration.
     *
     * @param   int  $port  The port number to set.
     */
    public function setPort($port)
    {
        $this->replace( self::LOCAL_CFG_PORT, $port );
    }

    /**
     * Gets the SSL port number.
     *
     * @return int The SSL port number.
     */
    public function getSslPort()
    {
        return $this->sslPort;
    }

    /**
     * Sets the SSL port number.
     *
     * This method updates the SSL port number in the configuration.
     *
     * @param   int  $sslPort  The SSL port number to set.
     */
    public function setSslPort($sslPort)
    {
        $this->replace( self::LOCAL_CFG_SSL_PORT, $sslPort );
    }

    /**
     * Gets the OpenSSL executable file path.
     *
     * @return string The OpenSSL executable file path.
     */
    public function getOpensslExe()
    {
        return $this->opensslExe;
    }
}
