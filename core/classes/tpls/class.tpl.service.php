<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplService
 *
 * Provides utility methods for generating action strings and menu items for service management.
 * This includes functionalities to create, start, stop, restart, install, and remove services.
 * Each service action can also be represented as a menu item with an associated glyph for visual representation.
 */
class TplService
{
    /**
     * Generates an action string for creating a service.
     *
     * @param string $sName The name of the service.
     * @return string The action command to create the service.
     */
    public static function getActionCreate($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::CREATE));
    }

    /**
     * Generates an action string for starting a service.
     *
     * @param string $sName The name of the service.
     * @return string The action command to start the service.
     */
    public static function getActionStart($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::START));
    }

    /**
     * Generates an action string for stopping a service.
     *
     * @param string $sName The name of the service.
     * @return string The action command to stop the service.
     */
    public static function getActionStop($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::STOP));
    }

    /**
     * Generates an action string for restarting a service.
     *
     * @param string $sName The name of the service.
     * @return string The action command to restart the service.
     */
    public static function getActionRestart($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::RESTART));
    }

    /**
     * Generates an action string for installing a service.
     *
     * @param string $sName The name of the service.
     * @return string The action command to install the service.
     */
    public static function getActionInstall($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::INSTALL));
    }

    /**
     * Generates an action string for removing a service.
     *
     * @param string $sName The name of the service.
     * @return string The action command to remove the service.
     */
    public static function getActionRemove($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::REMOVE));
    }

    /**
     * Generates a menu item for starting a service with a glyph.
     *
     * @param string $sName The name of the service.
     * @return string The menu item command to start the service.
     */
    public static function getItemStart($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::START),
            array($bearsamppLang->getValue(Lang::MENU_START_SERVICE), TplAestan::GLYPH_START)
        );
    }

    /**
     * Generates a menu item for stopping a service with a glyph.
     *
     * @param string $sName The name of the service.
     * @return string The menu item command to stop the service.
     */
    public static function getItemStop($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::STOP),
            array($bearsamppLang->getValue(Lang::MENU_STOP_SERVICE), TplAestan::GLYPH_STOP)
        );
    }

    /**
     * Generates a menu item for restarting a service with a glyph.
     *
     * @param string $sName The name of the service.
     * @return string The menu item command to restart the service.
     */
    public static function getItemRestart($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::RESTART),
            array($bearsamppLang->getValue(Lang::MENU_RESTART_SERVICE), TplAestan::GLYPH_RELOAD)
        );
    }

    /**
     * Generates a menu item for installing a service with a glyph.
     *
     * @param string $sName The name of the service.
     * @return string The menu item command to install the service.
     */
    public static function getItemInstall($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::INSTALL),
            array($bearsamppLang->getValue(Lang::MENU_INSTALL_SERVICE), TplAestan::GLYPH_SERVICE_INSTALL)
        );
    }

    /**
     * Generates a menu item for removing a service with a glyph.
     *
     * @param string $sName The name of the service.
     * @return string The menu item command to remove the service.
     */
    public static function getItemRemove($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::REMOVE),
            array($bearsamppLang->getValue(Lang::MENU_REMOVE_SERVICE), TplAestan::GLYPH_SERVICE_REMOVE)
        );
    }
}
