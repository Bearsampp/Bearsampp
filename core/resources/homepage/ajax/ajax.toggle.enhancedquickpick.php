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
 * This script handles AJAX requests for toggling the EnhancedQuickPick setting.
 * It expects a POST request with an optional 'value' parameter (0 or 1).
 * If no value is provided, it toggles the current setting.
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
    try {
        global $bearsamppConfig;
        
        // Get current value
        $currentValue = $bearsamppConfig->getEnhancedQuickPick();
        
        // Check if a specific value was provided
        $newValue = isset($_POST['value']) ? intval($_POST['value']) : null;
        
        // If no value provided, toggle the current value
        if ($newValue === null) {
            $newValue = $currentValue == 1 ? 0 : 1;
        }
        
        // Validate the new value (must be 0 or 1)
        if ($newValue !== 0 && $newValue !== 1) {
            $response = ['error' => 'Invalid value. Must be 0 or 1.'];
        } else {
            // Update the configuration
            $bearsamppConfig->replace('EnhancedQuickPick', $newValue);
            
            Log::info('EnhancedQuickPick setting changed from ' . $currentValue . ' to ' . $newValue);
            
            $response = [
                'success' => true,
                'message' => 'EnhancedQuickPick setting updated successfully',
                'previousValue' => $currentValue,
                'newValue' => $newValue,
                'mode' => $newValue == 1 ? 'enhanced' : 'standard'
            ];
        }
        
    } catch (Exception $e) {
        $response = ['error' => 'Exception: ' . $e->getMessage()];
        error_log('Exception in toggle EnhancedQuickPick: ' . $e->getMessage());
    }
} else {
    $response = ['error' => 'Invalid request method.'];
}

// Send the JSON response
echo json_encode($response);
exit;
