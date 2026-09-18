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
    /** @var int Progress bar gauge value for the save operation. */
    const GAUGE_SAVE = 2;
    /** @var resource The main application window. */
    private $wbWindow;
    /** @var resource The about dialog image handle drawn on the window. */
    private $wbImage;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the homepage hyperlink control. */
    private $wbLinkHomepage;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the donate hyperlink control. */
    private $wbLinkDonate;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the GitHub hyperlink control. */
    private $wbLinkGithub;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the OK button control. */
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
        $this->wbLinkHomepage = $bearsamppWinbinder->createHyperLink($this->wbWindow, HttpClient::getWebsiteUrlNoUtm(), 180, $yPos, 300, 15, WBC_LINES);
        $yPos                 += 20;

        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::DONATE) . ' :', 80, $yPos, 100, 15);
        $this->wbLinkDonate = $bearsamppWinbinder->createHyperLink($this->wbWindow, HttpClient::getWebsiteUrlNoUtm('donate'), 180, $yPos, 300, 15, WBC_LINES);
        $yPos               += 20;

        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::GITHUB) . ' :', 80, $yPos, 100, 15);
        $this->wbLinkGithub = $bearsamppWinbinder->createHyperLink($this->wbWindow, HttpClient::getGithubUserUrl(), 180, $yPos, 300, 15, WBC_LINES);
        $yPos               += 10;

        $this->wbBtnOk = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_OK), 390, 180);

        $this->wbImage = $bearsamppWinbinder->drawImage($this->wbWindow, Path::getImagesPath() . '/about.bmp');

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
                $bearsamppWinbinder->exec($bearsamppConfig->getBrowser(), HttpClient::getWebsiteUrl());
                break;
            case $this->wbLinkDonate[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->exec($bearsamppConfig->getBrowser(), HttpClient::getWebsiteUrl('donate'));
                break;
            case $this->wbLinkGithub[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->exec($bearsamppConfig->getBrowser(), HttpClient::getGithubUserUrl());
                break;
            case IDCLOSE:
            case $this->wbBtnOk[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}

