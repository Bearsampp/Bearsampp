<?php

class TplAppEditConf
{

    public static function process()
    {
        global $bearsamppLang, $bearsamppBs;

        return TplAestan::getItemNotepad(sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_CONF), "bearsampp.conf"), $bearsamppBs->GetConfigFilePath()) . PHP_EOL;
    }
}
