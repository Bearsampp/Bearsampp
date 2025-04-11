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
 * Class ActionAddVhost
 * Handles the creation of a new virtual host (vhost) in the Bearsampp application.
 */
class ActionAddVhost
{
    private $wbWindow;
    private $wbLabelServerName;
    private $wbInputServerName;
    private $wbLabelDocRoot;
    private $wbInputDocRoot;
    private $wbBtnDocRoot;
    private $wbLabelExp;
    private $wbProgressBar;
    private $wbBtnSave;
    private $wbBtnCancel;

    const GAUGE_SAVE = 2;

    /**
     * ActionAddVhost constructor.
     * Initializes the window and its components for adding a new virtual host.
     *
     * @param array $args Arguments passed to the constructor.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppWinbinder;

        $initServerName = 'test.local';
        $initDocumentRoot = Util::formatWindowsPath($bearsamppRoot->getWwwPath()) . '\\' . $initServerName;

        $bearsamppWinbinder->reset();
        $this->wbWindow = $bearsamppWinbinder->createAppWindow($bearsamppLang->getValue(Lang::ADD_VHOST_TITLE), 490, 200, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        $this->wbLabelServerName = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::VHOST_SERVER_NAME_LABEL) . ' :', 15, 15, 85, null, WBC_RIGHT);
        $this->wbInputServerName = $bearsamppWinbinder->createInputText($this->wbWindow, $initServerName, 105, 13, 150, null);

        $this->wbLabelDocRoot = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::VHOST_DOCUMENT_ROOT_LABEL) . ' :', 15, 45, 85, null, WBC_RIGHT);
        $this->wbInputDocRoot = $bearsamppWinbinder->createInputText($this->wbWindow, $initDocumentRoot, 105, 43, 190, null, null, WBC_READONLY);
        $this->wbBtnDocRoot = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_BROWSE), 300, 43, 110);

        $this->wbLabelExp = $bearsamppWinbinder->createLabel($this->wbWindow, sprintf($bearsamppLang->getValue(Lang::VHOST_EXP_LABEL), $initServerName, $initDocumentRoot), 15, 80, 470, 50);

        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, self::GAUGE_SAVE + 1, 15, 137, 275);
        $this->wbBtnSave = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_SAVE), 300, 132);
        $this->wbBtnCancel = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 387, 132);

        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes window events and handles user interactions.
     *
     * @param resource $window The window resource.
     * @param int $id The ID of the control that triggered the event.
     * @param resource $ctrl The control resource.
     * @param mixed $param1 Additional parameter 1.
     * @param mixed $param2 Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppLang, $bearsamppOpenSsl, $bearsamppWinbinder;

        $serverName = $bearsamppWinbinder->getText($this->wbInputServerName[WinBinder::CTRL_OBJ]);
        $documentRoot = $bearsamppWinbinder->getText($this->wbInputDocRoot[WinBinder::CTRL_OBJ]);

        switch ($id) {
            case $this->wbInputServerName[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setText(
                    $this->wbLabelExp[WinBinder::CTRL_OBJ],
                    sprintf($bearsamppLang->getValue(Lang::VHOST_EXP_LABEL), $serverName, $documentRoot)
                );
                $bearsamppWinbinder->setEnabled($this->wbBtnSave[WinBinder::CTRL_OBJ], empty($serverName) ? false : true);
                break;
            case $this->wbBtnDocRoot[WinBinder::CTRL_ID]:
                $documentRoot = $bearsamppWinbinder->sysDlgPath($window, $bearsamppLang->getValue(Lang::VHOST_DOC_ROOT_PATH), $documentRoot);
                if ($documentRoot && is_dir($documentRoot)) {
                    $bearsamppWinbinder->setText($this->wbInputDocRoot[WinBinder::CTRL_OBJ], $documentRoot . '\\');
                    $bearsamppWinbinder->setText(
                        $this->wbLabelExp[WinBinder::CTRL_OBJ],
                        sprintf($bearsamppLang->getValue(Lang::VHOST_EXP_LABEL), $serverName, $documentRoot . '\\')
                    );
                }
                break;
            case $this->wbBtnSave[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setProgressBarMax($this->wbProgressBar, self::GAUGE_SAVE + 1);
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                if (!Util::isValidDomainName($serverName)) {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf($bearsamppLang->getValue(Lang::VHOST_NOT_VALID_DOMAIN), $serverName),
                        $bearsamppLang->getValue(Lang::ADD_VHOST_TITLE));
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }

                if (is_file($bearsamppRoot->getVhostsPath() . '/' . $serverName . '.conf')) {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf($bearsamppLang->getValue(Lang::VHOST_ALREADY_EXISTS), $serverName),
                        $bearsamppLang->getValue(Lang::ADD_VHOST_TITLE));
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }

                if ($bearsamppOpenSsl->createCrt($serverName) && file_put_contents($bearsamppRoot->getVhostsPath() . '/' . $serverName . '.conf', $bearsamppBins->getApache()->getVhostContent($serverName, $documentRoot)) !== false) {
                    $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                    $bearsamppBins->getApache()->getService()->restart();
                    $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf($bearsamppLang->getValue(Lang::VHOST_CREATED), $serverName, $serverName, $documentRoot),
                        $bearsamppLang->getValue(Lang::ADD_VHOST_TITLE));
                    $bearsamppWinbinder->destroyWindow($window);
                } else {
                    $bearsamppWinbinder->messageBoxError($bearsamppLang->getValue(Lang::VHOST_CREATED_ERROR), $bearsamppLang->getValue(Lang::ADD_VHOST_TITLE));
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                }
                break;
            case IDCLOSE:
            case $this->wbBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}
