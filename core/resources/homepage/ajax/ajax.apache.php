<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Retrieves information about Apache server status, versions, modules, aliases, vhosts, directories, and URLs.
 * Returns a JSON-encoded array with the collected data.
 */

// Declare global variables
global $bearsamppRoot, $bearsamppBins, $bearsamppLang;

// Initialize result array
$result = array(
    'checkport' => '',
    'versions' => '',
    'modulescount' => '',
    'aliasescount' => '',
    'vhostscount' => '',
    'moduleslist' => '',
    'aliaseslist' => '',
    'wwwdirectory' => '',
    'vhostslist' => '',
);

/**
 * Check the status of Apache ports and update the result array.
 */
$port = $bearsamppBins->getApache()->getPort();
$sslPort = $bearsamppBins->getApache()->getSslPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

if ($bearsamppBins->getApache()->isEnable()) {
    if ($bearsamppBins->getApache()->checkPort($sslPort, true)) {
        $result['checkport'] .= '<span class="m-1 float-end badge text-bg-success">' . sprintf($textServiceStarted, $sslPort) . ' (SSL)</span>';
    } else {
        $result['checkport'] .= '<span class="m-1 float-end badge text-bg-danger">' . $textServiceStopped . ' (SSL)</span>';
    }
    if ($bearsamppBins->getApache()->checkPort($port)) {
        $result['checkport'] .= '<span class="m-1 float-end badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="m-1 float-end badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="m-1 float-end badge text-bg-secondary">' . $textDisabled . '</span>';
}

/**
 * Retrieve and format the list of Apache versions.
 */
foreach ($bearsamppBins->getApache()->getVersionList() as $version) {
    $versionBadge = '<span class="m-1 badge text-bg-%s">%s</span>';
    $isCurrent = $version === $bearsamppBins->getApache()->getVersion();

    $result['versions'] .= sprintf(
        $versionBadge,
        $isCurrent ? 'primary' : 'secondary',
        $version
    );
}

/**
 * Count and format the number of Apache modules.
 */
$modules = count($bearsamppBins->getApache()->getModules());
$modulesLoaded = count($bearsamppBins->getApache()->getModulesLoaded());
$result['modulescount'] .= '<span class="m-1 float-end badge text-bg-primary">' . $modulesLoaded . ' / ' . $modules . '</span>';

/**
 * Count and format the number of Apache aliases.
 */
$result['aliasescount'] .= '<span class="m-1 float-end badge text-bg-primary">' . count($bearsamppBins->getApache()->getAlias()) . '</span>';

/**
 * Count and format the number of Apache virtual hosts.
 */
$result['vhostscount'] .= '<span class="m-1 float-end badge text-bg-primary">' . count($bearsamppBins->getApache()->getVhosts()) . '</span>';

/**
 * Retrieve and format the list of Apache modules from the configuration.
 */
foreach ($bearsamppBins->getApache()->getModulesFromConf() as $moduleName => $moduleStatus) {
    if ($moduleStatus == ActionSwitchApacheModule::SWITCH_ON) {
        $result['moduleslist'] .= '<span class="p-1 col col-xs-12"><i class="fa-regular fa-circle-check"></i> <strong>' . $moduleName . '</strong></span>';
    } else {
        $result['moduleslist'] .= '<span class="p-1 col col-xs-12"><i class="fa-regular fa-circle"></i> ' . $moduleName . '</span>';
    }
}

/**
 * Retrieve and format the list of Apache aliases.
 */
foreach ($bearsamppBins->getApache()->getAlias() as $alias) {
    $result['aliaseslist'] .= '<div class="float-start p-1"><a class="btn btn-outline-dark" target="_blank" href="' . $bearsamppRoot->getLocalUrl($alias) . '"><i class="fa-solid fa-link"></i> ' . $alias . '</a></div>';
}

/**
 * Retrieve and format the list of Apache www directories.
 */
foreach ($bearsamppBins->getApache()->getWwwDirectories() as $wwwDirectory) {
    $result['wwwdirectory'] .= '<div class="float-start p-1"><a class="btn btn-outline-dark" target="_blank" href="' . $bearsamppRoot->getLocalUrl($wwwDirectory) . '"><i class="fa-solid fa-link"></i> ' . $wwwDirectory . '</a></div>';
}

/**
 * Retrieve and format the list of Apache virtual hosts.
 */
foreach ($bearsamppBins->getApache()->getVhostsUrl() as $vhost => $enabled) {
    if ($enabled) {
        $result['vhostslist'] .= '<div class="float-start p-1"><a class="btn btn-outline-dark" target="_blank" href="http://' . $vhost . '"><i class="fa-regular fa-circle-check"></i> ' . $vhost . '</a></div>';
    } else {
        $result['vhostslist'] .= '<div class="float-start p-1"><a class="btn btn-outline-dark" target="_blank" href="http://' . $vhost . '"><i class="fa-regular fa-circle"></i> ' . $vhost . '</a></div>';
    }
}

/**
 * Output the result array as a JSON-encoded string.
 */
echo json_encode($result);
