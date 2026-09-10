<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */
/**
 * Silent logging gate for read-only homepage polls.
 *
 * Read-only polls (e.g. summary, service status, latest version) are the main
 * source of homepage log growth because root.php re-initialises every class on
 * each 2s poll. To keep the log useful without the noise, such polls run inside
 * a silent logging transaction:
 *
 *   1. Log::startSilentBuffer() holds every entry in memory (ERROR still writes
 *      immediately so critical failures are never lost).
 *   2. The handler response body (the exact homepage state rendered) is hashed.
 *   3. At shutdown (registered BEFORE Log::init(), so it runs before Log::flush)
 *      the hash is compared with the one stored for this proc:
 *        - identical  -> nothing changed on the homepage -> rollback (discard)
 *        - different  -> a real state change occurred -> commit (write) + store
 *
 * No "heartbeat" entries are produced - identical polls write nothing at all.
 * State-changing endpoints (quickpick, toggleenhancedquickpick,
 * applymoduleconfig) are excluded and always log normally.
 */
$ajaxProcRaw = isset($_POST['proc']) ? $_POST['proc'] : '';
$ajaxReadOnlyProcs = array(
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
    'reloadstatus',
);

if (in_array($ajaxProcRaw, $ajaxReadOnlyProcs, true)) {
    require_once __DIR__ . '/../../classes/class.log.php';
    Log::startSilentBuffer();
    ob_start();

    register_shutdown_function(function () use ($ajaxProcRaw) {
        try {
            $output = ob_get_contents();
            if ($output === false) {
                $output = '';
            }
            $fingerprint = md5($output);

            if (!class_exists('Path')) {
                // Bootstrap failed before Path was available -> keep the entries.
                Log::commitSilentBuffer();
                return;
            }
            $fingerprintFile = Path::getTmpPath() . '/homepage-ajax-state-' . md5($ajaxProcRaw) . '.md5';
            $stored = @file_get_contents($fingerprintFile);

            if ($stored !== false && trim($stored) === $fingerprint) {
                // Homepage state unchanged -> discard this poll's log entries.
                Log::rollbackSilentBuffer();
            } else {
                // State changed (service started/stopped, version changed, ...) ->
                // write this poll's log entries and remember the new state.
                @file_put_contents($fingerprintFile, $fingerprint, LOCK_EX);
                Log::commitSilentBuffer();
            }
        } catch (Exception $e) {
            // On any failure, commit (write) the entries rather than lose them.
            Log::commitSilentBuffer();
        }
    });
}

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
    'quickpick' => __DIR__ . '/ajax/ajax.quickpick.php',
    'toggleenhancedquickpick' => __DIR__ . '/ajax/ajax.toggle.enhancedquickpick.php',
    'applymoduleconfig' => __DIR__ . '/ajax/ajax.apply.moduleconfig.php',
    'reloadstatus' => __DIR__ . '/ajax/ajax.reload.status.php'
];

/**
 * Clean and retrieve the 'proc' POST variable.
 *
 * UtilInput::cleanPostVar is assumed to be a method that sanitizes the input to prevent security issues such as SQL injection or XSS.
 *
 * @var string $proc The cleaned 'proc' parameter from the POST request.
 */
$proc = UtilInput::cleanPostVar('proc', 'text');  // Ensure 'proc' is cleaned and read correctly

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
 * Validate CSRF token for protected endpoints.
 * Sessions are only started for state-changing endpoints so that read-only
 * polling endpoints (e.g. reload status) never block on a session lock held
 * by a long-running operation such as a module install.
 */
if (in_array($proc, $csrfProtectedEndpoints, true)) {
    // State-changing endpoints must be reached via POST only
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json');
        header('Cache-Control: no-store');
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    Csrf::init();

    if (!Csrf::validateRequest()) {
        http_response_code(403);
        header('Content-Type: application/json');
        header('Cache-Control: no-store');
        echo json_encode([
            'error' => 'CSRF validation failed',
            'message' => 'Invalid or expired security token. Please refresh the page and try again.'
        ]);
        exit;
    }

    // Release the session lock so long-running handlers (module downloads /
    // extraction) do not block other requests sharing this session.
    session_write_close();
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
