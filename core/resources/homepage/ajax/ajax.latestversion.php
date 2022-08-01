<?php

$result = array(
    'display' => false,
    'download' => '',
    'changelog' => '',
);

$bearsamppCurrentVersion = $bearsamppCore->getAppVersion();
$bearsamppLatestVersion = Util::getLatestVersion();

if ($bearsamppLatestVersion != null && version_compare($bearsamppCurrentVersion, $bearsamppLatestVersion, '<')) {
    $result['display'] = true;

    $fullVersionUrl = Util::getVersionUrl($bearsamppLatestVersion);
    $result['download'] .= '<a role="button" class="btn btn-success fullversionurl" href="' . $fullVersionUrl . '" target="_blank"><i class="fa fa-download"></i> ';
    $result['download'] .= $bearsamppLang->getValue(Lang::DOWNLOAD) . ' <strong>' . APP_TITLE . ' ' . $bearsamppLatestVersion . '</strong><br />';
    $result['download'] .= '<small>bearsampp-' . $bearsamppLatestVersion . '.7z</small></a>';

    $result['changelog'] = ''; // Function removed since we don't use Changelog.md
}

echo json_encode($result);
