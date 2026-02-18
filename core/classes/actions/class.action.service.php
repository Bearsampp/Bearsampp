<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionService
 * Handles various actions related to services such as creating, starting, stopping, restarting, installing, and removing.
 */
class ActionService
{
    const CREATE = 'create';
    const START = 'start';
    const STOP = 'stop';
    const RESTART = 'restart';

    const INSTALL = 'install';
    const REMOVE = 'remove';

    /**
     * ActionService constructor.
     * Initializes the service action based on provided arguments.
     *
     * @param   array  $args  Arguments for the service action.
     */
    public function __construct($args)
    {
        global $bearsamppBins;
        Util::startLoading();

        // Reload bins
        $bearsamppBins->reload();

        if ( isset( $args[0] ) && !empty( $args[0] ) && isset( $args[1] ) && !empty( $args[1] ) ) {
            $sName          = $args[0];
            $bin            = null;
            $port           = 0;
            $syntaxCheckCmd = null;

            if ( $sName == BinMailpit::SERVICE_NAME ) {
                $bin  = $bearsamppBins->getMailpit();
                $port = $bin->getSmtpPort();
            }
            elseif ( $sName == BinMemcached::SERVICE_NAME ) {
                $bin  = $bearsamppBins->getMemcached();
                $port = $bin->getPort();
            }
            elseif ( $sName == BinApache::SERVICE_NAME ) {
                $bin            = $bearsamppBins->getApache();
                $port           = $bin->getPort();
                $syntaxCheckCmd = BinApache::CMD_SYNTAX_CHECK;
            }
            elseif ( $sName == BinMysql::SERVICE_NAME ) {
                $bin            = $bearsamppBins->getMysql();
                $port           = $bin->getPort();
                $syntaxCheckCmd = BinMysql::CMD_SYNTAX_CHECK;
            }
            elseif ( $sName == BinMariadb::SERVICE_NAME ) {
                $bin            = $bearsamppBins->getMariadb();
                $port           = $bin->getPort();
                $syntaxCheckCmd = BinMariadb::CMD_SYNTAX_CHECK;
            }
            elseif ( $sName == BinPostgresql::SERVICE_NAME ) {
                $bin  = $bearsamppBins->getPostgresql();
                $port = $bin->getPort();
            }
            elseif ( $sName == BinXlight::SERVICE_NAME ) {
                $bin  = $bearsamppBins->getXlight();
                $port = $bin->getPort();
            }

            $name    = $bin->getName();
            $service = $bin->getService();

            if ( !empty( $service ) && $service instanceof Win32Service ) {
                if ( $args[1] == self::CREATE ) {
                    $this->create( $service );
                }
                elseif ( $args[1] == self::START ) {
                    $this->start( $bin, $syntaxCheckCmd );
                }
                elseif ( $args[1] == self::STOP ) {
                    $this->stop( $service );
                }
                elseif ( $args[1] == self::RESTART ) {
                    $this->restart( $bin, $syntaxCheckCmd );
                }
                elseif ( $args[1] == self::INSTALL ) {
                    if ( !empty( $port ) ) {
                        $this->install( $bin, $port, $syntaxCheckCmd );
                    }
                }
                elseif ( $args[1] == self::REMOVE ) {
                    $this->remove( $service, $name );
                }
            }
        }

        Util::stopLoading();
    }

    /**
     * Creates a service.
     *
     * @param   Win32Service  $service  The service to create.
     */
    private function create($service)
    {
        $service->create();
    }

    /**
     * Starts a service.
     *
     * @param   mixed        $bin             The binary object of the service.
     * @param   string|null  $syntaxCheckCmd  The command to check syntax, if applicable.
     */
    private function start($bin, $syntaxCheckCmd)
    {
        // Update loading screen to show which service is starting
        Util::updateLoadingText('Starting ' . $bin->getName() . '...');
        
        Util::startService( $bin, $syntaxCheckCmd, true );
    }

    /**
     * Stops a service.
     *
     * @param   Win32Service  $service  The service to stop.
     */
    private function stop($service)
    {
        // Update loading screen to show which service is stopping
        Util::updateLoadingText('Stopping ' . $service->getName() . '...');
        
        $service->stop();
    }

    /**
     * Restarts a service.
     *
     * @param   mixed        $bin             The binary object of the service.
     * @param   string|null  $syntaxCheckCmd  The command to check syntax, if applicable.
     */
    private function restart($bin, $syntaxCheckCmd)
    {
        // Update loading screen to show service is restarting
        Util::updateLoadingText('Restarting ' . $bin->getName() . '...');
        
        if ( $bin->getService()->stop() ) {
            $this->start( $bin, $syntaxCheckCmd );
        }
    }

    /**
     * Installs a service.
     *
     * @param   mixed        $bin             The binary object of the service.
     * @param   int          $port            The port number for the service.
     * @param   string|null  $syntaxCheckCmd  The command to check syntax, if applicable.
     */
    private function install($bin, $port, $syntaxCheckCmd)
    {
        Util::installService( $bin, $port, $syntaxCheckCmd, true );
    }

    /**
     * Removes a service.
     *
     * @param   Win32Service  $service  The service to remove.
     * @param   string        $name     The name of the service.
     */
    private function remove($service, $name)
    {
        Util::removeService( $service, $name );
    }
}
