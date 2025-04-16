<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppMemcached
 *
 * This class provides methods to generate menu items and actions for managing Memcached within the Bearsampp application.
 * It includes functionalities for enabling/disabling Memcached, switching versions, changing ports, and managing services.
 */
class TplAppMemcached
{
    // Constants for menu and action identifiers
    const MENU = 'memcached';
    const MENU_VERSIONS = 'memcachedVersions';
    const MENU_SERVICE = 'memcachedService';

    const ACTION_ENABLE = 'enableMemcached';
    const ACTION_SWITCH_VERSION = 'switchMemcachedVersion';
    const ACTION_CHANGE_PORT = 'changeMemcachedPort';
    const ACTION_INSTALL_SERVICE = 'installMemcachedService';
    const ACTION_REMOVE_SERVICE = 'removeMemcachedService';

    /**
     * Generates the menu item for enabling/disabling Memcached.
     *
     * This method creates a menu item for enabling or disabling Memcached and defines the actions to be taken
     * when the menu item is selected. It uses the global language object to retrieve the localized string for Memcached.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated menu item for enabling/disabling Memcached.
     */
    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::MEMCACHED), self::MENU, get_called_class(), $bearsamppBins->getMemcached()->isEnable());
    }

    /**
     * Generates the menu items and actions for managing Memcached.
     *
     * This method creates menu items for downloading Memcached, enabling/disabling it, switching versions, managing services,
     * updating the environment PATH, and viewing logs. It uses the global language object to retrieve localized strings.
     *
     * @global object $bearsamppRoot Provides access to the root path of the application.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated menu items and actions for managing Memcached.
     */
    public static function getMenuMemcached()
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppLang;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getMemcached()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink($bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
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

    /**
     * Generates the menu items and actions for switching Memcached versions.
     *
     * This method creates menu items for each available Memcached version and defines the actions to be taken
     * when a version is selected. It uses the global language object to retrieve localized strings.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for switching Memcached versions.
     */
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

    /**
     * Generates the action to enable or disable Memcached.
     *
     * This method creates the action string for enabling or disabling Memcached. It includes commands to reload
     * the application after the action is performed.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param int $enable The value indicating whether to enable or disable Memcached.
     * @return string The generated action string for enabling or disabling Memcached.
     */
    public static function getActionEnableMemcached($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getMemcached()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch Memcached versions.
     *
     * This method creates the action string for switching Memcached versions. It includes commands to reload
     * the application after the action is performed.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @param string $version The version to switch to.
     * @return string The generated action string for switching Memcached versions.
     */
    public static function getActionSwitchMemcachedVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getMemcached()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the menu items and actions for managing Memcached services.
     *
     * This method creates menu items for starting, stopping, and restarting the Memcached service, as well as
     * checking and changing the port, and installing or removing the service. It uses the global language object
     * to retrieve localized strings.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for managing Memcached services.
     */
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

    /**
     * Generates the action to change the Memcached port.
     *
     * This method creates the action string for changing the Memcached port. It includes commands to reload
     * the application after the action is performed.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for changing the Memcached port.
     */
    public static function getActionChangeMemcachedPort()
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::CHANGE_PORT, array($bearsamppBins->getMemcached()->getName())) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to install the Memcached service.
     *
     * This method creates the action string for installing the Memcached service. It includes commands to reload
     * the application after the action is performed.
     *
     * @return string The generated action string for installing the Memcached service.
     */
    public static function getActionInstallMemcachedService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMemcached::SERVICE_NAME, ActionService::INSTALL)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Generates the action to remove the Memcached service.
     *
     * This method creates the action string for removing the Memcached service. It includes commands to reload
     * the application after the action is performed.
     *
     * @return string The generated action string for removing the Memcached service.
     */
    public static function getActionRemoveMemcachedService()
    {
        return TplApp::getActionRun(Action::SERVICE, array(BinMemcached::SERVICE_NAME, ActionService::REMOVE)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
