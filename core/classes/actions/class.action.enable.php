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
 * Class ActionEnable
 *
 * This class is responsible for enabling various services (Apache, PHP, MySQL, etc.) based on the provided arguments.
 */
class ActionEnable
{
    /**
     * Constructor for the ActionEnable class.
     *
     * @param   array  $args          An array of arguments where the first element is the service name and the second element is the enable flag.
     *
     * @global object  $bearsamppBins Global object containing instances of various services.
     */
    public function __construct($args)
    {
        global $bearsamppBins;

        if ( isset( $args[0] ) && !empty( $args[0] ) && isset( $args[1] ) ) {
            Util::startLoading();
            if ( $args[0] == $bearsamppBins->getApache()->getName() ) {
                $bearsamppBins->getApache()->setEnable( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getPhp()->getName() ) {
                $bearsamppBins->getPhp()->setEnable( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getMysql()->getName() ) {
                $bearsamppBins->getMysql()->setEnable( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getMariadb()->getName() ) {
                $bearsamppBins->getMariadb()->setEnable( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getNodejs()->getName() ) {
                $bearsamppBins->getNodejs()->setEnable( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getPostgresql()->getName() ) {
                $bearsamppBins->getPostgresql()->setEnable( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getMailpit()->getName() ) {
                $bearsamppBins->getMailpit()->setEnable( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getMemcached()->getName() ) {
                $bearsamppBins->getMemcached()->setEnable( $args[1], true );
            }
            elseif ( $args[0] == $bearsamppBins->getXlight()->getName() ) {
                $bearsamppBins->getXlight()->setEnable( $args[1], true );
            }
        }
    }
}
