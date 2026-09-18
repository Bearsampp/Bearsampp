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

    /** @var int Override: needs extra step for SSL cert regeneration. */
    const GAUGE_SAVE = 3;

    /**
     * Get the gauge value for save operation.
     *
     * @return int The gauge value.
     */
    protected function getGaugeSave()
    {
        return self::GAUGE_SAVE;
    }

    /**
     * Get the dialog window title.
     *
     * @return string The localized window title with the server name.
     */
    protected function getWindowTitle()
    {
        global $bearsamppLang;

        return sprintf($bearsamppLang->getValue(Lang::EDIT_VHOST_TITLE), $this->initValue);
    }

    /**
     * Get the dialog title for message boxes.
     *
     * @return string The localized dialog title with the server name.
     */
    protected function getDialogTitle()
    {
        global $bearsamppLang;

        return sprintf($bearsamppLang->getValue(Lang::EDIT_VHOST_TITLE), $this->initValue);
    }

    /**
     * Get the delete dialog title.
     *
     * @return string The localized delete dialog title.
     */
    protected function getDeleteDialogTitle()
    {
        global $bearsamppLang;

        return $bearsamppLang->getValue(Lang::DELETE_VHOST_TITLE);
    }

    /**
     * Initialize the dialog by loading the existing vhost configuration.
     *
     * @param   array  $args  Command line arguments where $args[0] is the server name.
     *
     * @return bool True if initialization successful, false otherwise.
     */
    protected function initializeDialog($args)
    {
        global $bearsamppRoot;

        if (!isset($args[0]) || empty($args[0])) {
            return false;
        }

        $filePath = Path::getVhostsPath() . '/' . $args[0] . '.conf';
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

    /**
     * Create the form fields for the edit vhost dialog.
     *
     * @param   object  $bearsamppWinbinder  The WinBinder instance.
     *
     * @return void
     */
    protected function createFormFields($bearsamppWinbinder)
    {
        global $bearsamppRoot, $bearsamppLang;

        // Load existing vhost data
        $filePath    = Path::getVhostsPath() . '/' . $this->initValue . '.conf';
        $fileContent = file_get_contents($filePath);
        preg_match('/DocumentRoot\s+"(.*)"/', $fileContent, $matchDocumentRoot);
        $initDocumentRoot = Path::formatWindowsPath(trim($matchDocumentRoot[1]));

        $this->wbLabelServerName = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::VHOST_SERVER_NAME_LABEL) . ' :',
            15,
            15,
            85,
            null,
            WBC_RIGHT
        );
        $this->wbInputServerName = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $this->initValue,
            105,
            13,
            150,
            null
        );

        $this->wbLabelDocRoot = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::VHOST_DOCUMENT_ROOT_LABEL) . ' :',
            15,
            45,
            85,
            null,
            WBC_RIGHT
        );
        $this->wbInputDocRoot = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $initDocumentRoot,
            105,
            43,
            190,
            null,
            null,
            WBC_READONLY
        );
        $this->wbBtnDocRoot   = $bearsamppWinbinder->createButton(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::BUTTON_BROWSE),
            300,
            43,
            110
        );

        $this->wbLabelExp = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            sprintf($bearsamppLang->getValue(Lang::VHOST_EXP_LABEL), $this->initValue, $initDocumentRoot),
            15,
            80,
            470,
            50
        );
    }

    /**
     * Get the current form values from the input controls.
     *
     * @param   object  $bearsamppWinbinder  The WinBinder instance.
     *
     * @return array Associative array with 'serverName' and 'documentRoot' keys.
     */
    protected function getFormValues($bearsamppWinbinder)
    {
        return [
            'serverName'   => $bearsamppWinbinder->getText($this->wbInputServerName[WinBinder::CTRL_OBJ]),
            'documentRoot' => $bearsamppWinbinder->getText($this->wbInputDocRoot[WinBinder::CTRL_OBJ])
        ];
    }

    /**
     * Validate the form input.
     *
     * @param   array  $values  The form values.
     *
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
     * Check if a different vhost with the same server name already exists.
     *
     * @param   array  $values  The form values.
     *
     * @return bool True if a conflicting vhost exists, false otherwise.
     */
    protected function itemExists($values)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppWinbinder;

        // Only check if name changed
        if ($values['serverName'] != $this->initValue && is_file(Path::getVhostsPath() . '/' . $values['serverName'] . '.conf')) {
            $bearsamppWinbinder->messageBoxError(
                sprintf($bearsamppLang->getValue(Lang::VHOST_ALREADY_EXISTS), $values['serverName']),
                $this->getDialogTitle()
            );

            return true;
        }

        return false;
    }

    /**
     * Save the vhost configuration file, removing old config and certificate first.
     *
     * @param   array  $values  The form values.
     *
     * @return bool True on success, false on failure.
     */
    protected function saveItem($values)
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppOpenSsl;

        // Remove old vhost and certificate
        $bearsamppOpenSsl->removeCrt($this->initValue);
        @unlink(Path::getVhostsPath() . '/' . $this->initValue . '.conf');

        // Create new SSL certificate
        if (!$bearsamppOpenSsl->createCrt($values['serverName'])) {
            return false;
        }

        // Create new vhost configuration file
        return file_put_contents(
                Path::getVhostsPath() . '/' . $values['serverName'] . '.conf',
                $bearsamppBins->getApache()->getVhostContent($values['serverName'], $values['documentRoot'])
            ) !== false;
    }

    /**
     * Delete the vhost configuration file and its SSL certificate.
     *
     * @return bool True on success, false on failure.
     */
    protected function deleteItem()
    {
        global $bearsamppRoot, $bearsamppOpenSsl;

        return $bearsamppOpenSsl->removeCrt($this->initValue) &&
            @unlink(Path::getVhostsPath() . '/' . $this->initValue . '.conf');
    }

    /**
     * Get the success message after saving.
     *
     * @param   array  $values  The form values.
     *
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
     * Get the delete confirmation message.
     *
     * @return string The localized confirmation message with the server name.
     */
    protected function getDeleteConfirmMessage()
    {
        global $bearsamppLang;

        return sprintf($bearsamppLang->getValue(Lang::DELETE_VHOST), $this->initValue);
    }

    /**
     * Get the success message after delete.
     *
     * @return string The localized success message with the server name.
     */
    protected function getDeleteSuccessMessage()
    {
        global $bearsamppLang;

        return sprintf($bearsamppLang->getValue(Lang::VHOST_REMOVED), $this->initValue);
    }

    /**
     * Get the error message after delete failure.
     *
     * @return string The localized error message with the file path.
     */
    protected function getDeleteErrorMessage()
    {
        global $bearsamppRoot, $bearsamppLang;

        return sprintf(
            $bearsamppLang->getValue(Lang::VHOST_REMOVE_ERROR),
            Path::getVhostsPath() . '/' . $this->initValue . '.conf'
        );
    }

    /**
     * Restart the Apache service after saving or deleting.
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
     * @param   resource  $window  The window resource.
     * @param   int       $id      The control ID.
     * @param   resource  $ctrl    The control resource.
     * @param   mixed     $param1  Additional parameter 1.
     * @param   mixed     $param2  Additional parameter 2.
     *
     * @return void
     */
    protected function handleCustomEvent($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        $serverName   = $bearsamppWinbinder->getText($this->wbInputServerName[WinBinder::CTRL_OBJ]);
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

