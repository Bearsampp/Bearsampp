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
 * Class ActionExec
 *
 * This class handles the execution of specific actions based on the content of a file.
 * The actions include quitting the application or reloading it. The actions are read
 * from a file whose path is provided by the global `$bearsamppCore` object.
 */
class ActionExec
{
    /**
     * Constant representing the 'quit' action.
     */
    const QUIT = 'quit';

    /**
     * Constant representing the 'reload' action.
     */
    const RESTART = 'restart';

    /**
     * ActionExec constructor.
     *
     * This constructor reads the action from a file specified by `$bearsamppCore->getExec()`.
     * If the action is 'quit', it calls `Batch::exitApp()`. If the action is 'restart', it calls
     * `Batch::restartApp()`. After executing the action, it deletes the action file.
     *
     * @param array $args Arguments passed to the constructor (not used in the current implementation).
     */
    public function __construct($args)
    {
        global $bearsamppCore;

        Util::logTrace('ActionExec constructor called');

        $execFile = $bearsamppCore->getExec();
        Util::logTrace('Checking for exec file: ' . $execFile);

        if (file_exists($execFile)) {
            Util::logTrace('Exec file exists');

            $action = file_get_contents($execFile);
            Util::logTrace('Action read from exec file: "' . $action . '"');

            if ($action == self::QUIT) {
                Util::logTrace('Executing quit action');
                Batch::exitApp();
            } elseif ($action == self::RESTART) {
                Util::logTrace('Executing restart action');
                Batch::restartApp();
            } else {
                Util::logTrace('Unknown action: "' . $action . '"');
            }

            Util::logTrace('Deleting exec file');
            $unlinkResult = @unlink($execFile);
            Util::logTrace('Unlink result: ' . ($unlinkResult ? 'success' : 'failed'));
        } else {
            Util::logTrace('Exec file does not exist: ' . $execFile);
        }
    }
}
