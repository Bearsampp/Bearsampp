<?php

class ActionCheckVersion
{
    const DISPLAY_OK = 'displayOk';

    private $wbWindow;

    private $wbImage;
    private $wbLinkChangelog;
    private $wbLinkFull;
    private $wbBtnOk;

    private $currentVersion;
    private $latestVersion;

    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppWinbinder;

        if (!file_exists($bearsamppCore->getExec())) {
            Util::startLoading();
            $this->currentVersion = $bearsamppCore->getAppVersion();
            $this->latestVersion =  Util::getLatestVersion();

            if ($this->latestVersion != null && version_compare($this->currentVersion, $this->latestVersion, '<')) {
                $labelFullLink = $bearsamppLang->getValue(Lang::DOWNLOAD) . ' ' . APP_TITLE . ' ' . $this->latestVersion;

                $bearsamppWinbinder->reset();
                $this->wbWindow = $bearsamppWinbinder->createAppWindow($bearsamppLang->getValue(Lang::CHECK_VERSION_TITLE), 480, 170, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

                $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::CHECK_VERSION_AVAILABLE_TEXT), 80, 15, 470, 120);

                $this->wbLinkFull = $bearsamppWinbinder->createHyperLink($this->wbWindow, $labelFullLink, 80, 87, 300, 20, WBC_LINES | WBC_RIGHT);

                $this->wbBtnOk = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_OK), 380, 103);
                $this->wbImage = $bearsamppWinbinder->drawImage($this->wbWindow, $bearsamppCore->getResourcesPath() . '/about.bmp');

                Util::stopLoading();
                $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
                $bearsamppWinbinder->mainLoop();
                $bearsamppWinbinder->reset();
            } elseif (isset($args[0]) && !empty($args[0]) && $args[0] == self::DISPLAY_OK) {
                Util::stopLoading();
                $bearsamppWinbinder->messageBoxInfo(
                    $bearsamppLang->getValue(Lang::CHECK_VERSION_LATEST_TEXT),
                    $bearsamppLang->getValue(Lang::CHECK_VERSION_TITLE));
            }
        }
    }

    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppConfig, $bearsamppWinbinder;

        switch ($id) {
            case $this->wbLinkFull[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->exec($bearsamppConfig->getBrowser(), Util::getVersionUrl($this->latestVersion));
                break;
            case IDCLOSE:
            case $this->wbBtnOk[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}
