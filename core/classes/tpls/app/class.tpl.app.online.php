<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */
/**
 * Class TplAppOnline
 *
 * Handles the generation of actions related to toggling the online/offline status of the application.
 */
class TplAppOnline
{
    /**
     * Constant to define the action type.
     */
    const ACTION = 'status';

    /**
     * Processes the request to generate a multi-action command for toggling the online status.
     *
     * @return string Returns a string that represents a multi-action command for toggling the online status.
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
     * Generates the action string to change the online status and restart necessary services.
     *
     * @param int $status The new status to set (enabled or disabled).
     * @return string Returns the action commands to execute the status change and service restarts.
     */
    public static function getActionStatus($status)
    {
        return TplApp::getActionRun(Action::SWITCH_ONLINE, array($status)) . PHP_EOL .
            TplService::getActionRestart(BinApache::SERVICE_NAME) . PHP_EOL .
            TplService::getActionRestart(BinFilezilla::SERVICE_NAME) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
}
