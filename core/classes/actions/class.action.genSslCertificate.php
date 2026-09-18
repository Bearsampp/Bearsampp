<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionGenSslCertificate
 *
 * This class handles the generation of SSL certificates through a WinBinder GUI.
 * It provides a user interface for inputting the server name and target directory,
 * and includes functionality for browsing directories, saving the certificate, and displaying progress.
 */
class ActionGenSslCertificate
{
    /** @var resource The main application window. */
    private $wbWindow;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label control of the server name field. */
    private $wbLabelName;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the text input control of the server name. */
    private $wbInputName;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label control of the target directory field. */
    private $wbLabelDest;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the read-only text input of the target directory path. */
    private $wbInputDest;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the browse button control selecting the target directory. */
    private $wbBtnDest;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the progress bar control. */
    private $wbProgressBar;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the save button control. */
    private $wbBtnSave;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the cancel button control. */
    private $wbBtnCancel;

    /** @var int Progress bar gauge value for the save operation. */
    const GAUGE_SAVE = 2;

    /**
     * Constructor for ActionGenSslCertificate.
     *
     * Initializes the WinBinder window and its controls, sets up event handlers, and starts the main loop.
     *
     * @param   array  $args  Command line arguments passed to the script.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppWinbinder;

        $initServerName   = 'test.local';
        $initDocumentRoot = Path::formatWindowsPath(Path::getSslPath());

        $bearsamppWinbinder->reset();
        $this->wbWindow = $bearsamppWinbinder->createAppWindow($bearsamppLang->getValue(Lang::GENSSL_TITLE), 490, 160, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        $this->wbLabelName = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::NAME) . ' :', 15, 15, 85, null, WBC_RIGHT);
        $this->wbInputName = $bearsamppWinbinder->createInputText($this->wbWindow, $initServerName, 105, 13, 150, null);

        $this->wbLabelDest = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::TARGET) . ' :', 15, 45, 85, null, WBC_RIGHT);
        $this->wbInputDest = $bearsamppWinbinder->createInputText($this->wbWindow, $initDocumentRoot, 105, 43, 190, null, null, WBC_READONLY);
        $this->wbBtnDest   = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_BROWSE), 300, 43, 110);

        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, self::GAUGE_SAVE + 1, 15, 97, 275);
        $this->wbBtnSave     = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_SAVE), 300, 92);
        $this->wbBtnCancel   = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 387, 92);

        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes window events.
     *
     * Handles button clicks and other window events, such as browsing for a directory,
     * saving the SSL certificate, and closing the window.
     *
     * @param   resource  $window  The window resource.
     * @param   int       $id      The control ID that triggered the event.
     * @param   resource  $ctrl    The control resource that triggered the event.
     * @param   mixed     $param1  Additional parameter 1.
     * @param   mixed     $param2  Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppLang, $bearsamppOpenSsl, $bearsamppWinbinder;

        $name   = $bearsamppWinbinder->getText($this->wbInputName[WinBinder::CTRL_OBJ]);
        $target = $bearsamppWinbinder->getText($this->wbInputDest[WinBinder::CTRL_OBJ]);

        switch ($id) {
            case $this->wbBtnDest[WinBinder::CTRL_ID]:
                $target = $bearsamppWinbinder->sysDlgPath($window, $bearsamppLang->getValue(Lang::GENSSL_PATH), $target);
                if ($target && is_dir($target)) {
                    $bearsamppWinbinder->setText($this->wbInputDest[WinBinder::CTRL_OBJ], $target . '\\');
                }
                break;
            case $this->wbBtnSave[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setProgressBarMax($this->wbProgressBar, self::GAUGE_SAVE + 1);
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                $target = Path::formatUnixPath($target);
                if ($bearsamppOpenSsl->createCrt($name, $target)) {
                    $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);
                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf($bearsamppLang->getValue(Lang::GENSSL_CREATED), $name),
                        $bearsamppLang->getValue(Lang::GENSSL_TITLE)
                    );
                    $bearsamppWinbinder->destroyWindow($window);
                } else {
                    $bearsamppWinbinder->messageBoxError($bearsamppLang->getValue(Lang::GENSSL_CREATED_ERROR), $bearsamppLang->getValue(Lang::GENSSL_TITLE));
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

