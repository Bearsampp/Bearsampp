<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppReload
 *
 * This class provides methods to generate actions and menu items for reloading the Bearsampp application.
 * It includes functionalities for creating reload actions and processing reload commands.
 */
class TplAppReload
{
    // Constant for the reload action identifier
    const ACTION = 'reload';

    /**
     * Generates the reload menu item and associated actions.
     *
     * This method creates a menu item for reloading the application and defines the actions to be taken
     * when the reload menu item is selected. It uses the global language object to retrieve the localized
     * string for the reload action.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated menu item and actions for reloading the application.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getActionMulti(
            self::ACTION, null,
            array($bearsamppLang->getValue(Lang::RELOAD), TplAestan::GLYPH_RELOAD),
            false, get_called_class()
        );
    }

    /**
     * Generates the action to reload the application.
     *
     * This method creates the action string for reloading the application. It includes commands to reset
     * services and read the configuration. The action string is used to define what happens when the reload
     * action is triggered.
     *
     * @return string The generated action string for reloading the application.
     */
    public static function getActionReload()
    {
        return TplApp::getActionRun(Action::RELOAD) . PHP_EOL .
            'Action: resetservices' . PHP_EOL .
            'Action: readconfig';
    }
}
