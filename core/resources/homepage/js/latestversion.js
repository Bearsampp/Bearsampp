/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

async function getLatestVersionStatus() {
    const url = AJAX_URL; // Ensure this variable is defined and points to your server-side script handling the AJAX requests.
    const senddata = new URLSearchParams();
    senddata.append('proc', 'latestversion'); // Setting 'proc' to 'latestversion'

    const options = {
        method: 'POST',
        body: senddata
    };

    try {
        let response = await fetch(url, options);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        let responseData = await response.json(); // Assuming the server responds with JSON data

        if (responseData.display) {
            document.querySelector('.latestversion-download').insertAdjacentHTML('beforeend', responseData.download);
            document.querySelector('.latestversion-changelog').insertAdjacentHTML('beforeend', responseData.changelog);
            document.getElementById("latestversionnotify").style.display = 'block';
        }
    } catch (error) {
        console.error('Failed to fetch latest version status:', error);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    getLatestVersionStatus();
});
