<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

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
