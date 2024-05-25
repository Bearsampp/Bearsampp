<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class ActionSwitchLogsVerbose
{
    public function __construct($args)
    {
        global $bearsamppConfig;

        if (isset($args[0]) && is_numeric($args[0]) && $args[0] >= 0 && $args[0] <= 3) {
            $bearsamppConfig->replace(Config::CFG_LOGS_VERBOSE, $args[0]);
        }
    }
}
