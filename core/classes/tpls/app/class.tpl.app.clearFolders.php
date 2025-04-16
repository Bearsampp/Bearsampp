<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppClearFolders
 *
 * This class provides a method to generate an action string for clearing folders within the Bearsampp application.
 */
class TplAppClearFolders
{
    /**
     * Generates an action string to clear folders.
     *
     * This method constructs and returns an action string that triggers the `CLEAR_FOLDERS` action.
     * The action string includes the caption and glyph for the menu item, which are retrieved from the language settings.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated action string for clearing folders.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::CLEAR_FOLDERS,
            null,
            array($bearsamppLang->getValue(Lang::MENU_CLEAR_FOLDERS), TplAestan::GLYPH_TRASHCAN)
        );
    }
}
