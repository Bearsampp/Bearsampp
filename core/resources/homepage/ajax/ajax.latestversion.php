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
 * @global object $bearsamppLang Language management object, used for retrieving language-specific values.
 * @global object $bearsamppCore Core application object, used for retrieving the current version.
 * @global array $githubVersionData Holds the latest version data retrieved from GitHub.
 */
global $bearsamppLang, $bearsamppCore, $githubVersionData;

$result = array(
    'display' => false,
    'download' => '',
);

// Assuming getAppVersion() returns the current version number
$bearsamppCurrentVersion = $bearsamppCore->getAppVersion();

/**
 * Retrieves the latest version data from GitHub.
 *
 * @return array|null Returns an array with version data or null if retrieval fails.
 */
$githubVersionData = Util::getLatestVersion(APP_GITHUB_LATEST_URL);
Util::logDebug('GitHub Version Data: ' . print_r($githubVersionData, true));

if (!empty($githubVersionData)) {
    Util::logDebug('GitHub Version Data: ' . print_r($githubVersionData, true));
} else {
    Util::logError('No data available in $githubVersionData');
}

/**
 * Checks if the version data retrieval failed.
 *
 * @return void Exits the function if version data is null.
 */
if ($githubVersionData === null) {
    Util::logError('Failed to retrieve version data from GitHub URL: ' . APP_GITHUB_LATEST_URL);
    return;
}

/**
 * Extracts relevant version information from the retrieved data.
 *
 * @var string $githubLatestVersion The latest version number.
 * @var string $githubLatestVersionUrl The URL to the latest version.
 * @var string $githubVersionName The name of the latest version.
 */
$githubLatestVersion = $githubVersionData['version'];
$githubLatestVersionUrl = $githubVersionData['html_url']; // URL of the latest version
$githubVersionName = $githubVersionData['name'];
Util::logDebug($githubLatestVersion, $githubLatestVersionUrl);

/**
 * Compares the current version with the latest version.
 *
 * If the latest version is greater, sets the display flag to true and constructs the download link.
 *
 * @param string $bearsamppCurrentVersion The current version of the application.
 * @param string $githubLatestVersion The latest version of the application.
 * @return void Modifies the $result array by reference.
 */
if (version_compare($bearsamppCurrentVersion, $githubLatestVersion, '<')) {
    $result['display'] = true;
    $result['download'] .= '<a role="button" class="btn btn-success fullversionurl" href="' . $githubLatestVersionUrl . '" target="_blank"><i class="fa-solid fa-cloud-arrow-down"></i> ';
    $result['download'] .= $bearsamppLang->getValue(Lang::DOWNLOAD) . ' <strong>' . APP_TITLE . ' ' . $githubVersionName . '</strong><br />';
    $result['changelog'] = '';
}

/**
 * Outputs the result array as a JSON string.
 *
 * @param array $result The result array containing the display flag and download link.
 * @return void Outputs the JSON-encoded result.
 */
echo json_encode($result);
