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
 * Path formatting utilities with a write-through cache.
 *
 * Converts between Windows (backslash) and Unix (forward-slash) path styles and
 * caches the results to avoid redundant string replacements for frequently used
 * paths such as root, bin, and tool paths.
 *
 * Usage:
 * ```
 * $win = Path::formatWindowsPath('/some/unix/path');
 * $unix = Path::formatUnixPath('C:\some\windows\path');
 * ```
 */
class Path
{
    /**
     * Retrieves the root path for a module.
     *
     * @param Module $module The module instance.
     * @return string The module root path.
     */
    public static function getModuleRootPath($module)
    {
        return $module->rootPath;
    }

    /**
     * Retrieves the current path for a module.
     *
     * @param Module $module The module instance.
     * @return string The module current path.
     */
    public static function getModuleCurrentPath($module)
    {
        return $module->currentPath;
    }

    /**
     * Retrieves the symlink path for a module.
     *
     * @param Module $module The module instance.
     * @return string The module symlink path.
     */
    public static function getModuleSymlinkPath($module)
    {
        return $module->symlinkPath;
    }

    /**
     * Cache for path formatting operations to avoid redundant string replacements.
     * @var array
     */
    private static $pathFormatCache = [];

    /**
     * The root path of the application.
     * @var string
     */
    private static $rootPath;

    /**
     * The path to the core directory.
     * @var string
     */
    private static $corePath;

    /**
     * Initializes the Path class with root and core paths.
     *
     * @param string $rootPath The root path of the application.
     * @param string $corePath The path to the core directory.
     * @return void
     */
    public static function init($rootPath, $corePath)
    {
        self::$rootPath = str_replace('\\', '/', rtrim($rootPath, '/\\'));
        self::$corePath = str_replace('\\', '/', rtrim($corePath, '/\\'));
    }

    /**
     * Maximum size for path format cache to prevent memory issues.
     * @var int
     */
    private static $pathFormatCacheMaxSize = 500;

    /**
     * Statistics for monitoring path format cache effectiveness.
     * @var array
     */
    private static $pathFormatStats = [
        'unix_hits'      => 0,
        'unix_misses'    => 0,
        'windows_hits'   => 0,
        'windows_misses' => 0,
    ];

    /**
     * Converts a Unix-style path to a Windows-style path with caching.
     * This is a Windows application, so paths use backslashes (\) as separators.
     *
     * Performance optimization: Caches results to avoid redundant string replacements
     * for frequently used paths (e.g., root paths, bin paths).
     *
     * @param   string  $path  The Unix-style path to convert.
     *
     * @return string Returns the converted Windows-style path.
     */
    public static function formatWindowsPath($path)
    {
        if (empty($path)) {
            return $path;
        }

        $cacheKey = 'w_' . $path;
        if (isset(self::$pathFormatCache[$cacheKey])) {
            self::$pathFormatStats['windows_hits']++;
            return self::$pathFormatCache[$cacheKey];
        }

        self::$pathFormatStats['windows_misses']++;

        $result = str_replace('/', '\\', $path);

        if (count(self::$pathFormatCache) < self::$pathFormatCacheMaxSize) {
            self::$pathFormatCache[$cacheKey] = $result;
        } else {
            $removeCount = (int)(self::$pathFormatCacheMaxSize * 0.1);
            self::$pathFormatCache = array_slice(self::$pathFormatCache, $removeCount, null, true);
            self::$pathFormatCache[$cacheKey] = $result;
        }

        return $result;
    }

