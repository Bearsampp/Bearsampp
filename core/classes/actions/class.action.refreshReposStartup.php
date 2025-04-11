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
 * Class ActionRefreshReposStartup
 *
 * This class is responsible for handling the action of setting the scan startup for repositories.
 */
class ActionRefreshReposStartup
{
    /**
     * ActionRefreshReposStartup constructor.
     *
     * This constructor initializes the action to set the scan startup for repositories based on the provided arguments.
     *
     * @param array $args An array of arguments where the first argument should be 'git' to trigger the git repository scan startup setting,
     *                    and the second argument is the value to set for the scan startup.
     */
    public function __construct($args)
    {
        // Global variable for accessing bearsamppTools
        global $bearsamppTools;

        // Check if the first and second arguments are set and not empty
        if (isset($args[0]) && !empty($args[0]) && isset($args[1])) {
            // If the first argument is 'git', set the scan startup value
            if ($args[0] == ActionRefreshRepos::GIT) {
                $bearsamppTools->getGit()->setScanStartup($args[1]);
            }
        }
    }
}
