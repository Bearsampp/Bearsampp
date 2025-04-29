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
 * This script handles AJAX requests for installing modules in the Bearsampp application.
 * It expects a POST request with 'module', 'version', and optionally 'filesize' parameters.
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
    $module  = isset($_POST['module']) ? $_POST['module'] : null;
    $version = isset($_POST['version']) ? $_POST['version'] : null;
    $filesize = isset($_POST['filesize']) ? $_POST['filesize'] : null;

    if ($module && $version) {
        // Only load the QuickPick class when needed
        include_once __DIR__ . '/../../../classes/actions/class.action.quickPick.php';
        
        // Start output buffering to capture any unwanted output
        ob_start();
        
        try {
            $QuickPick = new QuickPick();
            Util::logDebug('QuickPick initialized for module: ' . $module . ', version: ' . $version);
            
            // Install the module
            $response = $QuickPick->installModule($module, $version);
            
            if (!isset($response['error'])) {
                // Build success message
                $successMessage = "Module $module version $version installed successfully.";
                
                // Add appropriate instructions based on module type
                if (isset($QuickPick->modules[$module]) && $QuickPick->modules[$module]['type'] === "binary") {
                    $successMessage .= "\nReload needed...\nWhen you are done installing modules then\nRight click on menu and choose reload.";
                } else {
                    $successMessage .= "\nEdit Bearsampp.conf to use new version(s) then\nWhen you are done installing modules\nRight click on menu and choose reload.";
                }
                
                $response['message'] = $successMessage;
                $response['success'] = true;
            } else {
                error_log('Error in QuickPick installation: ' . json_encode($response));
            }
            
            Util::logDebug('Response: ' . json_encode($response));
        } catch (Exception $e) {
            $response = ['error' => 'Exception: ' . $e->getMessage()];
            error_log('Exception in QuickPick: ' . $e->getMessage());
        }
        
        // End output buffering and discard any remaining output
        ob_end_clean();
    } else {
        $response = ['error' => 'Invalid module or version.'];
    }
} else {
    $response = ['error' => 'Invalid request method.'];
}

// Send the JSON response
echo json_encode($response);
exit;
