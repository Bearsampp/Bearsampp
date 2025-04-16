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
 * This script generates information about the status of the MariaDB service and its versions.
 * It checks if the MariaDB service is enabled, checks the port, and retrieves the list of versions.
 * The output is encoded in JSON format and includes the port status and versions information.
 *
 * @global object $bearsamppBins  Provides access to various binaries including MariaDB.
 * @global object $bearsamppLang  Provides access to language-specific strings.
 */

// Initialize result array to store port status and versions information
$result = array(
    'checkport' => '',
    'versions' => '',
);

// Check port
$port = $bearsamppBins->getMariadb()->getPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

/**
 * Check if the MariaDB service is enabled and update the port status accordingly.
 * If the service is enabled, check if the port is open and update the status.
 * If the service is disabled, set the status to disabled.
 */
if ($bearsamppBins->getMariadb()->isEnable()) {
    if ($bearsamppBins->getMariadb()->checkPort($port)) {
        $result['checkport'] .= '<span class="float-end badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="float-end badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="float-end badge text-bg-secondary">' . $textDisabled . '</span>';
}

/**
 * Retrieve the list of available MariaDB versions and update the versions information.
 * Highlight the current version with a primary badge and other versions with a secondary badge.
 */
foreach ($bearsamppBins->getMariadb()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getMariadb()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getMariadb()->getVersion() . '</span>';
    }
}

// Output the result as a JSON-encoded string
echo json_encode($result);
