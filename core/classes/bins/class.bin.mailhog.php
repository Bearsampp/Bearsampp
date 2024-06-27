<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class BinMailhog
 *
 * This class represents the Mailhog module in the Bearsampp application.
 * It handles the configuration, initialization, and management of the Mailhog service.
 */
class BinMailhog extends Module
{
    const SERVICE_NAME = 'bearsamppmailhog';
    const SERVICE_PARAMS = '-hostname localhost -api-bind-addr 127.0.0.1:%d -ui-bind-addr 127.0.0.1:%d -smtp-bind-addr 127.0.0.1:%d -storage maildir -maildir-path "%s"';

    const ROOT_CFG_ENABLE = 'mailhogEnable';
    const ROOT_CFG_VERSION = 'mailhogVersion';

    const LOCAL_CFG_EXE = 'mailhogExe';
    const LOCAL_CFG_API_PORT = 'mailhogApiPort';
    const LOCAL_CFG_UI_PORT = 'mailhogUiPort';
    const LOCAL_CFG_SMTP_PORT = 'mailhogSmtpPort';

    private $service;
    private $log;

    private $exe;
    private $apiPort;
    private $uiPort;
    private $smtpPort;
    private $mailPath;

    /**
     * Constructs a BinMailhog object and initializes the module.
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

        $this->name    = $bearsamppLang->getValue( Lang::MAILHOG );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        $this->enable   = $this->enable && $bearsamppConfig->getRaw( self::ROOT_CFG_ENABLE );
        $this->service  = new Win32Service( self::SERVICE_NAME );
        $this->mailPath = $bearsamppRoot->getTmpPath() . '/mailhog';
        $this->log      = $bearsamppRoot->getLogsPath() . '/mailhog.log';

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe      = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->apiPort  = intval( $this->bearsamppConfRaw[self::LOCAL_CFG_API_PORT] );
            $this->uiPort   = intval( $this->bearsamppConfRaw[self::LOCAL_CFG_UI_PORT] );
            $this->smtpPort = intval( $this->bearsamppConfRaw[self::LOCAL_CFG_SMTP_PORT] );
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
        if ( empty( $this->apiPort ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_API_PORT, $this->apiPort ) );

            return;
        }
        if ( empty( $this->uiPort ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_UI_PORT, $this->uiPort ) );

            return;
        }
        if ( empty( $this->smtpPort ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_SMTP_PORT, $this->smtpPort ) );

            return;
        }

        $nssm = new Nssm( self::SERVICE_NAME );
        $nssm->setDisplayName( APP_TITLE . ' ' . $this->getName() );
        $nssm->setBinPath( $this->exe );
        $nssm->setParams( sprintf( self::SERVICE_PARAMS, $this->apiPort, $this->uiPort, $this->smtpPort, $this->mailPath ) );
        $nssm->setStart( Nssm::SERVICE_DEMAND_START );
        $nssm->setStdout( $bearsamppRoot->getLogsPath() . '/mailhog.out.log' );
        $nssm->setStderr( $bearsamppRoot->getLogsPath() . '/mailhog.err.log' );

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
                case self::LOCAL_CFG_API_PORT:
                    $this->apiPort = intval( $value );
                    break;
                case self::LOCAL_CFG_UI_PORT:
                    $this->uiPort = intval( $value );
                    break;
                case self::LOCAL_CFG_SMTP_PORT:
                    $this->smtpPort = intval( $value );
                    break;
            }
        }

        file_put_contents( $this->bearsamppConf, $content );
    }

    /**
     * Rebuilds the configuration in the Windows Registry.
     *
     * @return bool True if the configuration was successfully rebuilt, false otherwise.
     */
    public function rebuildConf()
    {
        global $bearsamppRegistry;

        $exists = $bearsamppRegistry->exists(
            Registry::HKEY_LOCAL_MACHINE,
            'SYSTEM\CurrentControlSet\Services\\' . self::SERVICE_NAME . '\Parameters',
            Nssm::INFO_APP_PARAMETERS
        );
        if ( $exists ) {
            return $bearsamppRegistry->setExpandStringValue(
                Registry::HKEY_LOCAL_MACHINE,
                'SYSTEM\CurrentControlSet\Services\\' . self::SERVICE_NAME . '\Parameters',
                Nssm::INFO_APP_PARAMETERS,
                sprintf( self::SERVICE_PARAMS, $this->apiPort, $this->uiPort, $this->smtpPort, $this->mailPath )
            );
        }

        return false;
    }

