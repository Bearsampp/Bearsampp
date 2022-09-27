<?php

class TplApp
{
    const ITEM_CAPTION = 0;
    const ITEM_GLYPH = 1;

    const SECTION_CALL = 0;
    const SECTION_CONTENT = 1;

    private function __construct()
    {
    }

    public static function process()
    {
        global $bearsamppCore;

        return TplAestan::getSectionConfig() . PHP_EOL .
            self::getSectionServices() . PHP_EOL .
            TplAestan::getSectionMessages() . PHP_EOL .
            self::getSectionStartupAction() . PHP_EOL .
            TplAestan::getSectionMenuRightSettings() . PHP_EOL .
            TplAestan::getSectionMenuLeftSettings(APP_TITLE . ' ' . $bearsamppCore->getAppVersion()) . PHP_EOL .
            self::getSectionMenuRight() . PHP_EOL .
            self::getSectionMenuLeft() . PHP_EOL;
    }

    public static function processLight()
    {
        return TplAestan::getSectionConfig() . PHP_EOL .
            self::getSectionServices() . PHP_EOL .
            TplAestan::getSectionMessages() . PHP_EOL .
            self::getSectionStartupAction() . PHP_EOL;
    }

    public static function getSectionName($name, $args = array())
    {
        return ucfirst($name) . (!empty($args) ? '-' . md5(serialize($args)) : '');
    }

    public static function getSectionContent($name, $class, $args = array())
    {
        $baseMethod = 'get' . ucfirst($name);
        $args = $args == null ? array() : $args;
        return '[' . self::getSectionName($name, $args) . ']' . PHP_EOL .
            call_user_func_array($class . '::' . $baseMethod, $args);
    }

    public static function getActionRun($action, $args = array(), $item = array(), $waitUntilTerminated = true)
    {
        global $bearsamppRoot, $bearsamppCore;
        $args = $args == null ? array() : $args;

        $argImp = '';
        foreach ($args as $arg) {
            $argImp .= ' ' . base64_encode($arg);
        }

        $result = 'Action: run; ' .
            'FileName: "' . $bearsamppCore->getPhpExe(true) . '"; ' .
            'Parameters: "' . Core::isRoot_FILE . ' ' . $action . $argImp . '"; ' .
            'WorkingDir: "' . $bearsamppRoot->getCorePath(true) . '"';

        if (!empty($item)) {
            $result = 'Type: item; ' . $result .
                '; Caption: "' . $item[self::ITEM_CAPTION] . '"' .
                (!empty($item[self::ITEM_GLYPH]) ? '; Glyph: "' . $item[self::ITEM_GLYPH] . '"' : '');
        } elseif ($waitUntilTerminated) {
            $result .= '; Flags: waituntilterminated';
        }

        return $result;
    }

    public static function getActionMulti($action, $args = array(), $item = array(), $disabled = false, $class = false)
    {
        $action = 'action' . ucfirst($action);
        $args = $args == null ? array() : $args;
        $sectionName = self::getSectionName($action, $args);

        //TODO: How managed disabled item??
        /*if ($disabled) {
            $call = 'Action: run; FileName: "%AeTrayMenuPath%core/libs/php/php-win.exe"; Parameters: "root.php switchApacheVersion 2.2.22"; WorkingDir: "%AeTrayMenuPath%core"; ';
        } else {*/
            $call = 'Action: multi; Actions: ' . $sectionName;
        //}

        if (!empty($item)) {
            $call = 'Type: item; ' . $call .
            '; Caption: "' . $item[self::ITEM_CAPTION] . '"' .
            (!empty($item[self::ITEM_GLYPH]) ? '; Glyph: "' . $item[self::ITEM_GLYPH] . '"' : '');
        } else {
            $call .= '; Flags: waituntilterminated';
        }

        return array($call, self::getSectionContent($action, $class, $args));
    }

    public static function getActionExec()
    {
        return self::getActionRun(Action::EXEC, array(), array(), false);
    }

