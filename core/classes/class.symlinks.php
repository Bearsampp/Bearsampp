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
    const CMDER_SYMLINK = 'cmder';
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
     * Deletes all symbolic links listed in the arrayOfCurrents.
     * Logs each operation's success or failure.
     *
     * This method iterates over a predefined list of symbolic link paths and attempts to delete each one.
     * If a symbolic link does not exist, an error is logged. If the deletion is successful, a debug message is logged.
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
            self::CMDER_SYMLINK => $toolsPath . '/cmder/current',
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
            Util::logError('Current symlinks array is not initialized or empty.');
            return;
        }

        // Purge "current" symlinks
        foreach ($array as $name => $path) {
            // Skip if path is null
            if (empty($path)) {
                continue;
            }
            
            if (!file_exists($path)) {
                // Skip if the symlink doesn't exist - no need to log an error
                continue;
            }
            
            // Simple approach: use rmdir for directories and unlink for files
            if (is_dir($path)) {
                if (@rmdir($path)) {
                    Util::logDebug('Deleted directory symlink: ' . $path);
                }
            } else {
                if (@unlink($path)) {
                    Util::logDebug('Deleted file symlink: ' . $path);
                }
            }
        }
    }
}
