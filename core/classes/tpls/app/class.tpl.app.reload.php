<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class TplAppReload
{
    const ACTION = 'reload';

    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getActionMulti(
            self::ACTION, null,
            array($bearsamppLang->getValue(Lang::RELOAD), TplAestan::GLYPH_RELOAD),
            false, get_called_class()
        );
    }

    public static function getActionReload()
    {
        return TplApp::getActionRun(Action::RELOAD) . PHP_EOL .
            'Action: resetservices' . PHP_EOL .
            'Action: readconfig';
    }
}
