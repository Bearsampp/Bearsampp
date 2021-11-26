<?php

class ActionChangeBrowser
{
    private $wbWindow;

    private $wbLabelExp;

    private $wbRadioButton;
    private $wbRadioButtonOther;
    private $wbInputBrowse;
    private $wbBtnBrowse;

    private $wbProgressBar;
    private $wbBtnSave;
    private $wbBtnCancel;

    const GAUGE_SAVE = 2;

    public function __construct($args)
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppWinbinder;

        $bearsamppWinbinder->reset();
        $this->wbWindow = $bearsamppWinbinder->createAppWindow($bearsamppLang->getValue(Lang::CHANGE_BROWSER_TITLE), 490, 350, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        $this->wbLabelExp = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::CHANGE_BROWSER_EXP_LABEL), 15, 15, 470, 50);

        $currentBrowser = $bearsamppConfig->getBrowser();
        $this->wbRadioButton[] = $bearsamppWinbinder->createRadioButton($this->wbWindow, $currentBrowser, true, 15, 40, 470, 20, true);

        $yPos = 70;
        $installedBrowsers = Vbs::getInstalledBrowsers();
        foreach ($installedBrowsers as $installedBrowser) {
            if ($installedBrowser != $currentBrowser) {
                $this->wbRadioButton[] = $bearsamppWinbinder->createRadioButton($this->wbWindow, $installedBrowser, false, 15, $yPos, 470, 20);
                $yPos += 30;
            }
        }

        $this->wbRadioButtonOther = $bearsamppWinbinder->createRadioButton($this->wbWindow, $bearsamppLang->getValue(Lang::CHANGE_BROWSER_OTHER_LABEL), false, 15, $yPos, 470, 15);

        $this->wbInputBrowse = $bearsamppWinbinder->createInputText($this->wbWindow, null, 30, $yPos + 30, 190, null, 20, WBC_READONLY);
        $this->wbBtnBrowse = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_BROWSE), 225, $yPos + 25, 110);
        $bearsamppWinbinder->setEnabled($this->wbBtnBrowse[WinBinder::CTRL_OBJ], false);

        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, self::GAUGE_SAVE, 15, 287, 275);
        $this->wbBtnSave = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_SAVE), 300, 282);
        $this->wbBtnCancel = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 387, 282);
        $bearsamppWinbinder->setEnabled($this->wbBtnSave[WinBinder::CTRL_OBJ], empty($currentBrowser) ? false : true);

        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppWinbinder;

        // Get other value
        $browserPath = $bearsamppWinbinder->getText($this->wbInputBrowse[WinBinder::CTRL_OBJ]);

        // Get value
        $selected = null;
        if ($bearsamppWinbinder->getValue($this->wbRadioButtonOther[WinBinder::CTRL_OBJ]) == 1) {
            $bearsamppWinbinder->setEnabled($this->wbBtnBrowse[WinBinder::CTRL_OBJ], true);
            $selected = $bearsamppWinbinder->getText($this->wbInputBrowse[WinBinder::CTRL_OBJ]);
        } else {
            $bearsamppWinbinder->setEnabled($this->wbBtnBrowse[WinBinder::CTRL_OBJ], false);
        }
        foreach ($this->wbRadioButton as $radioButton) {
            if ($bearsamppWinbinder->getValue($radioButton[WinBinder::CTRL_OBJ]) == 1) {
                $selected = $bearsamppWinbinder->getText($radioButton[WinBinder::CTRL_OBJ]);
                break;
            }
        }

        // Enable/disable save button
        $bearsamppWinbinder->setEnabled($this->wbBtnSave[WinBinder::CTRL_OBJ], empty($selected) ? false : true);

        switch ($id) {
            case $this->wbBtnBrowse[WinBinder::CTRL_ID]:
                $browserPath = trim($bearsamppWinbinder->sysDlgOpen(
                    $window,
                    $bearsamppLang->getValue(Lang::ALIAS_DEST_PATH),
                    array(array($bearsamppLang->getValue(Lang::EXECUTABLE), '*.exe')),
                    $browserPath
                ));
                if ($browserPath && is_file($browserPath)) {
                    $bearsamppWinbinder->setText($this->wbInputBrowse[WinBinder::CTRL_OBJ], $browserPath);
                }
                break;
            case $this->wbBtnSave[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);
                $bearsamppConfig->replace(Config::CFG_BROWSER, $selected);

                $bearsamppWinbinder->messageBoxInfo(
                    sprintf($bearsamppLang->getValue(Lang::CHANGE_BROWSER_OK), $selected),
                    $bearsamppLang->getValue(Lang::CHANGE_BROWSER_TITLE)
                );
                $bearsamppWinbinder->destroyWindow($window);

                break;
            case IDCLOSE:
            case $this->wbBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}
