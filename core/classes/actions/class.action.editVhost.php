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
 * Class ActionEditVhost
 * Handles the editing of virtual hosts within the Bearsampp application.
 */
class ActionEditVhost extends ActionDialogBase
{
    private $wbLabelServerName;
    private $wbInputServerName;
    private $wbLabelDocRoot;
    private $wbInputDocRoot;
    private $wbBtnDocRoot;
    private $wbLabelExp;

    const GAUGE_SAVE = 3; // Override: needs extra step for SSL cert regeneration

    protected function getGaugeSave()
    {
        return self::GAUGE_SAVE;
    }

    protected function getWindowTitle()
    {
        global $bearsamppLang;
        return sprintf($bearsamppLang->getValue(Lang::EDIT_VHOST_TITLE), $this->initValue);
    }

    protected function getDialogTitle()
    {
        global $bearsamppLang;
        return sprintf($bearsamppLang->getValue(Lang::EDIT_VHOST_TITLE), $this->initValue);
    }

    protected function getDeleteDialogTitle()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::DELETE_VHOST_TITLE);
    }

    protected function initializeDialog($args)
    {
        global $bearsamppRoot;

        if (!isset($args[0]) || empty($args[0])) {
            return false;
        }

        $filePath = $bearsamppRoot->getVhostsPath() . '/' . $args[0] . '.conf';
        if (!file_exists($filePath)) {
            return false;
        }

        $fileContent = file_get_contents($filePath);
        if (!preg_match('/ServerName\s+(.*)/', $fileContent, $matchServerName) ||
            !preg_match('/DocumentRoot\s+"(.*)"/', $fileContent, $matchDocumentRoot)) {
            return false;
        }

        $this->initValue = trim($matchServerName[1]);
        return true;
    }

    protected function createFormFields($bearsamppWinbinder)
    {
        global $bearsamppRoot, $bearsamppLang;

        // Load existing vhost data
        $filePath = $bearsamppRoot->getVhostsPath() . '/' . $this->initValue . '.conf';
        $fileContent = file_get_contents($filePath);
        preg_match('/DocumentRoot\s+"(.*)"/', $fileContent, $matchDocumentRoot);
        $initDocumentRoot = Util::formatWindowsPath(trim($matchDocumentRoot[1]));

        $this->wbLabelServerName = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::VHOST_SERVER_NAME_LABEL) . ' :',
            15, 15, 85, null, WBC_RIGHT
        );
        $this->wbInputServerName = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $this->initValue,
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
            sprintf($bearsamppLang->getValue(Lang::VHOST_EXP_LABEL), $this->initValue, $initDocumentRoot),
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

        // Only check if name changed
        if ($values['serverName'] != $this->initValue && is_file($bearsamppRoot->getVhostsPath() . '/' . $values['serverName'] . '.conf')) {
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

        // Remove old vhost and certificate
        $bearsamppOpenSsl->removeCrt($this->initValue);
        @unlink($bearsamppRoot->getVhostsPath() . '/' . $this->initValue . '.conf');

        // Create new SSL certificate
        if (!$bearsamppOpenSsl->createCrt($values['serverName'])) {
            return false;
        }

        // Create new vhost configuration file
        return file_put_contents(
            $bearsamppRoot->getVhostsPath() . '/' . $values['serverName'] . '.conf',
            $bearsamppBins->getApache()->getVhostContent($values['serverName'], $values['documentRoot'])
        ) !== false;
    }

    protected function deleteItem()
    {
        global $bearsamppRoot, $bearsamppOpenSsl;

        return $bearsamppOpenSsl->removeCrt($this->initValue) &&
               @unlink($bearsamppRoot->getVhostsPath() . '/' . $this->initValue . '.conf');
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
        global $bearsamppLang;
        return sprintf($bearsamppLang->getValue(Lang::DELETE_VHOST), $this->initValue);
    }

    protected function getDeleteSuccessMessage()
    {
        global $bearsamppLang;
        return sprintf($bearsamppLang->getValue(Lang::VHOST_REMOVED), $this->initValue);
    }

    protected function getDeleteErrorMessage()
    {
        global $bearsamppRoot, $bearsamppLang;
        return sprintf(
            $bearsamppLang->getValue(Lang::VHOST_REMOVE_ERROR),
            $bearsamppRoot->getVhostsPath() . '/' . $this->initValue . '.conf'
        );
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
