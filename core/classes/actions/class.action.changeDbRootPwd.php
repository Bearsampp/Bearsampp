<?php

class ActionChangeDbRootPwd
{
    private $bin;
    private $cntProcessActions;

    private $wbWindow;

    private $wbLabelCurrentPwd;
    private $wbInputCurrentPwd;

    private $wbLabelNewPwd1;
    private $wbInputNewPwd1;

    private $wbLabelNewPwd2;
    private $wbInputNewPwd2;

    private $wbProgressBar;
    private $wbBtnFinish;
    private $wbBtnCancel;

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
