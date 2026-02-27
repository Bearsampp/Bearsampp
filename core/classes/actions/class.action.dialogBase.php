<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionDialogBase
 *
 * Base class for dialog-based actions (Add/Edit Alias/Vhost).
 * This class provides common functionality for creating and managing dialog windows
 * with form fields, validation, and save/delete operations.
 */
abstract class ActionDialogBase
{
    protected $wbWindow;
    protected $wbProgressBar;
    protected $wbBtnSave;
    protected $wbBtnCancel;
    protected $wbBtnDelete;

    protected $initValue; // Initial value for edit operations

    const GAUGE_SAVE = 2;
    const GAUGE_DELETE = 2;

    /**
     * Get the dialog window title
     *
     * @return string The window title
     */
    abstract protected function getWindowTitle();

    /**
     * Get the gauge value for save operation (can be overridden)
     *
     * @return int The gauge value
     */
    protected function getGaugeSave()
    {
        return self::GAUGE_SAVE;
    }

    /**
     * Get the gauge value for delete operation (can be overridden)
     *
     * @return int The gauge value
     */
    protected function getGaugeDelete()
    {
        return self::GAUGE_DELETE;
    }

    /**
     * Create form fields specific to the dialog
     * This method should create all input fields, labels, and buttons
     *
     * @param object $bearsamppWinbinder The WinBinder instance
     * @return void
     */
    abstract protected function createFormFields($bearsamppWinbinder);

    /**
     * Get the current form values
     *
     * @param object $bearsamppWinbinder The WinBinder instance
     * @return array Associative array of form values
     */
    abstract protected function getFormValues($bearsamppWinbinder);

    /**
     * Validate the form input
     *
     * @param array $values The form values
     * @return array ['valid' => bool, 'error' => string|null]
     */
    abstract protected function validateInput($values);

    /**
     * Check if the item already exists (for add/edit operations)
     *
     * @param array $values The form values
     * @return bool True if exists, false otherwise
     */
    abstract protected function itemExists($values);

    /**
     * Save the item (create or update)
     *
     * @param array $values The form values
     * @return bool True on success, false on failure
     */
    abstract protected function saveItem($values);

    /**
     * Delete the item
     *
     * @return bool True on success, false on failure
     */
    abstract protected function deleteItem();

    /**
     * Get success message after save
     *
     * @param array $values The form values
     * @return string The success message
     */
    abstract protected function getSaveSuccessMessage($values);

    /**
     * Get error message after save failure
     *
     * @return string The error message
     */
    abstract protected function getSaveErrorMessage();

    /**
     * Get delete confirmation message
     *
     * @return string The confirmation message
     */
    abstract protected function getDeleteConfirmMessage();

    /**
     * Get success message after delete
     *
     * @return string The success message
     */
    abstract protected function getDeleteSuccessMessage();

    /**
     * Get error message after delete failure
     *
     * @return string The error message
     */
    abstract protected function getDeleteErrorMessage();

    /**
     * Get the dialog title for messages
     *
     * @return string The dialog title
     */
    abstract protected function getDialogTitle();

    /**
     * Get the delete dialog title
     *
     * @return string The delete dialog title
     */
    abstract protected function getDeleteDialogTitle();

    /**
     * Check if this is an edit operation (has delete button)
     *
     * @return bool True if edit operation, false if add operation
     */
    protected function isEditMode()
    {
        return isset($this->initValue) && !empty($this->initValue);
    }

    /**
     * Restart the service after save/delete
     *
     * @return void
     */
    abstract protected function restartService();

    /**
     * Initialize the dialog window
     *
     * @param array $args Command line arguments
     * @return bool True if initialization successful, false otherwise
     */
    protected function initializeDialog($args)
    {
        // To be implemented by child classes if needed
        return true;
    }

