<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionRefreshRepos
 *
 * This class is responsible for handling the action of refreshing repositories.
 */
class ActionRefreshRepos
{
    /**
     * Constant representing the 'git' command.
     */
    const GIT = 'git';

    /**
     * ActionRefreshRepos constructor.
     *
     * This constructor initializes the action to refresh repositories based on the provided arguments.
     *
     * @param array $args An array of arguments where the first argument can be 'git' to trigger the git repository refresh.
     */
    public function __construct($args)
    {
        // Global variable for accessing bearsamppTools
        global $bearsamppTools;

        // Start the loading process
        Util::startLoading();

        // Check if the first argument is set and not empty
        if (isset($args[0]) && !empty($args[0])) {
            // If the first argument is 'git', trigger the git repository refresh
            if ($args[0] == self::GIT) {
                $bearsamppTools->getGit()->findRepos(false);
            }
        }
    }
}
