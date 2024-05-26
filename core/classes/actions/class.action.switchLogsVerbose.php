<?php

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
