<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Constructs the download link for a new version if available.
 *
 * This function compares the current application version with the latest available version.
 * If the latest version is greater, it sets the display flag to true and constructs an HTML link
 * for downloading the new version, appending it to the 'download' key in the result array.
 *
 * @param array $result Holds the display flag and download link HTML.
 *
 * @return void Modifies the $result array by reference.
 * @global string $githubLatestVersionUrl The URL to download the latest version.
 * @global object $bearsamppLang Language management object, used for retrieving language-specific values.
 * @global string $bearsamppCurrentVersion The current version of the application.
 * @global string $githubLatestVersion The latest available version of the application.
 */
global $bearsamppLang, $bearsamppCore, $githubVersionData;

$result = array(
    'display' => false,
    'download' => '',
);

// Assuming getAppVersion() returns the current version number
$bearsamppCurrentVersion = $bearsamppCore->getAppVersion();

// Assuming getLatestVersion now returns an array with version and URL
$githubVersionData = Util::getLatestVersion(APP_GITHUB_LATEST_URL);
Util::logDebug('GitHub Version Data: ' . print_r($githubVersionData, true));

if (!empty($githubVersionData)) {
    Util::logDebug('GitHub Version Data: ' . print_r($githubVersionData, true));
} else {
    Util::logError('No data available in $githubVersionData');
}
/* check to see if everything went sideways */
if ($githubVersionData === null) {
    Util::logError('Failed to retrieve version data from GitHub URL: ' . APP_GITHUB_LATEST_URL);

    return;
}

/* Strip array into individual relevant strings */
$githubLatestVersion = $githubVersionData['version'];
$githubLatestVersionUrl = $githubVersionData['html_url']; // URL of the latest version
$githubVersionName = $githubVersionData['name'];
Util::logDebug($githubLatestVersion, $githubLatestVersionUrl);

// Directly compare version strings
if (version_compare($bearsamppCurrentVersion, $githubLatestVersion, '<')) {
    $result['display'] = true;
    $result['download'] .= '<a role="button" class="btn btn-success fullversionurl" href="' . $githubLatestVersionUrl . '" target="_blank"><i class="fa-solid fa-cloud-arrow-down"></i> ';
    $result['download'] .= $bearsamppLang->getValue(Lang::DOWNLOAD) . ' <strong>' . APP_TITLE . ' ' . $githubVersionName . '</strong><br />';
    $result['changelog'] = '';
}
echo json_encode($result);
