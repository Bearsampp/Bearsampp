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
 * @var array $procs
 * An array of valid process names that can be included.
 */
$procs = array(
    'summary',
    'latestversion',
    'apache',
    'mailpit',
    'memcached',
    'mariadb',
    'mysql',
    'nodejs',
    'php',
    'postgresql',
    'xlight',
    'quickpick'
);

/**
 * Clean and retrieve the 'proc' POST variable.
 *
 * Util::cleanPostVar is assumed to be a method that sanitizes the input to prevent security issues such as SQL injection or XSS.
 *
 * @var string $proc The cleaned 'proc' parameter from the POST request.
 */
$proc = Util::cleanPostVar('proc', 'text');  // Ensure 'proc' is cleaned and read correctly

/**
 * Check if the cleaned 'proc' parameter is in the list of valid processes.
 * If valid, include the corresponding AJAX handler file.
 * If not valid, return a JSON error message.
 */
if (in_array($proc, $procs)) {
    /**
     * Include the corresponding AJAX handler file based on the 'proc' parameter.
     * The file path is constructed dynamically.
     *
     * Example: If $proc is 'latestversion', the file 'ajax/ajax.latestversion.php' will be included.
     */
    include 'ajax/ajax.' . $proc . '.php';  // This line should correctly include 'ajax.latestversion.php'
} else {
    /**
     * Handle the case where the 'proc' parameter is not valid.
     * Return a JSON encoded error message indicating the invalid parameter.
     */
    echo json_encode(['error' => 'Invalid proc parameter']);
}
