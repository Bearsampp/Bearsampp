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
 * Filesystem utilities.
 *
 * Recursively clears and deletes directories, searches for files and
 * repositories, and enumerates folders and version directories.
 */
class Filesystem
{
    /**
     * Clears multiple directories by delegating each one to clearFolder.
     *
     * @param   array  $paths    The paths of the directories to clear.
     * @param   array  $exclude  An array of filenames to exclude from deletion.
     *
     * @return array Returns an associative array keyed by path, containing the clearFolder result for each directory.
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
                $r = self::clearFolder($path . '/' . $file, $exclude);
                if ($r === null || !$r['return']) {
                    $result['return'] = false;
                    closedir($handle);

                    return $result;
                }
                $result['nb_files'] += $r['nb_files'];
            } else {
                $r = @unlink($path . '/' . $file);
                if ($r) {
                    $result['nb_files']++;
                } else {
                    $result['return'] = false;
                    closedir($handle);

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
     *
     * @return void
     */
    public static function deleteFolder($path)
    {
        if (is_dir($path)) {
            $path  = rtrim($path, '/\\') . '/';
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
     * Gets the list of version directories in the specified path.
     * Returns version suffixes by stripping the common prefix (basename of path) if present.
     *
     * @param   string  $path  The directory path to scan for version directories.
     *
     * @return array Returns a sorted array of version suffixes, or an empty array if the directory cannot be opened.
     */
    public static function getVersionList($path)
    {
        $result = array();

        $handle = @opendir($path);
        if (!$handle) {
            Log::debug('getVersionList(): Failed to open directory: ' . $path);

            return $result;
        }

        $prefix = basename($path);

        while (false !== ($file = readdir($handle))) {
            $filePath = $path . '/' . $file;
            if ($file != '.' && $file != '..' && is_dir($filePath) && $file != 'current') {
                if (strpos($file, $prefix) === 0 && strlen($file) > strlen($prefix)) {
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

        if (is_file($startPath . '/' . $checkFile)) {
            $result[] = Path::formatUnixPath($startPath);
        }

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
            }
        }

        closedir($handle);

        return $result;
    }

    /**
     * Gets the list of folders in the specified path.
     *
     * A directory that cannot be opened is reported as false so callers can tell
     * "no folders" apart from "scan failed"; treating it as an empty list would
     * let a caller silently build a scan from missing paths.
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
            Log::error('getFolderList(): Failed to open directory: ' . $path);

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
}