    /**
     * Constructor for dialog actions
     *
     * @param array $args Command line arguments
     */
    public function __construct($args)
    {
        global $bearsamppWinbinder;

        // Initialize dialog (child class can load data, etc.)
        if (!$this->initializeDialog($args)) {
            return;
        }

        $bearsamppWinbinder->reset();
        $this->wbWindow = $bearsamppWinbinder->createAppWindow(
            $this->getWindowTitle(),
            490,
            200,
            WBC_NOTIFY,
            WBC_KEYDOWN | WBC_KEYUP
        );

        // Create form fields (implemented by child class)
        $this->createFormFields($bearsamppWinbinder);

        // Create progress bar and buttons
        $this->createButtons($bearsamppWinbinder);

        // Set up event handler
        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Create standard buttons (Save, Delete, Cancel)
     *
     * @param object $bearsamppWinbinder The WinBinder instance
     * @return void
     */
    protected function createButtons($bearsamppWinbinder)
    {
        global $bearsamppLang;

        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar(
            $this->wbWindow,
            $this->getGaugeSave() + 1,
            15,
            137,
            $this->isEditMode() ? 190 : 275
        );

        if ($this->isEditMode()) {
            // Edit mode: Save, Delete, Cancel
            $this->wbBtnSave = $bearsamppWinbinder->createButton(
                $this->wbWindow,
                $bearsamppLang->getValue(Lang::BUTTON_SAVE),
                215,
                132
            );
            $this->wbBtnDelete = $bearsamppWinbinder->createButton(
                $this->wbWindow,
                $bearsamppLang->getValue(Lang::BUTTON_DELETE),
                300,
                132
            );
            $this->wbBtnCancel = $bearsamppWinbinder->createButton(
                $this->wbWindow,
                $bearsamppLang->getValue(Lang::BUTTON_CANCEL),
                385,
                132
            );
        } else {
            // Add mode: Save, Cancel
            $this->wbBtnSave = $bearsamppWinbinder->createButton(
                $this->wbWindow,
                $bearsamppLang->getValue(Lang::BUTTON_SAVE),
                300,
                132
            );
            $this->wbBtnCancel = $bearsamppWinbinder->createButton(
                $this->wbWindow,
                $bearsamppLang->getValue(Lang::BUTTON_CANCEL),
                387,
                132
            );
        }
    }

    /**
     * Process window events
     *
     * @param resource $window The window resource
     * @param int $id The control ID
     * @param resource $ctrl The control resource
     * @param mixed $param1 Additional parameter 1
     * @param mixed $param2 Additional parameter 2
     * @return void
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppWinbinder;

        // Handle save button
        if ($id == $this->wbBtnSave[WinBinder::CTRL_ID]) {
            $this->handleSave($window);
            return;
        }

        // Handle delete button (if in edit mode)
        if ($this->isEditMode() && $id == $this->wbBtnDelete[WinBinder::CTRL_ID]) {
            $this->handleDelete($window);
            return;
        }

        // Handle cancel button or window close
        if ($id == IDCLOSE || $id == $this->wbBtnCancel[WinBinder::CTRL_ID]) {
            $bearsamppWinbinder->destroyWindow($window);
            return;
        }

        // Handle custom events (implemented by child class)
        $this->handleCustomEvent($window, $id, $ctrl, $param1, $param2);
    }

    /**
     * Handle custom events (can be overridden by child classes)
     *
     * @param resource $window The window resource
     * @param int $id The control ID
     * @param resource $ctrl The control resource
     * @param mixed $param1 Additional parameter 1
     * @param mixed $param2 Additional parameter 2
     * @return void
     */
    protected function handleCustomEvent($window, $id, $ctrl, $param1, $param2)
    {
        // Default: do nothing
        // Child classes can override to handle specific events
    }

    /**
     * Handle save operation
     *
     * @param resource $window The window resource
     * @return void
     */
    protected function handleSave($window)
    {
        global $bearsamppWinbinder;

        $bearsamppWinbinder->setProgressBarMax($this->wbProgressBar, $this->getGaugeSave() + 1);
        $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

        // Get form values
        $values = $this->getFormValues($bearsamppWinbinder);

        // Validate input
        $validation = $this->validateInput($values);
        if (!$validation['valid']) {
            $bearsamppWinbinder->messageBoxError(
                $validation['error'],
                $this->getDialogTitle()
            );
            $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
            return;
        }

        // Check if item already exists (for add or rename operations)
        if ($this->itemExists($values)) {
            $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
            return;
        }

        // Save the item
        if ($this->saveItem($values)) {
            $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

            // Restart service
            $this->restartService();
            $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

            // Show success message
            $bearsamppWinbinder->messageBoxInfo(
                $this->getSaveSuccessMessage($values),
                $this->getDialogTitle()
            );
            $bearsamppWinbinder->destroyWindow($window);
        } else {
            $bearsamppWinbinder->messageBoxError(
                $this->getSaveErrorMessage(),
                $this->getDialogTitle()
            );
            $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
        }
    }

    /**
     * Handle delete operation
     *
     * @param resource $window The window resource
     * @return void
     */
    protected function handleDelete($window)
    {
        global $bearsamppWinbinder;

        $bearsamppWinbinder->setProgressBarMax($this->wbProgressBar, $this->getGaugeDelete() + 1);

        // Confirm deletion
        $confirm = $bearsamppWinbinder->messageBoxYesNo(
            $this->getDeleteConfirmMessage(),
            $this->getDeleteDialogTitle()
        );

        $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

        if ($confirm) {
            if ($this->deleteItem()) {
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                // Restart service
                $this->restartService();
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                // Show success message
                $bearsamppWinbinder->messageBoxInfo(
                    $this->getDeleteSuccessMessage(),
                    $this->getDeleteDialogTitle()
                );
                $bearsamppWinbinder->destroyWindow($window);
            } else {
                $bearsamppWinbinder->messageBoxError(
                    $this->getDeleteErrorMessage(),
                    $this->getDeleteDialogTitle()
                );
                $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
            }
        } else {
            $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
        }
    }
}
