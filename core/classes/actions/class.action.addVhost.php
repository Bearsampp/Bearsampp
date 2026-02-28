<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionAddVhost
 * Handles the creation of a new virtual host (vhost) in the Bearsampp application.
 */
class ActionAddVhost extends ActionDialogBase
{
    private $wbLabelServerName;
    private $wbInputServerName;
    private $wbLabelDocRoot;
    private $wbInputDocRoot;
    private $wbBtnDocRoot;
    private $wbLabelExp;

    protected function getWindowTitle()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::ADD_VHOST_TITLE);
    }

    protected function getDialogTitle()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::ADD_VHOST_TITLE);
    }

    protected function getDeleteDialogTitle()
    {
        // Not used in add mode
        return '';
    }

    protected function createFormFields($bearsamppWinbinder)
    {
        global $bearsamppRoot, $bearsamppLang;

        $initServerName = 'test.local';
        $initDocumentRoot = Util::formatWindowsPath($bearsamppRoot->getWwwPath()) . '\\' . $initServerName;

        $this->wbLabelServerName = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::VHOST_SERVER_NAME_LABEL) . ' :',
            15, 15, 85, null, WBC_RIGHT
        );
        $this->wbInputServerName = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $initServerName,
            105, 13, 150, null
        );

        $this->wbLabelDocRoot = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::VHOST_DOCUMENT_ROOT_LABEL) . ' :',
            15, 45, 85, null, WBC_RIGHT
        );
        $this->wbInputDocRoot = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $initDocumentRoot,
            105, 43, 190, null, null, WBC_READONLY
        );
        $this->wbBtnDocRoot = $bearsamppWinbinder->createButton(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::BUTTON_BROWSE),
            300, 43, 110
        );

        $this->wbLabelExp = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            sprintf($bearsamppLang->getValue(Lang::VHOST_EXP_LABEL), $initServerName, $initDocumentRoot),
            15, 80, 470, 50
        );
    }

    protected function getFormValues($bearsamppWinbinder)
    {
        return [
            'serverName' => $bearsamppWinbinder->getText($this->wbInputServerName[WinBinder::CTRL_OBJ]),
            'documentRoot' => $bearsamppWinbinder->getText($this->wbInputDocRoot[WinBinder::CTRL_OBJ])
        ];
    }

    protected function validateInput($values)
    {
        global $bearsamppLang;

        if (!Util::isValidDomainName($values['serverName'])) {
            return [
                'valid' => false,
                'error' => sprintf($bearsamppLang->getValue(Lang::VHOST_NOT_VALID_DOMAIN), $values['serverName'])
            ];
        }

        return ['valid' => true];
    }

    protected function itemExists($values)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppWinbinder;

        if (is_file($bearsamppRoot->getVhostsPath() . '/' . $values['serverName'] . '.conf')) {
            $bearsamppWinbinder->messageBoxError(
                sprintf($bearsamppLang->getValue(Lang::VHOST_ALREADY_EXISTS), $values['serverName']),
                $this->getDialogTitle()
            );
            return true;
        }

        return false;
    }

    protected function saveItem($values)
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppOpenSsl;

        // Create SSL certificate
        if (!$bearsamppOpenSsl->createCrt($values['serverName'])) {
            return false;
        }

        // Create vhost configuration file
        return file_put_contents(
            $bearsamppRoot->getVhostsPath() . '/' . $values['serverName'] . '.conf',
            $bearsamppBins->getApache()->getVhostContent($values['serverName'], $values['documentRoot'])
        ) !== false;
    }

    protected function deleteItem()
    {
        // Not used in add mode
        return false;
    }

    protected function getSaveSuccessMessage($values)
    {
        global $bearsamppLang;
        return sprintf(
            $bearsamppLang->getValue(Lang::VHOST_CREATED),
            $values['serverName'],
            $values['serverName'],
            $values['documentRoot']
        );
    }

    protected function getSaveErrorMessage()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::VHOST_CREATED_ERROR);
    }

    protected function getDeleteConfirmMessage()
    {
        // Not used in add mode
        return '';
    }

    protected function getDeleteSuccessMessage()
    {
        // Not used in add mode
        return '';
    }

    protected function getDeleteErrorMessage()
    {
        // Not used in add mode
        return '';
    }

    protected function restartService()
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->getService()->restart();
    }

    protected function handleCustomEvent($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        $serverName = $bearsamppWinbinder->getText($this->wbInputServerName[WinBinder::CTRL_OBJ]);
        $documentRoot = $bearsamppWinbinder->getText($this->wbInputDocRoot[WinBinder::CTRL_OBJ]);

        // Handle server name input change
        if ($id == $this->wbInputServerName[WinBinder::CTRL_ID]) {
            $bearsamppWinbinder->setText(
                $this->wbLabelExp[WinBinder::CTRL_OBJ],
                sprintf($bearsamppLang->getValue(Lang::VHOST_EXP_LABEL), $serverName, $documentRoot)
            );
            $bearsamppWinbinder->setEnabled(
                $this->wbBtnSave[WinBinder::CTRL_OBJ],
                !empty($serverName)
            );
        }

        // Handle browse button
        if ($id == $this->wbBtnDocRoot[WinBinder::CTRL_ID]) {
            $documentRoot = $bearsamppWinbinder->sysDlgPath(
                $window,
                $bearsamppLang->getValue(Lang::VHOST_DOC_ROOT_PATH),
                $documentRoot
            );
            if ($documentRoot && is_dir($documentRoot)) {
                $bearsamppWinbinder->setText($this->wbInputDocRoot[WinBinder::CTRL_OBJ], $documentRoot . '\\');
                $bearsamppWinbinder->setText(
                    $this->wbLabelExp[WinBinder::CTRL_OBJ],
                    sprintf($bearsamppLang->getValue(Lang::VHOST_EXP_LABEL), $serverName, $documentRoot . '\\')
                );
            }
        }
    }
}
