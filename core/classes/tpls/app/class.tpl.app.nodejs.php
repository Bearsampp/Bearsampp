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
 * This class provides methods to generate and manage menu items and actions related to Node.js within the Bearsampp application.
 * It includes functionalities for enabling/disabling Node.js, switching Node.js versions, and generating menus for Node.js actions.
 */
class TplAppNodejs
{
    // Constants for menu and action identifiers
    const MENU = 'nodejs';
    const MENU_VERSIONS = 'nodejsVersions';

    const ACTION_ENABLE = 'enableNodejs';
    const ACTION_SWITCH_VERSION = 'switchNodejsVersion';

    /**
     * Processes and generates the Node.js menu.
     *
     * This method generates the menu for Node.js, including options to enable/disable Node.js and switch versions.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return array The generated menu for Node.js.
     */
    public static function process()
    {
        global $bearsamppLang, $bearsamppBins;

        return TplApp::getMenuEnable(
            $bearsamppLang->getValue(Lang::NODEJS),
            self::MENU,
            get_called_class(),
            $bearsamppBins->getNodejs()->isEnable()
        );
    }

    /**
     * Generates the Node.js menu items and associated actions.
     *
     * This method creates menu items for Node.js, including options to download more versions, enable/disable Node.js,
     * switch versions, open a console, and edit the configuration file.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppTools Provides access to various tools used in the application.
     *
     * @return string The generated menu items and actions for Node.js.
     */
    public static function getMenuNodejs()
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppTools, $bearsamppRoot;
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
            self::ACTION_ENABLE,
            array($isEnabled ? Config::DISABLED : Config::ENABLED),
            array($bearsamppLang->getValue(Lang::MENU_ENABLE), $isEnabled ? TplAestan::GLYPH_CHECK : ''),
            false,
            get_called_class()
        );
        $resultItems .= $tplEnable[TplApp::SECTION_CALL] . PHP_EOL;
        $resultActions .= $tplEnable[TplApp::SECTION_CONTENT] . PHP_EOL;

        if ($isEnabled) {
            $resultItems .= TplAestan::getItemSeparator() . PHP_EOL;

            // Versions
            $tplVersions = TplApp::getMenu(
                $bearsamppLang->getValue(Lang::VERSIONS),
                self::MENU_VERSIONS,
                get_called_class()
            );
            $resultItems .= $tplVersions[TplApp::SECTION_CALL] . PHP_EOL;
            $resultActions .= $tplVersions[TplApp::SECTION_CONTENT];

            // Console
            $resultItems .= TplAestan::getItemPowerShell(
                $bearsamppLang->getValue(Lang::CONSOLE),
                TplAestan::GLYPH_NODEJS,
                null,
                $bearsamppTools->getPowerShell()->getTabTitleNodejs(),
                $bearsamppRoot->getWwwPath(),
                null
            ) . PHP_EOL;

            // Conf
            $resultItems .= TplAestan::getItemNotepad(
                basename($bearsamppBins->getNodejs()->getConf()),
                $bearsamppBins->getNodejs()->getConf()
            ) . PHP_EOL;
        }

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the Node.js versions menu items and associated actions.
     *
     * This method creates menu items for switching between different Node.js versions.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated menu items and actions for Node.js versions.
     */
    public static function getMenuNodejsVersions()
    {
        global $bearsamppBins;
        $items = '';
        $actions = '';

        foreach ($bearsamppBins->getNodejs()->getVersionList() as $version) {
            $tplSwitchNodejsVersion = TplApp::getActionMulti(
                self::ACTION_SWITCH_VERSION,
                array($version),
                array($version, $version == $bearsamppBins->getNodejs()->getVersion() ? TplAestan::GLYPH_CHECK : ''),
                false,
                get_called_class()
            );

            // Item
            $items .= $tplSwitchNodejsVersion[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchNodejsVersion[TplApp::SECTION_CONTENT];
        }

        return $items . $actions;
    }

    /**
     * Generates the action to enable or disable Node.js.
     *
     * This method creates the action string for enabling or disabling Node.js. It includes commands to reload the application.
     *
     * @param int $enable The flag indicating whether to enable (1) or disable (0) Node.js.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for enabling or disabling Node.js.
     */
    public static function getActionEnableNodejs($enable)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::ENABLE,
            array($bearsamppBins->getNodejs()->getName(), $enable)
        ) . PHP_EOL . TplAppReload::getActionReload();
    }

    /**
     * Generates the action to switch Node.js versions.
     *
     * This method creates the action string for switching Node.js versions. It includes commands to reload the application.
     *
     * @param string $version The version of Node.js to switch to.
     *
     * @global object $bearsamppBins Provides access to system binaries and their configurations.
     *
     * @return string The generated action string for switching Node.js versions.
     */
    public static function getActionSwitchNodejsVersion($version)
    {
        global $bearsamppBins;

        return TplApp::getActionRun(
            Action::SWITCH_VERSION,
            array($bearsamppBins->getNodejs()->getName(), $version)
        ) . PHP_EOL . TplAppReload::getActionReload() . PHP_EOL;
    }
}
