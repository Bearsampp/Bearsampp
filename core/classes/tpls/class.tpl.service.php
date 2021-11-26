<?php

class TplService
{
    public static function getActionCreate($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::CREATE));
    }

    public static function getActionStart($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::START));
    }

    public static function getActionStop($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::STOP));
    }

    public static function getActionRestart($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::RESTART));
    }

    public static function getActionInstall($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::INSTALL));
    }

    public static function getActionRemove($sName)
    {
        return TplApp::getActionRun(Action::SERVICE, array($sName, ActionService::REMOVE));
    }

    public static function getItemStart($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::START),
            array($bearsamppLang->getValue(Lang::MENU_START_SERVICE), TplAestan::GLYPH_START)
        );
    }

    public static function getItemStop($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::STOP),
            array($bearsamppLang->getValue(Lang::MENU_STOP_SERVICE), TplAestan::GLYPH_STOP)
        );
    }

    public static function getItemRestart($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::RESTART),
            array($bearsamppLang->getValue(Lang::MENU_RESTART_SERVICE), TplAestan::GLYPH_RELOAD)
        );
    }

    public static function getItemInstall($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::INSTALL),
            array($bearsamppLang->getValue(Lang::MENU_INSTALL_SERVICE), TplAestan::GLYPH_SERVICE_INSTALL)
        );
    }

    public static function getItemRemove($sName)
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::SERVICE, array($sName, ActionService::REMOVE),
            array($bearsamppLang->getValue(Lang::MENU_REMOVE_SERVICE), TplAestan::GLYPH_SERVICE_REMOVE)
        );
    }
}
