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
            if (done) break;
            responseText += decoder.decode(value, {stream: true});

            const parts = responseText.split('}{').map((part, index, arr) => {
                if (index === 0) return part + '}';
                if (index === arr.length - 1) return '{' + part;
                return '{' + part + '}';
            });

            for (const part of parts) {
                try {
                    const data = JSON.parse(part);
                    if (data.progress) {
                        console.log('Progress:', data.progress);
                        const progressValue = data.progress;
                        progressbar.style.width = '100%';
                        if (isDownloading) {
                            progressbar.innerText = `${progressValue} KBytes Downloaded`;
                        } else {
                            progressbar.innerText = `${progressValue} Extracted`;
                        }
                    } else if (data.success) {
                        console.log(data);
                        isCompleted = true;
                        messageData = data; // Store the full response object, not just the message
                    } else if (data.error) {
                        console.error('Error:', data.error);
                        window.alert(`Error: ${data.error}`);
                    } else if (data.phase === 'extracting') {
                        isDownloading = false;
                    }
                } catch (error) {
                    // Ignore JSON parse errors for incomplete parts
                }
            }

            // Clear responseText to keep only the unprocessed part
            responseText = parts[parts.length - 1].startsWith('{') ? parts[parts.length - 1] : '';
        }
    } catch (error) {
        console.error('Failed to install module:', error);
        window.alert('Failed to install module: ' + error.message);
    } finally {
        if (isCompleted === true && messageData) {
            console.log('Final messageData:', messageData);
            console.log('showApplyButton:', messageData.showApplyButton);
            console.log('moduleName:', messageData.moduleName);

            // Check if we should show the apply config button
            if (messageData.showApplyButton && messageData.moduleName) {
                // Don't reload immediately for apps/tools - let user apply config first
                showApplyConfigDialog(messageData.message, messageData.moduleName, version);
                return; // Exit early to prevent reload
            } else {
                // Show enhanced mode or binary message in same styled modal
                showInfoDialog(messageData.message || messageData);
                return; // Exit early to prevent reload
            }
        }
        setTimeout(() => {
            location.reload();
        }, 100); // Delay of 100 milliseconds
    }
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
    const modalHTML = `
        <div class="modal fade show" id="applyConfigModal" tabindex="-1" style="display: block;" aria-modal="true" role="dialog" data-bs-theme="dark">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title w-100 text-center">Module Installation Complete</h5>
                        <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="white-space: pre-wrap;">
${message}
                    </div>
                    <div class="modal-footer border-secondary justify-content-center">
                        <button type="button" class="btn btn-secondary" id="closeModalBtn">Close</button>
                        <button type="button" class="btn btn-success" id="applyConfigBtn">Apply Config</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    `;

    // Insert modal into DOM
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHTML;
    document.body.appendChild(modalContainer);

    // Get button references
    const applyButton = document.getElementById('applyConfigBtn');
    const closeButton = document.getElementById('closeModalBtn');
    const closeX = modalContainer.querySelector('.btn-close');

    // Apply Config button handler
    applyButton.onclick = async () => {
        applyButton.disabled = true;
        applyButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Applying...';

        try {
            const result = await applyModuleConfig(moduleName, version);

            // Update modal to show success - keep same styling as initial message
            const modalBody = modalContainer.querySelector('.modal-body');
            modalBody.style.whiteSpace = 'pre-wrap';
            const htmlMessage = `Configuration updated successfully!<br><br>✓ Set ${moduleName}Version = "${version}"<br><br><span class='text-warning'><i class='fas fa-exclamation-triangle'></i> IMPORTANT: Right-click the Bearsampp tray icon and select 'Reload' to activate the new version.</span>`;
            modalBody.innerHTML = htmlMessage;

            // Change button to just "Close"
            applyButton.style.display = 'none';
            closeButton.textContent = 'OK';
            closeButton.classList.remove('btn-secondary');
            closeButton.classList.add('btn-primary');

        } catch (error) {
            applyButton.disabled = false;
            applyButton.textContent = 'Apply Config';

            // Show error in modal - keep same styling as initial message
            const modalBody = modalContainer.querySelector('.modal-body');
            const currentText = modalBody.textContent;
            modalBody.textContent = currentText + `\n\n❌ Error: ${error.message}`;
        }
    };

    // Close button handler
    const closeModal = () => {
        modalContainer.remove();
        // Reload after closing
        setTimeout(() => location.reload(), 100);
    };

    closeButton.onclick = closeModal;
    closeX.onclick = closeModal;
}

/**
 * Shows an info dialog for Enhanced Mode or binary installations
 *
 * @param {string} message - The message to display
 */
function showInfoDialog(message) {
    console.log('showInfoDialog called with:', message);

    // Create Bootstrap modal structure with dark theme
    const modalHTML = `
        <div class="modal fade show" id="infoModal" tabindex="-1" style="display: block;" aria-modal="true" role="dialog" data-bs-theme="dark">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title w-100 text-center">Module Installation Complete</h5>
                        <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="infoModalBody">
                    </div>
                    <div class="modal-footer border-secondary justify-content-center">
                        <button type="button" class="btn btn-primary" id="okBtn">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    `;

    // Insert modal into DOM
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHTML;
    document.body.appendChild(modalContainer);

    // Set message content (use innerHTML to support FontAwesome icons)
    const modalBody = document.getElementById('infoModalBody');
    // Convert newlines to <br> for HTML display, but preserve white-space for formatting
    modalBody.style.whiteSpace = 'pre-wrap';
    const htmlMessage = message.replace(/\n/g, '<br>');
    modalBody.innerHTML = htmlMessage;

    // Get button references
    const okButton = document.getElementById('okBtn');
    const closeX = modalContainer.querySelector('.btn-close');

    // Close handler
    const closeModal = () => {
        modalContainer.remove();
        // Reload after closing
        setTimeout(() => location.reload(), 100);
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
