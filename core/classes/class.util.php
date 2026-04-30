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
 * - Input cleaning and sanitization have been moved to UtilInput. @see UtilInput
 * - String manipulation methods have been moved to UtilString. @see UtilString
 * - File and directory management functions for deleting, clearing, or finding files and directories.
 * - System utilities for handling registry operations, managing environment variables, and executing system commands.
 * - Network utilities to validate IPs, domains, and manage HTTP requests.
 * - Helper functions for encoding, decoding, and file operations.
 *
 * Path formatting (formatWindowsPath / formatUnixPath) has been moved to UtilPath. @see UtilPath
 * Logging is handled by the Log class. @see Log
 *
 * This class is designed to be used as a helper or utility class where methods are accessed statically.
 * This means you do not need to instantiate it to use the methods, but can simply call them using the Util::methodName() syntax.
 *
 * Usage Example:
 * ```
 * $cleanedData = UtilInput::cleanGetVar('data', 'text');
 * $isAvailable = Util::isValidIp('192.168.1.1');
 * ```
 *
 * Each method is self-contained and provides specific functionality, making this class a central point for
 * common utility operations needed across a PHP application, especially in environments like web servers or command-line interfaces.
 */
class Util
{
    /**
     * Cache for file scan results
     * @var array|null
     */
    private static $fileScanCache = null;

    /**
     * Cache validity duration in seconds (default: 1 hour)
     * @var int
     */
    private static $fileScanCacheDuration = 3600;

    /**
     * Statistics for monitoring file scan cache effectiveness
     * @var array
     */
    private static $fileScanStats = [
        'hits' => 0,
        'misses' => 0,
        'invalidations' => 0
    ];

    /**
     * Secret key for cache file integrity verification
     * Generated once per session to prevent cache tampering
     * @var string|null
     */
    private static $cacheIntegrityKey = null;

    /**
     * Recursively deletes files from a specified directory while excluding certain files.
     *
     * @param   string  $path     The path to the directory to clear.
     * @param   array   $exclude  An array of filenames to exclude from deletion.
     *
     * @return array Returns an array with the status of the operation and the number of files deleted.
     */
    public static function clearFolders($paths, $exclude = array())
    {
        $result = array();
        foreach ($paths as $path) {
            $result[$path] = self::clearFolder($path, $exclude);
        }

        return $result;
    }

    /**
     * Recursively clears all files and directories within a specified directory, excluding specified items.
     *
     * @param   string  $path     The path of the directory to clear.
     * @param   array   $exclude  An array of filenames to exclude from deletion.
     *
     * @return array|null Returns an array with the operation status and count of files deleted, or null if the directory cannot be opened.
     */
    public static function clearFolder($path, $exclude = array())
    {
        $result             = array();
        $result['return']   = true;
        $result['nb_files'] = 0;

        $handle = @opendir($path);
        if (!$handle) {
            return null;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..' || in_array($file, $exclude)) {
                continue;
            }
            if (is_dir($path . '/' . $file)) {
                $r = self::clearFolder($path . '/' . $file);
                if (!$r) {
                    $result['return'] = false;

                    return $result;
                }
            } else {
                $r = @unlink($path . '/' . $file);
                if ($r) {
                    $result['nb_files']++;
                } else {
                    $result['return'] = false;

                    return $result;
                }
            }
        }

        closedir($handle);

