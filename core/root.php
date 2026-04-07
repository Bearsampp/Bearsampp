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
 * CRITICAL: Check for elevation IMMEDIATELY - must be FAST to minimize console window visibility
 */
if (isset($_SERVER['argv']) && isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] === 'startup') {
    $flagFile = sys_get_temp_dir() . '/bearsampp_no_admin.lock';

    // Quick exit if we already determined no admin recently
    if (file_exists($flagFile) && (time() - filemtime($flagFile)) < 10) {
        // Use PowerShell to kill processes silently (no window flashing)
        $currentPid = getmypid();
        $killCmd = 'powershell.exe -WindowStyle Hidden -Command "Stop-Process -Id ' . (int)$currentPid . ' -Force -ErrorAction SilentlyContinue; Stop-Process -Name bearsampp -Force -ErrorAction SilentlyContinue"';
        pclose(popen($killCmd, 'r'));
        exit(1);
    }

    // FAST elevation check - use ONLY net session (fastest method)
    $isElevated = false;
    $netOutput = @shell_exec('net session 2>&1');

    if ($netOutput !== null) {
        // Check for explicit denial
        if (stripos($netOutput, 'Access is denied') !== false ||
            stripos($netOutput, 'System error 5') !== false) {
            $isElevated = false;
        }
        // Check for success
        else if (stripos($netOutput, 'There are no entries') !== false ||
                 stripos($netOutput, 'Computer') !== false) {
            $isElevated = true;
        }
    }

    if (!$isElevated) {
        // Create flag file immediately
        @file_put_contents($flagFile, time());

        // Load language file for error message
        $langFile = dirname(__FILE__) . '/langs/english.lang';
        $langData = @parse_ini_file($langFile);

        // Get localized messages
        $title = isset($langData['errorAdminRequiredTitle']) ? $langData['errorAdminRequiredTitle'] : 'Administrator Rights Required';
        $messageText = isset($langData['errorAdminRequiredText']) ? $langData['errorAdminRequiredText'] : '%s requires administrator privileges to install and manage Windows services.@nl@@nl@Please right-click on bearsampp.exe and select "Run as administrator" to start the application.';

        // Replace placeholders
        $messageText = sprintf($messageText, APP_TITLE);
        $messageText = str_replace('@nl@', '|||NEWLINE|||', $messageText);

        // Split by newline placeholder
        $messageParts = explode('|||NEWLINE|||', $messageText);

        // Create PowerShell script that handles everything (no window flashing)
        $psFile = sys_get_temp_dir() . '/bearsampp_admin_error.ps1';
        $flagFilePs = str_replace('/', '\\', $flagFile);
        $psFilePs = str_replace('/', '\\', $psFile);

        // Escape for PowerShell
        $title = str_replace("'", "''", $title);
        $currentPid = getmypid();

        // PowerShell script that shows message FIRST, then kills processes
        $psContent = "# Show error message using Windows Forms\n";
        $psContent .= "Add-Type -AssemblyName System.Windows.Forms\n";

        // Build message from parts
        $psContent .= "\$messageParts = @(\n";
        $lastIndex = count($messageParts) - 1;
        foreach ($messageParts as $index => $part) {
            $part = str_replace("'", "''", trim($part));
            // Don't add comma after last item
            $comma = ($index < $lastIndex) ? ',' : '';
            $psContent .= "    '" . $part . "'" . $comma . "\n";
        }
        $psContent .= ")\n";
        $psContent .= "\$message = \$messageParts -join [Environment]::NewLine\n";
        $psContent .= "[System.Windows.Forms.MessageBox]::Show(\$message, '" . $title . "', [System.Windows.Forms.MessageBoxButtons]::OK, [System.Windows.Forms.MessageBoxIcon]::Error) | Out-Null\n";
        $psContent .= "\n";
        $psContent .= "# After user clicks OK, kill only Bearsampp-related processes\n";
        $psContent .= "Stop-Process -Id " . (int)$currentPid . " -Force -ErrorAction SilentlyContinue\n";
        $psContent .= "Stop-Process -Name bearsampp -Force -ErrorAction SilentlyContinue\n";
        $psContent .= "\n";
        $psContent .= "# Clean up\n";
        $psContent .= "Remove-Item -Path '" . $flagFilePs . "' -Force -ErrorAction SilentlyContinue\n";
        $psContent .= "Remove-Item -Path '" . $psFilePs . "' -Force -ErrorAction SilentlyContinue\n";

        @file_put_contents($psFile, $psContent);

        // Launch PowerShell to show the error message (hide console window, but message box will still show)
        pclose(popen('start "" powershell.exe -WindowStyle Hidden -ExecutionPolicy Bypass -File "' . $psFile . '"', 'r'));

        // Exit immediately - PowerShell will handle showing the message and cleanup
        exit(1);
    } else {
        // We're elevated, clean up any old flag
        @unlink($flagFile);
    }
}

/**
 * Includes the Root class file and creates an instance of Root.
 * Registers the root directory of the application.
 */
require_once dirname(__FILE__) . '/classes/class.root.php';
$bearsamppRoot = new Root(dirname(__FILE__));
$bearsamppRoot->register();

/**
 * Creates an instance of the Action class and processes the action based on command line arguments.
 */
$bearsamppAction = new Action();
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
