<?php

class TplAppMemcached
{
    const MENU = 'memcached';
    const MENU_VERSIONS = 'memcachedVersions';
    const MENU_SERVICE = 'memcachedService';

    const ACTION_ENABLE = 'enableMemcached';
    const ACTION_SWITCH_VERSION = 'switchMemcachedVersion';
    const ACTION_CHANGE_PORT = 'changeMemcachedPort';
    const ACTION_INSTALL_SERVICE = 'installMemcachedService';
    const ACTION_REMOVE_SERVICE = 'removeMemcachedService';

    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::MEMCACHED), self::MENU, get_called_class(), $bearsamppBins->getMemcached()->isEnable());
    }

    public static function getMenuMemcached()
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getMemcached()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $downloadTitle,
            Util::getWebsiteUrl('module/memcached', '#releases'),
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
            $resultActions .= $tplService[TplApp::SECTION_CONTENT];

            // Update environment PATH
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_UPDATE_ENV_PATH), $bearsamppRoot->getRootPath() . '/nssmEnvPaths.dat') . PHP_EOL;

            // Log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_LOGS), $bearsamppBins->getMemcached()->getLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    public static function getMenuMemcachedVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getMemcached()->getVersionList() as $version) {
            $tplSwitchMemcachedVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $version == $bearsamppBins->getMemcached()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchMemcachedVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchMemcachedVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    public static function getActionEnableMemcached($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getMemcached()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionSwitchMemcachedVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getMemcached()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    public static function getMenuMemcachedService()
    {
        global $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );

        $isInstalled = $bearsamppBins->getMemcached()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getMemcached()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getMemcached()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getMemcached()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getMemcached()->getName(), $bearsamppBins->getMemcached()->getPort()),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getMemcached()->getPort()), TplAestan::GLYPH_LIGHT)
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

    public static function getActionChangeMemcachedPort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getMemcached()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionInstallMemcachedService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMemcached::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionRemoveMemcachedService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMemcached::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
