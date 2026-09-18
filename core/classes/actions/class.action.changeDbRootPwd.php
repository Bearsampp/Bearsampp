<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionChangeDbRootPwd
 * Handles the process of changing the root password for various database systems.
 */
class ActionChangeDbRootPwd
{
    /**
     * @var object The database binary object (MySQL, MariaDB, PostgreSQL).
     */
    private $bin;

    /**
     * @var int The count of process actions required for the progress bar.
     */
    private $cntProcessActions;

    /**
     * @var resource The main application window created by WinBinder.
     */
    private $wbWindow;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label of the current password input field.
     */
    private $wbLabelCurrentPwd;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the input field of the current password.
     */
    private $wbInputCurrentPwd;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label of the new password input field.
     */
    private $wbLabelNewPwd1;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the input field of the new password.
     */
    private $wbInputNewPwd1;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label of the confirmation of the new password input field.
     */
    private $wbLabelNewPwd2;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the input field of the confirmation of the new password.
     */
    private $wbInputNewPwd2;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the progress bar showing the progress of the password change process.
     */
    private $wbProgressBar;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the finish button to submit the password change.
     */
    private $wbBtnFinish;

    /**
     * @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the cancel button to abort the password change process.
     */
    private $wbBtnCancel;

    /**
     * ActionChangeDbRootPwd constructor.
     * Initializes the window and controls for changing the database root password.
     *
     * @param array $args The arguments passed to the constructor, typically containing the database type.
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $this->bin = $bearsamppBins->getMysql();
            $this->cntProcessActions = 11;
            if ($args[0] == $bearsamppBins->getMariadb()->getName()) {
                $this->bin = $bearsamppBins->getMariadb();
                $this->cntProcessActions = 11;
            } elseif ($args[0] == $bearsamppBins->getPostgresql()->getName()) {
                $this->bin = $bearsamppBins->getPostgresql();
                $this->cntProcessActions = 10;
            }

            $bearsamppWinbinder->reset();
            $this->wbWindow = $bearsamppWinbinder->createAppWindow(sprintf($bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_TITLE), $args[0]), 400, 290, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

            $this->wbLabelCurrentPwd = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_CURRENTPWD_LABEL), 15, 15, 280);
            $this->wbInputCurrentPwd = $bearsamppWinbinder->createInputText($this->wbWindow, null, 15, 40, 200, null, null, WBC_MASKED);

            $this->wbLabelNewPwd1 = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_NEWPWD1_LABEL), 15, 80, 280);
            $this->wbInputNewPwd1 = $bearsamppWinbinder->createInputText($this->wbWindow, null, 15, 105, 200, null, null, WBC_MASKED);

            $this->wbLabelNewPwd2 = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_NEWPWD2_LABEL), 15, 145, 280);
            $this->wbInputNewPwd2 = $bearsamppWinbinder->createInputText($this->wbWindow, null, 15, 170, 200, null, null, WBC_MASKED);

            $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, $this->cntProcessActions + 1, 15, 227, 190);
            $this->wbBtnFinish = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_FINISH), 210, 222);
            $this->wbBtnCancel = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 297, 222);

            $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
            $bearsamppWinbinder->setFocus($this->wbInputCurrentPwd[WinBinder::CTRL_OBJ]);
            $bearsamppWinbinder->mainLoop();
            $bearsamppWinbinder->reset();
        }
    }

    /**
     * Processes the window events and handles the password change logic.
     *
     * @param object $window The window object.
     * @param int $id The control ID that triggered the event.
     * @param object $ctrl The control object that triggered the event.
     * @param mixed $param1 Additional parameter 1.
     * @param mixed $param2 Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_TITLE), $this->bin);
        $currentPwd = $bearsamppWinbinder->getText($this->wbInputCurrentPwd[WinBinder::CTRL_OBJ]);
        $newPwd1 = $bearsamppWinbinder->getText($this->wbInputNewPwd1[WinBinder::CTRL_OBJ]);
        $newPwd2 = $bearsamppWinbinder->getText($this->wbInputNewPwd2[WinBinder::CTRL_OBJ]);

        switch ($id) {
            case $this->wbBtnFinish[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);
                if ($newPwd1 != $newPwd2) {
                    $bearsamppWinbinder->messageBoxWarning($bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_NOTSAME_ERROR), $boxTitle);
                    $bearsamppWinbinder->setText($this->wbInputNewPwd1[WinBinder::CTRL_OBJ], '');
                    $bearsamppWinbinder->setText($this->wbInputNewPwd2[WinBinder::CTRL_OBJ], '');
                    $bearsamppWinbinder->setFocus($this->wbInputNewPwd1[WinBinder::CTRL_OBJ]);
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }

                $checkRootPwd = $this->bin->checkRootPassword($currentPwd, $this->wbProgressBar);
                if ($checkRootPwd !== true) {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf($bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_INCORRECT_ERROR), $this->bin->getName(), $checkRootPwd),
                        $boxTitle
                    );
                    $bearsamppWinbinder->setText($this->wbInputCurrentPwd[WinBinder::CTRL_OBJ], '');
                    $bearsamppWinbinder->setFocus($this->wbInputCurrentPwd[WinBinder::CTRL_OBJ]);
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }

                $changeRootPwd = $this->bin->changeRootPassword($currentPwd, $newPwd1, $this->wbProgressBar);
                if ($changeRootPwd !== true) {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf($bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_INCORRECT_ERROR), $this->bin->getName(), $changeRootPwd),
                        $boxTitle
                    );
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }

                $bearsamppWinbinder->messageBoxInfo(
                    $bearsamppLang->getValue(Lang::CHANGE_DB_ROOT_PWD_TEXT),
                    $boxTitle);
                $bearsamppWinbinder->destroyWindow($window);
                break;
            case IDCLOSE:
            case $this->wbBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}

