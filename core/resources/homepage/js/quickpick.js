/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Initializes event listeners and handles the UI interactions for the custom select dropdown.
 * This function is executed when the DOM content is fully loaded.
 */
document.addEventListener("DOMContentLoaded", function () {
    let selectedHeader = null; // Store which module has been selected to allow open/close of versions
    let progressValue = 0; // Initialize progressValue as a number

    // Initialize Enhanced QuickPick toggle switch
    const enhancedQuickPickSwitch = document.getElementById('enhancedQuickPickSwitch');
    if (enhancedQuickPickSwitch) {
        enhancedQuickPickSwitch.addEventListener('change', function() {
            toggleEnhancedQuickPick(this.checked ? 1 : 0);
        });
    }

    const customSelect = document.querySelector(".custom-select"); // parent div of quickpick select
    const selectBtn = document.querySelector(".select-button"); // trigger button to pop down ul
    const selectDropdown = document.querySelector(".select-dropdown"); // the dropdown menu

    if (selectBtn !== null) {
        // Make the dropdown focusable
        if (selectDropdown) {
            selectDropdown.setAttribute("tabindex", "-1");
        }

        // add a click event to select button
        selectBtn.addEventListener("click", () => {
            // add/remove active class on the container element to show/hide
            customSelect.classList.toggle("active");
            // update the aria-expanded attribute based on the current state
            selectBtn.setAttribute(
                "aria-expanded",
                selectBtn.getAttribute("aria-expanded") === "true" ? "false" : "true"
            );

            // If opening the dropdown, focus it
            if (customSelect.classList.contains("active") && selectDropdown) {
                setTimeout(() => {
                    selectDropdown.focus();
                }, 0);
            }

            scrolltoview();
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", function(event) {
            // Check if the click was outside the custom select and not on the select button itself
            if (customSelect && !customSelect.contains(event.target) && event.target !== selectBtn) {
                // Close the dropdown
                customSelect.classList.remove("active");
                selectBtn.setAttribute("aria-expanded", "false");
            }
        });

        // Add blur event to close dropdown when it loses focus
        if (selectDropdown) {
            selectDropdown.addEventListener("blur", function(event) {
                // Check if the new focus target is outside the dropdown
                if (!customSelect.contains(event.relatedTarget) && event.relatedTarget !== selectBtn) {
                    customSelect.classList.remove("active");
                    selectBtn.setAttribute("aria-expanded", "false");
                }
            });
        }

        // Add event listener to select button to stop propagation
        selectBtn.addEventListener("click", function(event) {
            // Stop the event from bubbling up to the document
            event.stopPropagation();
        });

        const optionsList = document.querySelectorAll(".select-dropdown li.moduleheader");
        optionsList.forEach((option) => {
            /**
             * Handles click and keyup events for module headers.
             * @param {Event} e - The event object.
             */
            function handler(e) {
                // Stop propagation to prevent document click handler from firing
                if (e.type === "click") {
                    e.stopPropagation();
                }

                // Click Events
                if (e.type === "click" && e.clientX !== 0 && e.clientY !== 0) {
                    if (selectedHeader !== e.target.innerText) {
                        showModule(e.target.innerText);
                        selectedHeader = e.target.innerText;
                    } else {
                        hideall();
                        selectedHeader = null;
                    }
                }
                // Key Events
                if (e.key === "Enter") {
                    if (selectedHeader !== e.target.innerText) {
                        showModule(e.target.innerText);
                        selectedHeader = e.target.innerText;
                    } else {
                        hideall();
                        selectedHeader = null;
                    }
                }
            }
            option.addEventListener("keyup", handler);
            option.addEventListener("click", handler);
        });

        hideall();

        let selects = document.querySelectorAll('.select-dropdown li.moduleoption');
        selects.forEach(function (select) {
            /**
             * Handles click events for module options.
             * @param {Event} e - The event object.
             */
            select.addEventListener('click', function (e) {
                // Stop propagation to prevent document click handler from firing
                e.stopPropagation();

                console.log(e);
                let selectedOption = e.target;

                let moduleName = selectedOption.getAttribute('data-module');
                let version = selectedOption.getAttribute('data-value');
                if (moduleName && version) {
                    installModule(moduleName, version);
                }
                hideall();
                // Close the dropdown and update aria-expanded
                customSelect.classList.remove("active");
                if (selectBtn) {
                    selectBtn.setAttribute("aria-expanded", "false");
                }
            });
        });
        scrolltoview();
    }
});