    /**
     * Converts a Windows-style path to a Unix-style path with caching.
     * Unix-style paths use forward slashes (/) as separators.
     *
     * Performance optimization: Caches results to avoid redundant string replacements
     * for frequently used paths (e.g., root paths, bin paths).
     *
     * @param   string  $path  The Windows-style path to convert.
     *
     * @return string Returns the converted Unix-style path.
     */
    public static function formatUnixPath($path)
    {
        if (empty($path)) {
            return $path;
        }

        $cacheKey = 'u_' . $path;
        if (isset(self::$pathFormatCache[$cacheKey])) {
            self::$pathFormatStats['unix_hits']++;
            return self::$pathFormatCache[$cacheKey];
        }

        self::$pathFormatStats['unix_misses']++;

        $result = str_replace('\\', '/', $path);

        if (count(self::$pathFormatCache) < self::$pathFormatCacheMaxSize) {
            self::$pathFormatCache[$cacheKey] = $result;
        } else {
            $removeCount = (int)(self::$pathFormatCacheMaxSize * 0.1);
            self::$pathFormatCache = array_slice(self::$pathFormatCache, $removeCount, null, true);
            self::$pathFormatCache[$cacheKey] = $result;
        }

        return $result;
    }

    /**
     * Gets path format cache statistics.
     * Useful for monitoring cache effectiveness and tuning cache size.
     *
     * @return array Array containing unix_hits, unix_misses, windows_hits, windows_misses.
     */
    public static function getPathFormatStats()
    {
        return self::$pathFormatStats;
    }

    /**
     * Replaces old path references with new path references in the specified files.
     *
     * @param array $filesToScan Array of file paths to scan and modify.
     * @param string|null $rootPath The new root path to replace the old one. If null, uses a default root path.
     * @return array Returns an array with the count of occurrences changed and the count of files changed.
     */
    public static function changePath($filesToScan, $rootPath = null)
    {
        global $bearsamppRoot, $bearsamppCore;

        $result = array(
            'countChangedOcc'   => 0,
            'countChangedFiles' => 0
        );

        $rootPath           = $rootPath != null ? $rootPath : Path::getRootPath();
        $unixOldPath        = Path::formatUnixPath($bearsamppCore->getLastPathContent());
        $windowsOldPath     = Path::formatWindowsPath($bearsamppCore->getLastPathContent());
        $unixCurrentPath    = Path::formatUnixPath($rootPath);
        $windowsCurrentPath = Path::formatWindowsPath($rootPath);

        foreach ($filesToScan as $fileToScan) {
            $tmpCountChangedOcc = 0;
            $fileContentOr      = file_get_contents($fileToScan);

            if ($fileContentOr === false) {
                Log::error("changePath(): Failed to read file: " . $fileToScan);
                continue;
            }

            $fileContent = $fileContentOr;

            // old path
            if (stripos($fileContent, $unixOldPath) !== false) {
                $fileContent        = str_ireplace($unixOldPath, $unixCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }
            if (stripos($fileContent, $windowsOldPath) !== false) {
                $fileContent        = str_ireplace($windowsOldPath, $windowsCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }

            // placeholders
            if (strpos($fileContent, Core::PATH_LIN_PLACEHOLDER) !== false) {
                $fileContent        = str_replace(Core::PATH_LIN_PLACEHOLDER, $unixCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }
            if (strpos($fileContent, Core::PATH_WIN_PLACEHOLDER) !== false) {
                $fileContent        = str_replace(Core::PATH_WIN_PLACEHOLDER, $windowsCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }

            if ($fileContentOr != $fileContent) {
                if (file_put_contents($fileToScan, $fileContent) !== false) {
                    $result['countChangedOcc']   += $tmpCountChangedOcc;
                    $result['countChangedFiles'] += 1;
                } else {
                    Log::error("changePath(): Failed to write to file: " . $fileToScan);
                }
            }
        }

        Log::debug('changePath() completed: ' . $result['countChangedFiles'] . ' files changed, ' . $result['countChangedOcc'] . ' total occurrences');

        return $result;
    }

    /**
     * Clears the path format cache.
     * Useful when paths change or for testing purposes.
     *
     * @return void
     */
    public static function clearPathFormatCache()
    {
        self::$pathFormatCache = [];
        self::$pathFormatStats = [
            'unix_hits'      => 0,
            'unix_misses'    => 0,
            'windows_hits'   => 0,
            'windows_misses' => 0,
        ];
    }

    /**
     * Gets the current size of the path format cache.
     *
     * @return int Number of cached path conversions.
     */
    public static function getPathFormatCacheSize()
    {
        return count(self::$pathFormatCache);
    }

    /**
     * Formats a path for AeTrayMenu.
     *
     * @param string $path The path to format.
     * @return string The formatted path.
     */
    public static function aetrayPath($path)
    {
        $rootPath = self::getRootPath();
        $path = str_replace($rootPath, '', $path);
        $path = ltrim(self::formatUnixPath($path), '/');
        return '%AeTrayMenuPath%' . $path;
    }

    /**
     * Retrieves the root path of the application.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The root path.
     */
    public static function getRootPath($aetrayPath = false)
    {
        return $aetrayPath ? self::aetrayPath(self::$rootPath) : self::$rootPath;
    }

    /**
     * Retrieves the path to the ajax directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the ajax directory.
     */
    public static function getAjaxPath($aetrayPath = false)
    {
        return self::getHomepagePath($aetrayPath) . '/ajax';
    }

    /**
     * Gets the path to the alias directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The alias path.
     */
    public static function getAliasPath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/alias';
    }

    /**
     * Gets the path to the apps directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The apps path.
     */
    public static function getAppsPath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/apps';
    }

    /**
     * Gets the path to the batch log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The batch log file path.
     */
    public static function getBatchLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp-batch.log';
    }

    /**
     * Gets the path to the bin directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The bin path.
     */
    public static function getBinPath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/bin';
    }

