<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionSwitchApacheModule
 *
 * This class is responsible for enabling or disabling Apache modules by modifying the Apache configuration file.
 */
class ActionSwitchApacheModule
{
    const SWITCH_ON = 'on';
    const SWITCH_OFF = 'off';

    /**
     * ActionSwitchApacheModule constructor.
     *
     * @param array $args An array containing the module name and the action (either 'on' or 'off').
     */
    public function __construct($args)
    {
        global $bearsamppBins;

        if (isset($args[0]) && !empty($args[0]) && isset($args[1]) && !empty($args[1])) {
            $onContent = 'LoadModule ' . $args[0];
            $offContent = '#LoadModule ' . $args[0];

            $httpdContent = file_get_contents($bearsamppBins->getApache()->getConf());
            if ($args[1] == self::SWITCH_ON) {
                $httpdContent = str_replace($offContent, $onContent, $httpdContent);
            } elseif ($args[1] == self::SWITCH_OFF) {
                $httpdContent = str_replace($onContent, $offContent, $httpdContent);
            }

            file_put_contents($bearsamppBins->getApache()->getConf(), $httpdContent);
        }
    }
}
