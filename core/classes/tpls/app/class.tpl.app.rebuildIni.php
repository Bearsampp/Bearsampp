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
 * Class TplAppRebuildIni
 *
 * This class provides a method to process the rebuilding of the INI configuration.
 * It generates the necessary action to run the REBUILD_INI command.
 */
class TplAppRebuildIni
{
    /**
     * Processes the action to rebuild the INI configuration.
     *
     * This method generates the action string to run the REBUILD_INI command.
     * It uses the global `$bearsamppLang` object to retrieve the localized string
     * for the menu item and the glyph icon for the trashcan.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated action string to rebuild the INI configuration.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::REBUILD_INI,
            null,
            array($bearsamppLang->getValue(Lang::MENU_REBUILD_INI), TplAestan::GLYPH_REBUILD_INI)
        );
    }
}
