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
 * Utility class providing a wide range of static methods for various purposes including:
 * - Input cleaning and sanitization have been moved to Sanitizer. @see Sanitizer
 * - String manipulation methods have been moved to Str. @see Str
 * - Random generation and secure tokens have been moved to Random. @see Random
 * - Filesystem operations (clearing, deleting, finding, enumerating) have been moved to Filesystem. @see Filesystem
 * - Input validation (IP, port, domain) has been moved to Validator. @see Validator
 * - The loading screen has been moved to LoadingScreen. @see LoadingScreen
 * - File-scan discovery (getFilesToScan and its helpers) has been moved to FileScanner. @see FileScanner
 * - System utilities for handling registry operations, managing environment variables, and executing system commands.
 * - Helper functions for encoding, decoding, and file operations.
 *
 * Path formatting (formatWindowsPath / formatUnixPath) has been moved to Path. @see Path
 * Logging is handled by the Log class. @see Log
 *
 * This class is designed to be used as a helper or utility class where methods are accessed statically.
 * This means you do not need to instantiate it to use the methods, but can simply call them using the Util::methodName() syntax.
 *
 * Usage Example:
 * ```
 * $cleanedData = Sanitizer::cleanGetVar('data', 'text');
 * $isAvailable = Validator::isValidIp('192.168.1.1');
 * ```
 *
 * Each method is self-contained and provides specific functionality, making this class a central point for
 * common utility operations needed across a PHP application, especially in environments like web servers or command-line interfaces.
 */
class Util
{

    /**
     * Checks if the current process is running with administrator/elevated privileges.
     * This is essential for operations that require admin rights, such as installing Windows services.
     *
     * @return bool True if running as administrator, false otherwise.
     */
    public static function isAdmin()
    {
        // Only applicable on Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            // On non-Windows systems, check if running as root
            if (function_exists('posix_geteuid')) {
                return posix_geteuid() === 0;
            }

            // If we can't determine on non-Windows, assume true to avoid blocking
            return true;
        }

        // Method 1: Try using shell_exec with 'net session' command
        // This command only succeeds when run with admin privileges
        $output = CommandRunner::shellExec('net session 2>&1');
        if ($output !== null) {
            // Check for access denied errors
            if (stripos($output, 'Access is denied') !== false ||
                stripos($output, 'System error 5') !== false ||
                stripos($output, 'Zugriff verweigert') !== false) { // German
                // Explicitly denied - not admin
                return false;
            }

            // If we got output without errors, we likely have admin rights
            if (stripos($output, 'There are no entries') !== false ||
                stripos($output, 'These workstations') !== false ||
                preg_match('/\\\\\\\\/', $output)) {
                return true;
            }
        }

        // Method 2: Check using whoami command (Windows Vista and later)
        $output = CommandRunner::shellExec('whoami /groups 2>&1');
        if ($output !== null && !empty($output)) {
            // Look for the Administrators group or High Mandatory Level
            if (stripos($output, 'S-1-16-12288') !== false || // High Mandatory Level
                stripos($output, 'S-1-5-32-544') !== false) {  // Administrators group
                return true;
            }

            // If we got output but no admin indicators, we're not admin
            if (stripos($output, 'S-1-16-8192') !== false) { // Medium Mandatory Level (not admin)
                return false;
            }
        }

        // Method 3: Try to write to a system directory
        // This is a fallback method that checks if we can write to Windows directory
        $testFile = getenv('SystemRoot') . '\\Temp\\bearsampp_admin_test_' . uniqid() . '.tmp';
        $result   = @file_put_contents($testFile, 'test');
        if ($result !== false) {
            @unlink($testFile);

            return true;
        }

