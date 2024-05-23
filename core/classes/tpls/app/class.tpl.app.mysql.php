<?php

class TplAppMysql
{
    const MENU = 'mysql';
    const MENU_VERSIONS = 'mysqlVersions';
    const MENU_SERVICE = 'mysqlService';
    const MENU_DEBUG = 'mysqlDebug';

    const ACTION_ENABLE = 'enableMysql';
    const ACTION_SWITCH_VERSION = 'switchMysqlVersion';
    const ACTION_CHANGE_PORT = 'changeMysqlPort';
    const ACTION_CHANGE_ROOT_PWD = 'changeMysqlRootPwd';
    const ACTION_INSTALL_SERVICE = 'installMysqlService';
    const ACTION_REMOVE_SERVICE = 'removeMysqlService';

    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::MYSQL), self::MENU, get_called_class(), $bearsamppBins->getMysql()->isEnable());
    }

    public static function getMenuMysql()
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppTools;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getMysql()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $downloadTitle,
            Util::getWebsiteUrl('module/mysql', '#releases'),
            false,
            TplAestan::GLYPH_BROWSER
        ) . PHP_EOL;

        // Enable
        $tplEnable = TplApp::getActionMulti(
            self::ACTION_ENABLE, array($isEnabled ? Config::DISABLED : Config::ENABLED),
            array($bearsamppLang->getValue(Lang::MENU_ENABLE), $isEnabled ? TplAestan::GLYPH_CHECK : ''),
            false, get_called_class()
        );
        $resultItems .= $tplEnable[TplApp::SECTION_CALL] . PHP_EOL;
        $resultActions .= $tplEnable[TplApp::SECTION_CONTENT] . PHP_EOL;

        if ($isEnabled) {
            $resultItems .= TplAestan::getItemSeparator() . PHP_EOL;

            // Versions
            $tplVersions = TplApp::getMenu($bearsamppLang->getValue(Lang::VERSIONS), self::MENU_VERSIONS, get_called_class());
            $resultItems .= $tplVersions[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplVersions[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Service
            $tplService = TplApp::getMenu($bearsamppLang->getValue(Lang::SERVICE), self::MENU_SERVICE, get_called_class());
            $resultItems .= $tplService[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplService[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Debug
            $tplDebug = TplApp::getMenu($bearsamppLang->getValue(Lang::DEBUG), self::MENU_DEBUG, get_called_class());
            $resultItems .= $tplDebug[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplDebug[TplApp::SECTION_CONTENT];

            // Console
            $resultItems .= TplAestan::getItemConsoleZ(
                $bearsamppLang->getValue(Lang::CONSOLE),
                TplAestan::GLYPH_CONSOLEZ,
                $bearsamppTools->getConsoleZ()->getTabTitleMysql()
            ) . PHP_EOL;

            // Conf
            $resultItems .= TplAestan::getItemNotepad(basename($bearsamppBins->getMysql()->getConf()), $bearsamppBins->getMysql()->getConf()) . PHP_EOL;

            // Errors log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_ERROR_LOGS), $bearsamppBins->getMysql()->getErrorLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    public static function getMenuMysqlVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getMysql()->getVersionList() as $version) {
            $tplSwitchMysqlVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $version == $bearsamppBins->getMysql()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchMysqlVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchMysqlVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    public static function getActionEnableMysql($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getMysql()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionSwitchMysqlVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getMysql()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    public static function getMenuMysqlService()
    {
        global $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );

        $isInstalled = $bearsamppBins->getMysql()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getMysql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getMysql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getMysql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getMysql()->getName(), $bearsamppBins->getMysql()->getPort()),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getMysql()->getPort()), TplAestan::GLYPH_LIGHT)
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL;

        $tplChangeRootPwd = null;
        if ($isInstalled) {
            $tplChangeRootPwd = TplApp::getActionMulti(
                self::ACTION_CHANGE_ROOT_PWD, null,
                array($bearsamppLang->getValue(Lang::MENU_CHANGE_ROOT_PWD), TplAestan::GLYPH_PASSWORD),
                !$isInstalled, get_called_class()
            );

            $result .= $tplChangeRootPwd[TplApp::SECTION_CALL] . PHP_EOL;
        }

        if (!$isInstalled) {
            $tplInstallService = TplApp::getActionMulti(
                self::ACTION_INSTALL_SERVICE, null,
                array($bearsamppLang->getValue(Lang::MENU_INSTALL_SERVICE), TplAestan::GLYPH_SERVICE_INSTALL),
                $isInstalled, get_called_class()
            );

            $result .= $tplInstallService[TplApp::SECTION_CALL] . PHP_EOL . PHP_EOL .
            $tplInstallService[TplApp::SECTION_CONTENT] . PHP_EOL;
        } else {
            $tplRemoveService = TplApp::getActionMulti(
                self::ACTION_REMOVE_SERVICE, null,
                array($bearsamppLang->getValue(Lang::MENU_REMOVE_SERVICE), TplAestan::GLYPH_SERVICE_REMOVE),
                !$isInstalled, get_called_class()
            );

            $result .= $tplRemoveService[TplApp::SECTION_CALL] . PHP_EOL . PHP_EOL .
            $tplRemoveService[TplApp::SECTION_CONTENT] . PHP_EOL;
        }

        $result .= $tplChangePort[TplApp::SECTION_CONTENT] . PHP_EOL .
            ($tplChangeRootPwd != null ? $tplChangeRootPwd[TplApp::SECTION_CONTENT] . PHP_EOL : '');

        return $result;
    }

    public static function getMenuMysqlDebug()
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::DEBUG_MYSQL, array(BinMysql::CMD_VERSION),
            array($bearsamppLang->getValue(Lang::DEBUG_MYSQL_VERSION), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_MYSQL, array(BinMysql::CMD_VARIABLES),
            array($bearsamppLang->getValue(Lang::DEBUG_MYSQL_VARIABLES), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_MYSQL, array(BinMysql::CMD_SYNTAX_CHECK),
            array($bearsamppLang->getValue(Lang::DEBUG_MYSQL_SYNTAX_CHECK), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL;
    }

    public static function getActionChangeMysqlPort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getMysql()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionChangeMysqlRootPwd()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_DB_ROOT_PWD, array($bearsamppBins->getMysql()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionInstallMysqlService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMysql::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionRemoveMysqlService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMysql::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
