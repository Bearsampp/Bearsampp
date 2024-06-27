<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppMailpit
 *
 * This class provides methods to generate menus and actions for managing the Mailpit application.
 * It includes functionalities for enabling/disabling Mailpit, switching versions, changing ports,
 * and managing the Mailpit service.
 */
class TplAppMailpit
{
    const MENU = 'mailpit';
    const MENU_VERSIONS = 'mailpitVersions';
    const MENU_SERVICE = 'mailpitService';

    const ACTION_ENABLE = 'enableMailpit';
    const ACTION_SWITCH_VERSION = 'switchMailpitVersion';
    const ACTION_CHANGE_PORT = 'changeMailpitPort';
    const ACTION_INSTALL_SERVICE = 'installMailpitService';
    const ACTION_REMOVE_SERVICE = 'removeMailpitService';

    /**
     * Processes the Mailpit menu.
     *
     * This method generates the menu for enabling or disabling Mailpit.
     * It uses the global language object to retrieve the localized string for Mailpit.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated menu for enabling or disabling Mailpit.
     */
    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::MAILPIT), self::MENU, get_called_class(), $bearsamppBins->getMailpit()->isEnable());
    }

    /**
     * Generates the Mailpit menu.
     *
     * This method creates the menu items and associated actions for Mailpit, including options for downloading,
     * enabling, switching versions, managing the service, and viewing logs.
     *
     * @global object $bearsamppRoot Provides access to the root path of the application.
     * @global object $bearsamppConfig Provides access to the application configuration.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated Mailpit menu items and actions.
     */
    public static function getMenuMailpit()
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getMailpit()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
                $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
                Util::getWebsiteUrl('module/mailpit', '#releases'),
                false,
                TplAestan::GLYPH_BROWSER
            ) . PHP_EOL;

        // Enable
        $tplEnable     = TplApp::getActionMulti(
            self::ACTION_ENABLE, array($isEnabled ? Config::DISABLED : Config::ENABLED),
            array($bearsamppLang->getValue(Lang::MENU_ENABLE), $isEnabled ? TplAestan::GLYPH_CHECK : ''),
            false, get_called_class()
        );
        $resultItems   .= $tplEnable[TplApp::SECTION_CALL] . PHP_EOL;
        $resultActions .= $tplEnable[TplApp::SECTION_CONTENT] . PHP_EOL;

        if ($isEnabled) {
            $resultItems .= TplAestan::getItemSeparator() . PHP_EOL;

            // Versions
            $tplVersions   = TplApp::getMenu($bearsamppLang->getValue(Lang::VERSIONS), self::MENU_VERSIONS, get_called_class());
            $resultItems   .= $tplVersions[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplVersions[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Service
            $tplService    = TplApp::getMenu($bearsamppLang->getValue(Lang::SERVICE), self::MENU_SERVICE, get_called_class());
            $resultItems   .= $tplService[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplService[TplApp::SECTION_CONTENT] . PHP_EOL;

            // Web page
            $resultItems .= TplAestan::getItemExe(
                    $bearsamppLang->getValue(Lang::MAILPIT),
                    $bearsamppConfig->getBrowser(),
                    TplAestan::GLYPH_WEB_PAGE,
                    $bearsamppRoot->getLocalUrl() . ':' . $bearsamppBins->getMailpit()->getUiPort() . '/' . $bearsamppBins->getMailpit()->getWebRoot()
                ) . PHP_EOL;

            // Log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_LOGS), $bearsamppBins->getMailpit()->getLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the Mailpit versions menu.
     *
     * This method creates the menu items and associated actions for switching between different versions of Mailpit.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated Mailpit versions menu items and actions.
     */
    public static function getMenuMailpitVersions()
    {
        global $bearsamppBins;
        $items   = '';
        $actions = '';

        foreach ($bearsamppBins->getMailpit()->getVersionList() as $version) {
            $tplSwitchMailpitVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $version == $bearsamppBins->getMailpit()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchMailpitVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchMailpitVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable Mailpit.
     *
     * This method creates the action string for enabling or disabling Mailpit and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The enable flag (1 to enable, 0 to disable).
     * @return string The generated action string for enabling or disabling Mailpit.
     */
    public static function getActionEnableMailpit($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getMailpit()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch the Mailpit version.
     *
     * This method creates the action string for switching the Mailpit version and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     * @return string The generated action string for switching the Mailpit version.
     */
    public static function getActionSwitchMailpitVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getMailpit()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the Mailpit service menu.
     *
     * This method creates the menu items and associated actions for managing the Mailpit service, including starting, stopping,
     * restarting, changing ports, and installing or removing the service.
     *
     * @global object $bearsamppRoot Provides access to the root path of the application.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated Mailpit service menu items and actions.
     */
    public static function getMenuMailpitService()
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );

        $isInstalled = $bearsamppBins->getMailpit()->getService()->isInstalled();

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getMailpit()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getMailpit()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getMailpit()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getMailpit()->getName(), $bearsamppBins->getMailpit()->getSmtpPort()),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getMailpit()->getSmtpPort()), TplAestan::GLYPH_LIGHT)
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL .
            TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_UPDATE_ENV_PATH), $bearsamppRoot->getRootPath() . '/nssmEnvPaths.dat') . PHP_EOL;

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

    /**
     * Generates the action to change the Mailpit port.
     *
     * This method creates the action string for changing the Mailpit port and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing the Mailpit port.
     */
    public static function getActionChangeMailpitPort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getMailpit()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install the Mailpit service.
     *
     * This method creates the action string for installing the Mailpit service and includes a command to reload the application.
     *
     * @return string The generated action string for installing the Mailpit service.
     */
    public static function getActionInstallMailpitService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMailpit::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove the Mailpit service.
     *
     * This method creates the action string for removing the Mailpit service and includes a command to reload the application.
     *
     * @return string The generated action string for removing the Mailpit service.
     */
    public static function getActionRemoveMailpitService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMailpit::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
