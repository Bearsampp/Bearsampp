<?php

class TplAppApache
{
    const MENU = 'apache';
    const MENU_VERSIONS = 'apacheVersions';
    const MENU_SERVICE = 'apacheService';
    const MENU_DEBUG = 'apacheDebug';
    const MENU_MODULES = 'apacheModules';
    const MENU_ALIAS = 'apacheAlias';
    const MENU_VHOSTS = 'apacheVhosts';

    const ACTION_ENABLE = 'enableApache';
    const ACTION_SWITCH_VERSION = 'switchApacheVersion';
    const ACTION_CHANGE_PORT = 'changeApachePort';
    const ACTION_INSTALL_SERVICE = 'installApacheService';
    const ACTION_REMOVE_SERVICE = 'removeApacheService';
    const ACTION_SWITCH_MODULE = 'switchApacheModule';
    const ACTION_ADD_ALIAS = 'addAlias';
    const ACTION_EDIT_ALIAS = 'editAlias';
    const ACTION_ADD_VHOST = 'addVhost';
    const ACTION_EDIT_VHOST = 'editVhost';

    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::APACHE), self::MENU, get_called_class(), $bearsamppBins->getApache()->isEnable());
    }

    public static function getMenuApache()
    {
        global $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getApache()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $downloadTitle,
            Util::getWebsiteUrl('module/apache', '#releases'),
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
            $resultActions .= $tplDebug[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Modules
            $tplModules = TplApp::getMenu($bearsamppLang->getValue(Lang::MODULES), self::MENU_MODULES, get_called_class());
            $resultItems .= $tplModules[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplModules[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Alias
            $tplAlias = TplApp::getMenu($bearsamppLang->getValue(Lang::ALIASES), self::MENU_ALIAS, get_called_class());
            $resultItems .= $tplAlias[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplAlias[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Vhosts
            $tplVhosts = TplApp::getMenu($bearsamppLang->getValue(Lang::VIRTUAL_HOSTS), self::MENU_VHOSTS, get_called_class());
            $resultItems .= $tplVhosts[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplVhosts[TplApp::SECTION_CONTENT];

            // Conf
            $resultItems .= TplAestan::getItemNotepad(basename($bearsamppBins->getApache()->getConf()), $bearsamppBins->getApache()->getConf()) . PHP_EOL;

            // Access log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_ACCESS_LOGS), $bearsamppBins->getApache()->getAccessLog()) . PHP_EOL;

            // Rewrite log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_REWRITE_LOGS), $bearsamppBins->getApache()->getRewriteLog()) . PHP_EOL;

            // Error log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_ERROR_LOGS), $bearsamppBins->getApache()->getErrorLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    public static function getMenuApacheVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getApache()->getVersionList() as $version) {
            $glyph = '';
            $apachePhpModule = $bearsamppBins->getPhp()->getApacheModule($version);
            if ($apachePhpModule === false) {
                $glyph = TplAestan::GLYPH_WARNING;
            } elseif ($version == $bearsamppBins->getApache()->getVersion()) {
                $glyph = TplAestan::GLYPH_CHECK;
            }

            $tplSwitchApacheVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $glyph),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchApacheVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchApacheVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    public static function getActionEnableApache($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getApache()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionSwitchApacheVersion($version)
    {
        global $bearsamppBins;
        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getApache()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    public static function getMenuApacheService()
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getApache()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getApache()->getName(), $bearsamppBins->getApache()->getPort()),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getApache()->getPort()), TplAestan::GLYPH_LIGHT)
            ) . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getApache()->getName(), $bearsamppBins->getApache()->getSslPort(), true),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getApache()->getSslPort()) . ' (SSL)', TplAestan::GLYPH_RED_LIGHT)
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_UPDATE_ENV_PATH), $bearsamppRoot->getRootPath() . '/nssmEnvPaths.dat') . PHP_EOL;

        $isInstalled = $bearsamppBins->getApache()->getService()->isInstalled();
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

    public static function getActionChangeApachePort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getApache()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionInstallApacheService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinApache::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionRemoveApacheService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinApache::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getMenuApacheDebug()
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_VERSION_NUMBER),
            array($bearsamppLang->getValue(Lang::DEBUG_APACHE_VERSION_NUMBER), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_COMPILE_SETTINGS),
            array($bearsamppLang->getValue(Lang::DEBUG_APACHE_COMPILE_SETTINGS), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_COMPILED_MODULES),
            array($bearsamppLang->getValue(Lang::DEBUG_APACHE_COMPILED_MODULES), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_CONFIG_DIRECTIVES),
            array($bearsamppLang->getValue(Lang::DEBUG_APACHE_CONFIG_DIRECTIVES), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_VHOSTS_SETTINGS),
            array($bearsamppLang->getValue(Lang::DEBUG_APACHE_VHOSTS_SETTINGS), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_LOADED_MODULES),
            array($bearsamppLang->getValue(Lang::DEBUG_APACHE_LOADED_MODULES), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_APACHE, array(BinApache::CMD_SYNTAX_CHECK),
            array($bearsamppLang->getValue(Lang::DEBUG_APACHE_SYNTAX_CHECK), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL;
    }

    public static function getMenuApacheModules()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getApache()->getModulesFromConf() as $module => $switch) {
            $tplSwitchApacheModule = TplApp::getActionMulti(
                self::ACTION_SWITCH_MODULE, array($module, $switch),
                array($module, ($switch == ActionSwitchApacheModule::SWITCH_ON ? TplAestan::GLYPH_CHECK : '')),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchApacheModule[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchApacheModule[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    public static function getActionSwitchApacheModule($module, $switch)
    {
        $switch = $switch == ActionSwitchApacheModule::SWITCH_OFF ? ActionSwitchApacheModule::SWITCH_ON : ActionSwitchApacheModule::SWITCH_OFF;
        return TplApp::getActionRun(Action::SWITCH_APACHE_MODULE, array($module, $switch)) . PHP_EOL .
            TplService::getActionRestart(BinApache::SERVICE_NAME) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    public static function getMenuApacheAlias()
    {
        global $bearsamppLang, $bearsamppBins;

        $tplAddAlias = TplApp::getActionMulti(
            self::ACTION_ADD_ALIAS, null,
            array($bearsamppLang->getValue(Lang::MENU_ADD_ALIAS), TplAestan::GLYPH_ADD),
            false, get_called_class()
        );

        // Items
        $items = $tplAddAlias[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL;

        // Actions
        $actions = PHP_EOL . $tplAddAlias[TplApp::SECTION_CONTENT];

        foreach ($bearsamppBins->getApache()->getAlias() as $alias) {
            $tplEditAlias = TplApp::getActionMulti(
                self::ACTION_EDIT_ALIAS, array($alias),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_ALIAS), $alias), TplAestan::GLYPH_FILE),
                false, get_called_class()
            );

            // Items
            $items .= $tplEditAlias[TplApp::SECTION_CALL] . PHP_EOL;

            // Actions
            $actions .= PHP_EOL . PHP_EOL . $tplEditAlias[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    public static function getActionAddAlias()
    {
        return TplApp::getActionRun(Action::ADD_ALIAS) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getActionEditAlias($alias)
    {
        return TplApp::getActionRun(Action::EDIT_ALIAS, array($alias)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    public static function getMenuApacheVhosts()
    {
        global $bearsamppLang, $bearsamppBins;

        $tplAddVhost = TplApp::getActionMulti(
            self::ACTION_ADD_VHOST, null,
            array($bearsamppLang->getValue(Lang::MENU_ADD_VHOST), TplAestan::GLYPH_ADD),
            false, get_called_class()
        );

        // Items
        $items = $tplAddVhost[TplApp::SECTION_CALL] . PHP_EOL .
        TplAestan::getItemSeparator() . PHP_EOL;

        // Actions
        $actions = PHP_EOL . $tplAddVhost[TplApp::SECTION_CONTENT];

        foreach ($bearsamppBins->getApache()->getVhosts() as $vhost) {
            $tplEditVhost = TplApp::getActionMulti(
                self::ACTION_EDIT_VHOST, array($vhost),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_VHOST), $vhost), TplAestan::GLYPH_FILE),
                false, get_called_class()
            );

            // Items
            $items .= $tplEditVhost[TplApp::SECTION_CALL] . PHP_EOL;

            // Actions
            $actions .= PHP_EOL . PHP_EOL . $tplEditVhost[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    public static function getActionAddVhost()
    {
        return TplApp::getActionRun(Action::ADD_VHOST) . PHP_EOL .
        TplAppReload::getActionReload();
    }

    public static function getActionEditVhost($vhost)
    {
        return TplApp::getActionRun(Action::EDIT_VHOST, array($vhost)) . PHP_EOL .
        TplAppReload::getActionReload();
    }
}
