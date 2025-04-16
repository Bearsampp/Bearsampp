<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

global $bearsamppBins, $bearsamppLang;

/**
 * This script checks the status of the MySQL service and retrieves its versions.
 * It creates an array with keys 'checkport' and 'versions', which are populated with HTML strings
 * indicating the status of the MySQL service and its available versions, respectively.
 * The final result is encoded in JSON format and echoed out.
 */

// Initialize result array
$result = array(
    'checkport' => '',
    'versions' => '',
);

// Check port
$port = $bearsamppBins->getMysql()->getPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

/**
 * Check if MySQL service is enabled and its port status.
 * If enabled, check if the port is open and set the appropriate status message.
 * If disabled, set the status message to indicate that the service is disabled.
 */
if ($bearsamppBins->getMysql()->isEnable()) {
    if ($bearsamppBins->getMysql()->checkPort($port)) {
        $result['checkport'] .= '<span class="float-end badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="float-end badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="float-end badge text-bg-secondary">' . $textDisabled . '</span>';
}

/**
 * Retrieve the list of MySQL versions and highlight the current version.
 * Add each version to the 'versions' key in the result array, using different badge styles
 * to indicate the current version and other versions.
 */
foreach ($bearsamppBins->getMysql()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getMysql()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getMysql()->getVersion() . '</span>';
    }
}

// Output the result as a JSON-encoded string
echo json_encode($result);
