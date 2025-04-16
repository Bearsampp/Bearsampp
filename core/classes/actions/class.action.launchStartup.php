<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionLaunchStartup
 *
 * This class manages the application's launch startup settings.
 * It initializes the settings based on the provided arguments and updates the configuration accordingly.
 */
class ActionLaunchStartup
{
    /**
     * ActionLaunchStartup constructor.
     *
     * This constructor uses the provided arguments to determine whether to enable or disable the application's
     * launch at startup feature. It starts the loading process, updates the launch startup configuration based on
     * the provided argument, and modifies the system's startup settings accordingly.
     *
     * @param array $args An array of arguments where the first element should be either Config::ENABLED or Config::DISABLED
     *                    to indicate the desired launch startup setting.
     */
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
