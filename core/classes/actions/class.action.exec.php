<?php

class ActionExec
{
    const QUIT = 'quit';
    const RESTART = 'restart';

    public function __construct($args)
    {
        global $bearsamppCore;

        if (file_exists($bearsamppCore->getExec())) {
            $action = file_get_contents($bearsamppCore->getExec());
            if ($action == self::QUIT) {
                Batch::exitApp();
            } elseif ($action == self::RESTART) {
                Batch::restartApp();
            }
            @unlink($bearsamppCore->getExec());
        }
    }
}
