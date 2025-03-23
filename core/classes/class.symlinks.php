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
    const ADMINER_SYMLINK = 'adminer';
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
    const CONSOLEZ_SYMLINK = 'consolez';
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
            self::ADMINER_SYMLINK => Util::formatWindowsPath($appsPath . '/adminer/current'),
            self::PHPMYADMIN_SYMLINK => Util::formatWindowsPath($appsPath . '/phpmyadmin/current'),
            self::PHPPGADMIN_SYMLINK => Util::formatWindowsPath($appsPath . '/phppgadmin/current'),
            self::APACHE_SYMLINK => Util::formatWindowsPath($binPath . '/apache/current'),
            self::MARIADB_SYMLINK => Util::formatWindowsPath($binPath . '/mariadb/current'),
            self::MEMCACHED_SYMLINK => Util::formatWindowsPath($binPath . '/memcached/current'),
            self::MYSQL_SYMLINK => Util::formatWindowsPath($binPath . '/mysql/current'),
            self::NODEJS_SYMLINK => Util::formatWindowsPath($binPath . '/nodejs/current'),
            self::PHP_SYMLINK => Util::formatWindowsPath($binPath . '/php/current'),
            self::POSTGRESQL_SYMLINK => Util::formatWindowsPath($binPath . '/postgresql/current'),
            self::COMPOSER_SYMLINK => Util::formatWindowsPath($toolsPath . '/composer/current'),
            self::CONSOLEZ_SYMLINK => Util::formatWindowsPath($toolsPath . '/consolez/current'),
            self::GHOSTSCRIPT_SYMLINK => Util::formatWindowsPath($toolsPath . '/ghostscript/current'),
            self::GIT_SYMLINK => Util::formatWindowsPath($toolsPath . '/git/current'),
            self::NGROK_SYMLINK => Util::formatWindowsPath($toolsPath . '/ngrok/current'),
            self::PERL_SYMLINK => Util::formatWindowsPath($toolsPath . '/perl/current'),
            self::PYTHON_SYMLINK => Util::formatWindowsPath($toolsPath . '/python/current'),
            self::RUBY_SYMLINK => Util::formatWindowsPath($toolsPath . '/ruby/current'),
            self::XLIGHT_SYMLINK => Util::formatWindowsPath($binPath . '/xlight/current'),
            self::MAILPIT_SYMLINK => Util::formatWindowsPath($binPath . '/mailpit/current'),
            self::BRUNO_SYMLINK => Util::formatWindowsPath($binPath . '/bruno/current')
        ];

        if (!is_array($array) || empty($array)) {
            Util::logError('Current symlinks array is not initialized or empty.');
            return;
        }

        // Purge "current" symlinks
        foreach ($array as $startPath) {
            if (!file_exists($startPath)) {
                Util::logError('Symlink does not exist: ' . $startPath);
                continue;
            } else {
                try {
                    if (!Batch::removeSymlink($startPath)) {
                        Util::logError('Failed to remove symlink: ' . $startPath . ' - ' . error_get_last()['message']);
                    } else {
                        Util::logDebug('Deleted: ' . $startPath);
                    }
                } catch (Exception $e) {
                    Util::logError('Error removing symlink ' . $startPath . ': ' . $e->getMessage());
                }
            }
        }
    }
}
