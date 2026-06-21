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
    const APACHE_SYMLINK = 'apache';
    const BRUNO_SYMLINK = 'bruno';
    const COMPOSER_SYMLINK = 'composer';
    const GHOSTSCRIPT_SYMLINK = 'ghostscript';
    const GIT_SYMLINK = 'git';
    const MAILPIT_SYMLINK = 'mailpit';
    const MARIADB_SYMLINK = 'mariadb';
    const MEMCACHED_SYMLINK = 'memcached';
    const MYSQL_SYMLINK = 'mysql';
    const NGROK_SYMLINK = 'ngrok';
    const NODEJS_SYMLINK = 'nodejs';
    const PERL_SYMLINK = 'perl';
    const PHP_SYMLINK = 'php';
    const PHPMYADMIN_SYMLINK = 'phpmyadmin';
    const PHPPGADMIN_SYMLINK = 'phppgadmin';
    const POSTGRESQL_SYMLINK = 'postgresql';
    const POWERSHELL_SYMLINK = 'powershell';
    const PYTHON_SYMLINK = 'python';
    const RUBY_SYMLINK = 'ruby';
    const XLIGHT_SYMLINK = 'xlight';

    /**
     * @var bool Whether to skip symlink creation.
     */
    private static $skipSymlinkCreation = false;

    /**
     * @var Root The root object providing access to system paths.
     */
    private static $root;

    /**
     * Constructs a Symlinks object and initializes paths to current directories.
     *
     * @param Root $root The root object associated with the Bearsampp environment.
     */
    public function __construct($root)
    {
        self::$root = $root;
        self::initializePaths();
    }

    /**
     * Initializes paths for symlinks.
     * This is called by the constructor or can be called manually.
     */
    public static function initializePaths()
    {
        // Path initialization logic can be added here if needed
        // For now, it's a placeholder as requested by the issue to restore it.
    }

    /**
     * Skip symlink creation during module reload
     * Useful for performance optimization during service checking phase
     *
     * @param bool $skip True to skip symlink creation
     * @return void
     */
    public static function setSkipSymlinkCreation(bool $skip): void
    {
        self::$skipSymlinkCreation = $skip;
    }

    /**
     * Check if symlink creation is being skipped
     *
     * @return bool True if symlink creation is skipped
     */
    public static function isSkippingSymlinkCreation(): bool
    {
        return self::$skipSymlinkCreation;
    }

    /**
     * Creates a symbolic link from the current path to the symlink path for a module.
     * If the symlink already exists and points to the correct target, no action is taken.
     *
     * @param Module $module The module instance.
     */
    public static function createModuleSymlink($module)
    {
        $src = Path::formatWindowsPath($module->currentPath);
        $dest = Path::formatWindowsPath($module->symlinkPath);

        if (is_link($dest)) {
            if (readlink($dest) === $src) {
                return;
            }
            Batch::removeSymlink($dest);
        } elseif (file_exists($dest)) {
            if (is_dir($dest)) {
                Log::error('Cannot create symlink: a real directory exists at the destination: ' . $dest);
                return;
            }
            Log::warning('Removing file at symlink location: ' . $dest);
            if (!@unlink($dest)) {
                Log::error('Failed to remove file at symlink location: ' . $dest);
                return;
            }
        }

        Batch::createSymlink($src, $dest);
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
            realpath(Path::getAppsPath()),
            realpath(Path::getBinPath()),
            realpath(Path::getToolsPath())
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
        return is_link($path);
    }

    /**
     * Safely removes a symlink or directory.
     * CRITICAL: Use with caution as it may perform recursive deletion if the path is a directory.
     *
     * @param string $path The path to remove
     * @return bool True on success, false on failure
     */
    public static function safeRemoveSymlink($path)
    {
        // Validate path is within allowed directories
        if (!self::isPathWithinAllowedBase($path)) {
            Log::error('Symlink removal blocked - path not in allowed directories: ' . $path);
            return false;
        }

        // Check if path exists
        if (!file_exists($path) && !is_link($path)) {
            Log::trace('Symlink or directory already deleted or missing: ' . $path);
            return false;
        }

        // If it's a directory (including junctions/directory-symlinks), use rmdir
        if (is_dir($path)) {
            // Attempt to remove as a symlink/junction first (non-recursive)
            if (@rmdir($path)) {
                Log::debug('Safely removed directory symlink/junction: ' . $path);
                return true;
            }

            // If it failed and it's NOT a link, it's a real directory with content.
            // We MUST NOT recursively delete it to avoid data loss.
            if (!is_link($path)) {
                Log::error('Symlink removal blocked - path is a real directory with content: ' . $path);
                return false;
            } else {
                // If it's a link but rmdir failed, try unlink (e.g. file symlink)
                if (@unlink($path)) {
                    Log::debug('Safely removed symlink via unlink: ' . $path);
                    return true;
                }
            }
        }

        // If it's a symlink but not a directory (e.g. file symlink), or if rmdir failed
        if (is_link($path)) {
            if (@unlink($path) || @rmdir($path)) {
                Log::debug('Safely removed symlink: ' . $path);
                return true;
            }
        }

        if (is_link($path)) {
            Log::error('Failed to remove symlink: ' . $path);
            return false;
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
        $appsPath  = Path::getAppsPath();
        $binPath   = Path::getBinPath();
        $toolsPath = Path::getToolsPath();

        $array = [
            self::APACHE_SYMLINK => $binPath . '/apache/current',
            self::BRUNO_SYMLINK => $toolsPath . '/bruno/current',
            self::COMPOSER_SYMLINK => $toolsPath . '/composer/current',
            self::GHOSTSCRIPT_SYMLINK => $toolsPath . '/ghostscript/current',
            self::GIT_SYMLINK => $toolsPath . '/git/current',
            self::MAILPIT_SYMLINK => $binPath . '/mailpit/current',
            self::MARIADB_SYMLINK => $binPath . '/mariadb/current',
            self::MEMCACHED_SYMLINK => $binPath . '/memcached/current',
            self::MYSQL_SYMLINK => $binPath . '/mysql/current',
            self::NGROK_SYMLINK => $toolsPath . '/ngrok/current',
            self::NODEJS_SYMLINK => $binPath . '/nodejs/current',
            self::PERL_SYMLINK => $toolsPath . '/perl/current',
            self::PHP_SYMLINK => $binPath . '/php/current',
            self::PHPMYADMIN_SYMLINK => $appsPath . '/phpmyadmin/current',
            self::PHPPGADMIN_SYMLINK => $appsPath . '/phppgadmin/current',
            self::POSTGRESQL_SYMLINK => $binPath . '/postgresql/current',
            self::POWERSHELL_SYMLINK => $toolsPath . '/powershell/current',
            self::PYTHON_SYMLINK => $toolsPath . '/python/current',
            self::RUBY_SYMLINK => $toolsPath . '/ruby/current',
            self::XLIGHT_SYMLINK => $binPath . '/xlight/current',
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
                // Log that the symlink was already missing
                Log::trace('Symlink already deleted or missing: ' . $path);
                continue;
            }

            // Use safe removal method with path validation and non-recursive guarantees
            self::safeRemoveSymlink($path);
        }
    }
}
