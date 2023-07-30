<?php

$result = array(
    'checkport' => '',
    'versions' => '',
);

// Check port
$smtpPort = $bearsamppBins->getMailhog()->getSmtpPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

if ($bearsamppBins->getMailhog()->checkPort($smtpPort)) {
    if ($bearsamppBins->getMailhog()->checkPort($smtpPort)) {
        $result['checkport'] .= '<span class="float-right badge text-bg-success">' . sprintf($textServiceStarted, $smtpPort) . '</span>';
    } else {
        $result['checkport'] .= '<span class="float-right badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="float-right badge text-bg-secondary">' . $textDisabled . '</span>';
}

// Versions
foreach ($bearsamppBins->getMailhog()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getMailhog()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    }
}
$result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getMailhog()->getVersion() . '</span>';

echo json_encode($result);
