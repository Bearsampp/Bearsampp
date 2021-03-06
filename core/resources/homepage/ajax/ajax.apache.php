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
        $result['checkport'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-success">' . sprintf($textServiceStarted, $sslPort) . ' (SSL)</span>';
    } else {
        $result['checkport'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-danger">' . $textServiceStopped . ' (SSL)</span>';
    }
    if ($bearsamppBins->getApache()->checkPort($port)) {
        $result['checkport'] .= '<span style="float:right;font-size:12px" class="label label-success">' . sprintf($textServiceStarted, $port) . '</span>';
    } else {
        $result['checkport'] .= '<span style="float:right;font-size:12px" class="label label-danger">' . $textServiceStopped . '</span>';
    }
} else {
    $result['checkport'] = '<span style="float:right;font-size:12px" class="label label-default">' . $textDisabled . '</span>';
}

// Versions
foreach ($bearsamppBins->getApache()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getApache()->getVersion()) {
        $result['versions'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-default">' . $version . '</span>';
    }
}
$result['versions'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-primary">' . $bearsamppBins->getApache()->getVersion() . '</span>';

// Modules count
$modules = count($bearsamppBins->getApache()->getModules());
$modulesLoaded = count($bearsamppBins->getApache()->getModulesLoaded());
$result['modulescount'] .= '<span style="float:right;font-size:12px" class="label label-primary">' . $modulesLoaded . ' / ' . $modules . '</span>';

// Aliases count
$result['aliasescount'] .= '<span style="float:right;font-size:12px" class="label label-primary">' . count($bearsamppBins->getApache()->getAlias()) . '</span>';

// Vhosts count
$result['vhostscount'] .= '<span style="float:right;font-size:12px" class="label label-primary">' . count($bearsamppBins->getApache()->getVhosts()) . '</span>';

// Modules list
foreach ($bearsamppBins->getApache()->getModulesFromConf() as $moduleName => $moduleStatus) {
    if ($moduleStatus == ActionSwitchApacheModule::SWITCH_ON) {
        $result['moduleslist'] .= '<div class="col-lg-2" style="padding:3px;"><i class="fa fa-check-square-o"></i> <strong>' . $moduleName . '</strong></div>';
    } else {
        $result['moduleslist'] .= '<div class="col-lg-2" style="padding:3px;"><i class="fa fa-square-o"></i> ' . $moduleName . '</div>';
    }
}

// Aliases list
foreach ($bearsamppBins->getApache()->getAlias() as $alias) {
    $result['aliaseslist'] .= '<div style="float:left;padding:3px;"><a class="btn btn-default" target="_blank" href="' . $bearsamppBs->getLocalUrl($alias) . '"><span class="fa fa-link"></span> ' . $alias . '</a></div>';
}

// Www directory
foreach ($bearsamppBins->getApache()->getWwwDirectories() as $wwwDirectory) {
    $result['wwwdirectory'] .= '<div style="float:left;padding:3px;"><a class="btn btn-default" target="_blank" href="' . $bearsamppBs->getLocalUrl($wwwDirectory) . '"><span class="fa fa-link"></span> ' . $wwwDirectory . '</a></div>';
}

// Vhosts list
foreach ($bearsamppBins->getApache()->getVhostsUrl() as $vhost => $enabled) {
    if ($enabled) {
        $result['vhostslist'] .= '<div style="float:left;padding:3px;"><a class="btn btn-default" target="_blank" href="http://' . $vhost . '"><span class="fa fa-check-square-o"></span> ' . $vhost . '</a></div>';
    } else {
        $result['vhostslist'] .= '<div style="float:left;padding:3px;"><a class="btn btn-default" target="_blank" href="http://' . $vhost . '"><span class="fa fa-square-o"></span> ' . $vhost . '</a></div>';
    }
}

echo json_encode($result);