        // If all methods fail or indicate no admin, return false
        return false;
    }

    /**
     * Replaces a defined constant in a file with a new value.
     *
     * @param   string  $path   The file path where the constant is defined.
     * @param   string  $var    The name of the constant.
     * @param   mixed   $value  The new value for the constant.
     *
     * @return void
     */
    public static function replaceDefine($path, $var, $value)
    {
        self::replaceInFile($path, array(
            '/^define\((.*?)' . $var . '(.*?),/' => 'define(\'' . $var . '\', ' . (is_int($value) ? $value : '\'' . $value . '\'') . ');'
        ));
    }

    /**
     * Performs replacements in a file based on a list of regular expression patterns.
     *
     * Patterns are matched against each line content with its line ending removed, so
     * end-anchored patterns (eg. '/^foo$/') match on both CRLF and LF files.
     * The original line ending of each line is preserved on write.
     *
     * @param   string  $path         The path to the file where replacements are to be made.
     * @param   array   $replaceList  An associative array where keys are regex patterns and values are replacement strings.
     *
     * @return void
     */
    public static function replaceInFile($path, $replaceList)
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path);
        if ($lines === false) {
            Log::error('replaceInFile(): Failed to read file: ' . $path);

            return;
        }

        $fp = fopen($path, 'w');
        if ($fp === false) {
            Log::error('replaceInFile(): Failed to open file for writing: ' . $path);

            return;
        }

        foreach ($lines as $nb => $line) {
            // Preserve original line ending if present in $line
            if (preg_match("/\r\n$/", $line)) {
                $ending = "\r\n";
            } elseif (preg_match("/\n$/", $line)) {
                $ending = "\n";
            } else {
                $ending = '';
            }
            $content = rtrim($line, "\r\n");

            $replaceDone = false;
            foreach ($replaceList as $regex => $replace) {
                if (preg_match($regex, $content, $matches)) {
                    $currentReplace = $replace;
                    $countParams    = preg_match_all('/{{(\d+)}}/', $currentReplace, $paramsMatches);
                    if ($countParams > 0 && $countParams <= count($matches)) {
                        foreach ($paramsMatches[1] as $paramsMatch) {
                            $currentReplace = str_replace('{{' . $paramsMatch . '}}', $matches[$paramsMatch], $currentReplace);
                        }
                    }
                    Log::trace('Replace in file ' . $path . ' :');
                    Log::trace('## line_num: ' . trim($nb));
                    Log::trace('## old: ' . trim($line));
                    Log::trace('## new: ' . trim($currentReplace));

                    fwrite($fp, rtrim($currentReplace, "\r\n") . $ending);

                    $replaceDone = true;
                    break;
                }
            }
            if (!$replaceDone) {
                fwrite($fp, $line);
            }
        }

        fclose($fp);
    }

    /**
     * Gets the current Unix timestamp with microseconds.
     *
     * @return float Returns the current Unix timestamp combined with microseconds.
     */
    public static function getMicrotime()
    {
        list($usec, $sec) = explode(' ', microtime());

        return ((float)$usec + (float)$sec);
    }


    /**
     * Checks if the application is set to launch at startup.
     *
     * @return bool True if the startup link exists, false otherwise.
     */
    public static function isLaunchStartup()
    {
        $lnk = Path::getStartupLnkPath();

        return $lnk ? file_exists($lnk) : false;
    }

    /**
     * Enables launching the application at startup by creating a shortcut in the startup folder.
     *
     * @return bool True on success, false on failure.
     */
    public static function enableLaunchStartup()
    {
        global $bearsamppRoot, $bearsamppCore;

        $shortcutPath = Path::getStartupLnkPath();
        if (!$shortcutPath) {
            return false;
        }

        $targetPath  = Path::getExeFilePath();
        $workingDir  = Path::getRootPath();
        $description = APP_TITLE . ' ' . $bearsamppCore->getAppVersion();
        $iconPath    = Path::getIconsPath() . '/app.ico';

        return Win32Native::createShortcut($shortcutPath, $targetPath, $workingDir, $description, $iconPath);
    }

    /**
     * Disables launching the application at startup by removing the shortcut from the startup folder.
     *
     * @return bool True on success, false on failure.
     */
    public static function disableLaunchStartup()
    {
        $startupLnkPath = Path::getStartupLnkPath();

        // Check if file exists before attempting to delete
        if (file_exists($startupLnkPath)) {
            return @unlink($startupLnkPath);
        }

        // Return true if the file doesn't exist (already disabled)
        return true;
    }


    /**
     * Converts an image file to a base64 encoded string.
     *
     * @param   string  $path  The path to the image file.
     *
     * @return string Returns the base64 encoded string of the image.
     */
    public static function imgToBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * Converts data between UTF-8 and Windows-1252 encodings.
     *
     * @param   string  $data       The data to convert.
     * @param   string  $direction  The conversion direction: 'to_cp1252' or 'to_utf8'. Defaults to 'to_cp1252'.
     *
     * @return string The converted data.
     */
    public static function convertEncoding($data, $direction = 'to_cp1252')
    {
        if ($direction === 'to_utf8') {
            return self::cp1252ToUtf8($data);
        } else {
            return self::utf8ToCp1252($data);
        }
    }

    /**
     * Converts UTF-8 encoded data to Windows-1252 encoding.
     *
     * @param   string  $data  The UTF-8 encoded data.
     *
     * @return string Returns the data encoded in Windows-1252.
     */
    public static function utf8ToCp1252($data)
    {
        return iconv('UTF-8', 'WINDOWS-1252//IGNORE', $data);
    }

    /**
     * Converts Windows-1252 encoded data to UTF-8 encoding.
     *
     * @param   string  $data  The Windows-1252 encoded data.
     *
     * @return string Returns the data encoded in UTF-8.
     */
    public static function cp1252ToUtf8($data)
    {
        return iconv('WINDOWS-1252', 'UTF-8//IGNORE', $data);
    }

    /**
     * Fetches the latest version information from a given url.
     *
     * @param   string  $url  The URL to fetch version information from.
     *
     * @return array|null Returns an array with 'version' and 'url' if successful, null otherwise.
     */
    public static function getLatestVersion($url)
    {
        Log::trace('[VCHK-3] getLatestVersion() START - fetching latest version from: ' . $url);

        $result = HttpClient::getApiJson($url);
        if (empty($result)) {
            Log::error('Cannot retrieve latest github info for: ' . $url);
            Log::trace('[VCHK-3] getLatestVersion() EXIT - empty response received');

            return null;
        }

        $resultArray = json_decode($result, true);
        if (!is_array($resultArray)) {
            Log::error('Cannot decode JSON response from: ' . $url);
            Log::trace('[VCHK-3] getLatestVersion() EXIT - response is not valid JSON');

            return null;
        }

        $tagName     = $resultArray['tag_name'] ?? null;
        $downloadUrl = $resultArray['assets'][0]['browser_download_url'] ?? null;
        $name        = $resultArray['name'] ?? '';
        if ($tagName !== null && $downloadUrl !== null) {
            Log::trace('Latest version tag name: ' . $tagName);
            Log::trace('Download URL: ' . $downloadUrl);
            Log::trace('Name: ' . $name);
            Log::trace('[VCHK-3] getLatestVersion() SUCCESS - version found: ' . $tagName);

            return ['version' => $tagName, 'html_url' => $downloadUrl, 'name' => $name];
        }

        Log::error('Tag name, download URL, or name not found in the response: ' . $result);
        Log::trace('[VCHK-3] getLatestVersion() EXIT - tag_name/download_url missing in JSON response');

        return null;
    }

    /**
     * Converts a file size in bytes to a human-readable format.
     *
     * Uses PHP's native human_readable_size() when available.
     * Falls back to manual conversion when a specific unit is requested
     * or when the native function is unavailable.
     *
     * @param   int     $size  The file size in bytes.
     * @param   string  $unit  Optional forced unit ('GB', 'MB', 'KB', or '').
     *
     * @return string  The formatted file size.
     */
    public static function humanFileSize(int $size, string $unit = ''): string
    {
        // Forced unit mode
        if ($unit !== '') {
            return match ($unit) {
                'GB' => number_format($size / (1024 ** 3), 2) . 'GB',
                'MB' => number_format($size / (1024 ** 2), 2) . 'MB',
                'KB' => number_format($size / (1024 ** 1), 2) . 'KB',
                default => number_format($size) . ' bytes',
            };
        }

        // Native PHP auto-selection when the function is available
        if (function_exists('human_readable_size')) {
            return human_readable_size($size, precision: 2);
        }

        // Manual fallback
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $power = $size > 0 ? (int)floor(log($size, 1024)) : 0;
        $power = min($power, count($units) - 1);

        return number_format($size / (1024 ** $power), 2) . $units[$power];
    }

    /**
     * Opens the given content in a temporary file using the editor configured in bearsampp.conf.
     *
     * @param   string  $caption  The caption/title for the temporary file.
     * @param   string  $content  The content to write to the temporary file.
     *
     * @return void
     */
    public static function openFileContent($caption, $content)
    {
        global $bearsamppWinbinder, $bearsamppConfig;

        $caption = preg_replace('/[<>:"\/\\\\|?*\x00-\x1F]|\.$/', '', trim($caption));
        if ($caption === '') {
            Log::error('openFileContent(): Caption is empty, using default caption');
            $caption = 'bearsampp';
        }
        $tmpFile = Path::getTmpPath() . '/' . $caption . '.txt';
        if (file_put_contents($tmpFile, $content) === false) {
            Log::error('openFileContent(): Failed to write temporary file: ' . $tmpFile);

            return;
        }

        // Open the file with the editor configured in bearsampp.conf
        $editor = $bearsamppConfig->getNotepad();
        if ($editor == null || trim($editor) == '') {
            $editor = 'notepad.exe';
        }

        $editor = Path::findExecutable($editor);
        if ($editor === false) {
            $editor = getenv('SystemRoot') . '\System32\notepad.exe';
        }

        $bearsamppWinbinder->exec($editor, '"' . $tmpFile . '"', false, true, true);
    }
}
