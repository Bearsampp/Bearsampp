<?php

class TplAppGit
{
    const MENU = 'git';
    const MENU_REPOS = 'gitRepos';

    const ACTION_REFRESH_REPOS = 'refreshGitRepos';
    const ACTION_REFRESH_REPOS_STARTUP = 'refreshGitReposStartup';

    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::GIT), self::MENU, get_called_class());
    }

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

    public static function getActionRefreshGitRepos()
    {
        return TplApp::getActionRun(Action::REFRESH_REPOS, array(ActionRefreshRepos::GIT)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }

    public static function getActionRefreshGitReposStartup($scanStartup)
    {
        return TplApp::getActionRun(Action::REFRESH_REPOS_STARTUP, array(ActionRefreshRepos::GIT, $scanStartup)) . PHP_EOL .
            TplAppReload::getActionReload() . PHP_EOL;
    }
}
