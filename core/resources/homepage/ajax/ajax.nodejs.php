<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

global $bearsamppBins, $bearsamppLang;
/**
 * This code snippet creates an array with 'status' and 'versions' keys.
 * It checks if Nodejs is enabled and sets the status accordingly.
 * Then, it loops through the Nodejs version list, adding versions to the 'versions' key.
 * Finally, it encodes the result array into a JSON format and echoes it.
 */
$result = array(
    'status' => '',
    'versions' => ''
);

// Status
if ($bearsamppBins->getNodejs()->isEnable()) {
    $result['status'] = '<span class="float-end badge text-bg-success">' . $bearsamppLang->getValue(Lang::ENABLED) . '</span>';
} else {
    $result['status'] = '<span class="float-end badge text-bg-danger">' . $bearsamppLang->getValue(Lang::DISABLED) . '</span>';
}

// Versions
foreach ($bearsamppBins->getNodejs()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getNodejs()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getNodejs()->getVersion() . '</span>';
    }
}

echo json_encode($result);
