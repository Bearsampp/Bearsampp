<?php
/*
 *
 *  * Copyright (c) 2021-2024 Bearsampp
 *  * License:  GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Manages the creation and deletion of symbolic links for various components within the Bearsampp environment.
 */
class Symlinks
{
    const PHPMYADMIN_SYMLINK = 'phpmyadmin';
    const PHPPGADMIN_SYMLINK = 'phppgadmin';
    const APACHE_SYMLINK = 'apache';
    const MARIADB_SYMLINK = 'mariadb';
    const MEMCACHED_SYMLINK = 'memcached';
    const MYSQL_SYMLINK = 'mysql';
    const NODEJS_SYMLINK = 'nodejs';
    const PHP_SYMLINK = 'php';
    const POSTGRESQL_SYMLINK = 'postgresql';
    const COMPOSER_SYMLINK = 'composer';
    const POWERSHELL_SYMLINK = 'powershell';
    const GHOSTSCRIPT_SYMLINK = 'ghostscript';
    const GIT_SYMLINK = 'git';
    const NGROK_SYMLINK = 'ngrok';
    const PERL_SYMLINK = 'perl';
    const PYTHON_SYMLINK = 'python';
    const RUBY_SYMLINK = 'ruby';
    const XLIGHT_SYMLINK = 'xlight';
    const MAILPIT_SYMLINK = 'mailpit';
    const BRUNO_SYMLINK = 'bruno';

    /**
     * @var Root The root object providing access to system paths.
     */
    private $root;

    /**
     * Constructs a Symlinks object and initializes paths to current directories.
     *
     * @param Root $root The root object associated with the Bearsampp environment.
     */
    public function __construct($root)
    {
        $this->root = $root;
        $this->initializePaths();
    }

    /**
     * Validates that a path is within allowed symlink directories.
     * Prevents deletion of paths outside the Bearsampp managed directories.
     *
     * @param string $path The path to validate
     * @return bool True if path is within allowed directories, false otherwise
     */
    private static function isPathWithinAllowedBase($path)
    {
        global $bearsamppRoot;

        // Normalize paths for comparison
        $normalizedPath = realpath($path);
        if ($normalizedPath === false) {
            Log::error('Failed to resolve path: ' . $path);
            return false;
        }

        $allowedBases = [
            realpath($bearsamppRoot->getAppsPath()),
            realpath($bearsamppRoot->getBinPath()),
            realpath($bearsamppRoot->getToolsPath())
        ];

        foreach ($allowedBases as $base) {
            if ($base === false) {
                continue;
            }

            // Ensure path starts with allowed base (with directory separator to prevent substring matches)
            if (strpos($normalizedPath, $base . DIRECTORY_SEPARATOR) === 0 ||
                $normalizedPath === $base) {
                return true;
            }
        }

        Log::error('Path is outside allowed symlink directories: ' . $path);
        return false;
    }

    /**
     * Checks if a path is a symlink (not following the link).
     * Uses lstat to avoid following symlinks.
     *
     * @param string $path The path to check
     * @return bool True if path is a symlink, false otherwise
     */
    private static function isSymlink($path)
    {
        // Use is_link to check without following symlinks
        return is_link($path);
    }

