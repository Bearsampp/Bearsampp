<?php

class TplAppLaunchStartup
{
    const ACTION = 'launchStartup';

    public static function process()
    {
        global $bearsamppLang;

        $isLaunchStartup = Util::isLaunchStartup();
        return TplApp::getActionMulti(
            self::ACTION, array($isLaunchStartup ? Config::DISABLED : Config::ENABLED),
            array($bearsamppLang->getValue(Lang::MENU_LAUNCH_STARTUP), $isLaunchStartup ? TplAestan::GLYPH_CHECK : ''),
            false, get_called_class()
        );
    }

    public static function getActionLaunchStartup($launchStartup)
    {
        return TplApp::getActionRun(Action::LAUNCH_STARTUP, array($launchStartup)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
}
