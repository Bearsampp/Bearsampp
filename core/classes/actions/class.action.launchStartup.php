<?php

/**
 * Initializes the ActionLaunchStartup class and manages the application's launch startup settings.
 *
 * This constructor uses the provided arguments to determine whether to enable or disable the application's
 * launch at startup feature. It starts the loading process, updates the launch startup configuration based on
 * the provided argument, and modifies the system's startup settings accordingly.
 *
 * @param array $args An array of arguments where the first element should be either Config::ENABLED or Config::DISABLED
 *                    to indicate the desired launch startup setting.
 */
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
