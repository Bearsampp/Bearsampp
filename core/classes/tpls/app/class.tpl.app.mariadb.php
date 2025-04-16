<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class TplAppMariadb
{
    const MENU = 'mariadb';
    const MENU_VERSIONS = 'mariadbVersions';
    const MENU_SERVICE = 'mariadbService';
    const MENU_DEBUG = 'mariadbDebug';

    const ACTION_ENABLE = 'enableMariadb';
    const ACTION_SWITCH_VERSION = 'switchMariadbVersion';
    const ACTION_CHANGE_PORT = 'changeMariadbPort';
    const ACTION_CHANGE_ROOT_PWD = 'changeMariadbRootPwd';
    const ACTION_INSTALL_SERVICE = 'installMariadbService';
    const ACTION_REMOVE_SERVICE = 'removeMariadbService';

    /**
     * Processes the MariaDB menu.
     *
     * This method generates the MariaDB menu and determines if MariaDB is enabled.
     * It uses the global language and binaries objects to retrieve the necessary values.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated MariaDB menu.
     */
    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::MARIADB), self::MENU, get_called_class(), $bearsamppBins->getMariadb()->isEnable());
    }

    /**
     * Generates the MariaDB menu items and actions.
     *
     * This method creates menu items and actions for managing MariaDB, including enabling/disabling,
     * switching versions, managing services, and debugging. It uses the global language, binaries,
     * and tools objects to retrieve the necessary values.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppTools Provides access to various tools and utilities.
     *
     * @return string The generated MariaDB menu items and actions.
     */
    public static function getMenuMariadb()
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppTools;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getMariadb()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink( $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/mariadb', '#releases'),
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
                $bearsamppTools->getConsoleZ()->getTabTitleMariadb()
            ) . PHP_EOL;

            // Conf
            $resultItems .= TplAestan::getItemNotepad(basename($bearsamppBins->getMariadb()->getConf()), $bearsamppBins->getMariadb()->getConf()) . PHP_EOL;

            // Errors log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_ERROR_LOGS), $bearsamppBins->getMariadb()->getErrorLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the MariaDB versions menu items and actions.
     *
     * This method creates menu items and actions for switching between different MariaDB versions.
     * It uses the global binaries object to retrieve the available versions.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated MariaDB versions menu items and actions.
     */
    public static function getMenuMariadbVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getMariadb()->getVersionList() as $version) {
            $tplSwitchMariadbVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $version == $bearsamppBins->getMariadb()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchMariadbVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchMariadbVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable MariaDB.
     *
     * This method creates the action string for enabling or disabling MariaDB.
     * It uses the global binaries object to retrieve the necessary values.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The enable flag (1 for enable, 0 for disable).
     *
     * @return string The generated action string for enabling or disabling MariaDB.
     */
    public static function getActionEnableMariadb($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getMariadb()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch the MariaDB version.
     *
     * This method creates the action string for switching the MariaDB version.
     * It uses the global binaries object to retrieve the necessary values.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     *
     * @return string The generated action string for switching the MariaDB version.
     */
    public static function getActionSwitchMariadbVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getMariadb()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the MariaDB service menu items and actions.
     *
     * This method creates menu items and actions for managing the MariaDB service, including starting,
     * stopping, restarting, changing the port, and changing the root password. It uses the global language
     * and binaries objects to retrieve the necessary values.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated MariaDB service menu items and actions.
     */
    public static function getMenuMariadbService()
    {
        global $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );

        $isInstalled = $bearsamppBins->getMariadb()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getMariadb()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getMariadb()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getMariadb()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getMariadb()->getName(), $bearsamppBins->getMariadb()->getPort()),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getMariadb()->getPort()), TplAestan::GLYPH_LIGHT)
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
     * Generates the MariaDB debug menu items and actions.
     *
     * This method creates menu items and actions for debugging MariaDB, including checking the version,
     * variables, and syntax. It uses the global language object to retrieve the necessary values.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated MariaDB debug menu items and actions.
     */
    public static function getMenuMariadbDebug()
    {
        global $bearsamppLang;

        return TplApp::getActionRun(
            Action::DEBUG_MARIADB, array(BinMariadb::CMD_VERSION),
            array($bearsamppLang->getValue(Lang::DEBUG_MARIADB_VERSION), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_MARIADB, array(BinMariadb::CMD_VARIABLES),
            array($bearsamppLang->getValue(Lang::DEBUG_MARIADB_VARIABLES), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL .
        TplApp::getActionRun(
            Action::DEBUG_MARIADB, array(BinMariadb::CMD_SYNTAX_CHECK),
            array($bearsamppLang->getValue(Lang::DEBUG_MARIADB_SYNTAX_CHECK), TplAestan::GLYPH_DEBUG)
        ) . PHP_EOL;
    }

    /**
     * Generates the action to change the MariaDB port.
     *
     * This method creates the action string for changing the MariaDB port.
     * It uses the global binaries object to retrieve the necessary values.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing the MariaDB port.
     */
    public static function getActionChangeMariadbPort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getMariadb()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to change the MariaDB root password.
     *
     * This method creates the action string for changing the MariaDB root password.
     * It uses the global binaries object to retrieve the necessary values.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing the MariaDB root password.
     */
    public static function getActionChangeMariadbRootPwd()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_DB_ROOT_PWD, array($bearsamppBins->getMariadb()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install the MariaDB service.
     *
     * This method creates the action string for installing the MariaDB service.
     *
     * @return string The generated action string for installing the MariaDB service.
     */
    public static function getActionInstallMariadbService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMariadb::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove the MariaDB service.
     *
     * This method creates the action string for removing the MariaDB service.
     *
     * @return string The generated action string for removing the MariaDB service.
     */
    public static function getActionRemoveMariadbService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMariadb::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
