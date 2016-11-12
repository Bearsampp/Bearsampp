<?php

class ActionClearFolders
{
    public function __construct($args)
    {
        global $neardBs, $neardCore;
        
        Util::clearFolder($neardBs->getTmpPath(), array('cachegrind', 'npm-cache', 'drush', 'openssl', 'wp-cli', 'mailhog', 'pip'));
        Util::clearFolder($neardCore->getTmpPath());
    }

}
