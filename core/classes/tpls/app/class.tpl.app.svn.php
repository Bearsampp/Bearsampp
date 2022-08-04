<?php

class TplAppSvn
{
    const MENU = 'svn';
    const MENU_VERSIONS = 'svnVersions';
    const MENU_SERVICE = 'svnService';
    const MENU_DEBUG = 'svnDebug';
    const MENU_REPOS = 'svnRepos';

    const ACTION_ENABLE = 'enableSvn';
    const ACTION_SWITCH_VERSION = 'switchSvnVersion';
    const ACTION_CHANGE_PORT = 'changeSvnPort';
    const ACTION_INSTALL_SERVICE = 'installSvnService';
    const ACTION_REMOVE_SERVICE = 'removeSvnService';

    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::SVN), self::MENU, get_called_class(), $bearsamppBins->getSvn()->isEnable());
    }

    public static function getMenuSvn()
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppTools;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getSvn()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
        $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/svn', '#releases'),
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

            // Repos
            $tplRepos = TplApp::getMenu($bearsamppLang->getValue(Lang::REPOS), self::MENU_REPOS, get_called_class());
            $emptyRepos = count(explode(PHP_EOL, $tplRepos[TplApp::SECTION_CONTENT])) == 2;
            if (!$emptyRepos) {
                $resultItems .= $tplRepos[TplApp::SECTION_CALL] . PHP_EOL;
                $resultActions .= $tplRepos[TplApp::SECTION_CONTENT] . PHP_EOL;
            }

            // Console
            $resultItems .= TplAestan::getItemConsoleZ(
                $bearsamppLang->getValue(Lang::SVN_CONSOLE),
                TplAestan::GLYPH_SVN,
                $bearsamppTools->getConsoleZ()->getTabTitleSvn()
            ) . PHP_EOL;

            // Log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_LOGS), $bearsamppBins->getSvn()->getLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    public static function getMenuSvnVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getSvn()->getVersionList() as $version) {
            $tplSwitchVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $version == $bearsamppBins->getSvn()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    public static function getActionEnableSvn($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getSvn()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionSwitchSvnVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getSvn()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    public static function getMenuSvnService()
    {
        global $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );

        $isInstalled = $bearsamppBins->getSvn()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getSvn()->getService()->getName()) . PHP_EOL .
        TplAestan::getItemActionServiceStop($bearsamppBins->getSvn()->getService()->getName()) . PHP_EOL .
        TplAestan::getItemActionServiceRestart($bearsamppBins->getSvn()->getService()->getName()) . PHP_EOL .
        TplAestan::getItemSeparator() . PHP_EOL .
        TplApp::getActionRun(
            Action::CHECK_PORT, array($bearsamppBins->getSvn()->getName(), $bearsamppBins->getSvn()->getPort()),
            array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getSvn()->getPort()), TplAestan::GLYPH_LIGHT)
        ) . PHP_EOL .
        $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL;

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

        $result .= $tplChangePort[TplApp::SECTION_CONTENT] . PHP_EOL;
        return $result;
    }

    public static function getMenuSvnDebug()
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::DEBUG_SVN, array(BinSvn::CMD_VERSION),
            array($bearsamppLang->getValue(Lang::DEBUG_SVN_VERSION), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL;
    }

    public static function getMenuSvnRepos()
    {
        global $bearsamppBins, $bearsamppTools;
        $result = '';

        foreach ($bearsamppBins->getSvn()->findRepos() as $repo) {
            $result .= TplAestan::getItemConsoleZ(
                basename($repo),
                TplAestan::GLYPH_SVN,
                $bearsamppTools->getConsoleZ()->getTabTitleSvn(),
                $bearsamppTools->getConsoleZ()->getTabTitleSvn($repo),
                $repo
            ) . PHP_EOL;
        }

        return $result;
    }

    public static function getActionChangeSvnPort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getSvn()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionInstallSvnService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinSvn::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionRemoveSvnService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinSvn::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
