<?php

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

// Check port
$port = $bearsamppBins->getApache()->getPort();
$sslPort = $bearsamppBins->getApache()->getSslPort();

$textServiceStarted = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STARTED);
$textServiceStopped = $bearsamppLang->getValue(Lang::HOMEPAGE_SERVICE_STOPPED);
$textDisabled = $bearsamppLang->getValue(Lang::DISABLED);

if ($bearsamppBins->getApache()->isEnable()) {
    if ($bearsamppBins->getApache()->checkPort($sslPort, true)) {
        $result['checkport'] .= '<span class="ms-1 float-end badge text-bg-success">' . sprintf($textServiceStarted, $sslPort) . ' (SSL)</span>';
    } else {
        $result['checkport'] .= '<span class="ms-1 float-end badge text-bg-danger">' . $textServiceStopped . ' (SSL)</span>';
    }
    if ($bearsamppBins->getApache()->checkPort($port)) {
        $result['checkport'] .= '<span class="ms-1 float-end badge text-bg-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span class="ms-1 float-end badge text-bg-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span class="ms-1 float-end badge text-bg-secondary">' . $textDisabled . '</span>';
}

// Versions
foreach ($bearsamppBins->getApache()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getApache()->getVersion()) {
        $result['versions'] .= '<span class="ms-1 float-end badge text-bg-secondary">' . $version . '</span>';
    }
}
$result['versions'] .= '<span class="ms-1 float-end badge text-bg-primary">' . $bearsamppBins->getApache()->getVersion() . '</span>';

// Modules count
$modules = count($bearsamppBins->getApache()->getModules());
$modulesLoaded = count($bearsamppBins->getApache()->getModulesLoaded());
$result['modulescount'] .= '<span class="ms-1 float-end badge text-bg-primary">' . $modulesLoaded . ' / ' . $modules . '</span>';

// Aliases count
$result['aliasescount'] .= '<span class="ms-1 float-end badge text-bg-primary">' . count($bearsamppBins->getApache()->getAlias()) . '</span>';

// Vhosts count
$result['vhostscount'] .= '<span class="ms-1 float-end badge text-bg-primary">' . count($bearsamppBins->getApache()->getVhosts()) . '</span>';

// Modules list
foreach ($bearsamppBins->getApache()->getModulesFromConf() as $moduleName => $moduleStatus) {
    if ($moduleStatus == ActionSwitchApacheModule::SWITCH_ON) {
        $result['moduleslist'] .= '<span class="span-grid col-xs-12 col-md-2"><i class="fa fa-check-square-o"></i> <strong>' . $moduleName . '</strong></span>';
    } else {
        $result['moduleslist'] .= '<span class="span-grid col-xs-12 col-md-2"><i class="fa fa-square-o"></i> ' . $moduleName . '</span>';
    }
}

// Aliases list
foreach ($bearsamppBins->getApache()->getAlias() as $alias) {
    $result['aliaseslist'] .= '<div style="float:left;padding:3px;"><a class="btn btn-outline-dark" target="_blank" href="' . $bearsamppRoot->getLocalUrl($alias) . '"><span class="fa fa-link"></span> ' . $alias . '</a></div>';
}

// Www directory
foreach ($bearsamppBins->getApache()->getWwwDirectories() as $wwwDirectory) {
    $result['wwwdirectory'] .= '<div style="float:left;padding:3px;"><a class="btn btn-outline-dark" target="_blank" href="' . $bearsamppRoot->getLocalUrl($wwwDirectory) . '"><span class="fa fa-link"></span> ' . $wwwDirectory . '</a></div>';
}

// Vhosts list
foreach ($bearsamppBins->getApache()->getVhostsUrl() as $vhost => $enabled) {
    if ($enabled) {
        $result['vhostslist'] .= '<div style="float:left;padding:3px;"><a class="btn btn-outline-dark" target="_blank" href="http://' . $vhost . '"><span class="fa fa-check-square-o"></span> ' . $vhost . '</a></div>';
    } else {
        $result['vhostslist'] .= '<div style="float:left;padding:3px;"><a class="btn btn-outline-dark" target="_blank" href="http://' . $vhost . '"><span class="fa fa-square-o"></span> ' . $vhost . '</a></div>';
    }
}

echo json_encode($result);
