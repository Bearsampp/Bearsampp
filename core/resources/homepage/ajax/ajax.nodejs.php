<?php

$result = array(
    'status' => '',
    'versions' => ''
);

// Status
if ($bearsamppBins->getNodejs()->isEnable()) {
    $result['status'] = '<span style="float:right;font-size:12px" class="label label-primary">' . $bearsamppLang->getValue(Lang::ENABLED) . '</span>';
} else {
    $result['status'] = '<span style="float:right;font-size:12px" class="label label-default">' . $bearsamppLang->getValue(Lang::DISABLED) . '</span>';
}

// Versions
foreach ($bearsamppBins->getNodejs()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getNodejs()->getVersion()) {
        $result['versions'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-default">' . $version . '</span>';
    }
}
$result['versions'] .= '<span style="float:right;font-size:12px;margin-left:2px;" class="label label-primary">' . $bearsamppBins->getNodejs()->getVersion() . '</span>';

echo json_encode($result);
