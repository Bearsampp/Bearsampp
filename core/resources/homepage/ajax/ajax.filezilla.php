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
        $result['checkport'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-success">' . sprintf($textServiceStarted, $sslPort) . ' (SSL)</span>';
    } else {
        $result['checkport'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-danger">' . $textServiceStopped . ' (SSL)</span>';
    }
    if ($bearsamppBins->getFilezilla()->checkPort($port)) {
        $result['checkport'] .= '<span style="float:right;font-size:12px" class="label label-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span style="float:right;font-size:12px" class="label label-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span style="float:right;font-size:12px" class="label label-default">' . $textDisabled . '</span>';
}

// Versions
foreach ($bearsamppBins->getFilezilla()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getFilezilla()->getVersion()) {
        $result['versions'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-default">' . $version . '</span>';
    }
}
$result['versions'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-primary">' . $bearsamppBins->getFilezilla()->getVersion() . '</span>';

echo json_encode($result);
