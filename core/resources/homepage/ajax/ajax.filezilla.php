<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * This code snippet retrieves information about the status of FileZilla service and its versions.
 * It checks if FileZilla service is enabled, checks the ports it is running on, and lists available versions.
 * The output is encoded in JSON format and includes 'checkport' and 'versions' keys with corresponding information.
 */
global $bearsamppBins, $bearsamppLang;
$result = array(
    'checkport' => '',
    'versions' => '',
);

// Check port
$port = $bearsamppBins->getFilezilla()->getPort();
$sslPort = $bearsamppBins->getFilezilla()->getSslPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

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

// Versions
foreach ($bearsamppBins->getFilezilla()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getFilezilla()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getFilezilla()->getVersion() . '</span>';
    }
}

echo json_encode($result);
