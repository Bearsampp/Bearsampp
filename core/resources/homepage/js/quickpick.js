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
                        messageData = data.message;
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
        if (isCompleted === true) {
            confirm(messageData);
        }
        setTimeout(() => {
            location.reload();
        }, 100); // Delay of 100 milliseconds
    }
}
