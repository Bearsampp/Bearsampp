<?php
/*
 * Copyright (c) 2022 - 2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
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
 * @global string $latestVersionUrl The URL to download the latest version.
 * @global object $bearsamppLang Language management object, used for retrieving language-specific values.
 * @global string $bearsamppCurrentVersion The current version of the application.
 * @global string $bearsamppLatestVersion The latest available version of the application.
 */
global $bearsamppLang, $bearsamppCore;

$result = array(
    'display' => false,
    'download' => '',
);

// Assuming getAppVersion() returns the current version number
$bearsamppCurrentVersion = $bearsamppCore->getAppVersion();

// Assuming getLatestVersion now returns an array with version and URL
$latestVersionData = Util::getLatestVersion(APP_GITHUB_LATEST_URL);

/* check to see if everything went sideways */
if ($latestVersionData === null) {
    Util::logError('Failed to retrieve version data from GitHub URL: ' . APP_GITHUB_LATEST_URL);

    return;
}

/* Strip array into individual relevant strings */
$bearsamppLatestVersion = $latestVersionData['version'];
$latestVersionUrl = $latestVersionData['url']; // URL of the latest version

// Directly compare version strings
if (version_compare($bearsamppCurrentVersion, $bearsamppLatestVersion, '<')) {
    $result['display'] = true;
    $result['download'] .= '<a role="button" class="btn btn-success fullversionurl" href="' . $latestVersionUrl . '" target="_blank"><i class="fa-solid fa-cloud-arrow-down"></i> ';
    $result['download'] .= $bearsamppLang->getValue(Lang::DOWNLOAD) . ' <strong>' . APP_TITLE . ' ' . $bearsamppLatestVersion . '</strong><br />';
    $result['download'] .= '<small>bearsampp-' . $bearsamppLatestVersion . '.7z</small></a>';
}
echo json_encode($result);
