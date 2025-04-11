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
 * Class TplAppGit
 *
 * This class provides methods to generate and manage Git-related menu items and actions
 * within the Bearsampp application. It includes functionalities for creating Git menus,
 * refreshing repositories, and handling startup actions.
 */
class TplAppGit
{
    // Constants for menu and action identifiers
    const MENU = 'git';
    const MENU_REPOS = 'gitRepos';

    const ACTION_REFRESH_REPOS = 'refreshGitRepos';
    const ACTION_REFRESH_REPOS_STARTUP = 'refreshGitReposStartup';

    /**
     * Processes and generates the main Git menu.
     *
     * This method generates the main Git menu item, which includes options for managing
     * Git repositories and actions related to Git functionalities.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated Git menu item and actions.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::GIT), self::MENU, get_called_class());
    }

    /**
     * Generates the Git menu with various options.
     *
     * This method creates the Git menu with options for opening Git console, Git GUI,
     * refreshing repositories, and setting up repository scanning at startup.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppTools Provides access to various tools and utilities.
     *
     * @return string The generated Git menu content.
     */
    public static function getMenuGit()
    {
        global $bearsamppLang, $bearsamppTools;

        $tplRepos = TplApp::getMenu($bearsamppLang->getValue(Lang::REPOS), self::MENU_REPOS, get_called_class());
        $emptyRepos = count(explode(PHP_EOL, $tplRepos[TplApp::SECTION_CONTENT])) == 2;
        $isScanStartup = $bearsamppTools->getGit()->isScanStartup();

        $tplRefreshRepos = TplApp::getActionMulti(
            self::ACTION_REFRESH_REPOS, null,
            array($bearsamppLang->getValue(Lang::MENU_REFRESH_REPOS), TplAestan::GLYPH_RELOAD),
            false, get_called_class()
        );
        $tplRefreshReposStartup = TplApp::getActionMulti(
            self::ACTION_REFRESH_REPOS_STARTUP, array($isScanStartup ? Config::DISABLED : Config::ENABLED),
            array($bearsamppLang->getValue(Lang::MENU_SCAN_REPOS_STARTUP), $isScanStartup ? TplAestan::GLYPH_CHECK : ''),
            false, get_called_class()
        );

        /* get path for git gui */
        $gitgui = $bearsamppTools->getGit()->getSymlinkPath() . '/cmd';

        return TplAestan::getItemConsoleZ(
                $bearsamppLang->getValue(Lang::GIT_CONSOLE),
                TplAestan::GLYPH_GIT,
                $bearsamppTools->getConsoleZ()->getTabTitleGit()
            ) . PHP_EOL .
            TplAestan::getItemExe(
                    $bearsamppLang->getValue(Lang::GITGUI),
                    $gitgui . '/git-gui',
                    TplAestan::GLYPH_GIT
                ) . PHP_EOL .
            TplAestan::getItemSeparator() . PHP_EOL .

            // Items
            (!$emptyRepos ? $tplRepos[TplApp::SECTION_CALL] . PHP_EOL : '') .
            $tplRefreshRepos[TplApp::SECTION_CALL] . PHP_EOL .
            $tplRefreshReposStartup[TplApp::SECTION_CALL] . PHP_EOL .

            // Actions
            (!$emptyRepos ? $tplRepos[TplApp::SECTION_CONTENT] . PHP_EOL : PHP_EOL) .
            $tplRefreshRepos[TplApp::SECTION_CONTENT] . PHP_EOL .
            $tplRefreshReposStartup[TplApp::SECTION_CONTENT];
    }

    /**
     * Generates the Git repositories menu.
     *
     * This method creates the menu for listing and managing Git repositories found
     * by the application.
     *
     * @global object $bearsamppTools Provides access to various tools and utilities.
     *
     * @return string The generated Git repositories menu content.
     */
    public static function getMenuGitRepos()
    {
        global $bearsamppTools;
        $result = '';

        foreach ($bearsamppTools->getGit()->findRepos() as $repo) {
            $result .= TplAestan::getItemConsoleZ(
                basename($repo),
                TplAestan::GLYPH_GIT,
                $bearsamppTools->getConsoleZ()->getTabTitleGit(),
                $bearsamppTools->getConsoleZ()->getTabTitleGit($repo),
                $repo
            ) . PHP_EOL;
        }

        return $result;
    }

    /**
     * Generates the action to refresh Git repositories.
     *
     * This method creates the action string for refreshing Git repositories. It includes
     * commands to reload the application after refreshing the repositories.
     *
     * @return string The generated action string for refreshing Git repositories.
     */
    public static function getActionRefreshGitRepos()
    {
        return TplApp::getActionRun(Action::REFRESH_REPOS, array(ActionRefreshRepos::GIT)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    /**
     * Generates the action to refresh Git repositories at startup.
     *
     * This method creates the action string for setting up repository scanning at startup.
     * It includes commands to reload the application after setting the startup action.
     *
     * @param int $scanStartup The flag indicating whether to enable or disable scanning at startup.
     *
     * @return string The generated action string for refreshing Git repositories at startup.
     */
    public static function getActionRefreshGitReposStartup($scanStartup)
    {
        return TplApp::getActionRun(Action::REFRESH_REPOS_STARTUP, array(ActionRefreshRepos::GIT, $scanStartup)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
}