    /**
     * Safely removes a symlink or empty directory.
     * CRITICAL: Does NOT recursively delete; only removes:
     * - Actual symlinks (using unlink)
     * - Empty directories (using rmdir)
     *
     * Will NOT remove:
     * - Non-empty directories
     * - Files that are not symlinks
     * - Paths outside allowed directories
     * - Junctions or other special link types if they contain files
     *
     * @param string $path The path to remove
     * @return bool True on success, false on failure
     */
    private static function safeRemoveSymlink($path)
    {
        // Validate path is within allowed directories
        if (!self::isPathWithinAllowedBase($path)) {
            Log::error('Symlink removal blocked - path not in allowed directories: ' . $path);
            return false;
        }

        // Check if path exists
        if (!file_exists($path) && !is_link($path)) {
            Log::debug('Symlink does not exist: ' . $path);
            return false;
        }

        // If it's a symlink, remove it appropriately.
        // On Windows, directory junctions require rmdir(), not unlink(). For broken junctions,
        // is_dir() returns false even though the link exists, so we try both methods.
        if (self::isSymlink($path)) {
            $removed = @unlink($path) || @rmdir($path);
            if ($removed) {
                Log::debug('Safely removed symlink: ' . $path);
                return true;
            } else {
                Log::error('Failed to remove symlink: ' . $path);
                return false;
            }
        }

        // If it's a directory, only remove if empty (rmdir fails on non-empty)
        if (is_dir($path)) {
            // Double-check: ensure we're not attempting recursive deletion
            $items = @scandir($path);
            if ($items === false) {
                Log::error('Cannot read directory contents: ' . $path);
                return false;
            }

            // Count real items (exclude . and ..)
            $realItems = array_diff($items, ['.', '..']);

            if (!empty($realItems)) {
                Log::warning('Directory is not empty - refusing to delete: ' . $path .
                    ' (contains ' . count($realItems) . ' items)');
                return false;
            }

            // Directory is empty, safe to remove
            if (@rmdir($path)) {
                Log::debug('Safely removed empty directory: ' . $path);
                return true;
            } else {
                Log::error('Failed to remove empty directory: ' . $path);
                return false;
            }
        }

        // Regular files should not be deleted here
        Log::warning('Path is a regular file, not a symlink - refusing deletion: ' . $path);
        return false;
    }

    /**
     * Deletes all symbolic links listed in the arrayOfCurrents.
     * Logs each operation's success or failure.
     *
     * This method iterates over a predefined list of symbolic link paths and attempts to delete each one.
     * Uses strict safety checks to prevent accidental deletion of user data:
     * - Only deletes symlinks or empty directories
     * - Validates all paths are within Bearsampp managed directories
     * - Refuses to perform recursive deletion
     * - Does not follow or delete junction points with content
     *
     * @global Root $bearsamppRoot The root object providing access to system paths.
     * @global Core $bearsamppCore The core object providing core functionalities.
     */
    public static function deleteCurrentSymlinks()
    {
        global $bearsamppRoot, $bearsamppCore;

        // Check to see if purging is necessary
        $appsPath  = $bearsamppRoot->getAppsPath();
        $binPath   = $bearsamppRoot->getBinPath();
        $toolsPath = $bearsamppRoot->getToolsPath();

        $array = [
            self::PHPMYADMIN_SYMLINK => $appsPath . '/phpmyadmin/current',
            self::PHPPGADMIN_SYMLINK => $appsPath . '/phppgadmin/current',
            self::APACHE_SYMLINK => $binPath . '/apache/current',
            self::MARIADB_SYMLINK => $binPath . '/mariadb/current',
            self::MEMCACHED_SYMLINK => $binPath . '/memcached/current',
            self::MYSQL_SYMLINK => $binPath . '/mysql/current',
            self::NODEJS_SYMLINK => $binPath . '/nodejs/current',
            self::PHP_SYMLINK => $binPath . '/php/current',
            self::POSTGRESQL_SYMLINK => $binPath . '/postgresql/current',
            self::COMPOSER_SYMLINK => $toolsPath . '/composer/current',
            self::POWERSHELL_SYMLINK => $toolsPath . '/powershell/current',
            self::GHOSTSCRIPT_SYMLINK => $toolsPath . '/ghostscript/current',
            self::GIT_SYMLINK => $toolsPath . '/git/current',
            self::NGROK_SYMLINK => $toolsPath . '/ngrok/current',
            self::PERL_SYMLINK => $toolsPath . '/perl/current',
            self::PYTHON_SYMLINK => $toolsPath . '/python/current',
            self::RUBY_SYMLINK => $toolsPath . '/ruby/current',
            self::XLIGHT_SYMLINK => $binPath . '/xlight/current',
            self::MAILPIT_SYMLINK => $binPath . '/mailpit/current',
            self::BRUNO_SYMLINK => $toolsPath . '/bruno/current'
        ];

        // Fix for PHP 8.2: Add null checks before accessing array elements
        if (!is_array($array) || empty($array)) {
            Log::error('Current symlinks array is not initialized or empty.');
            return;
        }

        // Purge "current" symlinks with safety checks
        foreach ($array as $name => $path) {
            // Skip if path is null
            if (empty($path)) {
                continue;
            }

            if (!file_exists($path) && !is_link($path)) {
                // Skip if the symlink doesn't exist - no need to log an error
                continue;
            }

            // Use safe removal method with path validation and non-recursive guarantees
            self::safeRemoveSymlink($path);
        }
    }
}
