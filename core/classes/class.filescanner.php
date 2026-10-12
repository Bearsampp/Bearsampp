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
 * File scanning utilities.
 *
 * Discovers the Bearsampp module/tool configuration files that must be scanned
 * (Apache, PHP, MySQL, MariaDB, PostgreSQL, Node.js, Composer, PowerShell, Python,
 * Ruby) and walks them with optional caching.
 */
class FileScanner
{
    /**
     * Retrieves a list of files to scan from specified paths or default paths.
     * Implements caching to avoid repeated expensive file system scans.
     *
     * @param   string|null  $path          Optional. The path to start scanning from. If null, uses default paths.
     * @param   bool         $useCache      Whether to use cached results (default: true).
     * @param   bool         $forceRefresh  Force refresh the cache even if valid (default: false).
     *
     * @return array|false Returns an array of files found during the scan, or false if path discovery failed. A failed
     *                      discovery returns false and is never cached, so an incomplete list cannot be reused later.
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
        $startTime = Util::getMicrotime();
        $result    = array();

        if (!empty($path)) {
            $pathsToScan = $path;
        } else {
            $pathsToScan = self::getPathsToScan();
            if ($pathsToScan === false) {
                // Return before the cache is written: an incomplete list must not
                // be reused, otherwise a transient scan failure poisons every
                // later call that would have succeeded.
                Log::error('getFilesToScan(): Path discovery failed, scan aborted and result not cached');

                return false;
            }
        }

        foreach ($pathsToScan as $pathToScan) {
            $pathStartTime = Util::getMicrotime();
            $findFiles     = self::findFiles($pathToScan['path'], $pathToScan['includes'], $pathToScan['recursive']);
            foreach ($findFiles as $findFile) {
                $result[] = $findFile;
            }
            Log::debug($pathToScan['path'] . ' scanned in ' . round(Util::getMicrotime() - $pathStartTime, 3) . 's');
        }

        $totalTime = round(Util::getMicrotime() - $startTime, 3);
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
     * @return array|false Returns an array of associative arrays, each containing 'path', 'includes', and 'recursive' keys, or false if any module folder could not be scanned.
     */
    private static function getPathsToScan()
    {
        global $bearsamppBins, $bearsamppTools;
        $paths      = array();
        $scanFailed = false;

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
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppBins->getApache()), $scanFailed);
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getApache()) . '/' . $folder,
                'includes'  => array('.ini', '.conf'),
                'recursive' => true
            );
        }

        // PHP
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppBins->getPhp()), $scanFailed);
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getPhp()) . '/' . $folder,
                'includes'  => array('.php', '.bat', '.ini', '.reg', '.inc'),
                'recursive' => true
            );
        }

        // MySQL
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppBins->getMysql()), $scanFailed);
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getMysql()) . '/' . $folder,
                'includes'  => array('my.ini'),
                'recursive' => false
            );
        }

        // MariaDB
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppBins->getMariadb()), $scanFailed);
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
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppBins->getPostgresql()), $scanFailed);
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppBins->getPostgresql()) . '/' . $folder,
                'includes'  => array('.conf', '.bat', '.ber'),
                'recursive' => true
            );
        }

        // Node.js
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppBins->getNodejs()), $scanFailed);
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
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppTools->getComposer()), $scanFailed);
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppTools->getComposer()) . '/' . $folder,
                'includes'  => array('giscus.json'),
                'recursive' => false
            );
        }

        // PowerShell
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppTools->getPowerShell()), $scanFailed);
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppTools->getPowerShell()) . '/' . $folder,
                'includes'  => array('console.xml', '.ini', '.btm'),
                'recursive' => true
            );
        }

        // Python
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppTools->getPython()), $scanFailed);
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
        $folderList = self::folderList(Path::getModuleRootPath($bearsamppTools->getRuby()), $scanFailed);
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path'      => Path::getModuleRootPath($bearsamppTools->getRuby()) . '/' . $folder . '/bin',
                'includes'  => array('!.dll', '!.exe'),
                'recursive' => false
            );
        }

        if ($scanFailed) {
            Log::error('getPathsToScan(): One or more module folders could not be scanned, path discovery is incomplete');

            return false;
        }

        return $paths;
    }

    /**
     * Returns the folder list of a module or tool root directory, recording a
     * failed scan instead of passing it off as a module with no versions.
     *
     * A module root that cannot be opened is not the same as one that holds no
     * version folders: treating it as empty would silently drop every file of
     * that module from the scan. getFolderList() logs the offending path, so
     * this only has to flag the failure for the caller.
     *
     * @param   string  $path        The module/tool root path to scan.
     * @param   bool    $scanFailed  Set to true by reference when the directory cannot be read.
     *
     * @return array Returns the folder names, or an empty array when the scan failed.
     */
    private static function folderList($path, &$scanFailed)
    {
        $folderList = Filesystem::getFolderList($path);
        if ($folderList === false) {
            $scanFailed = true;

            return array();
        }

        return $folderList;
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
                    if (Str::startsWith($include, '!')) {
                        $include = ltrim($include, '!');
                        if (Str::startsWith($file, '.') && !Str::endsWith($file, $include)) {
                            $result[] = Path::formatUnixPath($startPath . '/' . $file);
                        } elseif ($file != $include) {
                            $result[] = Path::formatUnixPath($startPath . '/' . $file);
                        }
                    } elseif (Str::endsWith($file, $include) || $file == $include || empty($include)) {
                        $result[] = Path::formatUnixPath($startPath . '/' . $file);
                    }
                }
            }
        }

        closedir($handle);

        return $result;
    }
}
