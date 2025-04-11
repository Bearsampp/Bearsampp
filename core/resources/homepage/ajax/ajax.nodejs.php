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
 * This script generates a JSON-encoded array containing the status and versions of Node.js.
 * It checks if Node.js is enabled and sets the status accordingly.
 * Then, it loops through the Node.js version list, adding versions to the 'versions' key.
 * Finally, it encodes the result array into a JSON format and echoes it.
 *
 * @global object $bearsamppBins  Provides access to various binaries including Node.js.
 * @global object $bearsamppLang  Provides access to language strings for localization.
 *
 * @return void
 */
$result = array(
    'status' => '',
    'versions' => ''
);

// Status
/**
 * Checks if Node.js is enabled and sets the status in the result array.
 * If enabled, sets the status to a success badge with the 'ENABLED' label.
 * If disabled, sets the status to a danger badge with the 'DISABLED' label.
 */
if ($bearsamppBins->getNodejs()->isEnable()) {
    $result['status'] = '<span class="float-end badge text-bg-success">' . $bearsamppLang->getValue(Lang::ENABLED) . '</span>';
} else {
    $result['status'] = '<span class="float-end badge text-bg-danger">' . $bearsamppLang->getValue(Lang::DISABLED) . '</span>';
}

// Versions
/**
 * Loops through the list of Node.js versions and adds them to the 'versions' key in the result array.
 * The current version is highlighted with a primary badge, while other versions are shown with a secondary badge.
 */
foreach ($bearsamppBins->getNodejs()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getNodejs()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getNodejs()->getVersion() . '</span>';
    }
}

// Output the result as a JSON-encoded string
echo json_encode($result);