/**
 * Scrolls the select dropdown into view.
 */
function scrolltoview() {
    let e = document.getElementById('select-dropdown');
    e.scrollIntoView(true);
}

/**
 * Shows the module options for the specified module name.
 * @param {string} modName - The name of the module to show.
 */
function showModule(modName) {
    hideall();
    let options = document.querySelectorAll('li[id^='.concat(modName).concat(']'));
    options.forEach(function (option) {
        option.hidden = false;
        option.removeAttribute('hidden');
    });
}

/**
 * Hides all module options.
 */
function hideall() {
    let options = document.querySelectorAll('.moduleoption');
    options.forEach(function (option) {
        option.hidden = true;
    });
}

/**
 * Installs the specified module and version.
 *
 * This function sends an AJAX request to install a module with the specified name and version.
 * It updates the UI to show the download and extraction progress and handles any errors that occur during the process.
 *
 * @param {string} moduleName - The name of the module to install.
 * @param {string} version - The version of the module to install.
 * @returns {Promise<void>} - A promise that resolves when the installation is complete.
 */
async function installModule(moduleName, version) {
    const url = AJAX_URL;
    const senddata = new URLSearchParams();
    const progress = document.getElementById('progress');
    const progressbar = document.getElementById('progress-bar');

    const downloadmodule = document.getElementById('download-module');
    const downloadversion = document.getElementById('download-version');
    let isCompleted = false;
    let messageData = '';
    progressbar.classList.add('progress-bar-animated', 'progress-bar-striped');
    progressbar.style.width = '100%';
    progressbar.innerText = `Downloading ${moduleName} ${version}`;
    progress.style.display = "block";
    downloadmodule.innerText = moduleName;
    downloadversion.innerText = version;
    senddata.append('module', moduleName);
    senddata.append('version', version);
    senddata.append('proc', 'quickpick');

    // Add CSRF token
    if (typeof addCsrfToken === 'function') {
        addCsrfToken(senddata);
    }

    const options = {
        method: 'POST',
        body: senddata,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    };

    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let responseText = '';
        let isDownloading = true;

        while (true) {
            const {done, value} = await reader.read();
            if (done) {
                // Process any remaining text in responseText
                if (responseText.trim()) {
                    try {
                        const data = JSON.parse(responseText.trim());
                        processJsonChunk(data);
                    } catch (e) {
                        console.error('Failed to parse final JSON chunk:', responseText, e);
                    }
                }
                break;
            }
            responseText += decoder.decode(value, {stream: true});

            const chunks = responseText.split('\n');
            for (let i = 0; i < chunks.length - 1; i++) {
                const chunk = chunks[i].trim();
                if (!chunk) continue;
                try {
                    const data = JSON.parse(chunk);
                    processJsonChunk(data);
                } catch (error) {
                    console.error('Failed to parse JSON chunk:', chunk, error);
                }
            }
            responseText = chunks[chunks.length - 1];
        }

        function processJsonChunk(data) {
            if (data.progress) {
                console.log('Progress:', data.progress);
                const progressValue = data.progress;
                // Since we don't know total size for download, 
                // we show it as "working" (100% width or just updating text)
                // Extraction progress is percentage-based in the string
                if (isDownloading) {
                    progressbar.style.width = '100%';
                    progressbar.innerText = `${progressValue} KBytes Downloaded`;
                } else {
                    const match = progressValue.match(/(\d+)%/);
                    const percentage = match ? parseInt(match[1]) : null;

                    if (progressValue.includes('...') || progressValue === 'Analyzing' || progressValue === 'Initializing' || !progressValue.includes('%') || percentage === 0) {
                        progressbar.style.width = '100%';
                        progressbar.classList.add('progress-bar-animated', 'progress-bar-striped');
                        progressbar.innerText = progressValue.includes('%') ? `Extraction: ${progressValue}` : progressValue;
                    } else {
                        progressbar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                        const width = percentage !== null ? percentage + '%' : '100%';
                        progressbar.style.width = width;
                        progressbar.innerText = `Extraction: ${progressValue}`;
                    }
                }
            } else if (data.success) {
                console.log('Installation success:', data);
                progressbar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                progressbar.style.width = '100%';
                isCompleted = true;
                messageData = data; // Store the full response object, not just the message
            } else if (data.error) {
                console.error('Error:', data.error);
                progressbar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                window.alert(`Error: ${data.error}`);
            } else if (data.phase === 'extracting') {
                isDownloading = false;
                progressbar.classList.add('progress-bar-animated', 'progress-bar-striped');
                progressbar.style.width = '100%';
                progressbar.innerText = 'Initializing extraction...';
            }
        }
    } catch (error) {
        console.error('Failed to install module:', error);
        window.alert('Failed to install module: ' + error.message);
    } finally {
        if (isCompleted === true && messageData) {
            console.log('Final messageData:', messageData);
            console.log('showApplyButton:', messageData.showApplyButton);
            console.log('moduleName:', messageData.moduleName);

            // Reload was launched automatically to apply the new version - show a brief
            // progress dialog while it runs, then refresh the page.
            if (messageData.reload_triggered) {
                showReloadingDialog(downloadmodule.innerText, version);
                return; // Exit early to prevent immediate reload
            }

            // Check if we should show the apply config button
            if (messageData.showApplyButton && messageData.moduleName) {
                // Don't reload immediately for apps/tools - let user apply config first
                showApplyConfigDialog(messageData.message, messageData.moduleName, version);
                return; // Exit early to prevent reload
            } else {
                // Show enhanced mode or binary message in same styled modal
                showInfoDialog(messageData.message || messageData, downloadmodule.innerText, version);
                return; // Exit early to prevent reload
            }
        }
        // Only reload if not completed (e.g. error or interrupted)
        // because success paths handle reload via their own modals
        if (!isCompleted) {
            setTimeout(() => {
                location.reload();
            }, 100); // Delay of 100 milliseconds
        }
    }
}