    /**
     * Gets the path to the configuration file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The configuration file path.
     */
    public static function getConfigFilePath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/bearsampp.conf';
    }

    /**
     * Retrieves the core path of the application.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The core path.
     */
    public static function getCorePath($aetrayPath = false)
    {
        return $aetrayPath ? self::aetrayPath(self::$corePath) : self::$corePath;
    }

    /**
     * Gets the name of the process.
     *
     * @return string The process name.
     */
    public static function getProcessName()
    {
        return 'bearsampp';
    }

    /**
     * Constructs a local URL with the specified request.
     *
     * @param string|null $request The specific request to append to the URL.
     * @return string The constructed local URL.
     */
    public static function getLocalUrl($request = null)
    {
        global $bearsamppBins;
        $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $scheme = $isHttps ? 'https://' : 'http://';
        $host = (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : 'localhost';
        
        $port = 80;
        if (isset($bearsamppBins) && $bearsamppBins->getApache() !== null) {
            $port = $isHttps ? $bearsamppBins->getApache()->getSslPort() : $bearsamppBins->getApache()->getPort();
        }

        $portSuffix = '';
        if ($isHttps) {
            if ($port != 443) {
                $portSuffix = ':' . $port;
            }
        } else {
            if ($port != 80) {
                $portSuffix = ':' . $port;
            }
        }

        return $scheme . $host . $portSuffix . (!empty($request) ? '/' . $request : '');
    }

    /**
     * Gets the path to the error log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The error log file path.
     */
    public static function getErrorLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp-error.log';
    }

    /**
     * Gets the path to the executable file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The executable file path.
     */
    public static function getExeFilePath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/bearsampp.exe';
    }

    /**
     * Gets the path to the homepage file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The homepage file path.
     */
    public static function getHomepageFilePath($aetrayPath = false)
    {
        return self::getWwwPath($aetrayPath) . '/index.php';
    }

    /**
     * Gets the path to the homepage log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The homepage log file path.
     */
    public static function getHomepageLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp-homepage.log';
    }

    /**
     * Retrieves the path to the homepage.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the homepage.
     */
    public static function getHomepagePath($aetrayPath = false)
    {
        return self::getResourcesPath($aetrayPath) . '/homepage';
    }

    /**
     * Retrieves the path to the HostsEditor directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the HostsEditor directory.
     */
    public static function getHostsEditorPath($aetrayPath = false)
    {
        return self::getLibsPath($aetrayPath) . '/hostseditor';
    }

    /**
     * Retrieves the path to the HostsEditor executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the HostsEditor executable.
     */
    public static function getHostsEditorExe($aetrayPath = false)
    {
        return self::getHostsEditorPath($aetrayPath) . '/' . Core::HOSTSEDITOR_EXE;
    }

    /**
     * Retrieves the path to the icons.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the icons.
     */
    public static function getIconsPath($aetrayPath = false)
    {
        return self::getImagesPath($aetrayPath) . '/icons';
    }

    /**
     * Retrieves the path to the images.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the images.
     */
    public static function getImagesPath($aetrayPath = false)
    {
        return self::getHomepagePath($aetrayPath) . '/img';
    }

    /**
     * Gets the path to the INI file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The INI file path.
     */
    public static function getIniFilePath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/bearsampp.ini';
    }

    /**
     * Retrieves the path to the root file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the root file.
     */
    public static function getisRootFilePath($aetrayPath = false)
    {
        return self::getCorePath($aetrayPath) . '/' . Core::isRoot_FILE;
    }

    /**
     * Retrieves the path to the language files.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the language files.
     */
    public static function getLangsPath($aetrayPath = false)
    {
        return self::getCorePath($aetrayPath) . '/langs';
    }

    /**
     * Retrieves the path to the last path file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the last path file.
     */
    public static function getLastPath($aetrayPath = false)
    {
        return self::getResourcesPath($aetrayPath) . '/' . Core::LAST_PATH;
    }

    /**
     * Retrieves the path to the libraries.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the libraries.
     */
    public static function getLibsPath($aetrayPath = false)
    {
        return self::getCorePath($aetrayPath) . '/libs';
    }

    /**
     * Retrieves the path to the LN directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the LN directory.
     */
    public static function getLnPath($aetrayPath = false)
    {
        return self::getLibsPath($aetrayPath) . '/ln';
    }

    /**
     * Retrieves the path to the LN executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the LN executable.
     */
    public static function getLnExe($aetrayPath = false)
    {
        return self::getLnPath($aetrayPath) . '/' . Core::LN_EXE;
    }

    /**
     * Gets the path to the log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The log file path.
     */
    public static function getLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp.log';
    }

    /**
     * Gets the path to the logs directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The logs path.
     */
    public static function getLogsPath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/logs';
    }

    /**
     * Retrieves the path to the NSSM directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the NSSM directory.
     */
    public static function getNssmPath($aetrayPath = false)
    {
        return self::getLibsPath($aetrayPath) . '/nssm';
    }

    /**
     * Retrieves the path to the NSSM executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the NSSM executable.
     */
    public static function getNssmExe($aetrayPath = false)
    {
        return self::getNssmPath($aetrayPath) . '/' . Core::NSSM_EXE;
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
        if (isset($bearsamppBins)) {
            if ($bearsamppBins->getApache() && $bearsamppBins->getApache()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppBins->getApache()) . '/bin;';
            }
            if ($bearsamppBins->getPhp() && $bearsamppBins->getPhp()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppBins->getPhp()) . ';';
                $paths .= self::getModuleSymlinkPath($bearsamppBins->getPhp()) . '/pear;';
                $paths .= self::getModuleSymlinkPath($bearsamppBins->getPhp()) . '/deps;';
                $paths .= self::getModuleSymlinkPath($bearsamppBins->getPhp()) . '/imagick;';
            }
            if ($bearsamppBins->getNodejs() && $bearsamppBins->getNodejs()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppBins->getNodejs()) . ';';
            }
        }

        // Add paths for enabled tools
        if (isset($bearsamppTools)) {
            if ($bearsamppTools->getComposer() && $bearsamppTools->getComposer()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getComposer()) . ';';
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getComposer()) . '/vendor/bin;';
            }
            if ($bearsamppTools->getGhostscript() && $bearsamppTools->getGhostscript()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getGhostscript()) . '/bin;';
            }
            if ($bearsamppTools->getGit() && $bearsamppTools->getGit()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getGit()) . '/bin;';
            }
            if ($bearsamppTools->getNgrok() && $bearsamppTools->getNgrok()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getNgrok()) . ';';
            }
            if ($bearsamppTools->getPerl() && $bearsamppTools->getPerl()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getPerl()) . '/perl/site/bin;';
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getPerl()) . '/perl/bin;';
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getPerl()) . '/c/bin;';
            }
            if ($bearsamppTools->getPython() && $bearsamppTools->getPython()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getPython()) . '/bin;';
            }
            if ($bearsamppTools->getRuby() && $bearsamppTools->getRuby()->isEnable()) {
                $paths .= self::getModuleSymlinkPath($bearsamppTools->getRuby()) . '/bin;';
            }
        }

        return self::formatWindowsPath($paths);
    }

    /**
     * Gets the path to the NSSM log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The NSSM log file path.
     */
    public static function getNssmLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp-nssm.log';
    }

    /**
     * Retrieves the path to the OpenSSL configuration file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the OpenSSL configuration file.
     */
    public static function getOpenSslConf($aetrayPath = false)
    {
        return self::getOpenSslPath($aetrayPath) . '/' . Core::OPENSSL_CONF;
    }

    /**
     * Retrieves the path to the OpenSSL executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the OpenSSL executable.
     */
    public static function getOpenSslExe($aetrayPath = false)
    {
        return self::getOpenSslPath($aetrayPath) . '/' . Core::OPENSSL_EXE;
    }

    /**
     * Retrieves the path to the OpenSSL directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the OpenSSL directory.
     */
    public static function getOpenSslPath($aetrayPath = false)
    {
        return self::getLibsPath($aetrayPath) . '/openssl';
    }

    /**
     * Retrieves the path to the PHP directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the PHP directory.
     */
    public static function getPhpPath($aetrayPath = false)
    {
        return self::getLibsPath($aetrayPath) . '/php';
    }

    /**
     * Retrieves the path to the PHP executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the PHP executable.
     */
    public static function getPhpExe($aetrayPath = false)
    {
        return self::getPhpPath($aetrayPath) . '/' . Core::PHP_EXE;
    }

    /**
     * Finds the path to the PowerShell executable in the Windows System32 directory.
     *
     * @return string|false Returns the path to powershell.exe if found, otherwise false.
     */
    public static function getPowerShellPath()
    {
        if (is_dir('C:\Windows\System32\WindowsPowerShell')) {
            return Util::findFile('C:\Windows\System32\WindowsPowerShell', 'powershell.exe');
        }

        return false;
    }

    /**
     * Retrieves the path to the PWGen directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the PWGen directory.
     */
    public static function getPwgenPath($aetrayPath = false)
    {
        return self::getLibsPath($aetrayPath) . '/pwgen';
    }

    /**
     * Retrieves the path to the PWGen executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the PWGen executable.
     */
    public static function getPwgenExe($aetrayPath = false)
    {
        return self::getPwgenPath($aetrayPath) . '/' . Core::PWGEN_EXE;
    }

    /**
     * Gets the path to the registry log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The registry log file path.
     */
    public static function getRegistryLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp-registry.log';
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
     * Retrieves the path to the resources.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the resources.
     */
    public static function getResourcesPath($aetrayPath = false)
    {
        return self::getCorePath($aetrayPath) . '/resources';
    }

    /**
     * Retrieves the path to a specific script.
     *
     * @param string $type The type of script.
     * @return string The path to the script.
     */
    public static function getScript($type)
    {
        return self::getScriptsPath() . '/' . $type;
    }

    /**
     * Retrieves the path to the scripts.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the scripts.
     */
    public static function getScriptsPath($aetrayPath = false)
    {
        return self::getCorePath($aetrayPath) . '/scripts';
    }

    /**
     * Gets the path to the services log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The services log file path.
     */
    public static function getServicesLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp-services.log';
    }

    /**
     * Retrieves the path to the SetEnv directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the SetEnv directory.
     */
    public static function getSetEnvPath($aetrayPath = false)
    {
        return self::getLibsPath($aetrayPath) . '/setenv';
    }

    /**
     * Retrieves the path to the SetEnv executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the SetEnv executable.
     */
    public static function getSetEnvExe($aetrayPath = false)
    {
        return self::getSetEnvPath($aetrayPath) . '/' . Core::SETENV_EXE;
    }

    /**
     * Gets the path to the SSL configuration file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The SSL configuration file path.
     */
    public static function getSslConfPath($aetrayPath = false)
    {
        return self::getSslPath($aetrayPath) . '/openssl.cnf';
    }

    /**
     * Gets the path to the SSL directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The SSL path.
     */
    public static function getSslPath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/ssl';
    }

    /**
     * Gets the path to the startup log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The startup log file path.
     */
    public static function getStartupLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp-startup.log';
    }

    /**
     * Retrieves the path to the temporary directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the temporary directory.
     */
    public static function getTmpPath($aetrayPath = false)
    {
        return self::getCorePath($aetrayPath) . '/tmp';
    }

    /**
     * Gets the path to the tools directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The tools path.
     */
    public static function getToolsPath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/tools';
    }

    /**
     * Gets the path to the mkcert directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The mkcert path.
     */
    public static function getMkcertPath($aetrayPath = false)
    {
        return self::getLibsPath($aetrayPath) . '/mkcert';
    }

    /**
     * Gets the path to the mkcert executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The mkcert executable path.
     */
    public static function getMkcertExe($aetrayPath = false)
    {
        return self::getMkcertPath($aetrayPath) . '/mkcert.exe';
    }

    /**
     * Gets the name of the mkcert root CA file.
     *
     * @return string The mkcert root CA filename.
     */
    public static function getMkcertRootCaName()
    {
        return 'rootCA.pem';
    }

    /**
     * Gets the path to the virtual hosts directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The virtual hosts path.
     */
    public static function getVhostsPath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/vhosts';
    }

    /**
     * Retrieves the web icons path.
     *
     * @return string The web icons path.
     */
    public static function getWebIconsPath()
    {
        return self::getWebImagesPath() . '/icons';
    }

    /**
     * Retrieves the web images path.
     *
     * @return string The web images path.
     */
    public static function getWebImagesPath()
    {
        return self::getWebResourcesPath() . '/img';
    }

    /**
     * Retrieves the web resources path.
     *
     * @return string The web resources path.
     */
    public static function getWebResourcesPath()
    {
        return rtrim(md5(APP_TITLE), '/');
    }

    /**
     * Retrieves the URL to the web resources.
     *
     * @return string The web resources URL.
     */
    public static function getWebResourcesUrl()
    {
        global $bearsamppBins;
        $request = self::getWebResourcesPath();
        $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $scheme = $isHttps ? 'https://' : 'http://';
        $host = (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : 'localhost';
        
        $port = 80;
        if (isset($bearsamppBins) && $bearsamppBins->getApache() !== null) {
            $port = $isHttps ? $bearsamppBins->getApache()->getSslPort() : $bearsamppBins->getApache()->getPort();
        }

        $portSuffix = '';
        if ($isHttps) {
            if ($port != 443) {
                $portSuffix = ':' . $port;
            }
        } else {
            if ($port != 80) {
                $portSuffix = ':' . $port;
            }
        }

        return $scheme . $host . $portSuffix . (!empty($request) ? '/' . $request : '');
    }

    /**
     * Gets the path to the Winbinder log file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The Winbinder log file path.
     */
    public static function getWinbinderLogFilePath($aetrayPath = false)
    {
        return self::getLogsPath($aetrayPath) . '/bearsampp-winbinder.log';
    }

    /**
     * Gets the path to the WWW directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The WWW path.
     */
    public static function getWwwPath($aetrayPath = false)
    {
        return self::getRootPath($aetrayPath) . '/www';
    }
}
