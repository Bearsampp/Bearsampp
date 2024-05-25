<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppNodejs
 *
 * This class provides methods to generate menu items and actions related to the Node.js module in the Bearsampp application.
 * It handles enabling/disabling Node.js, switching between different Node.js versions, and provides quick access to Node.js configuration and console.
 *
 * Methods:
 * - process(): Main method to generate the Node.js menu item with enable/disable functionality based on current status.
 * - getMenuNodejs(): Generates the submenu items for Node.js including options to download, enable/disable, switch versions, access the console, and edit the configuration file.
 * - getMenuNodejsVersions(): Generates the submenu items for switching between different Node.js versions.
 * - getActionEnableNodejs($enable): Returns the action string to enable or disable Node.js.
 * - getActionSwitchNodejsVersion($version): Returns the action string to switch to a specific Node.js version.
 */
class TplAppNodejs
{
    const MENU = 'nodejs';
    const MENU_VERSIONS = 'nodejsVersions';

    const ACTION_ENABLE = 'enableNodejs';
    const ACTION_SWITCH_VERSION = 'switchNodejsVersion';

    /**
     * Main processing method for the Node.js menu.
     * This method constructs the menu item for Node.js in the application's UI, enabling or disabling it based on its current state.
     *
     * @global object $bearsamppLang Language handler object to fetch localized strings.
     * @global object $bearsamppBins Binaries handler object to check if Node.js is enabled.
     * @return string Returns the menu item for Node.js, formatted as a string.
     */
    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable($bearsamppLang->getValue(Lang::NODEJS), self::MENU, get_called_class(), $bearsamppBins->getNodejs()->isEnable());
    }

    /**
     * Constructs the Node.js menu including options like download, enable/disable, versions, console, and configuration.
     * This method dynamically builds the Node.js menu based on whether Node.js is enabled or disabled.
     *
     * @global object $bearsamppBins Binaries handler object to access Node.js functionalities.
     * @global object $bearsamppLang Language handler object to fetch localized strings.
     * @global object $bearsamppTools Tools handler object to access tool-specific functionalities.
     * @return string Returns the complete Node.js menu as a string.
     */
    public static function getMenuNodejs()
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppTools;
        $resultItems = $resultActions = '';

        $isEnabled = $bearsamppBins->getNodejs()->isEnable();

        // Download
        $resultItems .= TplAestan::getItemLink(
        $bearsamppLang->getValue(Lang::DOWNLOAD_MORE),
            Util::getWebsiteUrl('module/nodejs', '#releases'),
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
            $resultActions .= $tplVersions[TplApp::SECTION_CONTENT];

            // Console
            $resultItems .= TplAestan::getItemConsoleZ(
                $bearsamppLang->getValue(Lang::CONSOLE),
                TplAestan::GLYPH_CONSOLEZ,
                $bearsamppTools->getConsoleZ()->getTabTitleNodejs()
            ) . PHP_EOL;

            // Conf
            $resultItems .= TplAestan::getItemNotepad(basename($bearsamppBins->getNodejs()->getConf()), $bearsamppBins->getNodejs()->getConf()) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Constructs the submenu for switching between different Node.js versions.
     * This method lists all available Node.js versions and allows the user to switch between them.
     *
     * @global object $bearsamppBins Binaries handler object to access Node.js functionalities.
     * @return string Returns the submenu for Node.js versions, formatted as a string.
     */
    public static function getMenuNodejsVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getNodejs()->getVersionList() as $version) {
            $tplSwitchNodejsVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION, array($version),
                array($version, $version == $bearsamppBins->getNodejs()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchNodejsVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchNodejsVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Constructs the action to enable or disable Node.js.
     * This method generates the command to toggle the enabled state of Node.js and triggers a UI reload.
     *
     * @param bool $enable Specifies whether to enable (true) or disable (false) Node.js.
     * @global object $bearsamppBins Binaries handler object to access Node.js functionalities.
     * @return string Returns the action command to enable/disable Node.js.
     */
    public static function getActionEnableNodejs($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::ENABLE, array($bearsamppBins->getNodejs()->getName(), $enable)) . PHP_EOL .
            TplAppReload::getActionReload();
    }

    /**
     * Constructs the action to switch to a specific Node.js version.
     * This method generates the command to change the Node.js version and triggers a UI reload.
     *
     * @param string $version The version of Node.js to switch to.
     * @global object $bearsamppBins Binaries handler object to access Node.js functionalities.
     * @return string Returns the action command to switch Node.js versions.
     */
    public static function getActionSwitchNodejsVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(Action::SWITCH_VERSION, array($bearsamppBins->getNodejs()->getName(), $version)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
}
