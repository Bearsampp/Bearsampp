<?php
/*
 * Copyright (c) 2026 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

header('Content-Type: application/json');
header('Cache-Control: no-store');

$response = [];

try {
    if (!CacheManager::isEnabled()) {
        Log::error('Cache clear attempted while caching is disabled');
        $response = [
            'success' => false,
            'error'   => 'Caching is disabled.',
        ];
        echo json_encode($response);
        exit;
    }

    $deleted = CacheManager::clearAll();
    $stats   = Root::getCacheStats();

    $response = [
        'success'    => true,
        'deleted'    => $deleted,
        'filesCount'   => $stats['filesCount'],
        'totalSize'    => $stats['totalSize'],
        'oldestEntry'  => $stats['oldestEntry'],
        'newestEntry'  => $stats['newestEntry'],
        'largestEntry' => $stats['largestEntry'],
    ];

    Log::info('Cache cleared from homepage: ' . $deleted . ' files deleted');
} catch (Exception $e) {
    Log::error('Error clearing cache: ' . $e->getMessage());
    $response = [
        'success' => false,
        'error'   => $e->getMessage(),
    ];
}

echo json_encode($response);
exit;
