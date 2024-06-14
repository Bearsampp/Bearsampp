<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionEditAlias
 * Handles the editing of Apache aliases within the Bearsampp application.
 */
class ActionEditAlias
{
    private $initName;
    private $wbWindow;
    private $wbLabelName;
    private $wbInputName;
    private $wbLabelDest;
    private $wbInputDest;
    private $wbBtnDest;
    private $wbLabelExp;
    private $wbProgressBar;
    private $wbBtnSave;
    private $wbBtnDelete;
    private $wbBtnCancel;

    const GAUGE_SAVE = 2;
    const GAUGE_DELETE = 2;

    /**
     * ActionEditAlias constructor.
     * Initializes the alias editing window and its components.
     *
     * @param array $args Command line arguments passed to the script.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $filePath = $bearsamppRoot->getAliasPath() . '/' . $args[0] . '.conf';
            $fileContent = file_get_contents($filePath);
            if (preg_match('/^Alias \/' . $args[0] . ' "(.+)"/', $fileContent, $match)) {
                $this->initName = $args[0];
                $initDest = Util::formatWindowsPath($match[1]);
                $apachePortUri = $bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';

                $bearsamppWinbinder->reset();
                $this->wbWindow = $bearsamppWinbinder->createAppWindow(sprintf($bearsamppLang->getValue(Lang::EDIT_ALIAS_TITLE), $this->initName), 490, 200, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

                $this->wbLabelName = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::ALIAS_NAME_LABEL) . ' :', 15, 15, 85, null, WBC_RIGHT);
                $this->wbInputName = $bearsamppWinbinder->createInputText($this->wbWindow, $this->initName, 105, 13, 150, null);

                $this->wbLabelDest = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::ALIAS_DEST_LABEL) . ' :', 15, 45, 85, null, WBC_RIGHT);
                $this->wbInputDest = $bearsamppWinbinder->createInputText($this->wbWindow, $initDest, 105, 43, 190, null, null, WBC_READONLY);
                $this->wbBtnDest = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_BROWSE), 300, 43, 110);

                $this->wbLabelExp = $bearsamppWinbinder->createLabel($this->wbWindow, sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $this->initName, $initDest), 15, 80, 470, 50);

                $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, self::GAUGE_SAVE + 1, 15, 137, 190);
                $this->wbBtnSave = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_SAVE), 215, 132);
                $this->wbBtnDelete = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_DELETE), 300, 132);
                $this->wbBtnCancel = $bearsamppWinbinder->createButton($this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 385, 132);

                $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
                $bearsamppWinbinder->mainLoop();
                $bearsamppWinbinder->reset();
            }
        }
    }

    /**
     * Processes window events and handles user interactions.
     *
     * @param resource $window The window resource.
     * @param int $id The control ID.
     * @param resource $ctrl The control resource.
     * @param mixed $param1 Additional parameter 1.
     * @param mixed $param2 Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppLang, $bearsamppWinbinder;

        $apachePortUri = $bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';
        $aliasName = $bearsamppWinbinder->getText($this->wbInputName[WinBinder::CTRL_OBJ]);
        $aliasDest = $bearsamppWinbinder->getText($this->wbInputDest[WinBinder::CTRL_OBJ]);

        switch ($id) {
            case $this->wbInputName[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setText(
                    $this->wbLabelExp[WinBinder::CTRL_OBJ],
                    sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $aliasName, $aliasDest)
                );
                $bearsamppWinbinder->setEnabled($this->wbBtnSave[WinBinder::CTRL_OBJ], empty($aliasName) ? false : true);
                break;
            case $this->wbBtnDest[WinBinder::CTRL_ID]:
                $aliasDest = $bearsamppWinbinder->sysDlgPath($window, $bearsamppLang->getValue(Lang::ALIAS_DEST_PATH), $aliasDest);
                if ($aliasDest && is_dir($aliasDest)) {
                    $bearsamppWinbinder->setText($this->wbInputDest[WinBinder::CTRL_OBJ], $aliasDest . '\\');
                    $bearsamppWinbinder->setText(
                        $this->wbLabelExp[WinBinder::CTRL_OBJ],
                        sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $aliasName, $aliasDest . '\\')
                    );
                }
                break;
            case $this->wbBtnSave[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setProgressBarMax($this->wbProgressBar, self::GAUGE_SAVE + 1);
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                if (!ctype_alnum($aliasName)) {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf($bearsamppLang->getValue(Lang::ALIAS_NOT_VALID_ALPHA), $aliasName),
                        $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE));
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }

                if ($aliasName != $this->initName && is_file($bearsamppRoot->getAliasPath() . '/' . $aliasName . '.conf')) {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf($bearsamppLang->getValue(Lang::ALIAS_ALREADY_EXISTS), $aliasName),
                        $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE));
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }
                if (file_put_contents($bearsamppRoot->getAliasPath() . '/' . $aliasName . '.conf', $bearsamppBins->getApache()->getAliasContent($aliasName, $aliasDest)) !== false) {
                    $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                    $bearsamppBins->getApache()->getService()->restart();
                    $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf($bearsamppLang->getValue(Lang::ALIAS_CREATED), $aliasName, $apachePortUri, $aliasName, $aliasDest),
                        $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE));
                    $bearsamppWinbinder->destroyWindow($window);
                } else {
                    $bearsamppWinbinder->messageBoxError($bearsamppLang->getValue(Lang::ALIAS_CREATED_ERROR), $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE));
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                }
                break;
            case $this->wbBtnDelete[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setProgressBarMax($this->wbProgressBar, self::GAUGE_DELETE + 1);

                $boxTitle = $bearsamppLang->getValue(Lang::DELETE_ALIAS_TITLE);
                $confirm = $bearsamppWinbinder->messageBoxYesNo(
                    sprintf($bearsamppLang->getValue(Lang::DELETE_ALIAS), $this->initName),
                    $boxTitle);

                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                if ($confirm) {
                    if (@unlink($bearsamppRoot->getAliasPath() . '/' . $this->initName . '.conf')) {
                        $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                        $bearsamppBins->getApache()->getService()->restart();
                        $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                        $bearsamppWinbinder->messageBoxInfo(
                            sprintf($bearsamppLang->getValue(Lang::ALIAS_REMOVED), $this->initName),
                            $boxTitle);
                        $bearsamppWinbinder->destroyWindow($window);
                    } else {
                        $bearsamppWinbinder->messageBoxError(
                            sprintf($bearsamppLang->getValue(Lang::ALIAS_REMOVE_ERROR), $bearsamppRoot->getAliasPath() . '/' . $this->initName . '.conf'),
                            $boxTitle);
                        $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    }
                }
                break;
            case IDCLOSE:
            case $this->wbBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}
