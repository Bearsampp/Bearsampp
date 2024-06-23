<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionCheckPort
 *
 * This class is responsible for checking the port status of various services (Apache, MySQL, MariaDB, PostgreSQL, Filezilla, Mailhog, Memcached, Xlight)
 * based on the provided arguments.
 */
class ActionCheckPort
{
    /**
     * Constructor for ActionCheckPort.
     *
     * @param   array  $args          An array of arguments where:
     *                                - $args[0] is the name of the service (e.g., Apache, MySQL).
     *                                - $args[1] is the port number to check.
     *                                - $args[2] (optional) indicates if SSL should be used.
     *
     * @global object  $bearsamppBins Global object containing instances of various services.
     */
    public function __construct($args)
    {
        global $bearsamppBins;

        // Check if the required arguments are provided and not empty
        if ( isset( $args[0] ) && !empty( $args[0] ) && isset( $args[1] ) && !empty( $args[1] ) ) {
            // Determine if SSL is to be used
            $ssl = isset( $args[2] ) && !empty( $args[2] );

            // Check the port for the specified service
            if ( $args[0] == $bearsamppBins->getApache()->getName() ) {
                $bearsamppBins->getApache()->checkPort( $args[1], $ssl, true );
            }
            elseif ( $args[0] == $bearsamppBins->getMysql()->getName() ) {
                $bearsamppBins->getMysql()->checkPort( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getMariadb()->getName() ) {
                $bearsamppBins->getMariadb()->checkPort( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getPostgresql()->getName() ) {
                $bearsamppBins->getPostgresql()->checkPort( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getFilezilla()->getName() ) {
                $bearsamppBins->getFilezilla()->checkPort( $args[1], $ssl, true );
            }
            elseif ( $args[0] == $bearsamppBins->getMailhog()->getName() ) {
                $bearsamppBins->getMailhog()->checkPort( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getMemcached()->getName() ) {
                $bearsamppBins->getMemcached()->checkPort( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getXlight()->getName() ) {
                $bearsamppBins->getXlight()->checkPort( $args[1], true );
            }
        }
    }
}
