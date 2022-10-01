<?php

class TplAppEditConf
{

    public static function process()
    {
        global $bearsamppLang, $bearsamppRoot;

        return TplAestan::getItemNotepad(sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_CONF), "bearsampp.conf"), $bearsamppRoot->GetConfigFilePath()) . PHP_EOL;
    }
}
