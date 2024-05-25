<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class TplAppClearFolders
{
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::CLEAR_FOLDERS, null,
            array($bearsamppLang->getValue(Lang::MENU_CLEAR_FOLDERS), TplAestan::GLYPH_TRASHCAN)
        );
    }
}
