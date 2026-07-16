<?php
/*
 * Copyright (c) 2025 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * This script handles AJAX requests for checking the reload action status.
 * It checks if the reload process has completed by looking for the reload-status.json file.
 *
 * @package    Bearsampp
 * @subpackage Core
 * @category   AJAX
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://bearsampp.com
 */

header('Content-Type: application/json');

$response = array();

try {
    $reloadStatusFile = Path::getLogsPath() . '/reload-status.json';

    if (file_exists($reloadStatusFile)) {
        $statusContent = file_get_contents($reloadStatusFile);
        $status = json_decode($statusContent, true);

        if ($status !== null) {
            $response = array(
                'completed' => true,
                'status' => $status
            );

            Log::debug('Reload status retrieved: ' . json_encode($status));
        } elseif (json_last_error() !== JSON_ERROR_NONE) {
            // Invalid JSON detected (likely from concurrent partial write)
            Log::warning('Reload status file has invalid JSON: ' . json_last_error_msg());
            $response = array(
                'completed' => false,
                'message' => 'Status file being updated'
            );
        } else {
            $response = array(
                'completed' => false,
                'message' => 'Reload in progress'
            );
        }
    } else {
        $response = array(
            'completed' => false,
            'message' => 'Reload in progress'
        );
    }
} catch (Exception $e) {
    Log::error('Error checking reload status: ' . $e->getMessage());
    $response = array(
        'error' => 'Failed to check reload status: ' . $e->getMessage(),
        'completed' => false
    );
}

echo json_encode($response);
exit;
