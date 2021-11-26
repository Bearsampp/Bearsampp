<?php

class ActionClearFolders
{
    public function __construct($args)
    {
        global $bearsamppBs, $bearsamppCore;

        Util::clearFolder($bearsamppBs->getTmpPath(), array('cachegrind', 'composer', 'openssl', 'mailhog', 'npm-cache', 'pip', 'yarn'));
        Util::clearFolder($bearsamppCore->getTmpPath());
    }
}
