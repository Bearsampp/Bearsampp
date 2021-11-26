<?php

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
