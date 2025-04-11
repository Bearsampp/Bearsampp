<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

global $bearsamppBins, $bearsamppLang;

/**
 * This script checks the status of the Memcached service, including the port check and service status.
 * It also retrieves and displays the list of Memcached versions, highlighting the current version.
 * The final output is encoded in JSON format.
 *
 * @global object $bearsamppBins Provides access to various service binaries including Memcached.
 * @global object $bearsamppLang Provides access to language strings for localization.
 */

// Initialize the result array with keys 'checkport' and 'versions'.
$result = array(
    'checkport' => '',
    'versions' => '',
);

// Retrieve the Memcached port number.
$port = $bearsamppBins->getMemcached()->getPort();

// Retrieve localized text strings for service status.
$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

/**
 * Check if the Memcached service is enabled and update the 'checkport' status accordingly.
 * If the service is enabled, check if the port is open and update the status.
 * If the service is disabled, set the status to disabled.
 */
if ($bearsamppBins->getMemcached()->isEnable()) {
    if ($bearsamppBins->getMemcached()->checkPort($port)) {
        $result['checkport'] .= '<span class="float-end badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="float-end badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="float-end badge text-bg-secondary">' . $textDisabled . '</span>';
}

/**
 * Retrieve the list of Memcached versions and update the 'versions' status.
 * Highlight the current version with a primary badge and other versions with a secondary badge.
 */
foreach ($bearsamppBins->getMemcached()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getMemcached()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getMemcached()->getVersion() . '</span>';
    }
}

// Encode the result array in JSON format and output it.
echo json_encode($result);
