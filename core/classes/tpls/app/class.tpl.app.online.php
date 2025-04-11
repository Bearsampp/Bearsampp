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
 * Class TplAppOnline
 *
 * This class provides methods to handle the online/offline status of the Bearsampp application.
 * It includes functionalities for generating actions and menu items to switch the application
 * between online and offline states.
 */
class TplAppOnline
{
    // Constant for the status action identifier
    const ACTION = 'status';

    /**
     * Generates the menu item and associated actions for switching the online/offline status.
     *
     * This method creates a menu item for switching the application's online/offline status and defines
     * the actions to be taken when the menu item is selected. It uses the global configuration and language
     * objects to retrieve the current status and localized strings.
     *
     * @global object $bearsamppConfig Provides access to the application's configuration settings.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated menu item and actions for switching the online/offline status.
     */
    public static function process()
    {
        global $bearsamppConfig, $bearsamppLang;

        return TplApp::getActionMulti(
            self::ACTION, array($bearsamppConfig->isOnline() ? Config::DISABLED : Config::ENABLED),
            array($bearsamppConfig->isOnline() ? $bearsamppLang->getValue(Lang::MENU_PUT_OFFLINE) : $bearsamppLang->getValue(Lang::MENU_PUT_ONLINE)),
            false, get_called_class()
        );
    }

    /**
     * Generates the action string to switch the online/offline status.
     *
     * This method creates the action string for switching the application's online/offline status. It includes
     * commands to restart relevant services and reload the application. The action string is used to define
     * what happens when the status switch action is triggered.
     *
     * @param int $status The status to switch to (enabled or disabled).
     *
     * @return string The generated action string for switching the online/offline status.
     */
    public static function getActionStatus($status)
    {
        return TplApp::getActionRun(Action::SWITCH_ONLINE, array($status)) . PHP_EOL .
            TplService::getActionRestart(BinApache::SERVICE_NAME) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
}