    /**
     * Changes the SMTP port for the Mailhog service.
     *
     * @param   int    $port           The new port number.
     * @param   bool   $checkUsed      Whether to check if the port is already in use.
     * @param   mixed  $wbProgressBar  The progress bar object for UI updates.
     *
     * @return bool|int True if the port was successfully changed, or the process using the port if in use.
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
            $this->setSmtpPort( $port );
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
     * Checks if a specific port is used by the Mailhog service.
     *
     * @param   int   $port        The port number to check.
     * @param   bool  $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the port is used by Mailhog, false otherwise.
     */
    public function checkPort($port, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::CHECK_PORT_TITLE ), $this->getName(), $port );

        if ( !Util::isValidPort( $port ) ) {
            Util::logError( $this->getName() . ' port not valid: ' . $port );

            return false;
        }

        $headers = Util::getHeaders( '127.0.0.1', $port );
        if ( !empty( $headers ) ) {
            if ( Util::contains( $headers[0], 'MailHog' ) ) {
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
     * Switches the version of the Mailhog service.
     *
     * @param   string  $version     The version to switch to.
     * @param   bool    $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the version was successfully switched, false otherwise.
     */
    public function switchVersion($version, $showWindow = false)
    {
        Util::logDebug( 'Switch ' . $this->name . ' version to ' . $version );

        return $this->updateConfig( $version, 0, $showWindow );
    }

    /**
     * Updates the configuration of the Mailhog service.
     *
     * @param   string|null  $version     The version to update to. If null, the current version is used.
     * @param   int          $sub         The sub-level for logging indentation.
     * @param   bool         $showWindow  Whether to show a message box with the result.
     *
     * @return bool True if the configuration was successfully updated, false otherwise.
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

        $bearsamppConf = str_replace( 'mailhog' . $this->getVersion(), 'mailhog' . $version, $this->bearsamppConf );
        if ( !file_exists( $bearsamppConf ) ) {
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

        return true;
    }

    /**
     * Sets the version of the Mailhog service.
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
     * Gets the Win32Service object for the Mailhog service.
     *
     * @return Win32Service The Win32Service object.
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Enables or disables the Mailhog service.
     *
     * @param   bool  $enabled     Whether to enable or disable the service.
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
            Util::installService( $this, $this->smtpPort, null, $showWindow );
        }
        else {
            Util::removeService( $this->service, $this->name );
        }
    }

    /**
     * Gets the log file path for the Mailhog service.
     *
     * @return string The log file path.
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Gets the executable file path for the Mailhog service.
     *
     * @return string The executable file path.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Gets the API port for the Mailhog service.
     *
     * @return int The API port.
     */
    public function getApiPort()
    {
        return $this->apiPort;
    }

    /**
     * Sets the API port for the Mailhog service.
     *
     * @param   int  $apiPort  The new API port.
     */
    public function setApiPort($apiPort)
    {
        $this->replace( self::LOCAL_CFG_API_PORT, $apiPort );
    }

    /**
     * Gets the UI port for the Mailhog service.
     *
     * @return int The UI port.
     */
    public function getUiPort()
    {
        return $this->uiPort;
    }

    /**
     * Sets the UI port for the Mailhog service.
     *
     * @param   int  $uiPort  The new UI port.
     */
    public function setUiPort($uiPort)
    {
        $this->replace( self::LOCAL_CFG_UI_PORT, $uiPort );
    }

    /**
     * Gets the SMTP port for the Mailhog service.
     *
     * @return int The SMTP port.
     */
    public function getSmtpPort()
    {
        return $this->smtpPort;
    }

    /**
     * Sets the SMTP port for the Mailhog service.
     *
     * @param   int  $smtpPort  The new SMTP port.
     */
    public function setSmtpPort($smtpPort)
    {
        $this->replace( self::LOCAL_CFG_SMTP_PORT, $smtpPort );
    }

    /**
     * Gets the mail directory path for the Mailhog service.
     *
     * This method returns the path to the directory where Mailhog stores its mail data.
     * The path is typically set during the initialization or reloading of the module
     * and is based on the application's root temporary path.
     *
     * @return string The mail directory path.
     */
    public function getMailPath()
    {
        return $this->mailPath;
    }
}
