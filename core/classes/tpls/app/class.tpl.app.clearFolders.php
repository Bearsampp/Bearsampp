<?php

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
