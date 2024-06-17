<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * This script retrieves information about the status of the FileZilla service and its versions.
 * It checks if the FileZilla service is enabled, verifies the ports it is running on, and lists available versions.
 * The output is encoded in JSON format and includes 'checkport' and 'versions' keys with corresponding information.
 */

// Declare global variables to access various parts of the application such as language settings and core functionalities.
global $bearsamppBins, $bearsamppLang;

// Initialize the result array with keys 'checkport' and 'versions'.
$result = array(
    'checkport' => '',
    'versions' => '',
);

// Retrieve the port and SSL port for the FileZilla service.
$port = $bearsamppBins->getFilezilla()->getPort();
$sslPort = $bearsamppBins->getFilezilla()->getSslPort();

// Retrieve localized strings for service status messages.
$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

/**
 * Check if the FileZilla service is enabled.
 * If enabled, check the status of the ports and update the 'checkport' key in the result array accordingly.
 * If not enabled, set the 'checkport' key to indicate that the service is disabled.
 */
if ($bearsamppBins->getFilezilla()->isEnable()) {
    if ($bearsamppBins->getFilezilla()->checkPort($sslPort, true)) {
        $result['checkport'] .= '<span class="float-end m-1 badge text-bg-success">' . sprintf($textServiceStarted, $sslPort) . ' (SSL)</span>';
    } else {
        $result['checkport'] .= '<span class="float-end m-1 badge text-bg-danger">' . $textServiceStopped . ' (SSL)</span>';
    }
    if ($bearsamppBins->getFilezilla()->checkPort($port)) {
        $result['checkport'] .= '<span class="float-end m-1 badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="float-end m-1 badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="float-end m-1 badge text-bg-secondary">' . $textDisabled . '</span>';
}

/**
 * Retrieve the list of available versions for the FileZilla service.
 * Update the 'versions' key in the result array with the version information.
 * Highlight the current version with a primary badge and other versions with a secondary badge.
 */
foreach ($bearsamppBins->getFilezilla()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getFilezilla()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getFilezilla()->getVersion() . '</span>';
    }
}

// Encode the result array in JSON format and output it.
echo json_encode($result);
