<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class TplAppBrowser
 *
 * This class provides methods to generate actions and menu items for changing the browser in the Bearsampp application.
 * It includes functionalities for creating change browser actions and processing change browser commands.
 */
class TplAppBrowser
{
    // Constant for the change browser action identifier
    const ACTION = 'changeBrowser';

    /**
     * Generates the change browser menu item and associated actions.
     *
     * This method creates a menu item for changing the browser and defines the actions to be taken
     * when the change browser menu item is selected. It uses the global language object to retrieve the localized
     * string for the change browser action.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated menu item and actions for changing the browser.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getActionMulti(
            self::ACTION, null,
            array($bearsamppLang->getValue(Lang::CHANGE_BROWSER_TITLE), TplAestan::GLYPH_BROWSER),
            false, get_called_class()
        );
    }

    /**
     * Generates the action to change the browser.
     *
     * This method creates the action string for changing the browser. It includes commands to reload the application
     * after changing the browser. The action string is used to define what happens when the change browser action is triggered.
     *
     * @return string The generated action string for changing the browser.
     */
    public static function getActionChangeBrowser()
    {
        return TplApp::getActionRun(Action::CHANGE_BROWSER) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
