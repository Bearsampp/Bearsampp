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
            $path = rtrim($path, '/\\') . '/';
            $files = glob($path . '*', GLOB_MARK);

            if ($files === false) {
                Log::error("deleteFolder(): Failed to glob path: " . $path);
                return;
            }

            foreach ($files as $file) {
                $normalizedFile = rtrim($file, '/\\');

                if (is_link($normalizedFile)) {
                    if (!@unlink($normalizedFile) && !@rmdir($normalizedFile)) {
                        Log::error("deleteFolder(): Failed to unlink symlink: " . $normalizedFile);
                    }
                } elseif (is_dir($file)) {
                    self::deleteFolder($file);
                } else {
                    if (!@unlink($normalizedFile)) {
                        Log::error("deleteFolder(): Failed to unlink file: " . $normalizedFile);
                    }
                }
            }

            if (!@rmdir($path)) {
                // Only log error if directory still exists (might have been deleted by recursion)
                if (is_dir($path)) {
                    Log::error("deleteFolder(): Failed to remove directory: " . $path);
                }
            }
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
    public static function findFile($startPath, $findFile)
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
                $result = Path::formatUnixPath($startPath . '/' . $file);
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
                        $currentReplace = $replace;
                        $countParams = preg_match_all('/{{(\d+)}}/', $currentReplace, $paramsMatches);
                        if ($countParams > 0 && $countParams <= count($matches)) {
                            foreach ($paramsMatches[1] as $paramsMatch) {
                                $currentReplace = str_replace('{{' . $paramsMatch . '}}', $matches[$paramsMatch], $currentReplace);
                            }
                        }
                        Log::trace('Replace in file ' . $path . ' :');
                        Log::trace('## line_num: ' . trim($nb));
                        Log::trace('## old: ' . trim($line));
                        Log::trace('## new: ' . trim($currentReplace));
                        
                        // Preserve original line ending if present in $line
                        $ending = (preg_match("/\r\n$/", $line)) ? "\r\n" : (preg_match("/\n$/", $line) ? "\n" : "");
                        fwrite($fp, rtrim($currentReplace) . $ending);

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

        $targetPath = Path::getExeFilePath();
        $workingDir = Path::getRootPath();
        $description = APP_TITLE . ' ' . $bearsamppCore->getAppVersion();
        $iconPath = Path::getIconsPath() . '/app.ico';

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
                $result[] = Path::formatUnixPath($startPath);
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
        Log::trace('PHP executable: ' . Path::getPhpExe());
        Log::trace('Root file: ' . Core::isRoot_FILE);
        Log::trace('Action: ' . Action::LOADING);

        Log::trace('Executing command: ' . Path::getPhpExe() . ' ' . Core::isRoot_FILE . ' ' . Action::LOADING);

        $result = $bearsamppWinbinder->exec(Path::getPhpExe(), [Core::isRoot_FILE, Action::LOADING], true, false);
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

        $statusFile = Path::getTmpPath() . '/loading_status.txt';
        file_put_contents($statusFile, json_encode(['text' => $text]));
    }

    /**
     * Clears the loading status file
     */
    public static function clearLoadingText()
    {
        global $bearsamppCore;

        $statusFile = Path::getTmpPath() . '/loading_status.txt';
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
            $cachedResult = Cache::get($cacheKey);
            if ($cachedResult !== false) {
                Cache::recordHit();
                Log::debug('File scan cache HIT (saved expensive scan operation)');
                return $cachedResult;
            }
        }

        Cache::recordMiss();
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
            Cache::set($cacheKey, $result);
        }

        return $result;
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
            'path'      => Path::getAliasPath(),
            'includes'  => array(''),
            'recursive' => false
        );

        // Vhosts
        $paths[] = array(
            'path'      => Path::getVhostsPath(),
            'includes'  => array(''),
            'recursive' => false
        );

        // OpenSSL
        $paths[] = array(
            'path'      => Path::getOpenSslPath(),
            'includes'  => array('openssl.cfg'),
            'recursive' => false
        );

        // Homepage
        $paths[] = array(
            'path'      => Path::getResourcesPath() . '/homepage',
            'includes'  => array('alias.conf'),
            'recursive' => false
        );

        // Apache
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppBins->getApache()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getApache()) . '/' . $folder,
                'includes'  => array('.ini', '.conf'),
                'recursive' => true
            );
        }

        // PHP
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppBins->getPhp()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getPhp()) . '/' . $folder,
                'includes'  => array('.php', '.bat', '.ini', '.reg', '.inc'),
                'recursive' => true
            );
        }

        // MySQL
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppBins->getMysql()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getMysql()) . '/' . $folder,
                'includes'  => array('my.ini'),
                'recursive' => false
            );
        }

        // MariaDB
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppBins->getMariadb()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getMariadb()) . '/' . $folder,
                'includes'  => array('my.ini'),
                'recursive' => false
            );
            // Also scan data directory for my.ini (created during initialization)
            $dataPath = Path::getModuleRootPath($bearsamppBins->getMariadb()) . '/' . $folder . '/data';
            if (is_dir($dataPath)) {
                $paths[] = array(
                    'path'      => $dataPath,
                    'includes'  => array('my.ini'),
                    'recursive' => false
                );
            }
        }

        // PostgreSQL
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppBins->getPostgresql()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getPostgresql()) . '/' . $folder,
                'includes'  => array( '.conf', '.bat', '.ber'),
                'recursive' => true
            );
        }

        // Node.js
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppBins->getNodejs()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getNodejs()) . '/' . $folder . '/etc',
                'includes'  => array('npmrc'),
                'recursive' => true
            );
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getNodejs()) . '/' . $folder . '/node_modules/npm',
                'includes'  => array('npmrc'),
                'recursive' => false
            );
        }

        // Composer
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppTools->getComposer()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppTools->getComposer()) . '/' . $folder,
                'includes'  => array('giscus.json'),
                'recursive' => false
            );
        }

        // PowerShell
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppTools->getPowerShell()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppTools->getPowerShell()) . '/' . $folder,
                'includes'  => array('console.xml', '.ini', '.btm'),
                'recursive' => true
            );
        }

        // Python
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppTools->getPython()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppTools->getPython()) . '/' . $folder . '/bin',
                'includes'  => array('.bat'),
                'recursive' => false
            );
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppTools->getPython()) . '/' . $folder . '/settings',
                'includes'  => array('winpython.ini'),
                'recursive' => false
            );
        }

        // Ruby
        $folderList = self::getFolderList(Path::getModuleRootPath($bearsamppTools->getRuby()));
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppTools->getRuby()) . '/' . $folder . '/bin',
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
                            $result[] = Path::formatUnixPath($startPath . '/' . $file);
                        } elseif ($file != $include) {
                            $result[] = Path::formatUnixPath($startPath . '/' . $file);
                        }
                    } elseif (UtilString::endWith($file, $include) || $file == $include || empty($include)) {
                        $result[] = Path::formatUnixPath($startPath . '/' . $file);
                    }
                }
            }
        }

        closedir($handle);

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
        Log::trace('[VCHK-3] getLatestVersion() START - fetching latest version from: ' . $url);

        $result = HttpClient::getApiJson($url);
        if (empty($result)) {
            Log::error('Cannot retrieve latest github info for: ' . $result . ' RESULT');
            Log::trace('[VCHK-3] getLatestVersion() EXIT - empty response received');

            return null;
        }

        $resultArray = json_decode($result, true);
        if (isset($resultArray['tag_name']) && isset($resultArray['assets'][0]['browser_download_url'])) {
            $tagName     = $resultArray['tag_name'];
            $downloadUrl = $resultArray['assets'][0]['browser_download_url'];
            $name        = $resultArray['name'];
            Log::trace('Latest version tag name: ' . $tagName);
            Log::trace('Download URL: ' . $downloadUrl);
            Log::trace('Name: ' . $name);
            Log::trace('[VCHK-3] getLatestVersion() SUCCESS - version found: ' . $tagName);

            return ['version' => $tagName, 'html_url' => $downloadUrl, 'name' => $name];
        } else {
            Log::error('Tag name, download URL, or name not found in the response: ' . $result);
            Log::trace('[VCHK-3] getLatestVersion() EXIT - tag_name/download_url missing in JSON response');

            return null;
        }
    }

    /**
     * Converts a file size in bytes to a human-readable format.
     *
     * Uses PHP's native human_readable_size() when no unit is forced.
     * Falls back to manual conversion when a specific unit is requested.
     *
     * @param  int     $size  The file size in bytes.
     * @param  string  $unit  Optional forced unit ('GB', 'MB', 'KB', or '').
     *
     * @return string  The formatted file size.
     */
    public static function humanFileSize(int $size, string $unit = ''): string
    {
        // Forced unit mode
        if ($unit !== '') {
            return match ($unit) {
                'GB' => number_format($size / (1 << 30), 2) . 'GB',
                'MB' => number_format($size / (1 << 20), 2) . 'MB',
                'KB' => number_format($size / (1 << 10), 2) . 'KB',
                default => number_format($size) . ' bytes',
            };
        }

        // Native PHP 8.3+ auto-selection
        return human_readable_size($size, precision: 2);
    }

    /**
     * Checks if the operating system is 32-bit.
     *
     * @return bool True if the OS is 32-bit, false otherwise.
     */
    public static function is32BitsOs()
    {
        global $bearsamppRegistry;
        $processor = $bearsamppRegistry->getProcessorRegKey();

        return UtilString::contains($processor, 'x86');
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
        return filter_var($domainName, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
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

        $tmpFile = Path::getTmpPath() . '/' . $caption . '.txt';
        file_put_contents($tmpFile, $content);

        // Open the file with the configured editor from bearsampp.conf
        $editor = $bearsamppConfig->getNotepad();
        $bearsamppCore->getWinbinder()->exec($editor, '"' . $tmpFile . '"');
    }
}
