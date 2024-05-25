<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class TplAppApps
{
    const MENU = 'apps';

    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::APPS), self::MENU, get_called_class());
    }

    public static function getMenuApps()
    {
        global $bearsamppLang;

        return TplAestan::getItemLink(
                $bearsamppLang->getValue(Lang::ADMINER),
                'adminer/',
                true
            ) . PHP_EOL .
            TplAestan::getItemLink(
                $bearsamppLang->getValue(Lang::PHPMYADMIN),
                'phpmyadmin/',
                true
            ) . PHP_EOL .
            TplAestan::getItemLink(
                $bearsamppLang->getValue(Lang::PHPPGADMIN),
                'phppgadmin/',
                true
                ) . PHP_EOL .
            TplAestan::getItemLink(
                $bearsamppLang->getValue(Lang::WEBGRIND),
                'webgrind/',
                true
            );
    }
}