/**
 * Shows a progress dialog while the reload action applies the newly installed version,
 * then refreshes the page. The reload runs as a background process on the server
 * (restarting services as needed). Polls for completion status instead of using a fixed timeout.
 *
 * @param {string} moduleName - The module name (e.g., 'MySQL')
 * @param {string} version - The version that was installed
 */
function showReloadingDialog(moduleName, version) {
    console.log('showReloadingDialog called with:', {moduleName, version});

    const modalContainer = document.createElement('div');

    const modal = document.createElement('div');
    modal.className = 'modal fade show';
    modal.id = 'reloadingModal';
    modal.setAttribute('tabindex', '-1');
    modal.style.display = 'block';
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('data-bs-theme', 'dark');

    const modalDialog = document.createElement('div');
    modalDialog.className = 'modal-dialog modal-dialog-centered';

    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content bg-dark text-light';

    const modalHeader = document.createElement('div');
    modalHeader.className = 'modal-header border-secondary';

    const title = document.createElement('h5');
    title.className = 'modal-title w-100 text-center';
    title.textContent = `Applying ${moduleName} ${version}`;

    modalHeader.appendChild(title);

    const modalBody = document.createElement('div');
    modalBody.className = 'modal-body text-center';
    modalBody.id = 'reloadingModalBody';

    const spinner = document.createElement('div');
    spinner.className = 'spinner-border text-primary mb-3';
    spinner.setAttribute('role', 'status');

    const spinnerHidden = document.createElement('span');
    spinnerHidden.className = 'visually-hidden';
    spinnerHidden.textContent = 'Loading...';
    spinner.appendChild(spinnerHidden);

    const message = document.createElement('p');
    message.className = 'mb-0';
    message.id = 'reloadingMessage';
    message.textContent = 'Applying version changes and restarting services...';

    const small = document.createElement('small');
    small.className = 'text-muted';
    small.id = 'reloadingStatus';
    small.textContent = 'This page will refresh automatically.';

    modalBody.appendChild(spinner);
    modalBody.appendChild(message);
    modalBody.appendChild(small);

    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);

    modalDialog.appendChild(modalContent);
    modal.appendChild(modalDialog);

    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';

    modalContainer.appendChild(modal);
    modalContainer.appendChild(backdrop);
    document.body.appendChild(modalContainer);

    pollReloadCompletion(modalContainer, moduleName, version);
}

/**
 * Polls the server for reload completion status.
 * Checks the reload status endpoint and refreshes when complete or on timeout.
 *
 * @param {Element} modalContainer - The modal container element to remove after reload
 */
