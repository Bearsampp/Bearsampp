<?php

class TplAppBrowser
{
    const ACTION = 'changeBrowser';

    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getActionMulti(
            self::ACTION, null,
            array($bearsamppLang->getValue(Lang::CHANGE_BROWSER_TITLE), TplAestan::GLYPH_BROWSER),
            false, get_called_class()
        );
    }

    public static function getActionChangeBrowser()
    {
        return TplApp::getActionRun(Action::CHANGE_BROWSER) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
