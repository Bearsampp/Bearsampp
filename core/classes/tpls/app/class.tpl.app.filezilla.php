<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppFilezilla
 *
 * This class provides methods to generate actions and menu items for managing the Filezilla module in the Bearsampp application.
 * It includes functionalities for enabling/disabling Filezilla, switching versions, changing ports, and managing services.
 */
class TplAppFilezilla
{
    const MENU = 'filezilla';
    const MENU_VERSIONS = 'filezillaVersions';
    const MENU_SERVICE = 'filezillaService';

    const ACTION_ENABLE = 'enableFilezilla';
    const ACTION_SWITCH_VERSION = 'switchFilezillaVersion';
    const ACTION_CHANGE_PORT = 'changeFilezillaPort';
    const ACTION_INSTALL_SERVICE = 'installFilezillaService';
    const ACTION_REMOVE_SERVICE = 'removeFilezillaService';

    /**
     * Generates the menu item and associated actions for enabling/disabling Filezilla.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated menu item and actions for enabling/disabling Filezilla.
     */
    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::FILEZILLA), self::MENU, get_called_class(), $bearsamppBins->getFilezilla()->isEnable());
    }

    /**
     * Generates the menu items and actions for managing Filezilla.
     *
     * This method creates menu items for downloading, enabling/disabling, switching versions, managing services,
     * accessing the admin interface, and viewing logs for Filezilla.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated menu items and actions for managing Filezilla.
     */
    public static function getMenuFilezilla()
    {
        global $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getFilezilla()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
            $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/filezilla', '#releases'),
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

            // Admin interface
            $resultItems .= TplAestan::getItemExe(
                $bearsamppLang->getValue(Lang::ADMINISTRATION),
                $bearsamppBins->getFilezilla()->getItfExe(),
                TplAestan::GLYPH_FILEZILLA
            ) . PHP_EOL;

            // Log
            $resultItems .= TplAestan::getItemNotepad($bearsamppLang->getValue(Lang::MENU_LOGS), $bearsamppBins->getFilezilla()->getLog()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the menu items and actions for switching Filezilla versions.
     *
     * This method creates menu items for each available version of Filezilla, allowing the user to switch between them.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for switching Filezilla versions.
     */
    public static function getMenuFilezillaVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getFilezilla()->getVersionList() as $version) {
            $tplSwitchFilezillaVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $version == $bearsamppBins->getFilezilla()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchFilezillaVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchFilezillaVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable Filezilla.
     *
     * This method creates the action string for enabling or disabling Filezilla, and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The enable/disable flag (1 for enable, 0 for disable).
     * @return string The generated action string for enabling/disabling Filezilla.
     */
    public static function getActionEnableFilezilla($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getFilezilla()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch Filezilla versions.
     *
     * This method creates the action string for switching Filezilla versions, and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     * @return string The generated action string for switching Filezilla versions.
     */
    public static function getActionSwitchFilezillaVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getFilezilla()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the menu items and actions for managing Filezilla services.
     *
     * This method creates menu items for starting, stopping, restarting, checking ports, changing ports, and installing/removing the Filezilla service.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for managing Filezilla services.
     */
    public static function getMenuFilezillaService()
    {
        global $bearsamppLang, $bearsamppBins;

        $tplChangePort = TplApp::getActionMulti(
            self::ACTION_CHANGE_PORT, null,
            array($bearsamppLang->getValue(Lang::MENU_CHANGE_PORT), TplAestan::GLYPH_NETWORK),
            false, get_called_class()
        );

        $result = TplAestan::getItemActionServiceStart($bearsamppBins->getFilezilla()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceStop($bearsamppBins->getFilezilla()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemActionServiceRestart($bearsamppBins->getFilezilla()->getService()->getName()) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getFilezilla()->getName(), $bearsamppBins->getFilezilla()->getPort()),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getFilezilla()->getPort()), TplAestan::GLYPH_LIGHT)
            ) . PHP_EOL .
            TplApp::getActionRun(
                Action::CHECK_PORT, array($bearsamppBins->getFilezilla()->getName(), $bearsamppBins->getFilezilla()->getSslPort(), true),
                array(sprintf($bearsamppLang->getValue(Lang::MENU_CHECK_PORT), $bearsamppBins->getFilezilla()->getSslPort()) . ' (SSL)', TplAestan::GLYPH_RED_LIGHT)
            ) . PHP_EOL .
            $tplChangePort[TplApp::SECTION_CALL] . PHP_EOL;

        $isInstalled = $bearsamppBins->getFilezilla()->getService()->isInstalled();
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
     * Generates the action to change the Filezilla port.
     *
     * This method creates the action string for changing the Filezilla port, and includes a command to reload the application.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing the Filezilla port.
     */
    public static function getActionChangeFilezillaPort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getFilezilla()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install the Filezilla service.
     *
     * This method creates the action string for installing the Filezilla service, and includes a command to reload the application.
     *
     * @return string The generated action string for installing the Filezilla service.
     */
    public static function getActionInstallFilezillaService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinFilezilla::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove the Filezilla service.
     *
     * This method creates the action string for removing the Filezilla service, and includes a command to reload the application.
     *
     * @return string The generated action string for removing the Filezilla service.
     */
    public static function getActionRemoveFilezillaService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinFilezilla::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