async function pollReloadCompletion(modalContainer, moduleName, version) {
    const maxWaitTime = 60000; // Maximum 60 seconds wait
    const pollInterval = 1000; // Poll every 1 second
    const startTime = Date.now();
    let pollCount = 0;

    const updateStatus = (message) => {
        const statusElement = document.getElementById('reloadingStatus');
        if (statusElement) {
            statusElement.textContent = message;
        }
    };

    const pollStatus = async () => {
        try {
            const url = AJAX_URL;
            const senddata = new URLSearchParams();
            senddata.append('proc', 'reloadstatus');

            // Add CSRF token
            if (typeof addCsrfToken === 'function') {
                addCsrfToken(senddata);
            }

            const response = await fetch(url, {
                method: 'POST',
                body: senddata,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            pollCount++;

            if (data.completed && data.status && data.status.success) {
                console.log('Reload completed successfully after', pollCount, 'polls');
                modalContainer.remove();
                showInfoDialog(`Module ${moduleName} version ${version} installed successfully!`);
                return;
            } else if (data.completed && data.status && !data.status.success) {
                console.error('Reload completed with failures:', data.status);
                updateStatus('Reload completed with errors. Refreshing...');
                setTimeout(() => {
                    location.reload();
                }, 2000);
                return;
            }

            const elapsed = Date.now() - startTime;
            if (elapsed >= maxWaitTime) {
                console.warn('Reload status check timeout after', maxWaitTime, 'ms');
                updateStatus('Timeout waiting for reload. Refreshing...');
                setTimeout(() => {
                    location.reload();
                }, 1000);
                return;
            }

            updateStatus(`Waiting for reload completion... (${Math.round(elapsed / 1000)}s)`);
            setTimeout(pollStatus, pollInterval);

        } catch (error) {
            console.error('Error polling reload status:', error);
            const elapsed = Date.now() - startTime;

            if (elapsed >= maxWaitTime) {
                console.warn('Giving up after max wait time');
                updateStatus('Reload timeout. Refreshing...');
                setTimeout(() => {
                    location.reload();
                }, 1000);
                return;
            }

            updateStatus(`Connection issue. Retrying... (${pollCount} attempts)`);
            setTimeout(pollStatus, pollInterval * 2);
        }
    };

    pollStatus();
}

/**
 * Shows a custom dialog with an "Apply Config" button for apps/tools
 *
 * @param {string} message - The success message to display
 * @param {string} moduleName - The module name (e.g., 'composer', 'git')
 * @param {string} version - The version to apply
 */
function showApplyConfigDialog(message, moduleName, version) {
    console.log('showApplyConfigDialog called with:', {message, moduleName, version});

    // Create Bootstrap modal structure with dark theme
    const modalContainer = document.createElement('div');

    const modal = document.createElement('div');
    modal.className = 'modal fade show';
    modal.id = 'applyConfigModal';
    modal.setAttribute('tabindex', '-1');
    modal.style.display = 'block';
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('data-bs-theme', 'dark');

    const modalDialog = document.createElement('div');
    modalDialog.className = 'modal-dialog modal-dialog-centered';

    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content bg-dark text-light';

    const modalHeader = document.createElement('div');
    modalHeader.className = 'modal-header border-secondary';

    const title = document.createElement('h5');
    title.className = 'modal-title w-100 text-center';
    title.textContent = 'Module Installation Complete';

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'btn-close btn-close-white position-absolute end-0 me-3';
    closeBtn.setAttribute('data-bs-dismiss', 'modal');
    closeBtn.setAttribute('aria-label', 'Close');

    modalHeader.appendChild(title);
    modalHeader.appendChild(closeBtn);

    const modalBody = document.createElement('div');
    modalBody.className = 'modal-body';
    modalBody.style.whiteSpace = 'pre-wrap';
    modalBody.textContent = message;

    const modalFooter = document.createElement('div');
    modalFooter.className = 'modal-footer border-secondary justify-content-center';

    const closeButton = document.createElement('button');
    closeButton.id = 'closeModalBtn';
    closeButton.type = 'button';
    closeButton.className = 'btn btn-secondary';
    closeButton.textContent = 'Close';

    const applyButton = document.createElement('button');
    applyButton.id = 'applyConfigBtn';
    applyButton.type = 'button';
    applyButton.className = 'btn btn-success';
    applyButton.textContent = 'Apply Config';

    modalFooter.appendChild(closeButton);
    modalFooter.appendChild(applyButton);

    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalContent.appendChild(modalFooter);

    modalDialog.appendChild(modalContent);
    modal.appendChild(modalDialog);

    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';

    modalContainer.appendChild(modal);
    modalContainer.appendChild(backdrop);
    document.body.appendChild(modalContainer);

    // Apply Config button handler
    applyButton.onclick = async () => {
        applyButton.disabled = true;
        applyButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Applying...';

        try {
            const result = await applyModuleConfig(moduleName, version);

            // Update modal to show success - clear and rebuild with new content
            modalBody.innerHTML = '';
            modalBody.style.whiteSpace = 'pre-wrap';

            const successMsg = document.createElement('div');
            successMsg.textContent = 'Configuration updated successfully!';
            modalBody.appendChild(successMsg);

            const br1 = document.createElement('br');
            const br2 = document.createElement('br');
            const br3 = document.createElement('br');

            const configMsg = document.createElement('div');
            configMsg.textContent = `✓ Set ${moduleName}Version = "${version}"`;

            const warning = document.createElement('div');
            warning.className = 'text-warning mt-3';
            const warningIcon = document.createElement('i');
            warningIcon.className = 'fas fa-exclamation-triangle';
            const warningText = document.createTextNode(' IMPORTANT: Right-click the Bearsampp tray icon and select \'Reload\' to activate the new version.');
            warning.appendChild(warningIcon);
            warning.appendChild(warningText);

            modalBody.appendChild(br1);
            modalBody.appendChild(br2);
            modalBody.appendChild(configMsg);
            modalBody.appendChild(br3);
            modalBody.appendChild(warning);

            // Change button to just "Close"
            applyButton.style.display = 'none';
            closeButton.textContent = 'OK';
            closeButton.classList.remove('btn-secondary');
            closeButton.classList.add('btn-primary');

        } catch (error) {
            applyButton.disabled = false;
            applyButton.textContent = 'Apply Config';

            // Show error in modal
            const errorMsg = document.createElement('div');
            errorMsg.className = 'mt-2';
            errorMsg.textContent = `❌ Error: ${error.message}`;
            modalBody.appendChild(errorMsg);
        }
    };

    // Close button handler
    const closeModal = () => {
        modalContainer.remove();
        // Reload after closing
        setTimeout(() => location.reload(), 100);
    };

    closeButton.onclick = closeModal;
    closeBtn.onclick = closeModal;
}

/**
 * Shows an info dialog for Enhanced Mode or binary installations
 *
 * @param {string} message - The message to display
 */
function showInfoDialog(message) {
    console.log('showInfoDialog called with:', message);

    const successText = typeof message === 'string' ? message : JSON.stringify(message);

    // Build modal via DOM APIs so dynamic content is never parsed as HTML
    const modalContainer = document.createElement('div');

    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';

    const modal = document.createElement('div');
    modal.className = 'modal fade show';
    modal.id = 'infoModal';
    modal.setAttribute('tabindex', '-1');
    modal.style.display = 'block';
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('data-bs-theme', 'dark');

    const modalDialog = document.createElement('div');
    modalDialog.className = 'modal-dialog modal-dialog-centered';

    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content bg-dark text-light';

    // Header
    const modalHeader = document.createElement('div');
    modalHeader.className = 'modal-header border-secondary position-relative';

    const title = document.createElement('h5');
    title.className = 'modal-title w-100 text-center';
    title.textContent = 'Module Installation Complete';

    const closeX = document.createElement('button');
    closeX.type = 'button';
    closeX.className = 'btn-close btn-close-white position-absolute end-0 me-3';
    closeX.setAttribute('aria-label', 'Close');

    modalHeader.appendChild(title);
    modalHeader.appendChild(closeX);

    // Body
    const modalBody = document.createElement('div');
    modalBody.className = 'modal-body';

    const successP = document.createElement('p');
    successP.className = 'mb-3';
    successP.textContent = successText;

    const extractedP = document.createElement('p');
    extractedP.className = 'mb-1';
    extractedP.textContent = '\u2713 Files extracted';

    const configP = document.createElement('p');
    configP.className = 'mb-3';
    configP.textContent = '\u2713 Configuration updated';

    const warning = document.createElement('div');
    warning.className = 'alert alert-warning d-flex align-items-start gap-2 mb-0';
    warning.setAttribute('role', 'alert');

    const warnIcon = document.createElement('span');
    warnIcon.textContent = '\u26a0';

    const warnText = document.createElement('span');
    warnText.textContent = "IMPORTANT: Right-click the Bearsampp tray icon and select 'Reload' to activate the new version.";

    warning.appendChild(warnIcon);
    warning.appendChild(warnText);

    modalBody.appendChild(successP);
    modalBody.appendChild(extractedP);
    modalBody.appendChild(configP);
    modalBody.appendChild(warning);

    // Footer
    const modalFooter = document.createElement('div');
    modalFooter.className = 'modal-footer border-secondary justify-content-center';

    const okButton = document.createElement('button');
    okButton.type = 'button';
    okButton.className = 'btn btn-primary';
    okButton.textContent = 'OK';

    modalFooter.appendChild(okButton);

    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalContent.appendChild(modalFooter);
    modalDialog.appendChild(modalContent);
    modal.appendChild(modalDialog);
    modalContainer.appendChild(modal);
    modalContainer.appendChild(backdrop);
    document.body.appendChild(modalContainer);

    // Close handler
    const closeModal = () => {
        modalContainer.remove();
        setTimeout(() => {
            location.reload();
        }, 100);
    };

    okButton.onclick = closeModal;
    closeX.onclick = closeModal;
}

/**
 * Applies the module version to bearsampp.conf
 *
 * @param {string} moduleName - The module name (e.g., 'composer', 'git')
 * @param {string} version - The version to apply
 */
async function applyModuleConfig(moduleName, version) {
    const url = AJAX_URL;
    const senddata = new URLSearchParams();
    senddata.append('proc', 'applymoduleconfig');
    senddata.append('moduleName', moduleName);
    senddata.append('version', version);

    // Add CSRF token
    if (typeof addCsrfToken === 'function') {
        addCsrfToken(senddata);
    }

    const options = {
        method: 'POST',
        body: senddata,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    };

    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (data.success) {
            console.log('Config applied successfully');
            // Don't use alert - the modal will close and page will reload
            // The success message is already in data.message if needed
            return data;
        } else if (data.error) {
            console.error('Error applying config:', data.error);
            throw new Error(data.error);
        }
    } catch (error) {
        console.error('Failed to apply config:', error);
        throw error;
    }
}

/**
 * Toggles the EnhancedQuickPick setting via AJAX.
 *
 * @param {number} value - The value to set (0 or 1).
 */
async function toggleEnhancedQuickPick(value) {
    const url = AJAX_URL;
    const senddata = new URLSearchParams();
    senddata.append('proc', 'toggleenhancedquickpick');
    senddata.append('value', value);

    // Add CSRF token
    if (typeof addCsrfToken === 'function') {
        addCsrfToken(senddata);
    }

    const options = {
        method: 'POST',
        body: senddata,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    };

    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (data.success) {
            console.log('EnhancedQuickPick mode changed to:', data.mode);

            // Show a brief notification to the user
            const modeName = data.mode === 'enhanced' ? 'Enhanced' : 'Standard';
            const message = `QuickPick mode switched to ${modeName}`;

            // Create a temporary notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
            notification.style.zIndex = '9999';
            notification.textContent = message;
            document.body.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        } else if (data.error) {
            console.error('Error toggling EnhancedQuickPick:', data.error);

            // Check if it's an "Invalid proc parameter" error
            if (data.error.includes('Invalid proc parameter')) {
                // Extract the proc value from the error message if available
                const procMatch = data.error.match(/"([^"]+)"/);
                const procValue = procMatch ? procMatch[1] : 'unknown';
                window.alert(`Configuration Error: The requested procedure "${procValue}" is not recognized.\n\nThe EnhancedQuickPick parameter may be missing from bearsampp.conf. Please add it manually or reload the application.`);
            } else {
                window.alert(`Error: ${data.error}`);
            }

            // Revert the switch state
            const enhancedQuickPickSwitch = document.getElementById('enhancedQuickPickSwitch');
            if (enhancedQuickPickSwitch) {
                enhancedQuickPickSwitch.checked = !enhancedQuickPickSwitch.checked;
            }
        }
    } catch (error) {
        console.error('Failed to toggle EnhancedQuickPick:', error);
        window.alert('Failed to toggle EnhancedQuickPick: ' + error.message);

        // Revert the switch state
        const enhancedQuickPickSwitch = document.getElementById('enhancedQuickPickSwitch');
        if (enhancedQuickPickSwitch) {
            enhancedQuickPickSwitch.checked = !enhancedQuickPickSwitch.checked;
        }
    }
}
