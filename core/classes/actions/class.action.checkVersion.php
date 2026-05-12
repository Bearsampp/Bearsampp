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
    private $githubLatestVersionUrl;
    private $didStartLoading = false;

    /**
     * Constructor for the ActionCheckVersion class.
     *
     * @param array $args Command line arguments passed to the script.
     */
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppWinbinder, $appGithubHeader;

        // Check if we're being called from the version check menu item
        $isMenuCheck = !empty($args[0]) && $args[0] == self::DISPLAY_OK;

        // Start loading only if the exec file doesn't exist or if we're doing a menu check
        if (!file_exists($bearsamppCore->getExec()) || $isMenuCheck) {
            if ($isMenuCheck) {
                Log::debug('ActionCheckVersion: Manual check detected, starting loading');
                Util::startLoading();
                $this->didStartLoading = true;
            }
            $this->currentVersion = $bearsamppCore->getAppVersion();
            Log::debug('ActionCheckVersion: Current version: ' . $this->currentVersion);

            // Assuming getLatestVersion now returns an array with version and URL
            $githubVersionData = HttpClient::getLatestVersion(APP_GITHUB_LATEST_URL);
            Log::debug('ActionCheckVersion: GitHub version data: ' . var_export($githubVersionData, true));

            if ($githubVersionData != null && isset($githubVersionData['version'], $githubVersionData['html_url'])) {
                $githubLatestVersion = $githubVersionData['version'];
                $this->githubLatestVersionUrl = $githubVersionData['html_url']; // URL of the latest version
                Log::debug('ActionCheckVersion: GitHub latest version: ' . $githubLatestVersion);
                if (version_compare($this->currentVersion, $githubLatestVersion, '<')) {
                    Log::debug('ActionCheckVersion: Update available, showing update window');
                    $this->showVersionUpdateWindow($bearsamppLang, $bearsamppWinbinder, $bearsamppCore, $githubLatestVersion);
                } elseif ($isMenuCheck) {
                    Log::debug('ActionCheckVersion: Version is up to date, showing OK message box');
                    $this->showVersionOkMessageBox($bearsamppLang, $bearsamppWinbinder);
                } else {
                    Log::debug('ActionCheckVersion: Version is up to date (background check)');
                    if ($this->didStartLoading) {
                        Util::stopLoading();
                    }
                }
            } elseif ($isMenuCheck) {
                Log::debug('ActionCheckVersion: Failed to retrieve version data during manual check');
                // If it's a menu check but we couldn't get version data, we must stop loading
                if ($this->didStartLoading) {
                    Util::stopLoading();
                }
                Log::debug('ActionCheckVersion: Showing error message box');
                $bearsamppWinbinder->messageBoxError(
                    'Failed to retrieve version data from GitHub during manual check.',
                    $bearsamppLang->getValue(Lang::CHECK_VERSION_TITLE)
                );
                Log::debug('ActionCheckVersion: Error message box returned');
                Log::error('Failed to retrieve version data from GitHub during manual check.');
            } else {
                Log::debug('ActionCheckVersion: Failed to retrieve version data (background check)');
                // Not a menu check, only stop loading if we started it
                if ($this->didStartLoading) {
                    Util::stopLoading();
                }
            }
        }
    }

    /**
     * Displays a window with the latest version information.
     *
     * @param Lang $lang Language processor instance.
     * @param WinBinder $winbinder WinBinder instance for creating windows and controls.
     * @param Core $core Core instance for accessing application resources.
     * @param string $githubLatestVersion The latest version available on GitHub.
     */
    private function showVersionUpdateWindow($lang, $winbinder, $core, $githubLatestVersion)
    {
        $labelFullLink = $lang->getValue(Lang::DOWNLOAD) . ' ' . APP_TITLE . ' ' . $githubLatestVersion;

        $winbinder->reset();
        $this->wbWindow = $winbinder->createAppWindow($lang->getValue(Lang::CHECK_VERSION_TITLE), 380, 170, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        $winbinder->createLabel($this->wbWindow, $lang->getValue(Lang::CHECK_VERSION_AVAILABLE_TEXT), 80, 35, 370, 120);

        $this->wbLinkFull = $winbinder->createHyperLink($this->wbWindow, $labelFullLink, 80, 87, 200, 20, WBC_LINES | WBC_RIGHT);

        $this->wbBtnOk = $winbinder->createButton($this->wbWindow, $lang->getValue(Lang::BUTTON_OK), 280, 103);
        $this->wbImage = $winbinder->drawImage($this->wbWindow, $core->getImagesPath() . '/about.bmp');

        Util::stopLoading();
        $winbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $winbinder->mainLoop();
        $winbinder->reset();
    }

    /**
     * Displays a message box indicating that the current version is the latest.
     *
     * @param Lang $lang Language processor instance.
     * @param WinBinder $winbinder WinBinder instance for creating windows and controls.
     */
    private function showVersionOkMessageBox($lang, $winbinder)
    {
        Log::debug('ActionCheckVersion: Calling Util::stopLoading() in showVersionOkMessageBox');
        Util::stopLoading();
        Log::debug('ActionCheckVersion: Calling messageBoxInfo');
        $winbinder->messageBoxInfo(
            $lang->getValue(Lang::CHECK_VERSION_LATEST_TEXT),
            $lang->getValue(Lang::CHECK_VERSION_TITLE)
        );
        Log::debug('ActionCheckVersion: messageBoxInfo returned');
    }

    /**
     * Processes window events and handles user interactions.
     *
     * @param resource $window The window resource.
     * @param int $id The control ID that triggered the event.
     * @param resource $ctrl The control resource.
     * @param mixed $param1 Additional parameter 1.
     * @param mixed $param2 Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppConfig, $bearsamppWinbinder;

        switch ($id) {
            case $this->wbLinkFull[WinBinder::CTRL_ID]:
                $latestVersionInfo = HttpClient::getLatestVersion(APP_GITHUB_LATEST_URL);
                if ($latestVersionInfo && isset($latestVersionInfo['html_url'])) {
                    $browserPath = $bearsamppConfig->getBrowser();
                    if (!$bearsamppWinbinder->exec($browserPath, $latestVersionInfo['html_url'])) {
                        Log::error("Failed to open browser at path: $browserPath with URL: " . $latestVersionInfo['html_url']);
                    }
                } else {
                    Log::error("Failed to retrieve latest version info or 'html_url' not set.");
                }
                break;
            case IDCLOSE:
            case $this->wbBtnOk[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
            default:
                Log::error("Unhandled window control ID: $id");
        }
    }
}
