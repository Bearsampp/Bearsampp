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
 * Class TplAppPostgresql
 *
 * This class provides methods to generate menu items and actions for managing PostgreSQL services
 * within the Bearsampp application. It includes functionalities for enabling/disabling PostgreSQL,
 * switching versions, changing ports, changing root passwords, and managing services.
 */
class TplAppPostgresql
{
    const MENU = 'postgresql';
    const MENU_VERSIONS = 'postgresqlVersions';
    const MENU_SERVICE = 'postgresqlService';
    const MENU_DEBUG = 'postgresqlDebug';

    const ACTION_ENABLE = 'enablePostgresql';
    const ACTION_SWITCH_VERSION = 'switchPostgresqlVersion';
    const ACTION_CHANGE_PORT = 'changePostgresqlPort';
    const ACTION_CHANGE_ROOT_PWD = 'changePostgresqlRootPwd';
    const ACTION_INSTALL_SERVICE = 'installPostgresqlService';
    const ACTION_REMOVE_SERVICE = 'removePostgresqlService';

    /**
     * Generates the main PostgreSQL menu with options to enable/disable PostgreSQL and access submenus.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated menu items and actions for PostgreSQL.
     */
    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::POSTGRESQL), self::MENU, get_called_class(), $bearsamppBins->getPostgresql()->isEnable());
    }

    /**
     * Generates the PostgreSQL menu with options for versions, service, debug, and console access.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppTools Provides access to various tools and utilities.
     *
     * @return string The generated menu items and actions for PostgreSQL.
     */
    public static function getMenuPostgresql()
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppTools;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getPostgresql()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/postgresql', '#releases'),
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
                $bearsamppTools->getConsoleZ()->getTabTitlePostgresql()
            ) . PHP_EOL;

            // Conf
            $resultItems .= TplAestan::getItemNotepad(basename($bearsamppBins->getPostgresql()->getConf()), $bearsamppBins->getPostgresql()->getConf()) . PHP_EOL;

            // Errors log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_ERROR_LOGS), $bearsamppBins->getPostgresql()->getErrorLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the PostgreSQL versions menu with options to switch between different versions.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for PostgreSQL versions.
     */
    public static function getMenuPostgresqlVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getPostgresql()->getVersionList() as $version) {
            $tplSwitchPostgresqlVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $version == $bearsamppBins->getPostgresql()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchPostgresqlVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchPostgresqlVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable PostgreSQL.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The flag to enable (1) or disable (0) PostgreSQL.
     * @return string The generated action to enable or disable PostgreSQL.
     */
    public static function getActionEnablePostgresql($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getPostgresql()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch the PostgreSQL version.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     * @return string The generated action to switch the PostgreSQL version.
     */
    public static function getActionSwitchPostgresqlVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getPostgresql()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the PostgreSQL service menu with options to start, stop, restart, and manage the service.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for PostgreSQL service.
     */
    public static function getMenuPostgresqlService()
    {
        global $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );

        $isInstalled = $bearsamppBins->getPostgresql()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getPostgresql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getPostgresql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getPostgresql()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getPostgresql()->getName(), $bearsamppBins->getPostgresql()->getPort()),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getPostgresql()->getPort()), TplAestan::GLYPH_LIGHT)
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

    /**
     * Generates the PostgreSQL debug menu with options to run various debug commands.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated menu items and actions for PostgreSQL debug.
     */
    public static function getMenuPostgresqlDebug()
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::DEBUG_POSTGRESQL, array(BinPostgresql::CMD_VERSION),
            array($bearsamppLang->getValue(Lang::DEBUG_POSTGRESQL_VERSION), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL;
    }

    /**
     * Generates the action to change the PostgreSQL port.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action to change the PostgreSQL port.
     */
    public static function getActionChangePostgresqlPort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getPostgresql()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to change the PostgreSQL root password.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action to change the PostgreSQL root password.
     */
    public static function getActionChangePostgresqlRootPwd()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_DB_ROOT_PWD, array($bearsamppBins->getPostgresql()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install the PostgreSQL service.
     *
     * @return string The generated action to install the PostgreSQL service.
     */
    public static function getActionInstallPostgresqlService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinPostgresql::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove the PostgreSQL service.
     *
     * @return string The generated action to remove the PostgreSQL service.
     */
    public static function getActionRemovePostgresqlService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinPostgresql::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
