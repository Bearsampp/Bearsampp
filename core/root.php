<?php
/*
 * Copyright (c) 2022 - 2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Defines constants used throughout the Bearsampp application.
 */
const APP_AUTHOR_NAME = 'N6REJ';
const APP_TITLE = 'Bearsampp';
const APP_WEBSITE = 'https://bearsampp.com';
const APP_LICENSE = 'GPL3 License';
const APP_GITHUB_USER = 'Bearsampp';
const APP_GITHUB_REPO = 'Bearsampp';
const APP_GITHUB_USERAGENT = 'Bearsampp';
const APP_GITHUB_LATEST_URL = 'https://api.github.com/repos/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . '/releases/latest';
const RETURN_TAB = '	';

// Membership Pro API key & URL
const QUICKPICK_API_KEY = '4abe15e5-95f2-4663-ad12-eadb245b28b4';
const QUICKPICK_API_URL = 'https://bearsampp.com/index.php?option=com_osmembership&task=api.get_active_plan_ids&api_key=';

// URL where quickpick-releases.json lives
const QUICKPICK_JSON_URL = 'https://raw.githubusercontent.com/Bearsampp/Bearsampp/main/core/resources/quickpick-releases.json';

/**
 * Includes the Root class file and creates an instance of Root.
 * Registers the root directory of the application.
 */
require_once dirname(__FILE__) . '/classes/class.root.php';
$bearsamppRoot = new Root(dirname(__FILE__));
$bearsamppRoot->register();

/**
 * Creates an instance of the Action class and processes the action based on command line arguments.
 * Added input validation to prevent command injection and other security vulnerabilities.
 */
$bearsamppAction = new Action();

// Validate command line arguments before processing
if (isset($_SERVER['argv']) && count($_SERVER['argv']) > 1) {
    // Validate the action parameter (argv[1])
    $action = $_SERVER['argv'][1];

    // Define allowed actions (whitelist approach)
    $allowedActions = [
        'about', 'addAlias', 'addVhost', 'changeBrowser', 'changeDbRootPwd',
        'changePort', 'checkPort', 'checkVersion', 'clearFolders', 'debugApache',
        'debugMariadb', 'debugMysql', 'debugPostgresql', 'editAlias', 'editVhost',
        'enable', 'exec', 'genSslCertificate', 'launchStartup', 'manualRestart',
        'loading', 'quit', 'rebuildIni', 'refreshRepos', 'refreshReposStartup',
        'reload', 'restart', 'service', 'startup', 'switchApacheModule',
        'switchLang', 'switchLogsVerbose', 'switchPhpExtension', 'switchPhpParam',
        'switchOnline', 'switchVersion', 'ext'
    ];

    // Validate action name - only allow alphanumeric characters and specific allowed actions
    if (!preg_match('/^[a-zA-Z0-9]+$/', $action) || !in_array($action, $allowedActions)) {
        Util::logError('Invalid action parameter: ' . $action);
        die('Invalid action parameter');
    }

    // Validate additional arguments (argv[2] and beyond)
    if (count($_SERVER['argv']) > 2) {
        for ($i = 2; $i < count($_SERVER['argv']); $i++) {
            $arg = $_SERVER['argv'][$i];

            // For 'ext' action, allow raw arguments (but still validate for basic safety)
            if ($action === 'ext') {
                // Basic validation for ext arguments - prevent null bytes and control characters
                if (strpos($arg, "\0") !== false || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $arg)) {
                    Util::logError('Invalid characters in ext argument: ' . $arg);
                    die('Invalid argument format');
                }
            } else {
                // For other actions, arguments should be base64 encoded
                // Validate base64 format and decode to check content
                if (!preg_match('/^[A-Za-z0-9+\/]*={0,2}$/', $arg)) {
                    Util::logError('Invalid base64 argument format: ' . $arg);
                    die('Invalid argument format');
                }

                $decoded = base64_decode($arg, true);
                if ($decoded === false) {
                    Util::logError('Failed to decode base64 argument: ' . $arg);
                    die('Invalid argument encoding');
                }

                // Validate decoded content - prevent null bytes and excessive length
                $maxArgLength = 2048; // Configurable limit for decoded arguments
                if (strpos($decoded, "\0") !== false || strlen($decoded) > $maxArgLength) {
                    Util::logError('Invalid decoded argument content or length exceeded: ' . strlen($decoded) . ' bytes');
                    die('Invalid argument content');
                }
            }
        }
    }

    Util::logDebug('Action validation passed for: ' . $action);
}

$bearsamppAction->process();

/**
 * Checks if the current user has root privileges and stops loading if true.
 */
if ($bearsamppRoot->isRoot()) {
    Util::stopLoading();
}

/**
 * Retrieves the locale setting from the global language instance.
 */
global $bearsamppLang;
$locale = $bearsamppLang->getValue('locale');
