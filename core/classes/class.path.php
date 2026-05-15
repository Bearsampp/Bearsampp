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
     * Cache for path formatting operations to avoid redundant string replacements.
     * @var array
     */
    private static $pathFormatCache = [];

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
     * Retrieves the path to the language files.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the language files.
     */
    public static function getLangsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/langs';
    }

    /**
     * Retrieves the path to the libraries.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the libraries.
     */
    public static function getLibsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/libs';
    }

    /**
     * Retrieves the path to the resources.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the resources.
     */
    public static function getResourcesPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/resources';
    }

    /**
     * Retrieves the path to the icons.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the icons.
     */
    public static function getIconsPath($aetrayPath = false)
    {
        return self::getImagesPath($aetrayPath) . '/icons';
    }

    /**
     * Retrieves the path to the images.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the images.
     */
    public static function getImagesPath($aetrayPath = false)
    {
        return self::getHomepagePath($aetrayPath) . '/img';
    }

    /**
     * Retrieves the path to the scripts.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the scripts.
     */
    public static function getScriptsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/scripts';
    }

    /**
     * Retrieves the path to the homepage.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the homepage.
     */
    public static function getHomepagePath($aetrayPath = false)
    {
        return self::getResourcesPath( $aetrayPath ) . '/homepage';
    }

    /**
     * Retrieves the path to the ajax directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the ajax directory.
     */
    public static function getAjaxPath($aetrayPath = false)
    {
        return self::getHomepagePath( $aetrayPath ) . '/ajax';
    }

    /**
     * Retrieves the path to a specific script.
     *
     * @param   string  $type  The type of script.
     *
     * @return string The path to the script.
     */
    public static function getScript($type)
    {
        return self::getScriptsPath() . '/' . $type;
    }

    /**
     * Retrieves the path to the temporary directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the temporary directory.
     */
    public static function getTmpPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/tmp';
    }

    /**
     * Retrieves the path to the root file.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the root file.
     */
    public static function getisRootFilePath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/' . Core::isRoot_FILE;
    }

    /**
     * Retrieves the path to the last path file.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the last path file.
     */
    public static function getLastPath($aetrayPath = false)
    {
        return self::getResourcesPath( $aetrayPath ) . '/' . Core::LAST_PATH;
    }

    /**
     * Retrieves the path to the PHP directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the PHP directory.
     */
    public static function getPhpPath($aetrayPath = false)
    {
        return self::getLibsPath( $aetrayPath ) . '/php';
    }

    /**
     * Retrieves the path to the PHP executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the PHP executable.
     */
    public static function getPhpExe($aetrayPath = false)
    {
        return self::getPhpPath( $aetrayPath ) . '/' . Core::PHP_EXE;
    }

    /**
     * Retrieves the path to the SetEnv directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the SetEnv directory.
     */
    public static function getSetEnvPath($aetrayPath = false)
    {
        return self::getLibsPath( $aetrayPath ) . '/setenv';
    }

    /**
     * Retrieves the path to the SetEnv executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the SetEnv executable.
     */
    public static function getSetEnvExe($aetrayPath = false)
    {
        return self::getSetEnvPath( $aetrayPath ) . '/' . Core::SETENV_EXE;
    }

    /**
     * Retrieves the path to the NSSM directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the NSSM directory.
     */
    public static function getNssmPath($aetrayPath = false)
    {
        return self::getLibsPath( $aetrayPath ) . '/nssm';
    }

    /**
     * Retrieves the path to the NSSM executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the NSSM executable.
     */
    public static function getNssmExe($aetrayPath = false)
    {
        return self::getNssmPath( $aetrayPath ) . '/' . Core::NSSM_EXE;
    }

    /**
     * Retrieves the path to the OpenSSL directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the OpenSSL directory.
     */
    public static function getOpenSslPath($aetrayPath = false)
    {
        return self::getLibsPath( $aetrayPath ) . '/openssl';
    }

    /**
     * Retrieves the path to the OpenSSL executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the OpenSSL executable.
     */
    public static function getOpenSslExe($aetrayPath = false)
    {
        return self::getOpenSslPath( $aetrayPath ) . '/' . Core::OPENSSL_EXE;
    }

    /**
     * Retrieves the path to the OpenSSL configuration file.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the OpenSSL configuration file.
     */
    public static function getOpenSslConf($aetrayPath = false)
    {
        return self::getOpenSslPath( $aetrayPath ) . '/' . Core::OPENSSL_CONF;
    }

    /**
     * Retrieves the path to the HostsEditor directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the HostsEditor directory.
     */
    public static function getHostsEditorPath($aetrayPath = false)
    {
        return self::getLibsPath( $aetrayPath ) . '/hostseditor';
    }

    /**
     * Retrieves the path to the HostsEditor executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the HostsEditor executable.
     */
    public static function getHostsEditorExe($aetrayPath = false)
    {
        return self::getHostsEditorPath( $aetrayPath ) . '/' . Core::HOSTSEDITOR_EXE;
    }

    /**
     * Retrieves the path to the LN directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the LN directory.
     */
    public static function getLnPath($aetrayPath = false)
    {
        return self::getLibsPath( $aetrayPath ) . '/ln';
    }

    /**
     * Retrieves the path to the LN executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the LN executable.
     */
    public static function getLnExe($aetrayPath = false)
    {
        return self::getLnPath( $aetrayPath ) . '/' . Core::LN_EXE;
    }

    /**
     * Retrieves the path to the PWGen directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the PWGen directory.
     */
    public static function getPwgenPath($aetrayPath = false)
    {
        return self::getLibsPath( $aetrayPath ) . '/pwgen';
    }

    /**
     * Retrieves the path to the PWGen executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the PWGen executable.
     */
    public static function getPwgenExe($aetrayPath = false)
    {
        return self::getPwgenPath( $aetrayPath ) . '/' . Core::PWGEN_EXE;
    }
}
