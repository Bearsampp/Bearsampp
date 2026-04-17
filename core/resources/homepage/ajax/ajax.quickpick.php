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
            global $bearsamppConfig;
            $QuickPick = new QuickPick();
            Log::debug('QuickPick initialized for module: ' . $module . ', version: ' . $version);
            
            // Check if enhanced mode is enabled
            $enhancedMode = $bearsamppConfig->getEnhancedQuickPick();
            Log::debug('Enhanced QuickPick mode: ' . ($enhancedMode ? 'enabled' : 'disabled'));
            
            // Install the module
            $response = $QuickPick->installModule($module, $version);
            
            if (!isset($response['error'])) {
                // Determine module type for appropriate messaging
                // Use the helper method to normalize the module name consistently
                $moduleKey = $QuickPick->normalizeModuleName($module);
                $moduleName = strtolower($moduleKey ?? $module);
                $moduleType = ($moduleKey && isset($QuickPick->modules[$moduleKey])) ? $QuickPick->modules[$moduleKey]['type'] : 'binary';
                
                // Build success message based on mode and module type
                if ($enhancedMode == 1) {
                    // Enhanced mode: config auto-updated, just need to reload
                    $successMessage = "Module $module version $version installed successfully!";
                    $successMessage .= "\n\n✓ Files extracted";
                    $successMessage .= "\n✓ Configuration updated";
                    $successMessage .= "\n\n<span class='text-warning'><i class='fas fa-exclamation-triangle'></i> IMPORTANT: Right-click the Bearsampp tray icon and select 'Reload' to activate the new version.</span>";
                } else {
                    // Standard mode: offer to update config for all module types
                    $successMessage = "Module $module version $version has been downloaded and extracted successfully!";
                    $successMessage .= "\n\nNext steps:";
                    $successMessage .= "\n1. Click 'Apply Config' below to update bearsampp.conf";
                    $successMessage .= "\n2. Right-click the Bearsampp tray icon and select 'Reload'";
                    
                    // Include module info for the apply button
                    $response['moduleType'] = $moduleType;
                    $response['moduleName'] = $moduleName;
                    $response['showApplyButton'] = true;
                }
                
                $response['message'] = $successMessage;
                $response['success'] = true;
            } else {
                error_log('Error in QuickPick installation: ' . json_encode($response));
            }
            
            Log::debug('Response: ' . json_encode($response));
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
