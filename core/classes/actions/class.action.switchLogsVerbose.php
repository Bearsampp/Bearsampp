<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionSwitchLogsVerbose
 *
 * This class handles the action of switching the verbosity level of logs.
 */
class ActionSwitchLogsVerbose
{
    /**
     * ActionSwitchLogsVerbose constructor.
     *
     * @param array $args An array of arguments where the first element should be the verbosity level (0-3).
     */
    public function __construct($args)
    {
        global $bearsamppConfig;

        // Check if the first argument is set, is numeric, and within the valid range (0-3)
        if (isset($args[0]) && is_numeric($args[0]) && $args[0] >= 0 && $args[0] <= 3) {
            // Replace the current verbosity level in the configuration
            $bearsamppConfig->replace(Config::CFG_LOGS_VERBOSE, $args[0]);
        }
    }
}
