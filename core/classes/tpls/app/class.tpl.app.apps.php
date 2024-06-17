<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppApps
 *
 * This class provides methods to generate and manage the "Apps" menu within the Bearsampp application.
 * It includes functionalities for creating the menu and adding specific application links.
 */
class TplAppApps
{
    /**
     * Constant representing the menu identifier for apps.
     */
    const MENU = 'apps';

    /**
     * Processes and generates the "Apps" menu.
     *
     * This method generates the "Apps" menu by calling the `getMenu` method from the `TplApp` class.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array An array containing the call string and the menu content.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::APPS), self::MENU, get_called_class());
    }

    /**
     * Generates the content of the "Apps" menu.
     *
     * This method generates the content of the "Apps" menu by adding links to various applications
     * such as Adminer, phpMyAdmin, phpPgAdmin, and Webgrind.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated menu content as a concatenated string.
     */
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
