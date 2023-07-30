<?php

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
        $result['checkport'] .= '<span class="float-right m-1 badge text-bg-success">' . sprintf($textServiceStarted, $sslPort) . ' (SSL)</span>';
    } else {
        $result['checkport'] .= '<span class="float-right m-1 badge text-bg-danger">' . $textServiceStopped . ' (SSL)</span>';
    }
    if ($bearsamppBins->getFilezilla()->checkPort($port)) {
        $result['checkport'] .= '<span class="float-right m-1 badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="float-right m-1 badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="float-right m-1 badge text-bg-secondary">' . $textDisabled . '</span>';
}

// Versions
foreach ($bearsamppBins->getFilezilla()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getFilezilla()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    }
}
$result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getFilezilla()->getVersion() . '</span>';

echo json_encode($result);
