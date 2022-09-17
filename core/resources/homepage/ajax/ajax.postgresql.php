<?php

$result = array(
    'checkport' => '',
    'versions' => '',
);

// Check port
$port = $bearsamppBins->getPostgresql()->getPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

if ($bearsamppBins->getPostgresql()->isEnable()) {
    if ($bearsamppBins->getPostgresql()->checkPort($port)) {
        $result['checkport'] .= '<span style="float:right;font-size: 1em" class="badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span style="float:right;font-size: 1em" class="badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span style="float:right;font-size: 1em" class="badge text-bg-secondary">' . $textDisabled . '</span>';
}

// Versions
foreach ($bearsamppBins->getPostgresql()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getPostgresql()->getVersion()) {
        $result['versions'] .= '<span style="float:right;font-size: 1em;margin-left: .25em;" class="badge text-bg-secondary">' . $version . '</span>';
    }
}
$result['versions'] .= '<span style="float:right;font-size: 1em;margin-left: .25em;" class="badge text-bg-primary">' . $bearsamppBins->getPostgresql()->getVersion() . '</span>';

echo json_encode($result);
