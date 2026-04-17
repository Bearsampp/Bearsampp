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

        Log::trace('ActionExec constructor called');

        $execFile = $bearsamppCore->getExec();
        Log::trace('Checking for exec file: ' . $execFile);

        if (file_exists($execFile)) {
            Log::trace('Exec file exists');

            $action = file_get_contents($execFile);
            Log::trace('Action read from exec file: "' . $action . '"');

            if ($action == self::QUIT) {
                Log::trace('Executing quit action');
                Batch::exitApp();
            } elseif ($action == self::RESTART) {
                Log::trace('Executing restart action');
                Batch::restartApp();
            } else {
                Log::trace('Unknown action: "' . $action . '"');
            }

            // Do NOT delete the exec file yet if it's a restart,
            // as we need the next instance to know it's a restart.
            // ActionStartup will handle the unlinking.
            if ($action != self::RESTART) {
                Log::trace('Deleting exec file');
                $unlinkResult = @unlink($execFile);
                Log::trace('Unlink result: ' . ($unlinkResult ? 'success' : 'failed'));
            }
        } else {
            Log::trace('Exec file does not exist: ' . $execFile);
        }
    }
}
