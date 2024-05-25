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
 * This code snippet generates information about the status of the MariaDB service and its versions.
 * It checks if the MariaDB service is enabled, checks the port, and retrieves the list of versions.
 * The output is encoded in JSON format and includes the port status and versions information.
 */
$result = array(
    'checkport' => '',
    'versions' => '',
);

// Check port
$port = $bearsamppBins->getMariadb()->getPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

if ($bearsamppBins->getMariadb()->isEnable()) {
    if ($bearsamppBins->getMariadb()->checkPort($port)) {
        $result['checkport'] .= '<span class="float-end badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="float-end badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="float-end badge text-bg-secondary">' . $textDisabled . '</span>';
}

// Versions
foreach ($bearsamppBins->getMariadb()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getMariadb()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getMariadb()->getVersion() . '</span>';
    }
}

echo json_encode($result);
