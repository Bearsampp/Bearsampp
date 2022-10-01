<?php

class ActionClearFolders
{
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppCore;

        Util::clearFolder($bearsamppRoot->getTmpPath(), array('cachegrind', 'composer', 'openssl', 'mailhog', 'npm-cache', 'pip', 'yarn'));
        Util::clearFolder($bearsamppCore->getTmpPath());
    }
}