    public static function getMenu($caption, $menu, $class)
    {
        $menu = 'menu' . ucfirst($menu);

        $call = 'Type: submenu; ' .
            'Caption: "' . $caption . '"; ' .
            'SubMenu: ' . self::getSectionName($menu) . '; ' .
            'Glyph: ' . TplAestan::GLYPH_FOLDER_CLOSE;

        return array($call, self::getSectionContent($menu, $class, null));
    }

    public static function getMenuEnable($caption, $menu, $class, $enabled = true)
    {
        $menu = 'menu' . ucfirst($menu);

        $call = 'Type: submenu; ' .
            'Caption: "' . $caption . '"; ' .
            'SubMenu: ' . self::getSectionName($menu) . '; ' .
            'Glyph: ' . ($enabled ? TplAestan::GLYPH_FOLDER_CLOSE : TplAestan::GLYPH_FOLDER_DISABLED);

        return array($call, self::getSectionContent($menu, $class, null));
    }

    private static function getSectionServices()
    {
        global $bearsamppBins;

        $result = '[Services]' . PHP_EOL;
        foreach ($bearsamppBins->getServices() as $service) {
            $result .= 'Name: ' . $service->getName() . PHP_EOL;
        }

        return $result;
    }

    private static function getSectionStartupAction()
    {
        return '[StartupAction]' . PHP_EOL .
            self::getActionRun(Action::STARTUP) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL .
            self::getActionRun(Action::CHECK_VERSION) . PHP_EOL .
            self::getActionExec() . PHP_EOL;
    }

    private static function getSectionMenuRight()
    {
        global $bearsamppLang;

        $tplReload = TplAppReload::process();
        $tplBrowser = TplAppBrowser::process();
        $tplLang = TplAppLang::process();
        $tplLogsVerbose = TplAppLogsVerbose::process();
        $tplLaunchStartup = TplAppLaunchStartup::process();
        $tplExit = TplAppExit::process();

        return
            // Items
            '[Menu.Right]' . PHP_EOL .
            self::getActionRun(Action::ABOUT, null, array($bearsamppLang->getValue(Lang::MENU_ABOUT), TplAestan::GLYPH_ABOUT)) . PHP_EOL .
            self::getActionRun(
                Action::CHECK_VERSION,
                array(ActionCheckVersion::DISPLAY_OK),
                array($bearsamppLang->getValue(Lang::MENU_CHECK_UPDATE), TplAestan::GLYPH_UPDATE)
            ) . PHP_EOL .
            TplAestan::getItemLink($bearsamppLang->getValue(Lang::HELP), Util::getWebsiteUrl('faq')) . PHP_EOL .

            TplAestan::getItemSeparator() . PHP_EOL .
            $tplReload[self::SECTION_CALL] . PHP_EOL .
            TplAppClearFolders::process() . PHP_EOL .
            $tplBrowser[self::SECTION_CALL] . PHP_EOL .
            TplAppEditConf::process() . PHP_EOL .


        TplAestan::getItemSeparator() . PHP_EOL .
            $tplLang[self::SECTION_CALL] . PHP_EOL .
            $tplLogsVerbose[self::SECTION_CALL] . PHP_EOL .
            $tplLaunchStartup[self::SECTION_CALL] . PHP_EOL .

            TplAestan::getItemSeparator() . PHP_EOL .
            $tplExit[self::SECTION_CALL] . PHP_EOL .

            // Actions
            PHP_EOL . $tplReload[self::SECTION_CONTENT] . PHP_EOL .
            PHP_EOL . $tplBrowser[self::SECTION_CONTENT] . PHP_EOL .
            PHP_EOL . $tplLang[self::SECTION_CONTENT] .
            PHP_EOL . $tplLogsVerbose[self::SECTION_CONTENT] .
            PHP_EOL . $tplLaunchStartup[self::SECTION_CONTENT] .
            PHP_EOL . $tplExit[self::SECTION_CONTENT] . PHP_EOL;
    }

    private static function getSectionMenuLeft()
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppLang;

