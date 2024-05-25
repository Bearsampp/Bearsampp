<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class ActionRefreshReposStartup
{
    public function __construct($args)
    {
        global $bearsamppTools;

        if (isset($args[0]) && !empty($args[0]) && isset($args[1])) {
            if ($args[0] == ActionRefreshRepos::GIT) {
                $bearsamppTools->getGit()->setScanStartup($args[1]);
            }
        }
    }
}
