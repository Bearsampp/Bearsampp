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
 * Include the root configuration file.
 * This file is expected to set up the environment and include necessary configurations.
 */
include_once __DIR__ . '/../../root.php';

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
    'quickpick' => __DIR__ . '/ajax/ajax.quickpick.php'
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
     */
    echo json_encode(['error' => 'Invalid proc parameter']);
}
