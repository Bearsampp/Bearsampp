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
 * Class ActionAbout
 * Handles the creation and management of the "About" window in the Bearsampp application.
 */
class ActionAbout
{
    const GAUGE_SAVE = 2;
    private $wbWindow;
    private $wbImage;
    private $wbLinkHomepage;
    private $wbLinkDonate;
    private $wbLinkGithub;
    private $wbBtnOk;

    /**
     * ActionAbout constructor.
     * Initializes the "About" window and its components.
     *
     * @param   array  $args  Arguments passed to the constructor.
     */
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppWinbinder;

        $bearsamppWinbinder->reset();
        $this->wbWindow = $bearsamppWinbinder->createAppWindow($bearsamppLang->getValue(Lang::ABOUT_TITLE), 520, 250, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        // Get the about text
        $aboutText = sprintf($bearsamppLang->getValue(Lang::ABOUT_TEXT), APP_TITLE . ' ' . $bearsamppCore->getAppVersion(), date('Y'), APP_AUTHOR_NAME);

        // Split the text using the custom newline marker "@nl@"
        $textLines = explode('@nl@', $aboutText);

        // Display each line at a specific position
        $yPos       = 20;
        $lineHeight = 20;
        foreach ($textLines as $line) {
            $bearsamppWinbinder->createLabel($this->wbWindow, trim($line), 80, $yPos, 470, $lineHeight);
            $yPos += $lineHeight;
        }

        // Add exactly one line of blank space
        $yPos = 125;

        // Add hyperlinks
        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::WEBSITE) . ' :', 80, $yPos, 100, 15);
        $this->wbLinkHomepage = $bearsamppWinbinder->createHyperLink($this->wbWindow, Util::getWebsiteUrlNoUtm(), 180, $yPos, 300, 15, WBC_LINES);
        $yPos                 += 20;

        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::DONATE) . ' :', 80, $yPos, 100, 15);
        $this->wbLinkDonate = $bearsamppWinbinder->createHyperLink($this->wbWindow, Util::getWebsiteUrlNoUtm('donate'), 180, $yPos, 300, 15, WBC_LINES);
        $yPos               += 20;

        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::GITHUB) . ' :', 80, $yPos, 100, 15);
        $this->wbLinkGithub = $bearsamppWinbinder->createHyperLink($this->wbWindow, Util::getGithubUserUrl(), 180, $yPos, 300, 15, WBC_LINES);
        $yPos               += 10;

        $this->wbBtnOk = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_OK), 390, 180);

        $this->wbImage = $bearsamppWinbinder->drawImage($this->wbWindow, $bearsamppCore->getImagesPath() . '/about.bmp');

        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes window events and handles user interactions.
     *
     * @param   int    $window  The window identifier.
     * @param   int    $id      The control identifier.
     * @param   int    $ctrl    The control object.
     * @param   mixed  $param1  Additional parameter 1.
     * @param   mixed  $param2  Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppConfig, $bearsamppWinbinder;

        switch ($id) {
            case $this->wbLinkHomepage[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->exec($bearsamppConfig->getBrowser(), Util::getWebsiteUrl());
                break;
            case $this->wbLinkDonate[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->exec($bearsamppConfig->getBrowser(), Util::getWebsiteUrl('donate'));
                break;
            case $this->wbLinkGithub[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->exec($bearsamppConfig->getBrowser(), Util::getGithubUserUrl());
                break;
            case IDCLOSE:
            case $this->wbBtnOk[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}
