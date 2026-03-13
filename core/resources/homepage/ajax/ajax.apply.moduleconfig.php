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
 * This script handles AJAX requests for applying module version to bearsampp.conf.
 * It expects a POST request with 'module' and 'version' parameters.
 *
 * @package    Bearsampp
 * @subpackage Core
 * @category   AJAX
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://bearsampp.com
 */

// Set appropriate headers for AJAX response
header('Content-Type: application/json');

// Initialize response array
$response = array();

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleName = isset($_POST['moduleName']) ? $_POST['moduleName'] : null;
    $version = isset($_POST['version']) ? $_POST['version'] : null;

    if ($moduleName && $version) {
        try {
            global $bearsamppConfig;
            
            Util::logDebug("Applying config for module: $moduleName, version: $version");
            
            // Update the configuration file
            $configKey = $moduleName . 'Version';
            $bearsamppConfig->replace($configKey, $version);
            
            Util::logInfo("Successfully updated $moduleName version to $version in bearsampp.conf");
            
            $response = [
                'success' => true,
                'message' => "Configuration updated successfully!\n\n✓ Set $moduleName" . "Version = \"$version\"\n\nNow right-click the Bearsampp tray icon and select 'Reload' to activate the new version."
            ];
            
        } catch (Exception $e) {
            $response = ['error' => 'Failed to update configuration: ' . $e->getMessage()];
            error_log('Exception in apply module config: ' . $e->getMessage());
        }
    } else {
        $response = ['error' => 'Invalid module name or version.'];
    }
} else {
    $response = ['error' => 'Invalid request method.'];
}

// Send the JSON response
echo json_encode($response);
exit;
