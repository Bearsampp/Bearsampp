<?php

$result = array(
    'checkport' => '',
    'versions' => '',
);

// Check port
$port = $bearsamppBins->getMemcached()->getPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

if ($bearsamppBins->getMemcached()->isEnable()) {
    if ($bearsamppBins->getMemcached()->checkPort($port)) {
        $result['checkport'] .= '<span style="float:right;font-size:12px" class="badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span style="float:right;font-size:12px" class="badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span style="float:right;font-size:12px" class="badge text-bg-secondary">' . $textDisabled . '</span>';
}

// Versions
foreach ($bearsamppBins->getMemcached()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getMemcached()->getVersion()) {
        $result['versions'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="badge text-bg-secondary">' . $version . '</span>';
    }
}
$result['versions'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="badge text-bg-primary">' . $bearsamppBins->getMemcached()->getVersion() . '</span>';

echo json_encode($result);