        return $result;
    }

    /**
     * Recursively deletes a directory and all its contents.
     *
     * @param   string  $path  The path of the directory to delete.
     */
    public static function deleteFolder($path)
    {
        if (is_dir($path)) {
            if (substr($path, strlen($path) - 1, 1) != '/') {
                $path .= '/';
            }
            $files = glob($path . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::deleteFolder($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($path);
        }
    }

    /**
     * Recursively searches for a file starting from a specified directory.
     *
     * @param   string  $startPath  The directory path to start the search.
     * @param   string  $findFile   The filename to search for.
     *
     * @return string|false Returns the path to the file if found, or false if not found.
     */
    private static function findFile($startPath, $findFile)
    {
        $result = false;

        $handle = @opendir($startPath);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($startPath . '/' . $file)) {
                $result = self::findFile($startPath . '/' . $file, $findFile);
                if ($result !== false) {
                    break;
                }
            } elseif ($file == $findFile) {
                $result = UtilPath::formatUnixPath($startPath . '/' . $file);
                break;
            }
        }

        closedir($handle);

        return $result;
    }

    /**
     * Validates an IP address.
     *
     * @param   string  $ip  The IP address to validate.
     *
     * @return bool Returns true if the IP address is valid, otherwise false.
     */
    public static function isValidIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Validates a port number.
     *
     * @param   int  $port  The port number to validate.
     *
     * @return bool Returns true if the port number is valid and within the range of 1 to 65535, otherwise false.
     */
    public static function isValidPort($port)
    {
        return is_numeric($port) && ($port > 0 && $port <= 65535);
    }

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
        $result = @file_put_contents($testFile, 'test');
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
     * @param   string  $path         The path to the file where replacements are to be made.
     * @param   array   $replaceList  An associative array where keys are regex patterns and values are replacement strings.
     */
    public static function replaceInFile($path, $replaceList)
    {
        if (file_exists($path)) {
            $lines = file($path);
            $fp    = fopen($path, 'w');
            foreach ($lines as $nb => $line) {
                $replaceDone = false;
                foreach ($replaceList as $regex => $replace) {
                    if (preg_match($regex, $line, $matches)) {
                        $countParams = preg_match_all('/{{(\d+)}}/', $replace, $paramsMatches);
                        if ($countParams > 0 && $countParams <= count($matches)) {
                            foreach ($paramsMatches[1] as $paramsMatch) {
                                $replace = str_replace('{{' . $paramsMatch . '}}', $matches[$paramsMatch], $replace);
                            }
                        }
                        Log::trace('Replace in file ' . $path . ' :');
                        Log::trace('## line_num: ' . trim($nb));
                        Log::trace('## old: ' . trim($line));
                        Log::trace('## new: ' . trim($replace));
                        fwrite($fp, $replace . PHP_EOL);

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
    }

    /**
     * Gets the list of version directories in the specified path.
     * Returns version suffixes by stripping the common prefix (basename of path) if present.
     *
     * @param   string  $path  The directory path to scan for version directories.
     *
     * @return array|false Returns a sorted array of version suffixes, or false if the directory cannot be opened.
     */
    public static function getVersionList($path)
    {
        $result = array();

        $handle = @opendir($path);
        if (!$handle) {
            return false;
        }

        $prefix = basename($path);

        while (false !== ($file = readdir($handle))) {
            $filePath = $path . '/' . $file;
            if ($file != '.' && $file != '..' && is_dir($filePath) && $file != 'current') {
                if (strpos($file, $prefix) === 0) {
                    $version = substr($file, strlen($prefix));
                } else {
                    $version = $file;
                }
                $result[] = $version;
            }
        }

        closedir($handle);
        natcasesort($result);

        return $result;
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

    public static function getAppBinsRegKey($fromRegistry = true)
    {
        global $bearsamppRegistry;

        if ($fromRegistry) {
            $value = $bearsamppRegistry->getValue(
                Registry::HKEY_LOCAL_MACHINE,
                Registry::ENV_KEY,
                Registry::APP_BINS_REG_ENTRY
            );
            Log::debug('App reg key from registry: ' . $value);
        } else {
            global $bearsamppBins, $bearsamppTools;
            $value = '';
            if ($bearsamppBins->getApache()->isEnable()) {
                $value .= $bearsamppBins->getApache()->getSymlinkPath() . '/bin;';
            }
            if ($bearsamppBins->getPhp()->isEnable()) {
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . ';';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/pear;';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/deps;';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/imagick;';
            }
            if ($bearsamppBins->getNodejs()->isEnable()) {
                $value .= $bearsamppBins->getNodejs()->getSymlinkPath() . ';';
            }
            if ($bearsamppTools->getComposer()->isEnable()) {
                $value .= $bearsamppTools->getComposer()->getSymlinkPath() . ';';
                $value .= $bearsamppTools->getComposer()->getSymlinkPath() . '/vendor/bin;';
            }
            if ($bearsamppTools->getGhostscript()->isEnable()) {
                $value .= $bearsamppTools->getGhostscript()->getSymlinkPath() . '/bin;';
            }
            if ($bearsamppTools->getGit()->isEnable()) {
                $value .= $bearsamppTools->getGit()->getSymlinkPath() . '/bin;';
            }
            if ($bearsamppTools->getNgrok()->isEnable()) {
                $value .= $bearsamppTools->getNgrok()->getSymlinkPath() . ';';
            }
            if ($bearsamppTools->getPerl()->isEnable()) {
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/perl/site/bin;';
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/perl/bin;';
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/c/bin;';
            }
            if ($bearsamppTools->getPython()->isEnable()) {
                $value .= $bearsamppTools->getPython()->getSymlinkPath() . '/bin;';
            }
            if ($bearsamppTools->getRuby()->isEnable()) {
                $value .= $bearsamppTools->getRuby()->getSymlinkPath() . '/bin;';
            }
            $value = UtilPath::formatWindowsPath($value);
            Log::debug('Generated app bins reg key: ' . $value);
        }

        return $value;
    }

    /**
     * Retrieves or generates the application binaries registry key.
     *
     * @param   bool  $fromRegistry  Determines whether to retrieve the key from the registry or generate it.
     *
     * @return string Returns the application binaries registry key.
     */
    public static function setAppBinsRegKey($value)
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->setStringValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::APP_BINS_REG_ENTRY,
            $value
        );
    }

    /**
     * Retrieves the application path from the registry.
     *
     * @return mixed The value of the application path registry key or false on error.
     */
    public static function getAppPathRegKey()
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::APP_PATH_REG_ENTRY
        );
    }

    /**
     * Sets the application path in the registry.
     *
     * @param   string  $value  The new value for the application path.
     *
     * @return bool True on success, false on failure.
     */
    public static function setAppPathRegKey($value)
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->setStringValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::APP_PATH_REG_ENTRY,
            $value
        );
    }

    /**
     * Retrieves the system path from the registry.
     *
     * @return mixed The value of the system path registry key or false on error.
     */
    public static function getSysPathRegKey()
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::SYSPATH_REG_ENTRY
        );
    }

    /**
     * Sets the system path in the registry.
     *
     * @param   string  $value  The new value for the system path.
     *
     * @return bool True on success, false on failure.
     */
    public static function setSysPathRegKey($value)
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->setExpandStringValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::SYSPATH_REG_ENTRY,
            $value
        );
    }

    /**
     * Retrieves the processor identifier from the registry.
     *
     * @return mixed The value of the processor identifier registry key or false on error.
     */
    public static function getProcessorRegKey()
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::PROCESSOR_REG_SUBKEY,
            Registry::PROCESSOR_REG_ENTRY
        );
    }

    /**
     * Retrieves the path for the startup link file.
     *
     * @return string The full path to the startup link file.
     */
    public static function getStartupLnkPath()
    {
        $startupPath = Win32Native::getSpecialFolderPath('Startup');
        return $startupPath ? $startupPath . '/' . APP_TITLE . '.lnk' : false;
    }

    /**
     * Checks if the application is set to launch at startup.
     *
     * @return bool True if the startup link exists, false otherwise.
     */
    public static function isLaunchStartup()
    {
        $lnk = self::getStartupLnkPath();
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

        $shortcutPath = self::getStartupLnkPath();
        if (!$shortcutPath) {
            return false;
        }

        $targetPath = $bearsamppRoot->getExeFilePath();
        $workingDir = $bearsamppRoot->getRootPath();
        $description = APP_TITLE . ' ' . $bearsamppCore->getAppVersion();
        $iconPath = $bearsamppCore->getIconsPath() . '/app.ico';

        return Win32Native::createShortcut($shortcutPath, $targetPath, $workingDir, $description, $iconPath);
    }

    /**
     * Disables launching the application at startup by removing the shortcut from the startup folder.
     *
     * @return bool True on success, false on failure.
     */
    public static function disableLaunchStartup()
    {
        $startupLnkPath = self::getStartupLnkPath();

        // Check if file exists before attempting to delete
        if (file_exists($startupLnkPath)) {
            return @unlink($startupLnkPath);
        }

        // Return true if the file doesn't exist (already disabled)
        return true;
    }

    /**
     * Finds the path to the PowerShell executable in the Windows System32 directory.
     *
     * @return string|false Returns the path to powershell.exe if found, otherwise false.
     */
    public static function getPowerShellPath()
    {
        if (is_dir('C:\Windows\System32\WindowsPowerShell')) {
            return self::findFile('C:\Windows\System32\WindowsPowerShell', 'powershell.exe');
        }

        return false;
    }

    /**
     * Recursively searches for repositories starting from a given path up to a specified depth.
     *
     * @param   string  $initPath   The initial path from where the search begins.
     * @param   string  $startPath  The current path from where to search.
     * @param   string  $checkFile  The file name to check for in the directory to consider it a repository.
     * @param   int     $maxDepth   The maximum depth of directories to search into.
     *
     * @return array Returns an array of paths that contain the specified file.
     */
    public static function findRepos($initPath, $startPath, $checkFile, $maxDepth = 1)
    {
        $depth  = substr_count(str_replace($initPath, '', $startPath), '/');
        $result = array();

        $handle = @opendir($startPath);
        if (!$handle) {
            return $result;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($startPath . '/' . $file) && ($initPath == $startPath || $depth <= $maxDepth)) {
                $tmpResults = self::findRepos($initPath, $startPath . '/' . $file, $checkFile, $maxDepth);
                foreach ($tmpResults as $tmpResult) {
                    $result[] = $tmpResult;
                }
            } elseif (is_file($startPath . '/' . $checkFile) && !in_array($startPath, $result)) {
                $result[] = UtilPath::formatUnixPath($startPath);
            }
        }

        closedir($handle);

        return $result;
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
     * @param   string  $data      The data to convert.
     * @param   string  $direction The conversion direction: 'to_cp1252' or 'to_utf8'. Defaults to 'to_cp1252'.
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
     * Initiates a loading process using external components.
     */
    public static function startLoading()
    {
        global $bearsamppCore, $bearsamppWinbinder;

        Log::trace('startLoading() called');
        Log::trace('PHP executable: ' . $bearsamppCore->getPhpExe());
        Log::trace('Root file: ' . Core::isRoot_FILE);
        Log::trace('Action: ' . Action::LOADING);

        $command = Core::isRoot_FILE . ' ' . Action::LOADING;
        Log::trace('Executing command: ' . $bearsamppCore->getPhpExe() . ' ' . $command);

        $result = $bearsamppWinbinder->exec($bearsamppCore->getPhpExe(), $command);
        Log::trace('exec() returned: ' . var_export($result, true));

        Log::trace('startLoading() completed');
    }

    /**
     * Stops a previously started loading process and cleans up related resources.
     */
    public static function stopLoading()
    {
        global $bearsamppCore;
        if (file_exists($bearsamppCore->getLoadingPid())) {
            $pids = file($bearsamppCore->getLoadingPid());
            foreach ($pids as $pid) {
                Win32Ps::kill($pid);
            }
            @unlink($bearsamppCore->getLoadingPid());
        }

        // Clean up status file
        self::clearLoadingText();
    }

    /**
     * Updates the loading screen text (if loading screen is active)
     * This allows dynamic updates to show which service is being processed
     *
     * @param string $text The text to display on the loading screen
     */
    public static function updateLoadingText($text)
    {
        global $bearsamppCore;

        $statusFile = $bearsamppCore->getTmpPath() . '/loading_status.txt';
        file_put_contents($statusFile, json_encode(['text' => $text]));
    }

    /**
     * Clears the loading status file
     */
    public static function clearLoadingText()
    {
        global $bearsamppCore;

        $statusFile = $bearsamppCore->getTmpPath() . '/loading_status.txt';
        if (file_exists($statusFile)) {
            @unlink($statusFile);
        }
    }

    /**
     * Retrieves a list of files to scan from specified paths or default paths.
     * Implements caching to avoid repeated expensive file system scans.
     *
     * @param   string|null  $path          Optional. The path to start scanning from. If null, uses default paths.
     * @param   bool         $useCache      Whether to use cached results (default: true).
     * @param   bool         $forceRefresh  Force refresh the cache even if valid (default: false).
     *
     * @return array Returns an array of files found during the scan.
     */
    public static function getFilesToScan($path = null, $useCache = true, $forceRefresh = false)
    {
        // Generate cache key based on path parameter
        $cacheKey = md5(serialize($path));

        // Try to get from cache if enabled and not forcing refresh
        if ($useCache && !$forceRefresh) {
            $cachedResult = self::getFileScanCache($cacheKey);
            if ($cachedResult !== false) {
                self::$fileScanStats['hits']++;
                Log::debug('File scan cache HIT (saved expensive scan operation)');
                return $cachedResult;
            }
        }

        self::$fileScanStats['misses']++;
        Log::debug('File scan cache MISS (performing full scan)');

        // Perform the actual scan
        $startTime = self::getMicrotime();
        $result      = array();
        $pathsToScan = !empty($path) ? $path : self::getPathsToScan();

        foreach ($pathsToScan as $pathToScan) {
            $pathStartTime = self::getMicrotime();
            $findFiles = self::findFiles($pathToScan['path'], $pathToScan['includes'], $pathToScan['recursive']);
            foreach ($findFiles as $findFile) {
                $result[] = $findFile;
            }
            Log::debug($pathToScan['path'] . ' scanned in ' . round(self::getMicrotime() - $pathStartTime, 3) . 's');
        }

        $totalTime = round(self::getMicrotime() - $startTime, 3);
        Log::info('Full file scan completed in ' . $totalTime . 's (' . count($result) . ' files found)');

        // Store in cache if enabled
        if ($useCache) {
            self::setFileScanCache($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Gets cached file scan results if valid.
     * Includes integrity verification to prevent cache tampering.
     *
     * @param   string  $cacheKey  The cache key to retrieve.
     *
     * @return array|false Returns cached results or false if cache is invalid/missing.
     */
    private static function getFileScanCache($cacheKey)
    {
        global $bearsamppRoot;

        // Check if we have in-memory cache first
        if (self::$fileScanCache !== null && isset(self::$fileScanCache[$cacheKey])) {
            $cache = self::$fileScanCache[$cacheKey];

            // Check if cache is still valid
            if (time() - $cache['timestamp'] < self::$fileScanCacheDuration) {
                return $cache['data'];
            } else {
                self::$fileScanStats['invalidations']++;
                unset(self::$fileScanCache[$cacheKey]);
            }
        }

        // Try to load from file cache
        if (!isset($bearsamppRoot)) {
            return false;
        }

        $cacheFile = $bearsamppRoot->getTmpPath() . '/filescan_cache_' . $cacheKey . '.dat';

        if (file_exists($cacheFile)) {
            $fileContents = @file_get_contents($cacheFile);

            if ($fileContents === false) {
                return false;
            }

            // Verify file integrity before unserializing
            if (!self::verifyCacheIntegrity($fileContents, $cacheKey)) {
                Log::warning('File scan cache integrity check failed for key: ' . $cacheKey . '. Possible tampering detected.');
                @unlink($cacheFile);
                return false;
            }

            $cacheData = @unserialize($fileContents);

            if ($cacheData !== false && isset($cacheData['timestamp']) && isset($cacheData['data']) && isset($cacheData['hmac'])) {
                // Check if file cache is still valid
                if (time() - $cacheData['timestamp'] < self::$fileScanCacheDuration) {
                    // Store in memory cache for faster subsequent access
                    if (self::$fileScanCache === null) {
                        self::$fileScanCache = [];
                    }
                    self::$fileScanCache[$cacheKey] = $cacheData;

                    return $cacheData['data'];
                } else {
                    // Cache expired, delete file
                    self::$fileScanStats['invalidations']++;
                    @unlink($cacheFile);
                }
            } else {
                // Invalid cache structure, delete file
                Log::warning('Invalid cache structure detected for key: ' . $cacheKey);
                @unlink($cacheFile);
            }
        }

        return false;
    }

    /**
     * Stores file scan results in cache with integrity protection.
     *
     * @param   string  $cacheKey  The cache key to store under.
     * @param   array   $data      The scan results to cache.
     *
     * @return void
     */
    private static function setFileScanCache($cacheKey, $data)
    {
        global $bearsamppRoot;

        // Generate HMAC for integrity verification
        $hmac = self::generateCacheHMAC($data, $cacheKey);

        $cacheData = [
            'timestamp' => time(),
            'data' => $data,
            'hmac' => $hmac
        ];

        // Store in memory cache
        if (self::$fileScanCache === null) {
            self::$fileScanCache = [];
        }
        self::$fileScanCache[$cacheKey] = $cacheData;

        // Store in file cache
        if (isset($bearsamppRoot)) {
            $cacheFile = $bearsamppRoot->getTmpPath() . '/filescan_cache_' . $cacheKey . '.dat';
            @file_put_contents($cacheFile, serialize($cacheData), LOCK_EX);
            Log::debug('File scan results cached to: ' . $cacheFile);
        }
    }

    /**
     * Generates or retrieves the cache integrity key.
     * This key is unique per session to prevent cross-session cache tampering.
     *
     * @return string The cache integrity key
     */
    private static function getCacheIntegrityKey()
    {
        if (self::$cacheIntegrityKey === null) {
            global $bearsamppRoot;

            // Try to load existing key from session file
            if (isset($bearsamppRoot)) {
                $keyFile = $bearsamppRoot->getTmpPath() . '/cache_integrity.key';

                if (file_exists($keyFile)) {
                    $key = @file_get_contents($keyFile);
                    if ($key !== false && strlen($key) === 64) {
                        self::$cacheIntegrityKey = $key;
                        return self::$cacheIntegrityKey;
                    }
                }

                // Generate new key if none exists or invalid
                try {
                    self::$cacheIntegrityKey = bin2hex(random_bytes(32));
                    @file_put_contents($keyFile, self::$cacheIntegrityKey, LOCK_EX);
                } catch (Exception $e) {
                    Log::error('Failed to generate cache integrity key: ' . $e->getMessage());
                    // Fallback to a less secure but functional key
                    self::$cacheIntegrityKey = hash('sha256', uniqid('bearsampp_cache_', true));
                }
            } else {
                // Fallback if bearsamppRoot not available
                try {
                    self::$cacheIntegrityKey = bin2hex(random_bytes(32));
                } catch (Exception $e) {
                    self::$cacheIntegrityKey = hash('sha256', uniqid('bearsampp_cache_', true));
                }
            }
        }

        return self::$cacheIntegrityKey;
    }

    /**
     * Generates HMAC for cache data integrity verification.
     *
     * @param   array   $data      The data to generate HMAC for
     * @param   string  $cacheKey  The cache key
     *
     * @return string The HMAC hash
     */
    private static function generateCacheHMAC($data, $cacheKey)
    {
        $key = self::getCacheIntegrityKey();
        $message = serialize($data) . $cacheKey;
        return hash_hmac('sha256', $message, $key);
    }

    /**
     * Verifies cache file integrity using HMAC.
     *
     * @param   string  $fileContents  The serialized cache file contents
     * @param   string  $cacheKey      The cache key
     *
     * @return bool True if integrity check passes, false otherwise
     */
    private static function verifyCacheIntegrity($fileContents, $cacheKey)
    {
        $cacheData = @unserialize($fileContents);

        if ($cacheData === false || !isset($cacheData['hmac']) || !isset($cacheData['data'])) {
            return false;
        }

        $expectedHmac = self::generateCacheHMAC($cacheData['data'], $cacheKey);

        // Use hash_equals to prevent timing attacks
        return hash_equals($expectedHmac, $cacheData['hmac']);
    }

    /**
     * Clears all file scan caches.
     *
     * @return void
     */
    public static function clearFileScanCache()
    {
        global $bearsamppRoot;

        // Clear memory cache
        self::$fileScanCache = null;

        // Clear file caches
        if (isset($bearsamppRoot)) {
            $tmpPath = $bearsamppRoot->getTmpPath();
            $cacheFiles = glob($tmpPath . '/filescan_cache_*.dat');

            if ($cacheFiles !== false) {
                foreach ($cacheFiles as $cacheFile) {
                    @unlink($cacheFile);
                }
                Log::info('Cleared ' . count($cacheFiles) . ' file scan cache files');
            }
        }

        // Reset stats
        self::$fileScanStats = [
            'hits' => 0,
            'misses' => 0,
            'invalidations' => 0
        ];
    }

    /**
     * Gets file scan cache statistics.
     *
     * @return array Array containing hits, misses, and invalidations counts
     */
    public static function getFileScanStats()
    {
        return self::$fileScanStats;
    }

    /**
     * Sets the file scan cache duration.
     *
     * @param   int  $seconds  Cache duration in seconds (default: 3600 = 1 hour).
     *
     * @return void
     */
    public static function setFileScanCacheDuration($seconds)
    {
        if ($seconds > 0 && $seconds <= 86400) { // Max 24 hours
            self::$fileScanCacheDuration = $seconds;
            Log::debug('File scan cache duration set to ' . $seconds . ' seconds');
        }
    }

    /**
     * Gets the current file scan cache duration.
     *
     * @return int Cache duration in seconds
     */
    public static function getFileScanCacheDuration()
    {
        return self::$fileScanCacheDuration;
    }

    /**
     * Retrieves a list of directories and file types to scan within the BEARSAMPP environment.
     *
     * This method compiles an array of paths from various components of the BEARSAMPP stack,
     * including Apache, PHP, MySQL, MariaDB, PostgreSQL, Node.js, Composer, PowerShell,
     * Python and Ruby. Each path entry includes the directory path, file types to include
     * in the scan, and whether the scan should be recursive.
     *
     * The method uses global variables to access the root paths of each component. It then
     * dynamically fetches specific subdirectories using the `getFolderList` method (which is
     * assumed to be defined elsewhere in this class or in the global scope) and constructs
     * an array of path specifications.
     *
     * Each path specification is an associative array with the following keys:
     * - 'path': The full directory path to scan.
     * - 'includes': An array of file extensions or filenames to include in the scan.
     * - 'recursive': A boolean indicating whether the scan should include subdirectories.
     *
     * The method is designed to be used for setting up scans of configuration files and other
     * important files within the BEARSAMPP environment, possibly for purposes like configuration
     * management, backup, or security auditing.
     *
     * @return array An array of associative arrays, each containing 'path', 'includes', and 'recursive' keys.
     */
    private static function getPathsToScan()
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppBins, $bearsamppApps, $bearsamppTools;
        $paths = array();

        // Alias
        $paths[] = array(
            'path'      => $bearsamppRoot->getAliasPath(),
            'includes'  => array(''),
            'recursive' => false
        );

        // Vhosts
        $paths[] = array(
            'path'      => $bearsamppRoot->getVhostsPath(),
            'includes'  => array(''),
            'recursive' => false
        );

        // OpenSSL
        $paths[] = array(
            'path'      => $bearsamppCore->getOpenSslPath(),
            'includes'  => array('openssl.cfg'),
            'recursive' => false
        );

        // Homepage
        $paths[] = array(
            'path'      => $bearsamppCore->getResourcesPath() . '/homepage',
            'includes'  => array('alias.conf'),
            'recursive' => false
        );

        // Apache
        $folderList = self::getFolderList($bearsamppBins->getApache()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppBins->getApache()->getRootPath() . '/' . $folder,
                'includes'  => array('.ini', '.conf'),
                'recursive' => true
            );
        }

        // PHP
        $folderList = self::getFolderList($bearsamppBins->getPhp()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppBins->getPhp()->getRootPath() . '/' . $folder,
                'includes'  => array('.php', '.bat', '.ini', '.reg', '.inc'),
                'recursive' => true
            );
        }

        // MySQL
        $folderList = self::getFolderList($bearsamppBins->getMysql()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppBins->getMysql()->getRootPath() . '/' . $folder,
                'includes'  => array('my.ini'),
                'recursive' => false
            );
        }

        // MariaDB
        $folderList = self::getFolderList($bearsamppBins->getMariadb()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppBins->getMariadb()->getRootPath() . '/' . $folder,
                'includes'  => array('my.ini'),
                'recursive' => false
            );
            // Also scan data directory for my.ini (created during initialization)
            $dataPath = $bearsamppBins->getMariadb()->getRootPath() . '/' . $folder . '/data';
            if (is_dir($dataPath)) {
                $paths[] = array(
                    'path'      => $dataPath,
                    'includes'  => array('my.ini'),
                    'recursive' => false
                );
            }
        }

        // PostgreSQL
        $folderList = self::getFolderList($bearsamppBins->getPostgresql()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppBins->getPostgresql()->getRootPath() . '/' . $folder,
                'includes'  => array( '.conf', '.bat', '.ber'),
                'recursive' => true
            );
        }

        // Node.js
        $folderList = self::getFolderList($bearsamppBins->getNodejs()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppBins->getNodejs()->getRootPath() . '/' . $folder . '/etc',
                'includes'  => array('npmrc'),
                'recursive' => true
            );
            $paths[] = array(
                'path'      => $bearsamppBins->getNodejs()->getRootPath() . '/' . $folder . '/node_modules/npm',
                'includes'  => array('npmrc'),
                'recursive' => false
            );
        }

        // Composer
        $folderList = self::getFolderList($bearsamppTools->getComposer()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppTools->getComposer()->getRootPath() . '/' . $folder,
                'includes'  => array('giscus.json'),
                'recursive' => false
            );
        }

        // PowerShell
        $folderList = self::getFolderList($bearsamppTools->getPowerShell()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppTools->getPowerShell()->getRootPath() . '/' . $folder,
                'includes'  => array('console.xml', '.ini', '.btm'),
                'recursive' => true
            );
        }

        // Python
        $folderList = self::getFolderList($bearsamppTools->getPython()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppTools->getPython()->getRootPath() . '/' . $folder . '/bin',
                'includes'  => array('.bat'),
                'recursive' => false
            );
            $paths[] = array(
                'path'      => $bearsamppTools->getPython()->getRootPath() . '/' . $folder . '/settings',
                'includes'  => array('winpython.ini'),
                'recursive' => false
            );
        }

        // Ruby
        $folderList = self::getFolderList($bearsamppTools->getRuby()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => $bearsamppTools->getRuby()->getRootPath() . '/' . $folder . '/bin',
                'includes'  => array('!.dll', '!.exe'),
                'recursive' => false
            );
        }

        return $paths;
    }

    /**
     * Recursively finds files in a directory that match a set of inclusion patterns.
     *
     * @param   string  $startPath  The directory path to start the search from.
     * @param   array   $includes   An array of file patterns to include in the search. Patterns starting with '!' are excluded.
     * @param   bool    $recursive  Determines whether the search should be recursive.
     *
     * @return array An array of files that match the inclusion patterns.
     */
    private static function findFiles($startPath, $includes = array(''), $recursive = true)
    {
        $result = array();

        $handle = @opendir($startPath);
        if (!$handle) {
            return $result;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($startPath . '/' . $file) && $recursive) {
                $tmpResults = self::findFiles($startPath . '/' . $file, $includes);
                foreach ($tmpResults as $tmpResult) {
                    $result[] = $tmpResult;
                }
            } elseif (is_file($startPath . '/' . $file)) {
                foreach ($includes as $include) {
                    if (UtilString::startWith($include, '!')) {
                        $include = ltrim($include, '!');
                        if (UtilString::startWith($file, '.') && !UtilString::endWith($file, $include)) {
                            $result[] = UtilPath::formatUnixPath($startPath . '/' . $file);
                        } elseif ($file != $include) {
                            $result[] = UtilPath::formatUnixPath($startPath . '/' . $file);
                        }
                    } elseif (UtilString::endWith($file, $include) || $file == $include || empty($include)) {
                        $result[] = UtilPath::formatUnixPath($startPath . '/' . $file);
                    }
                }
            }
        }

        closedir($handle);

        return $result;
    }

    /**
     * Replaces old path references with new path references in the specified files.
     *
     * @param   array        $filesToScan  Array of file paths to scan and modify.
     * @param   string|null  $rootPath     The new root path to replace the old one. If null, uses a default root path.
     *
     * @return array Returns an array with the count of occurrences changed and the count of files changed.
     */
    public static function changePath($filesToScan, $rootPath = null)
    {
        global $bearsamppRoot, $bearsamppCore;

        $result = array(
            'countChangedOcc'   => 0,
            'countChangedFiles' => 0
        );

        $rootPath           = $rootPath != null ? $rootPath : $bearsamppRoot->getRootPath();
        $unixOldPath        = UtilPath::formatUnixPath($bearsamppCore->getLastPathContent());
        $windowsOldPath     = UtilPath::formatWindowsPath($bearsamppCore->getLastPathContent());
        $unixCurrentPath    = UtilPath::formatUnixPath($rootPath);
        $windowsCurrentPath = UtilPath::formatWindowsPath($rootPath);

        foreach ($filesToScan as $fileToScan) {
            $tmpCountChangedOcc = 0;
            $fileContentOr      = file_get_contents($fileToScan);
            $fileContent        = $fileContentOr;

            // old path
            preg_match('#' . $unixOldPath . '#i', $fileContent, $unixMatches);
            if (!empty($unixMatches)) {
                $fileContent        = str_replace($unixOldPath, $unixCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }
            preg_match('#' . str_replace('\\', '\\\\', $windowsOldPath) . '#i', $fileContent, $windowsMatches);
            if (!empty($windowsMatches)) {
                $fileContent        = str_replace($windowsOldPath, $windowsCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }

            // placeholders
            preg_match('#' . Core::PATH_LIN_PLACEHOLDER . '#i', $fileContent, $unixMatches);
            if (!empty($unixMatches)) {
                $fileContent        = str_replace(Core::PATH_LIN_PLACEHOLDER, $unixCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }
            preg_match('#' . Core::PATH_WIN_PLACEHOLDER . '#i', $fileContent, $windowsMatches);
            if (!empty($windowsMatches)) {
                $fileContent        = str_replace(Core::PATH_WIN_PLACEHOLDER, $windowsCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }

            if ($fileContentOr != $fileContent) {
                $result['countChangedOcc']   += $tmpCountChangedOcc;
                $result['countChangedFiles'] += 1;
                file_put_contents($fileToScan, $fileContent);
            }
        }

        Log::debug('changePath() completed: ' . $result['countChangedFiles'] . ' files changed, ' . $result['countChangedOcc'] . ' total occurrences');

        return $result;
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
        $result = self::getApiJson($url);
        if (empty($result)) {
            Log::error('Cannot retrieve latest github info for: ' . $result . ' RESULT');

            return null;
        }

        $resultArray = json_decode($result, true);
        if (isset($resultArray['tag_name']) && isset($resultArray['assets'][0]['browser_download_url'])) {
            $tagName     = $resultArray['tag_name'];
            $downloadUrl = $resultArray['assets'][0]['browser_download_url'];
            $name        = $resultArray['name'];
            Log::debug('Latest version tag name: ' . $tagName);
            Log::debug('Download URL: ' . $downloadUrl);
            Log::debug('Name: ' . $name);

            return ['version' => $tagName, 'html_url' => $downloadUrl, 'name' => $name];
        } else {
            Log::error('Tag name, download URL, or name not found in the response: ' . $result);

            return null;
        }
    }

    /**
     * Constructs a complete website URL with optional path, fragment, and UTM source parameters.
     *
     * @param   string  $path       Optional path to append to the base URL.
     * @param   string  $fragment   Optional fragment to append to the URL.
     * @param   bool    $utmSource  Whether to include UTM source parameters. Defaults to true.
     *
     * @return string The constructed URL.
     */
    public static function getWebsiteUrl($path = '', $fragment = '', $utmSource = true)
    {
        global $bearsamppCore;

        $url = APP_WEBSITE;
        if (!empty($path)) {
            $url .= '/' . ltrim($path, '/');
        }
        if ($utmSource) {
            $url = rtrim($url, '/') . '/?utm_source=bearsampp-' . $bearsamppCore->getAppVersion();
        }
        if (!empty($fragment)) {
            $url .= $fragment;
        }

        return $url;
    }

    /**
     * Constructs a website URL without UTM parameters.
     *
     * @param   string  $path      Optional path to append to the base URL.
     * @param   string  $fragment  Optional fragment to append to the URL.
     *
     * @return string The constructed URL without UTM parameters.
     */
    public static function getWebsiteUrlNoUtm($path = '', $fragment = '')
    {
        return self::getWebsiteUrl($path, $fragment, false);
    }

    /**
     * Constructs the URL to the changelog page, optionally including UTM parameters.
     *
     * @param   bool  $utmSource  Whether to include UTM source parameters.
     *
     * @return string The URL to the changelog page.
     */
    public static function getChangelogUrl($utmSource = true)
    {
        return self::getWebsiteUrl('doc/changelog', null, $utmSource);
    }

    /**
     * Retrieves the file size of a remote file.
     *
     * @param   string  $url            The URL of the remote file.
     * @param   bool    $humanFileSize  Whether to return the size in a human-readable format.
     *
     * @return mixed The file size, either in bytes or as a formatted string.
     */
    public static function getRemoteFilesize($url, $humanFileSize = true)
    {
        $size = 0;

        $data = get_headers($url, true);
        if (isset($data['Content-Length'])) {
            $size = intval($data['Content-Length']);
        }

        return $humanFileSize ? self::humanFileSize($size) : $size;
    }

    /**
     * Converts a file size in bytes to a human-readable format.
     *
     * @param   int     $size  The file size in bytes.
     * @param   string  $unit  The unit to convert to ('GB', 'MB', 'KB', or ''). If empty, auto-selects the unit.
     *
     * @return string The formatted file size.
     */
    public static function humanFileSize($size, $unit = '')
    {
        if ((!$unit && $size >= 1 << 30) || $unit == 'GB') {
            return number_format($size / (1 << 30), 2) . 'GB';
        }
        if ((!$unit && $size >= 1 << 20) || $unit == 'MB') {
            return number_format($size / (1 << 20), 2) . 'MB';
        }
        if ((!$unit && $size >= 1 << 10) || $unit == 'KB') {
            return number_format($size / (1 << 10), 2) . 'KB';
        }

        return number_format($size) . ' bytes';
    }

    /**
     * Checks if the operating system is 32-bit.
     *
     * @return bool True if the OS is 32-bit, false otherwise.
     */
    public static function is32BitsOs()
    {
        $processor = self::getProcessorRegKey();

        return UtilString::contains($processor, 'x86');
    }

    /**
     * Retrieves HTTP headers from a given URL using either cURL or fopen, depending on availability.
     *
     * @param   string  $pingUrl  The URL to ping for headers.
     *
     * @return array An array of HTTP headers.
     */
    public static function getHttpHeaders($pingUrl)
    {
        if (function_exists('curl_version')) {
            $result = self::getCurlHttpHeaders($pingUrl);
        } else {
            $result = self::getFopenHttpHeaders($pingUrl);
        }

        if (!empty($result)) {
            $rebuildResult = array();
            foreach ($result as $row) {
                $row = trim($row);
                if (!empty($row)) {
                    $rebuildResult[] = $row;
                }
            }
            $result = $rebuildResult;

            Log::debug('getHttpHeaders:');
            foreach ($result as $header) {
                Log::debug('-> ' . $header);
            }
        }

        return $result;
    }

    /**
     * Retrieves HTTP headers from a given URL using the fopen function.
     *
     * This method creates a stream context to disable SSL peer and peer name verification,
     * which allows self-signed certificates. It attempts to open the URL and read the HTTP
     * response headers.
     *
     * @param   string  $url  The URL from which to fetch the headers.
     *
     * @return array An array of headers if successful, otherwise an empty array.
     */
    public static function getFopenHttpHeaders($url)
    {
        $result = array();

        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            )
        ));

        $fp = @fopen($url, 'r', false, $context);
        if ($fp) {
            $meta   = stream_get_meta_data($fp);
            $result = isset($meta['wrapper_data']) ? $meta['wrapper_data'] : $result;
            fclose($fp);
        }

        return $result;
    }

    /**
     * Retrieves HTTP headers from a given URL using cURL.
     *
     * This method initializes a cURL session, sets various options to fetch headers
     * including disabling SSL peer verification, and executes the request. It logs
     * the raw response for debugging purposes and parses the headers from the response.
     *
     * @param   string  $url  The URL from which to fetch the headers.
     *
     * @return array An array of headers if successful, otherwise an empty array.
     */
    public static function getCurlHttpHeaders($url)
    {
        $result = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = @curl_exec($ch);
        if (empty($response)) {
            return $result;
        }

        Log::trace('getCurlHttpHeaders:' . $response);
        $responseHeaders = explode("\r\n\r\n", $response, 2);
        if (!isset($responseHeaders[0]) || empty($responseHeaders[0])) {
            return $result;
        }

        return explode("\n", $responseHeaders[0]);
    }

    /**
     * Retrieves the initial response line from a specified host and port using a socket connection.
     *
     * This method optionally uses SSL and creates a stream context similar to `getFopenHttpHeaders`.
     * It attempts to connect to the host and port, reads the first line of the response, and parses it.
     * Detailed debug information is logged for each header line received.
     *
     * @param   string  $host  The host name or IP address to connect to.
     * @param   int     $port  The port number to connect to.
     * @param   bool    $ssl   Whether to use SSL (defaults to false).
     *
     * @return array An array containing the first line of the response, split into parts, or an empty array if unsuccessful.
     */
    public static function getHeaders($host, $port, $ssl = false)
    {
        $result  = array();
        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            )
        ));

        $fp = @stream_socket_client(($ssl ? 'ssl://' : '') . $host . ':' . $port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
        if ($fp) {
            $out    = fgets($fp);
            $result = explode(PHP_EOL, $out);
            @fclose($fp);
        }

        if (!empty($result)) {
            $rebuildResult = array();
            foreach ($result as $row) {
                $row = trim($row);
                if (!empty($row)) {
                    $rebuildResult[] = $row;
                }
            }
            $result = $rebuildResult;

            Log::debug('getHeaders:');
            foreach ($result as $header) {
                Log::debug('-> ' . $header);
            }
        }

        return $result;
    }

    /**
     * Sends a GET request to the specified URL and returns the response.
     *
     * @param   string  $url  The URL to send the GET request to.
     *
     * @return string The trimmed response data from the URL.
     */
    public static function getApiJson($url)
    {
        $header = self::setupCurlHeaderWithToken();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('CURL Error: ' . curl_error($ch));
        }

        // curl_close() is deprecated in PHP 8.5+ as it has no effect since PHP 8.0
        // The resource is automatically closed when it goes out of scope
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }

        return trim($data);
    }

    /**
     * Checks if a specific port is in use.
     *
     * @param   int  $port  The port number to check
     *
     * @return mixed False if the port is not in use, otherwise returns the process using the port
     */
    public static function isPortInUse($port)
    {
        // Set localIP statically
        $localIP = '127.0.0.1';

        // Save current error reporting level
        $errorReporting = error_reporting();

        // Disable error reporting temporarily
        error_reporting(0);

        $connection = @fsockopen($localIP, $port);

        // Restore original error reporting level
        error_reporting($errorReporting);

        if (is_resource($connection)) {
            fclose($connection);
            $process = Batch::getProcessUsingPort($port);

            return $process != null ? $process : 'N/A';
        }

        return false;
    }

    /**
     * Validates a domain name based on specific criteria.
     *
     * @param   string  $domainName  The domain name to validate.
     *
     * @return bool Returns true if the domain name is valid, false otherwise.
     */
    public static function isValidDomainName($domainName)
    {
        return preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $domainName)
            && preg_match('/^.{1,253}$/', $domainName)
            && preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $domainName);
    }

    /**
     * Attempts to install and start a service on a specific port, with optional syntax checking and user notifications.
     *
     * @param   object  $bin             An object containing the binary information and methods related to the service.
     * @param   int     $port            The port number on which the service should run.
     * @param   string  $syntaxCheckCmd  The command to execute for syntax checking of the service configuration.
     * @param   bool    $showWindow      Optional. Whether to show message boxes for information, warnings, and errors. Defaults to false.
     *
     * @return bool Returns true if the service is successfully installed and started, false otherwise.
     */
    public static function installService($bin, $port, $syntaxCheckCmd, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        if (method_exists($bin, 'initData')) {
            $bin->initData();
        }

        $name     = $bin->getName();
        $service  = $bin->getService();
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::INSTALL_SERVICE_TITLE), $name);

        $isPortInUse = self::isPortInUse($port);
        if ($isPortInUse === false) {
            if (!$service->isInstalled()) {
                $service->create();
                if ($service->start()) {
                    Log::info(sprintf('%s service successfully installed. (name: %s ; port: %s)', $name, $service->getName(), $port));
                    if ($showWindow) {
                        $bearsamppWinbinder->messageBoxInfo(
                            sprintf($bearsamppLang->getValue(Lang::SERVICE_INSTALLED), $name, $service->getName(), $port),
                            $boxTitle
                        );
                    }

                    return true;
                } else {
                    $serviceError    = sprintf($bearsamppLang->getValue(Lang::SERVICE_INSTALL_ERROR), $name);
                    $serviceErrorLog = sprintf('Error during the installation of %s service', $name);
                    if (!empty($syntaxCheckCmd)) {
                        $cmdSyntaxCheck = $bin->getCmdLineOutput($syntaxCheckCmd);
                        if (!$cmdSyntaxCheck['syntaxOk']) {
                            $serviceError    .= PHP_EOL . sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_SYNTAX_ERROR), $cmdSyntaxCheck['content']);
                            $serviceErrorLog .= sprintf(' (conf errors detected : %s)', $cmdSyntaxCheck['content']);
                        }
                    }
                    Log::error($serviceErrorLog);
                    if ($showWindow) {
                        $bearsamppWinbinder->messageBoxError($serviceError, $boxTitle);
                    }
                }
            } else {
                Log::warning(sprintf('%s service already installed', $name));
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxWarning(
                        sprintf($bearsamppLang->getValue(Lang::SERVICE_ALREADY_INSTALLED), $name),
                        $boxTitle
                    );
                }

                return true;
            }
        } elseif ($service->isRunning()) {
            Log::warning(sprintf('%s service already installed and running', $name));
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf($bearsamppLang->getValue(Lang::SERVICE_ALREADY_INSTALLED), $name),
                    $boxTitle
                );
            }

            return true;
        } else {
            Log::error(sprintf('Port %s is used by an other application : %s', $port, $isPortInUse));
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED_BY), $port, $isPortInUse),
                    $boxTitle
                );
            }
        }

        return false;
    }

    /**
     * Removes a service if it is installed.
     *
     * @param   Win32Service  $service  The service object to be removed.
     * @param   string        $name     The name of the service.
     *
     * @return bool Returns true if the service is successfully removed, false otherwise.
     */
    public static function removeService($service, $name)
    {
        if (!($service instanceof Win32Service)) {
            Log::error('$service not an instance of Win32Service');

            return false;
        }

        if ($service->isInstalled()) {
            if ($service->delete()) {
                Log::info(sprintf('%s service successfully removed', $name));

                return true;
            } else {
                Log::error(sprintf('Error during the uninstallation of %s service', $name));

                return false;
            }
        } else {
            Log::warning(sprintf('%s service does not exist', $name));
        }

        return true;
    }

    /**
     * Attempts to start a service and performs a syntax check if required.
     *
     * @param   object  $bin             An object containing service details.
     * @param   string  $syntaxCheckCmd  Command to check syntax errors.
     * @param   bool    $showWindow      Whether to show error messages in a window.
     *
     * @return bool Returns true if the service starts successfully, false otherwise.
     */
    public static function startService($bin, $syntaxCheckCmd, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        if (method_exists($bin, 'initData')) {
            $bin->initData();
        }

        $name     = $bin->getName();
        $service  = $bin->getService();
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::START_SERVICE_TITLE), $name);

        if (!$service->start()) {
            $serviceError    = sprintf($bearsamppLang->getValue(Lang::START_SERVICE_ERROR), $name);
            $serviceErrorLog = sprintf('Error while starting the %s service', $name);
            if (!empty($syntaxCheckCmd)) {
                $cmdSyntaxCheck = $bin->getCmdLineOutput($syntaxCheckCmd);
                if (!$cmdSyntaxCheck['syntaxOk']) {
                    $serviceError    .= PHP_EOL . sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_SYNTAX_ERROR), $cmdSyntaxCheck['content']);
                    $serviceErrorLog .= sprintf(' (conf errors detected : %s)', $cmdSyntaxCheck['content']);
                }
            }
            Log::error($serviceErrorLog);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError($serviceError, $boxTitle);
            }

            return false;
        }

        return true;
    }

    /**
     * Generates various GitHub URLs based on the specified type.
     *
     * @param string $type The type of URL ('user', 'repo', 'raw'). Defaults to 'user'.
     * @param string $user The GitHub username. Defaults to 'Bearsampp'.
     * @param string|null $repo The repository name (required for 'repo' and 'raw' types).
     * @param string|null $branch The branch name (required for 'raw' type).
     * @param string|null $path The file path (required for 'raw' type).
     * @return string|false The generated URL or false on invalid input.
     */
    public static function getGithubUrl($type = 'user', $user = 'Bearsampp', $repo = null, $branch = null, $path = null) {
        if (empty($user) || !is_string($user)) {
            return false;
        }

        // Encode as URL path segment (not query encoding)
        $user = rawurlencode($user);

        switch ($type) {
            case 'user':
                return "https://github.com/{$user}";

            case 'repo':
                if (empty($repo) || !is_string($repo)) {
                    return false;
                }
                $repo = rawurlencode($repo);
                return "https://github.com/{$user}/{$repo}";

            case 'raw':
                if (empty($repo) || empty($branch) || empty($path) || !is_string($repo) || !is_string($branch) || !is_string($path)) {
                    return false;
                }
                $repo = rawurlencode($repo);
                $branch = rawurlencode($branch);

                $path = ltrim($path, '/');
                $segments = array_map('rawurlencode', explode('/', $path));
                $pathEncoded = implode('/', $segments);

                return "https://raw.githubusercontent.com/{$user}/{$repo}/{$branch}/{$pathEncoded}";

            default:
                return false;
        }
    }

    /**
     * Gets the GitHub user URL for Bearsampp.
     *
     * @return string The GitHub user URL.
     */
    public static function getGithubUserUrl()
    {
        return self::getGithubUrl('user', 'Bearsampp');
    }

    /**
     * Checks the current state of the internet connection.
     *
     * This method attempts to reach a well-known website (e.g., www.google.com) to determine the state of the internet connection.
     * It returns `true` if the connection is successful, otherwise it returns `false`.
     *
     * @return bool True if the internet connection is active, false otherwise.
     */
    public static function checkInternetState()
    {
        $connected = @fsockopen('www.google.com', 80);
        if ($connected) {
            fclose($connected);

            return true; // Internet connection is active
        } else {
            return false; // Internet connection is not active
        }
    }

    /**
     * Gets the list of folders in the specified path.
     *
     * @param   string  $path  The directory path to scan for folders.
     *
     * @return array|false Returns a sorted array of folder names, or false if the directory cannot be opened.
     */
    public static function getFolderList($path)
    {
        $result = array();

        $handle = @opendir($path);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            $filePath = $path . '/' . $file;
            if ($file != '.' && $file != '..' && is_dir($filePath) && $file != 'current') {
                $result[] = $file;
            }
        }

        closedir($handle);
        natcasesort($result);

        return $result;
    }

    /**
     * Gets the NSSM environment paths.
     *
     * @return string The NSSM environment paths string.
     */
    public static function getNssmEnvPaths()
    {
        global $bearsamppBins, $bearsamppTools;

        $paths = '';

        // Add paths for enabled bins
        if ($bearsamppBins->getApache()->isEnable()) {
            $paths .= $bearsamppBins->getApache()->getSymlinkPath() . '/bin;';
        }
        if ($bearsamppBins->getPhp()->isEnable()) {
            $paths .= $bearsamppBins->getPhp()->getSymlinkPath() . ';';
            $paths .= $bearsamppBins->getPhp()->getSymlinkPath() . '/pear;';
            $paths .= $bearsamppBins->getPhp()->getSymlinkPath() . '/deps;';
            $paths .= $bearsamppBins->getPhp()->getSymlinkPath() . '/imagick;';
        }
        if ($bearsamppBins->getNodejs()->isEnable()) {
            $paths .= $bearsamppBins->getNodejs()->getSymlinkPath() . ';';
        }
        if ($bearsamppTools->getComposer()->isEnable()) {
            $paths .= $bearsamppTools->getComposer()->getSymlinkPath() . ';';
            $paths .= $bearsamppTools->getComposer()->getSymlinkPath() . '/vendor/bin;';
        }
        if ($bearsamppTools->getGhostscript()->isEnable()) {
            $paths .= $bearsamppTools->getGhostscript()->getSymlinkPath() . '/bin;';
        }
        if ($bearsamppTools->getGit()->isEnable()) {
            $paths .= $bearsamppTools->getGit()->getSymlinkPath() . '/bin;';
        }
        if ($bearsamppTools->getNgrok()->isEnable()) {
            $paths .= $bearsamppTools->getNgrok()->getSymlinkPath() . ';';
        }
        if ($bearsamppTools->getPerl()->isEnable()) {
            $paths .= $bearsamppTools->getPerl()->getSymlinkPath() . '/perl/site/bin;';
            $paths .= $bearsamppTools->getPerl()->getSymlinkPath() . '/perl/bin;';
            $paths .= $bearsamppTools->getPerl()->getSymlinkPath() . '/c/bin;';
        }
        if ($bearsamppTools->getPython()->isEnable()) {
            $paths .= $bearsamppTools->getPython()->getSymlinkPath() . '/bin;';
        }
        if ($bearsamppTools->getRuby()->isEnable()) {
            $paths .= $bearsamppTools->getRuby()->getSymlinkPath() . '/bin;';
        }

        return UtilPath::formatWindowsPath($paths);
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
        global $bearsamppCore, $bearsamppConfig;

        $tmpFile = $bearsamppCore->getTmpPath() . '/' . $caption . '.txt';
        file_put_contents($tmpFile, $content);

        // Open the file with the configured editor from bearsampp.conf
        $editor = $bearsamppConfig->getNotepad();
        $bearsamppCore->getWinbinder()->exec($editor, '"' . $tmpFile . '"');
    }

    /**
     * Sets up cURL headers with token for API requests.
     *
     * @return array The array of cURL headers.
     */
    public static function setupCurlHeaderWithToken()
    {
        // Return headers with User-Agent, which is required by GitHub API
        return array(
            'User-Agent: ' . APP_GITHUB_USERAGENT . ' (https://github.com/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . ')',
            'Accept: application/vnd.github.v3+json'
        );
    }
}
