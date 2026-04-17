<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
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
            $bin = $bearsamppBins->getBinByName($args[0]);
            if ($bin !== null) {
                $bin->setEnable($args[1], true);
            }
        }
    }
}
