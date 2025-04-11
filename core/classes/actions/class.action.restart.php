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
 * Class ActionRestart
 * Handles the restart action for the Bearsampp application.
 */
class ActionRestart
{
    /**
     * ActionRestart constructor.
     * Displays a message box with restart information.
     *
     * @param array $args Command line arguments passed to the action.
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        $bearsamppWinbinder->messageBoxInfo(
            sprintf($bearsamppLang->getValue(Lang::RESTART_TEXT), APP_TITLE),
            $bearsamppLang->getValue(Lang::RESTART_TITLE));
    }
}
