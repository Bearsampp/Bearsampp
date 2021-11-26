<?php

class ActionLaunchStartup
{
    public function __construct($args)
    {
        global $bearsamppConfig;
        
        if (isset($args[0])) {
            Util::startLoading();
            $launchStartup = $args[0] == Config::ENABLED;
            if ($launchStartup) {
                Util::enableLaunchStartup();
            } else {
                Util::disableLaunchStartup();
            }
            $bearsamppConfig->replace(Config::CFG_LAUNCH_STARTUP, $args[0]);
        }
    }
}
