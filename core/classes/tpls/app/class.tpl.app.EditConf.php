<?php

class TplAppEditConf
{

    public static function process()
    {
        global $bearsamppLang;

        return TplAestan::getItemNotepad(sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_CONF), "bearsampp.conf"), "bearsampp.conf") . PHP_EOL;
    }
}
