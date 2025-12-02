<?php
/*
 *
 *  * Copyright (c) 2021-2024 Bearsampp
 *  * License:  GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class TplAppTools
 *
 * This class provides methods to generate menu items and actions for managing various tools
 * within the Bearsampp application. It includes functionalities for accessing tools like Git, Python,
 * Composer, Ghostscript, Ngrok, Pear, Perl, Ruby and more.
 */
class TplAppTools
{
    // Constants for menu and action identifiers
    const MENU = 'tools';
    const ACTION_GEN_SSL_CERTIFICATE = 'genSslCertificate';

    /**
     * Generates the main Tools menu with options to access various tools.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated menu items and actions for Tools.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::TOOLS), self::MENU, get_called_class());
    }

    /**
     * Generates the Tools menu with options for accessing various tools like Git, Python, Composer, etc.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppCore Provides access to core functionalities and configurations.
     * @global object $bearsamppTools Provides access to various tools and their configurations.
     *
     * @return string The generated menu items and actions for Tools.
     */
    public static function getMenuTools()
    {
        global $bearsamppLang, $bearsamppCore, $bearsamppTools, $bearsamppRoot, $bearsamppBins;
        $resultItems = $resultActions = '';

        // Git
        $tplGit = TplAppGit::process();
        $resultItems .= $tplGit[TplApp::SECTION_CALL] . PHP_EOL;
        $resultActions .= $tplGit[TplApp::SECTION_CONTENT] . PHP_EOL;

        // Python
        $tplPython = TplAppPython::process();
        $resultItems .= $tplPython[TplApp::SECTION_CALL] . PHP_EOL;
        $resultActions .= $tplPython[TplApp::SECTION_CONTENT] . PHP_EOL;

        // Bruno postman IDE
        $resultItems .= TplAestan::getItemExe(
                $bearsamppLang->getValue(Lang::BRUNO),
                $bearsamppTools->getBruno()->getExe(),
                TplAestan::GLYPH_BRUNO
            ) . PHP_EOL;

        // Composer
        $resultItems .= TplAestan::getItemPowerShell(
            $bearsamppLang->getValue(Lang::COMPOSER),
            TplAestan::GLYPH_COMPOSER,
            null,
            $bearsamppTools->getPowerShell()->getTabTitleComposer(),
            $bearsamppRoot->getWwwPath(),
            null
        ) . PHP_EOL;

        // Ghostscript
        $resultItems .= TplAestan::getItemPowerShell(
            $bearsamppLang->getValue(Lang::GHOSTSCRIPT),
            TplAestan::GLYPH_GHOSTSCRIPT,
            null,
            $bearsamppTools->getPowerShell()->getTabTitleGhostscript(),
            $bearsamppRoot->getWwwPath(),
            null
        ) . PHP_EOL;

        // Ngrok
        $resultItems .= TplAestan::getItemPowerShell(
            $bearsamppLang->getValue(Lang::NGROK),
            TplAestan::GLYPH_NGROK,
            null,
            $bearsamppTools->getPowerShell()->getTabTitleNgrok(),
            $bearsamppRoot->getWwwPath(),
            null
        ) . PHP_EOL;

        // Pear
        $resultItems .= TplAestan::getItemPowerShell(
            $bearsamppLang->getValue(Lang::PEAR),
            TplAestan::GLYPH_PEAR,
            null,
            $bearsamppTools->getPowerShell()->getTabTitlePear(),
            $bearsamppBins->getPhp()->getSymlinkPath() . '/pear',
            null
        ) . PHP_EOL;

        // Perl
        $resultItems .= TplAestan::getItemPowerShell(
            $bearsamppLang->getValue(Lang::PERL),
            TplAestan::GLYPH_PERL,
            null,
            $bearsamppTools->getPowerShell()->getTabTitlePerl(),
            $bearsamppRoot->getWwwPath(),
            null
        ) . PHP_EOL;

        // Ruby
        $resultItems .= TplAestan::getItemPowerShell(
            $bearsamppLang->getValue(Lang::RUBY),
            TplAestan::GLYPH_RUBY,
            null,
            $bearsamppTools->getPowerShell()->getTabTitleRuby(),
            $bearsamppRoot->getWwwPath(),
            null
        ) . PHP_EOL;

        // Line Separator
        $resultItems .= TplAestan::getItemSeparator() . PHP_EOL;

        // Console
        $resultItems .= TplAestan::getItemPowerShell(
            $bearsamppLang->getValue(Lang::CONSOLE),
            TplAestan::GLYPH_POWERSHELL
        ) . PHP_EOL;

        // HostsEditor
        $resultItems .= TplAestan::getItemExe(
            $bearsamppLang->getValue(Lang::HOSTSEDITOR),
            $bearsamppCore->getHostsEditorExe(),
            TplAestan::GLYPH_HOSTSEDITOR
        ) . PHP_EOL;

        // Pwgen password manager
        $resultItems .= TplAestan::getItemExe(
                $bearsamppLang->getValue(Lang::PWGEN),
                $bearsamppCore->getPwgenExe(),
                TplAestan::GLYPH_PWGEN
            ) . PHP_EOL;

        // Generate SSL Certificate
        $tplGenSslCertificate = TplApp::getActionMulti(
            self::ACTION_GEN_SSL_CERTIFICATE, null,
            array($bearsamppLang->getValue(Lang::MENU_GEN_SSL_CERTIFICATE), TplAestan::GLYPH_SSL_CERTIFICATE),
            false, get_called_class()
        );
        $resultItems .= $tplGenSslCertificate[TplApp::SECTION_CALL] . PHP_EOL;
        $resultActions .= $tplGenSslCertificate[TplApp::SECTION_CONTENT];

        return $resultItems . PHP_EOL . $resultActions;
    }

    /**
     * Generates the action to generate an SSL certificate.
     *
     * @return string The generated action to generate an SSL certificate.
     */
    public static function getActionGenSslCertificate()
    {
        return TplApp::getActionRun(Action::GEN_SSL_CERTIFICATE);
    }
}
