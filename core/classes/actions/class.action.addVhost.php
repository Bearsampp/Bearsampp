<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
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
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label control of the server name field. */
    private $wbLabelServerName;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the text input control of the server name. */
    private $wbInputServerName;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label control of the document root field. */
    private $wbLabelDocRoot;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the read-only text input of the document root path. */
    private $wbInputDocRoot;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the browse button selecting the document root directory. */
    private $wbBtnDocRoot;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label displaying the generated vhost directive preview. */
    private $wbLabelExp;

    /**
     * Get the dialog window title.
     *
     * @return string The localized window title.
     */
    protected function getWindowTitle()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::ADD_VHOST_TITLE);
    }

    /**
     * Get the dialog title for message boxes.
     *
     * @return string The localized dialog title.
     */
    protected function getDialogTitle()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::ADD_VHOST_TITLE);
    }

    /**
     * Get the delete dialog title.
     *
     * @return string Empty string, not used in add mode.
     */
    protected function getDeleteDialogTitle()
    {
        // Not used in add mode
        return '';
    }

    /**
     * Create the form fields for the add vhost dialog.
     *
     * @param object $bearsamppWinbinder The WinBinder instance.
     * @return void
     */
    protected function createFormFields($bearsamppWinbinder)
    {
        global $bearsamppRoot, $bearsamppLang;

        $initServerName = 'test.local';
        $initDocumentRoot = Path::formatWindowsPath(Path::getWwwPath()) . '\\' . $initServerName;

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

    /**
     * Get the current form values from the input controls.
     *
     * @param object $bearsamppWinbinder The WinBinder instance.
     * @return array Associative array with 'serverName' and 'documentRoot' keys.
     */
    protected function getFormValues($bearsamppWinbinder)
    {
        return [
            'serverName' => $bearsamppWinbinder->getText($this->wbInputServerName[WinBinder::CTRL_OBJ]),
            'documentRoot' => $bearsamppWinbinder->getText($this->wbInputDocRoot[WinBinder::CTRL_OBJ])
        ];
    }

    /**
     * Validate the form input.
     *
     * @param array $values The form values.
     * @return array ['valid' => bool, 'error' => string|null]
     */
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

    /**
     * Check if a vhost configuration file already exists.
     *
     * @param array $values The form values.
     * @return bool True if the vhost already exists, false otherwise.
     */
    protected function itemExists($values)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppWinbinder;

        if (is_file(Path::getVhostsPath() . '/' . $values['serverName'] . '.conf')) {
            $bearsamppWinbinder->messageBoxError(
                sprintf($bearsamppLang->getValue(Lang::VHOST_ALREADY_EXISTS), $values['serverName']),
                $this->getDialogTitle()
            );
            return true;
        }

        return false;
    }

    /**
     * Save the vhost configuration file and create an SSL certificate.
     *
     * @param array $values The form values.
     * @return bool True on success, false on failure.
     */
    protected function saveItem($values)
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppOpenSsl;

        // Create SSL certificate
        if (!$bearsamppOpenSsl->createCrt($values['serverName'])) {
            return false;
        }

        // Create vhost configuration file
        return file_put_contents(
            Path::getVhostsPath() . '/' . $values['serverName'] . '.conf',
            $bearsamppBins->getApache()->getVhostContent($values['serverName'], $values['documentRoot'])
        ) !== false;
    }

    /**
     * Delete the vhost. Not used in add mode.
     *
     * @return bool Always returns false.
     */
    protected function deleteItem()
    {
        // Not used in add mode
        return false;
    }

    /**
     * Get the success message after saving.
     *
     * @param array $values The form values.
     * @return string The localized success message.
     */
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

    /**
     * Get the error message after save failure.
     *
     * @return string The localized error message.
     */
    protected function getSaveErrorMessage()
    {
        global $bearsamppLang;
        return $bearsamppLang->getValue(Lang::VHOST_CREATED_ERROR);
    }

    /**
     * Get the delete confirmation message. Not used in add mode.
     *
     * @return string Empty string.
     */
    protected function getDeleteConfirmMessage()
    {
        // Not used in add mode
        return '';
    }

    /**
     * Get the success message after delete. Not used in add mode.
     *
     * @return string Empty string.
     */
    protected function getDeleteSuccessMessage()
    {
        // Not used in add mode
        return '';
    }

    /**
     * Get the error message after delete failure. Not used in add mode.
     *
     * @return string Empty string.
     */
    protected function getDeleteErrorMessage()
    {
        // Not used in add mode
        return '';
    }

    /**
     * Restart the Apache service after saving.
     *
     * @return void
     */
    protected function restartService()
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->getService()->restart();
    }

    /**
     * Handle custom window events (server name input change and browse button).
     *
     * @param resource $window The window resource.
     * @param int      $id     The control ID.
     * @param resource $ctrl   The control resource.
     * @param mixed    $param1 Additional parameter 1.
     * @param mixed    $param2 Additional parameter 2.
     * @return void
     */
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

