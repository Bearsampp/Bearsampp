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
class ActionEditAlias extends ActionDialogBase
{
    private $wbLabelName;
    private $wbInputName;
    private $wbLabelDest;
    private $wbInputDest;
    private $wbBtnDest;
    private $wbLabelExp;

    protected function getWindowTitle()
    {
        global $bearsamppLang;
        return sprintf($bearsamppLang->getValue(Lang::EDIT_ALIAS_TITLE), $this->initValue);
    }

    protected function getDialogTitle()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE);
    }

    protected function getDeleteDialogTitle()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::DELETE_ALIAS_TITLE);
    }

    protected function initializeDialog($args)
    {
        global $bearsamppRoot;

        if (!isset($args[0]) || empty($args[0])) {
            return false;
        }

        $filePath = $bearsamppRoot->getAliasPath() . '/' . $args[0] . '.conf';
        if (!file_exists($filePath)) {
            return false;
        }

        $fileContent = file_get_contents($filePath);
        if (!preg_match('/^Alias \/' . $args[0] . ' "(.+)"/', $fileContent, $match)) {
            return false;
        }

        $this->initValue = $args[0];
        return true;
    }

    protected function createFormFields($bearsamppWinbinder)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppBins;

        // Load existing alias data
        $filePath = $bearsamppRoot->getAliasPath() . '/' . $this->initValue . '.conf';
        $fileContent = file_get_contents($filePath);
        preg_match('/^Alias \/' . $this->initValue . ' "(.+)"/', $fileContent, $match);
        $initDest = Util::formatWindowsPath($match[1]);
        $apachePortUri = $bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';

        $this->wbLabelName = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::ALIAS_NAME_LABEL) . ' :',
            15, 15, 85, null, WBC_RIGHT
        );
        $this->wbInputName = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $this->initValue,
            105, 13, 150, null
        );

        $this->wbLabelDest = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::ALIAS_DEST_LABEL) . ' :',
            15, 45, 85, null, WBC_RIGHT
        );
        $this->wbInputDest = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $initDest,
            105, 43, 190, null, null, WBC_READONLY
        );
        $this->wbBtnDest = $bearsamppWinbinder->createButton(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::BUTTON_BROWSE),
            300, 43, 110
        );

        $this->wbLabelExp = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $this->initValue, $initDest),
            15, 80, 470, 50
        );
    }

    protected function getFormValues($bearsamppWinbinder)
    {
        return [
            'name' => $bearsamppWinbinder->getText($this->wbInputName[WinBinder::CTRL_OBJ]),
            'dest' => $bearsamppWinbinder->getText($this->wbInputDest[WinBinder::CTRL_OBJ])
        ];
    }

    protected function validateInput($values)
    {
        global $bearsamppLang;

        if (!ctype_alnum($values['name'])) {
            return [
                'valid' => false,
                'error' => sprintf($bearsamppLang->getValue(Lang::ALIAS_NOT_VALID_ALPHA), $values['name'])
            ];
        }

        return ['valid' => true];
    }

    protected function itemExists($values)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppWinbinder;

        // Only check if name changed
        if ($values['name'] != $this->initValue && is_file($bearsamppRoot->getAliasPath() . '/' . $values['name'] . '.conf')) {
            $bearsamppWinbinder->messageBoxError(
                sprintf($bearsamppLang->getValue(Lang::ALIAS_ALREADY_EXISTS), $values['name']),
                $this->getDialogTitle()
            );
            return true;
        }

        return false;
    }

    protected function saveItem($values)
    {
        global $bearsamppRoot, $bearsamppBins;

        return file_put_contents(
            $bearsamppRoot->getAliasPath() . '/' . $values['name'] . '.conf',
            $bearsamppBins->getApache()->getAliasContent($values['name'], $values['dest'])
        ) !== false;
    }

    protected function deleteItem()
    {
        global $bearsamppRoot;

        return @unlink($bearsamppRoot->getAliasPath() . '/' . $this->initValue . '.conf');
    }

    protected function getSaveSuccessMessage($values)
    {
        global $bearsamppLang, $bearsamppBins;

        $apachePortUri = $bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';
        return sprintf(
            $bearsamppLang->getValue(Lang::ALIAS_CREATED),
            $values['name'],
            $apachePortUri,
            $values['name'],
            $values['dest']
        );
    }

    protected function getSaveErrorMessage()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::ALIAS_CREATED_ERROR);
    }

    protected function getDeleteConfirmMessage()
    {
        global $bearsamppLang;
        return sprintf($bearsamppLang->getValue(Lang::DELETE_ALIAS), $this->initValue);
    }

    protected function getDeleteSuccessMessage()
    {
        global $bearsamppLang;
        return sprintf($bearsamppLang->getValue(Lang::ALIAS_REMOVED), $this->initValue);
    }

    protected function getDeleteErrorMessage()
    {
        global $bearsamppRoot, $bearsamppLang;
        return sprintf(
            $bearsamppLang->getValue(Lang::ALIAS_REMOVE_ERROR),
            $bearsamppRoot->getAliasPath() . '/' . $this->initValue . '.conf'
        );
    }

    protected function restartService()
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->getService()->restart();
    }

    protected function handleCustomEvent($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        $apachePortUri = $bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';
        $aliasName = $bearsamppWinbinder->getText($this->wbInputName[WinBinder::CTRL_OBJ]);
        $aliasDest = $bearsamppWinbinder->getText($this->wbInputDest[WinBinder::CTRL_OBJ]);

        // Handle name input change
        if ($id == $this->wbInputName[WinBinder::CTRL_ID]) {
            $bearsamppWinbinder->setText(
                $this->wbLabelExp[WinBinder::CTRL_OBJ],
                sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $aliasName, $aliasDest)
            );
            $bearsamppWinbinder->setEnabled(
                $this->wbBtnSave[WinBinder::CTRL_OBJ],
                !empty($aliasName)
            );
        }

        // Handle browse button
        if ($id == $this->wbBtnDest[WinBinder::CTRL_ID]) {
            $aliasDest = $bearsamppWinbinder->sysDlgPath(
                $window,
                $bearsamppLang->getValue(Lang::ALIAS_DEST_PATH),
                $aliasDest
            );
            if ($aliasDest && is_dir($aliasDest)) {
                $bearsamppWinbinder->setText($this->wbInputDest[WinBinder::CTRL_OBJ], $aliasDest . '\\');
                $bearsamppWinbinder->setText(
                    $this->wbLabelExp[WinBinder::CTRL_OBJ],
                    sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $aliasName, $aliasDest . '\\')
                );
            }
        }
    }
}
