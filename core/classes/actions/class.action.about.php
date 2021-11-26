<?php

class ActionAbout
{
    private $wbWindow;
    
    private $wbImage;
    private $wbLinkHomepage;
    private $wbLinkDonate;
    private $wbLinkGithub;
    private $wbBtnOk;
    
    const GAUGE_SAVE = 2;
    
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppWinbinder;
        
        $bearsamppWinbinder->reset();
        $this->wbWindow = $bearsamppWinbinder->createAppWindow($bearsamppLang->getValue(Lang::ABOUT_TITLE), 450, 250, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);
        
        $aboutText = sprintf($bearsamppLang->getValue(Lang::ABOUT_TEXT), APP_TITLE . ' ' . $bearsamppCore->getAppVersion(), date('Y'), APP_AUTHOR_NAME);
        $bearsamppWinbinder->createLabel($this->wbWindow, $aboutText, 80, 20, 420, 120);
        
        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::WEBSITE) . ' :', 80, 105, 420, 15);
        $this->wbLinkHomepage = $bearsamppWinbinder->createHyperLink($this->wbWindow, Util::getWebsiteUrlNoUtm(), 180, 105, 250, 15, WBC_LINES);
        
        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::DONATE) . ' :', 80, 125, 420, 15);
        $this->wbLinkDonate = $bearsamppWinbinder->createHyperLink($this->wbWindow, Util::getWebsiteUrlNoUtm('donate'), 180, 125, 250, 15, WBC_LINES | WBC_RIGHT);
        
        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::GITHUB) . ' :', 80, 145, 420, 15);
        $this->wbLinkGithub = $bearsamppWinbinder->createHyperLink($this->wbWindow, Util::getGithubUserUrl(), 180, 145, 250, 15, WBC_LINES | WBC_RIGHT);
        
        $this->wbBtnOk = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_OK), 340, 180);
        
        $this->wbImage = $bearsamppWinbinder->drawImage($this->wbWindow, $bearsamppCore->getResourcesPath() . '/about.bmp');
        
        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }
    
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
