<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */
/**
 * Class ActionCheckVersion
 *
 * This class is responsible for checking the current version of the application and displaying a window
 * with the latest version information if an update is available. It also handles the user interaction with
 * the window, such as clicking on links or buttons.
 *
 * @package Bearsampp
 */
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

    private $latestVersionUrl;

    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppWinbinder, $appGithubHeader;

        if (!file_exists($bearsamppCore->getExec())) {
            Util::startLoading();
            $this->currentVersion = $bearsamppCore->getAppVersion();

            // Assuming getLatestVersion now returns an array with version and URL
            $latestVersionData = Util::getLatestVersion(APP_GITHUB_LATEST_URL, APP_GITHUB_TOKEN, $appGithubHeader);

            if ($latestVersionData != null) {
                $bearsamppLatestVersion = $latestVersionData['version'];
                $this->latestVersionUrl = $latestVersionData['url']; // URL of the latest version
                if (version_compare($this->currentVersion, $bearsamppLatestVersion, '<')) {
                    $this->showVersionUpdateWindow($bearsamppLang, $bearsamppWinbinder, $bearsamppCore, $bearsamppLatestVersion);
                } elseif (isset($args[0]) && !empty($args[0]) && $args[0] == self::DISPLAY_OK) {
                    $this->showVersionOkMessageBox($bearsamppLang, $bearsamppWinbinder);
                }
            }
        }
    }

    private function showVersionUpdateWindow($lang, $winbinder, $core, $bearsamppLatestVersion)
    {
        $labelFullLink = $lang->getValue(Lang::DOWNLOAD) . ' ' . APP_TITLE . ' ' . $bearsamppLatestVersion;

        $winbinder->reset();
        $this->wbWindow = $winbinder->createAppWindow($lang->getValue(Lang::CHECK_VERSION_TITLE), 380, 170, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        $winbinder->createLabel($this->wbWindow, $lang->getValue(Lang::CHECK_VERSION_AVAILABLE_TEXT), 80, 35, 370, 120);

        $this->wbLinkFull = $winbinder->createHyperLink($this->wbWindow, $labelFullLink, 80, 87, 200, 20, WBC_LINES | WBC_RIGHT);

        $this->wbBtnOk = $winbinder->createButton($this->wbWindow, $lang->getValue(Lang::BUTTON_OK), 280, 103);
        $this->wbImage = $winbinder->drawImage($this->wbWindow, $core->getResourcesPath() . '/homepage/img/about.bmp');

        Util::stopLoading();
        $winbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $winbinder->mainLoop();
        $winbinder->reset();
    }

    private function showVersionOkMessageBox($lang, $winbinder)
    {
        Util::stopLoading();
        $winbinder->messageBoxInfo(
            $lang->getValue(Lang::CHECK_VERSION_LATEST_TEXT),
            $lang->getValue(Lang::CHECK_VERSION_TITLE)
        );
    }

    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppConfig, $bearsamppWinbinder;

        switch ($id) {
            case $this->wbLinkFull[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->exec($bearsamppConfig->getBrowser(), $this->latestVersionUrl);
                break;
            case IDCLOSE:
            case $this->wbBtnOk[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}
