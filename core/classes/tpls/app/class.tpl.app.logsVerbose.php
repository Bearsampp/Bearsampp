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
 * Class TplAppLogsVerbose
 *
 * This class provides methods to generate and manage the logs verbosity menu and actions
 * within the Bearsampp application. It includes functionalities for creating the logs verbosity
 * menu and processing actions to switch the logs verbosity level.
 */
class TplAppLogsVerbose
{
    // Constant for the logs verbosity menu identifier
    const MENU = 'logsVerbose';

    /**
     * Generates the logs verbosity menu.
     *
     * This method creates a menu for selecting the logs verbosity level. It uses the global language
     * object to retrieve the localized string for the logs verbosity menu.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated menu for selecting the logs verbosity level.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::LOGS_VERBOSE), self::MENU, get_called_class());
    }

    /**
     * Generates the menu items and actions for switching logs verbosity levels.
     *
     * This method creates menu items for each verbosity level and defines the actions to be taken
     * when a verbosity level is selected. It uses the global language object to retrieve the localized
     * strings for each verbosity level and the global configuration object to get the current verbosity level.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppConfig Provides access to the application's configuration settings.
     *
     * @return string The generated menu items and actions for switching logs verbosity levels.
     */
    public static function getMenuLogsVerbose()
    {
        global $bearsamppLang, $bearsamppConfig;

        $items = '';
        $actions = '';

        $verboses = array(
            Config::VERBOSE_SIMPLE => $bearsamppLang->getValue(Lang::VERBOSE_SIMPLE),
            Config::VERBOSE_REPORT => $bearsamppLang->getValue(Lang::VERBOSE_REPORT),
            Config::VERBOSE_DEBUG  => $bearsamppLang->getValue(Lang::VERBOSE_DEBUG),
            Config::VERBOSE_TRACE  => $bearsamppLang->getValue(Lang::VERBOSE_TRACE),
        );

        foreach ($verboses as $verbose => $caption) {
            $tplSwitchLogsVerbose = TplApp::getActionMulti(
                Action::SWITCH_LOGS_VERBOSE, array($verbose),
                array($caption, $verbose == $bearsamppConfig->getLogsVerbose() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchLogsVerbose[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchLogsVerbose[TplApp::SECTION_CONTENT] .  PHP_EOL;
        }

        return $items . $actions;
    }

    /**
     * Generates the action to switch the logs verbosity level.
     *
     * This method creates the action string for switching the logs verbosity level. It includes commands
     * to reload the application after changing the verbosity level.
     *
     * @param int $verbose The verbosity level to switch to.
     *
     * @return string The generated action string for switching the logs verbosity level.
     */
    public static function getActionSwitchLogsVerbose($verbose)
    {
        return TplApp::getActionRun(Action::SWITCH_LOGS_VERBOSE, array($verbose)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
