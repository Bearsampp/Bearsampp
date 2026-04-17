<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */
/**
 * Include the root configuration file.
 * This file is expected to set up the environment and include necessary configurations.
 */
include_once __DIR__ . '/../../root.php';

/**
 * Initialize CSRF protection
 */
Csrf::init();

/**
 * Define a mapping of valid process names to their corresponding file paths.
 * This approach is more secure than direct string concatenation.
 *
 * @var array $procMap A mapping of process names to their file paths.
 */
$procMap = [
    'summary' => __DIR__ . '/ajax/ajax.summary.php',
    'latestversion' => __DIR__ . '/ajax/ajax.latestversion.php',
    'apache' => __DIR__ . '/ajax/ajax.apache.php',
    'mailpit' => __DIR__ . '/ajax/ajax.mailpit.php',
    'memcached' => __DIR__ . '/ajax/ajax.memcached.php',
    'mariadb' => __DIR__ . '/ajax/ajax.mariadb.php',
    'mysql' => __DIR__ . '/ajax/ajax.mysql.php',
    'nodejs' => __DIR__ . '/ajax/ajax.nodejs.php',
    'php' => __DIR__ . '/ajax/ajax.php.php',
    'postgresql' => __DIR__ . '/ajax/ajax.postgresql.php',
    'xlight' => __DIR__ . '/ajax/ajax.xlight.php',
    'quickpick' => __DIR__ . '/ajax/ajax.quickpick.php',
    'toggleenhancedquickpick' => __DIR__ . '/ajax/ajax.toggle.enhancedquickpick.php',
    'applymoduleconfig' => __DIR__ . '/ajax/ajax.apply.moduleconfig.php'
];

/**
 * Define which endpoints require CSRF protection.
 * Read-only endpoints (GET-like operations) don't need CSRF protection.
 * Write operations (POST that changes state) require CSRF protection.
 */
$csrfProtectedEndpoints = [
    'quickpick',                    // Installs modules
    'toggleenhancedquickpick',      // Changes configuration
    'applymoduleconfig'             // Applies configuration changes
];

/**
 * Clean and retrieve the 'proc' POST variable.
 *
 * Util::cleanPostVar is assumed to be a method that sanitizes the input to prevent security issues such as SQL injection or XSS.
 *
 * @var string $proc The cleaned 'proc' parameter from the POST request.
 */
$proc = Util::cleanPostVar('proc', 'text');  // Ensure 'proc' is cleaned and read correctly

/**
 * Validate CSRF token for protected endpoints
 */
if (in_array($proc, $csrfProtectedEndpoints, true)) {
    if (!Csrf::validateRequest()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'CSRF validation failed',
            'message' => 'Invalid or expired security token. Please refresh the page and try again.'
        ]);
        exit;
    }
}

/**
 * Check if the cleaned 'proc' parameter exists in our secure mapping.
 * If valid, include the corresponding AJAX handler file using the pre-defined path.
 * If not valid, return a JSON error message.
 */
if (isset($procMap[$proc]) && file_exists($procMap[$proc])) {
    /**
     * Include the corresponding AJAX handler file based on the secure mapping.
     */
    include $procMap[$proc];
} else {
    /**
     * Handle the case where the 'proc' parameter is not valid.
     * Return a JSON encoded error message indicating the invalid parameter.
     * Include the requested proc value for debugging purposes.
     */
    $errorMessage = 'Invalid proc parameter';
    if (!empty($proc)) {
        $errorMessage .= ': "' . htmlspecialchars($proc) . '" is not a valid procedure';
    } else {
        $errorMessage .= ': no procedure was specified';
    }
    echo json_encode(['error' => $errorMessage]);
}
