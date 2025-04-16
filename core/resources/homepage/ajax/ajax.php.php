<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Retrieves status, versions, extensions count, PEAR version, and extensions list information
 * and returns it as a JSON-encoded array.
 *
 * This script checks the status of PHP (enabled or disabled), lists all PHP versions available,
 * counts the total and loaded PHP extensions, retrieves the PEAR version, and compiles a list of PHP extensions
 * with their respective statuses and versions. The output is JSON-encoded, making it suitable for use in web applications
 * where such information might be displayed to the user.
 *
 * @global object $bearsamppBins Provides access to system binaries and their configurations.
 * @global object $bearsamppLang Provides language support for retrieving language-specific values.
 *
 * @return void Outputs a JSON string that can be parsed by JavaScript or other languages to display PHP configuration details.
 */
global $bearsamppBins, $bearsamppLang;

/**
 * Generates a JSON-encoded array containing the status, versions, extension count, PEAR version, and a list of PHP extensions.
 *
 * @global object $bearsamppBins Provides access to system binaries and their configurations.
 * @global object $bearsamppLang Provides language support for retrieving language-specific values.
 *
 * @return void Outputs a JSON string that can be parsed by JavaScript or other languages to display PHP configuration details.
 */
$result = array(
    'status' => '',
    'versions' => '',
    'extscount' => '',
    'pearversion' => '',
    'extslist' => '',
);

/**
 * Checks if PHP is enabled and sets the status in the result array.
 */
if ($bearsamppBins->getPhp()->isEnable()) {
    $result['status'] = '<span class="float-end badge text-bg-success">' . $bearsamppLang->getValue(Lang::ENABLED) . '</span>';
} else {
    $result['status'] = '<span class="float-end badge text-bg-danger">' . $bearsamppLang->getValue(Lang::DISABLED) . '</span>';
}

/**
 * Retrieves the list of PHP versions and sets it in the result array.
 */
foreach ($bearsamppBins->getPhp()->getVersionList() as $version) {
    if ($version != $bearsamppBins->getPhp()->getVersion()) {
        $result['versions'] .= '<span class="m-1 badge text-bg-secondary">' . $version . '</span>';
    } else {
        $result['versions'] .= '<span class="m-1 badge text-bg-primary">' . $bearsamppBins->getPhp()->getVersion() . '</span>';
    }
}

/**
 * Counts the total and loaded PHP extensions and sets the count in the result array.
 */
$exts = count($bearsamppBins->getPhp()->getExtensions());
$extsLoaded = count($bearsamppBins->getPhp()->getExtensionsLoaded());
$result['extscount'] .= '<span class="m-1 float-end badge text-bg-primary">' . $extsLoaded . ' / ' . $exts . '</span>';

/**
 * Retrieves the PEAR version and sets it in the result array.
 */
$result['pearversion'] .= '<span class="m-1 float-end badge text-bg-primary">' . $bearsamppBins->getPhp()->getPearVersion(true) . '</span>';

/**
 * Retrieves the list of PHP extensions from the configuration and sets it in the result array.
 */
foreach ($bearsamppBins->getPhp()->getExtensionsFromConf() as $extName => $extStatus) {
    if ($extStatus == ActionSwitchPhpExtension::SWITCH_ON) {
        $result['extslist'] .= '<span class="p-1 col-xs-12 col-md-2"><i class="fa-regular fa-circle-check"></i> <strong>' . $extName . ' <sup>' . phpversion(substr($extName, 4)) . '</sup></strong></span>';
    } else {
        $result['extslist'] .= '<span class="p-1 col-xs-12 col-md-2"><i class="fa-regular fa-circle"></i> ' . $extName . '</span>';
    }
}

/**
 * Outputs the result array as a JSON-encoded string.
 */
echo json_encode($result);
