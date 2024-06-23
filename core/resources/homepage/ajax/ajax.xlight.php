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
 * This script retrieves information about the Xlight service status and versions.
 * It checks the SMTP port status and retrieves the list of available versions.
 * The results are returned as a JSON-encoded array.
 */

// Initialize result array
$result = array(
    'checkport' => '',
    'versions' => '',
);

// Check SMTP port
$port = $bearsamppBins->getXlight()->getPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

/**
 * Check if the Xlight service is running on the specified port.
 * If the port is open, indicate that the service is started.
 * If the port is closed, indicate that the service is stopped.
 * If the service is disabled, indicate that it is disabled.
 */
if ($bearsamppBins->getXlight()->checkPort($port)) {
    if ($bearsamppBins->getXlight()->checkPort($port)) {
        $result['checkport'] .= '<span class="float-end badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="float-end badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="float-end badge text-bg-secondary">' . $textDisabled . '</span>';
}

/**
 * Retrieve the list of available Xlight versions.
 * Highlight the current version with a primary badge.
 * Other versions are displayed with a secondary badge.
 */
foreach ($bearsamppBins->getXlight()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getXlight()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getXlight()->getVersion() . '</span>';
    }
}

// Output the result as a JSON-encoded array
echo json_encode($result);
