<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class TplAppEditConf
{

    public static function process()
    {
        global $bearsamppLang, $bearsamppRoot;

        return TplAestan::getItemNotepad(sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_CONF), "bearsampp.conf"), $bearsamppRoot->GetConfigFilePath()) . PHP_EOL;
    }
}
