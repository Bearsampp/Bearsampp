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
        // Create VBScript to kill processes silently and exit immediately
        $killScript = sys_get_temp_dir() . '/bearsampp_kill.vbs';
        $vbsKill = 'CreateObject("WScript.Shell").Run "taskkill /F /IM bearsampp.exe /IM php.exe", 0, False';
        @file_put_contents($killScript, $vbsKill);
        pclose(popen('start /B wscript //nologo "' . $killScript . '"', 'r'));
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

        // Create comprehensive VBScript that handles everything
        $vbsFile = sys_get_temp_dir() . '/bearsampp_admin_error.vbs';
        $line1 = APP_TITLE . ' requires administrator privileges to install and manage Windows services.';
        $line2 = 'Please right-click on bearsampp.exe and select "Run as administrator" to start the application.';

        // Escape double quotes
        $line1 = str_replace('"', '""', $line1);
        $line2 = str_replace('"', '""', $line2);

        // VBScript that kills processes IMMEDIATELY then shows message
        $vbsContent = 'Option Explicit' . "\r\n";
        $vbsContent .= 'Dim objShell' . "\r\n";
        $vbsContent .= 'Set objShell = CreateObject("WScript.Shell")' . "\r\n";
        $vbsContent .= "\r\n";
        $vbsContent .= '\'Kill ALL processes immediately and WAIT for completion' . "\r\n";
        $vbsContent .= 'objShell.Run "taskkill /F /IM php.exe", 0, True' . "\r\n";
        $vbsContent .= 'objShell.Run "taskkill /F /IM bearsampp.exe", 0, True' . "\r\n";
        $vbsContent .= "\r\n";
        $vbsContent .= '\'Brief pause to ensure processes are killed' . "\r\n";
        $vbsContent .= 'WScript.Sleep 500' . "\r\n";
        $vbsContent .= "\r\n";
        $vbsContent .= '\'Show error message' . "\r\n";
        $vbsContent .= 'MsgBox "' . $line1 . '" & vbCrLf & vbCrLf & "' . $line2 . '", vbCritical, "Administrator Rights Required"' . "\r\n";
        $vbsContent .= "\r\n";
        $vbsContent .= '\'Clean up' . "\r\n";
        $vbsContent .= 'On Error Resume Next' . "\r\n";
        $vbsContent .= 'CreateObject("Scripting.FileSystemObject").DeleteFile "' . str_replace('/', '\\', $flagFile) . '"' . "\r\n";
        $vbsContent .= 'CreateObject("Scripting.FileSystemObject").DeleteFile WScript.ScriptFullName' . "\r\n";

        @file_put_contents($vbsFile, $vbsContent);

        // Launch VBScript and exit PHP immediately
        // The VBScript will kill this PHP process within milliseconds
        pclose(popen('start /B wscript //nologo "' . $vbsFile . '"', 'r'));

        // Give VBScript a moment to start, then exit
        usleep(100000); // 100ms
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
