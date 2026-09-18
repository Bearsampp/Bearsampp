<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionAddAlias
 * Handles the creation of a new alias in the Bearsampp application.
 */
class ActionAddAlias extends ActionDialogBase
{
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label control of the alias name field. */
    private $wbLabelName;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the text input control of the alias name. */
    private $wbInputName;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label control of the alias destination field. */
    private $wbLabelDest;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the read-only text input of the alias destination path. */
    private $wbInputDest;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the browse button selecting the destination directory. */
    private $wbBtnDest;
    /** @var array{0: int, 1: mixed} WinBinder control wrapper (control ID at WinBinder::CTRL_ID, handle at WinBinder::CTRL_OBJ) for the label displaying the generated Apache alias directive. */
    private $wbLabelExp;

    /**
     * Get the dialog window title.
     *
     * @return string The localized window title.
     */
    protected function getWindowTitle()
    {
        global $bearsamppLang;

        return $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE);
    }

    /**
     * Get the dialog title for message boxes.
     *
     * @return string The localized dialog title.
     */
    protected function getDialogTitle()
    {
        global $bearsamppLang;

        return $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE);
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
     * Create the form fields for the add alias dialog.
     *
     * @param   object  $bearsamppWinbinder  The WinBinder instance.
     *
     * @return void
     */
    protected function createFormFields($bearsamppWinbinder)
    {
        global $bearsamppLang, $bearsamppBins;

        $initName      = 'test';
        $initDest      = 'C:\\';
        $apachePortUri = $bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';

        $this->wbLabelName = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::ALIAS_NAME_LABEL) . ' :',
            15,
            15,
            85,
            null,
            WBC_RIGHT
        );
        $this->wbInputName = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $initName,
            105,
            13,
            150,
            null
        );

        $this->wbLabelDest = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::ALIAS_DEST_LABEL) . ' :',
            15,
            45,
            85,
            null,
            WBC_RIGHT
        );
        $this->wbInputDest = $bearsamppWinbinder->createInputText(
            $this->wbWindow,
            $initDest,
            105,
            43,
            190,
            null,
            null,
            WBC_READONLY
        );
        $this->wbBtnDest   = $bearsamppWinbinder->createButton(
            $this->wbWindow,
            $bearsamppLang->getValue(Lang::BUTTON_BROWSE),
            300,
            43,
            110
        );

        $this->wbLabelExp = $bearsamppWinbinder->createLabel(
            $this->wbWindow,
            sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $initName, $initDest),
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
     * @return array Associative array with 'name' and 'dest' keys.
     */
    protected function getFormValues($bearsamppWinbinder)
    {
        return [
            'name' => $bearsamppWinbinder->getText($this->wbInputName[WinBinder::CTRL_OBJ]),
            'dest' => $bearsamppWinbinder->getText($this->wbInputDest[WinBinder::CTRL_OBJ])
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

        if (!ctype_alnum($values['name'])) {
            return [
                'valid' => false,
                'error' => sprintf($bearsamppLang->getValue(Lang::ALIAS_NOT_VALID_ALPHA), $values['name'])
            ];
        }

        return ['valid' => true];
    }

    /**
     * Check if an alias configuration file already exists.
     *
     * @param   array  $values  The form values.
     *
     * @return bool True if the alias already exists, false otherwise.
     */
    protected function itemExists($values)
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppWinbinder;

        if (is_file(Path::getAliasPath() . '/' . $values['name'] . '.conf')) {
            $bearsamppWinbinder->messageBoxError(
                sprintf($bearsamppLang->getValue(Lang::ALIAS_ALREADY_EXISTS), $values['name']),
                $this->getDialogTitle()
            );

            return true;
        }

        return false;
    }

    /**
     * Save the alias configuration file.
     *
     * @param   array  $values  The form values.
     *
     * @return bool True on success, false on failure.
     */
    protected function saveItem($values)
    {
        global $bearsamppRoot, $bearsamppBins;

        return file_put_contents(
                Path::getAliasPath() . '/' . $values['name'] . '.conf',
                $bearsamppBins->getApache()->getAliasContent($values['name'], $values['dest'])
            ) !== false;
    }

    /**
     * Delete the alias. Not used in add mode.
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
     * @param   array  $values  The form values.
     *
     * @return string The localized success message.
     */
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

    /**
     * Get the error message after save failure.
     *
     * @return string The localized error message.
     */
    protected function getSaveErrorMessage()
    {
        global $bearsamppLang;

        return $bearsamppLang->getValue(Lang::ALIAS_CREATED_ERROR);
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
     * Handle custom window events (name input change and browse button).
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
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        $apachePortUri = $bearsamppBins->getApache()->getPort() != 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';
        $aliasName     = $bearsamppWinbinder->getText($this->wbInputName[WinBinder::CTRL_OBJ]);
        $aliasDest     = $bearsamppWinbinder->getText($this->wbInputDest[WinBinder::CTRL_OBJ]);

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

