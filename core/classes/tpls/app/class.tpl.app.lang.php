<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppLang
 *
 * This class provides methods to generate and manage language-related menu items and actions
 * within the Bearsampp application. It includes functionalities for creating language switch
 * actions and processing language menus.
 */
class TplAppLang
{
    // Constant for the language menu identifier
    const MENU = 'lang';

    /**
     * Processes and generates the language menu.
     *
     * This method generates the language menu for the application, including the available
     * languages and the actions to be taken when a language is selected.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated language menu and actions.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::LANG), self::MENU, get_called_class());
    }

    /**
     * Generates the language menu items and associated actions.
     *
     * This method creates menu items for each available language and defines the actions to be taken
     * when a language menu item is selected. It uses the global language object to retrieve the list
     * of available languages and the current language.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated language menu items and actions.
     */
    public static function getMenuLang()
    {
        global $bearsamppLang;
        $items = '';
        $actions = '';

        foreach ($bearsamppLang->getList() as $lang) {
            $tplSwitchLang = TplApp::getActionMulti(
                Action::SWITCH_LANG, array($lang),
                array(ucfirst($lang), $lang == $bearsamppLang->getCurrent() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchLang[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchLang[TplApp::SECTION_CONTENT] .  PHP_EOL;
        }

        return $items . $actions;
    }

    /**
     * Generates the action to switch the application language.
     *
     * This method creates the action string for switching the application language. It includes
     * commands to reload the application after the language switch. The action string is used to
     * define what happens when the switch language action is triggered.
     *
     * @param string $lang The language code to switch to.
     *
     * @return string The generated action string for switching the language.
     */
    public static function getActionSwitchLang($lang)
    {
        return TplApp::getActionRun(Action::SWITCH_LANG, array($lang)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