        $tplNodejs = TplAppNodejs::process();
        $tplApache = TplAppApache::process();
        $tplPhp = TplAppPhp::process();
        $tplMysql = TplAppMysql::process();
        $tplMariadb = TplAppMariadb::process();
        $tplPostgresql = TplAppPostgresql::process();
        $tplMailhog = TplAppMailhog::process();
        $tplMemcached = TplAppMemcached::process();
        $tplFilezilla = TplAppFilezilla::process();

        $tplLogs = TplAppLogs::process();
        $tplApps = TplAppApps::process();
        $tplTools = TplAppTools::process();

        $tplServices = TplAppServices::process();

        $tplOnline = TplAppOnline::process();

        $httpUrl = 'http://localhost' . ($bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '');
        $httpsUrl = 'https://localhost' . ($bearsamppBins->getApache()->getSslPort() != 443 ? ':' . $bearsamppBins->getApache()->getSslPort() : '');

        return
            // Items
            '[Menu.Left]' . PHP_EOL .
            TplAestan::getItemLink($bearsamppLang->getValue(Lang::MENU_LOCALHOST), $httpUrl) . PHP_EOL .
            TplAestan::getItemLink($bearsamppLang->getValue(Lang::MENU_LOCALHOST) . ' (SSL)', $httpsUrl) . PHP_EOL .
            TplAestan::getItemExplore($bearsamppLang->getValue(Lang::MENU_WWW_DIRECTORY), $bearsamppRoot->getWwwPath()) . PHP_EOL .

            //// Bins menus
            TplAestan::getItemSeparator() . PHP_EOL .
            $tplNodejs[self::SECTION_CALL] . PHP_EOL .
            $tplApache[self::SECTION_CALL] . PHP_EOL .
            $tplPhp[self::SECTION_CALL] . PHP_EOL .
            $tplMysql[self::SECTION_CALL] . PHP_EOL .
            $tplMariadb[self::SECTION_CALL] . PHP_EOL .
            $tplPostgresql[self::SECTION_CALL] . PHP_EOL .
            $tplMailhog[self::SECTION_CALL] . PHP_EOL .
            $tplMemcached[self::SECTION_CALL] . PHP_EOL .
            $tplFilezilla[self::SECTION_CALL] . PHP_EOL .

            //// Stuff menus
            TplAestan::getItemSeparator() . PHP_EOL .
            $tplLogs[self::SECTION_CALL] . PHP_EOL .
            $tplTools[self::SECTION_CALL] . PHP_EOL .
            $tplApps[self::SECTION_CALL] . PHP_EOL .

            //// Services
            TplAestan::getItemSeparator() . PHP_EOL .
            $tplServices[self::SECTION_CALL] .

            //// Put online/offline
            TplAestan::getItemSeparator() . PHP_EOL .
            $tplOnline[self::SECTION_CALL] . PHP_EOL .

            // Actions
            PHP_EOL . $tplNodejs[self::SECTION_CONTENT] .
            PHP_EOL . $tplApache[self::SECTION_CONTENT] .
            PHP_EOL . $tplPhp[self::SECTION_CONTENT] .
            PHP_EOL . $tplMysql[self::SECTION_CONTENT] .
            PHP_EOL . $tplMariadb[self::SECTION_CONTENT] .
            PHP_EOL . $tplPostgresql[self::SECTION_CONTENT] .
            PHP_EOL . $tplMailhog[self::SECTION_CONTENT] .
            PHP_EOL . $tplMemcached[self::SECTION_CONTENT] .
            PHP_EOL . $tplFilezilla[self::SECTION_CONTENT] .
            PHP_EOL . $tplLogs[self::SECTION_CONTENT] .
            PHP_EOL . $tplTools[self::SECTION_CONTENT] .
            PHP_EOL . $tplApps[self::SECTION_CONTENT] .
            PHP_EOL . $tplServices[self::SECTION_CONTENT] .
            PHP_EOL . $tplOnline[self::SECTION_CONTENT];
    }
}
