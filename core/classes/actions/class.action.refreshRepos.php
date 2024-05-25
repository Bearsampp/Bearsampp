<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class ActionRefreshRepos
{
    const GIT = 'git';

    public function __construct($args)
    {
        global $bearsamppTools;

        Util::startLoading();
        if (isset($args[0]) && !empty($args[0])) {
            if ($args[0] == self::GIT) {
                $bearsamppTools->getGit()->findRepos(false);
            }
        }
    }
}
