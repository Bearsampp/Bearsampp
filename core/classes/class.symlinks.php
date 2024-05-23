<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Manages the creation and deletion of symbolic links for various components within the Bearsampp environment.
 */
class Symlinks
{
    /**
     * @var Root The root object providing access to system paths.
     */
    private $root;

    /**
     * Constructs a Symlinks object and initializes paths to current directories.
     *
     * @param   Root  $root  The root object associated with the Bearsampp environment.
     */
    public function __construct($root)
    {
        $this->root = $root;
        $this->initializePaths();
    }

    /**
     * Deletes all symbolic links listed in the arrayOfCurrents.
     * Logs each operation's success or failure.
     */
    public static function deleteCurrentSymlinks()
    {
        global $bearsamppRoot, $bearsamppCore;

        // Check to see if purging is necessary
        $appsPath  = $bearsamppRoot->getAppsPath();
        $binPath   = $bearsamppRoot->getBinPath();
        $toolsPath = $bearsamppRoot->getToolsPath();

        $array = [
            '1'  => Util::formatWindowsPath( $appsPath . '/adminer/current' ),
            '2'  => Util::formatWindowsPath( $appsPath . '/phpmyadmin/current' ),
            '3'  => Util::formatWindowsPath( $appsPath . '/phppgadmin/current' ),
            '4'  => Util::formatWindowsPath( $appsPath . '/webgrind/current' ),
            '5'  => Util::formatWindowsPath( $binPath . '/apache/current' ),
            '6'  => Util::formatWindowsPath( $binPath . '/filezilla/current' ),
            '7'  => Util::formatWindowsPath( $binPath . '/mailhog/current' ),
            '8'  => Util::formatWindowsPath( $binPath . '/mariadb/current' ),
            '9'  => Util::formatWindowsPath( $binPath . '/memcached/current' ),
            '10' => Util::formatWindowsPath( $binPath . '/mysql/current' ),
            '11' => Util::formatWindowsPath( $binPath . '/nodejs/current' ),
            '12' => Util::formatWindowsPath( $binPath . '/php/current' ),
            '13' => Util::formatWindowsPath( $binPath . '/postgresql/current' ),
            '14' => Util::formatWindowsPath( $toolsPath . '/composer/current' ),
            '15' => Util::formatWindowsPath( $toolsPath . '/consolez/current' ),
            '16' => Util::formatWindowsPath( $toolsPath . '/ghostscript/current' ),
            '17' => Util::formatWindowsPath( $toolsPath . '/git/current' ),
            '18' => Util::formatWindowsPath( $toolsPath . '/ngrok/current' ),
            '19' => Util::formatWindowsPath( $toolsPath . '/perl/current' ),
            '20' => Util::formatWindowsPath( $toolsPath . '/python/current' ),
            '21' => Util::formatWindowsPath( $toolsPath . '/ruby/current' ),
            '22' => Util::formatWindowsPath( $toolsPath . '/xdc/current' ),
            '23' => Util::formatWindowsPath( $toolsPath . '/yarn/current' )
        ];

        if ( !is_array( $array ) || empty( $array ) ) {
            Util::logError( 'Current symlinks array is not initialized or empty.' );

            return;
        }

        // purge "current" symlinks
        foreach ( $array as $startPath ) {
            if ( !file_exists( $startPath ) ) {
                Util::logError( 'Symlink does not exist: ' . $startPath );
                continue;
            }
            else {
                if ( @Batch::removeSymlink( $startPath ) ) {
                    Util::logDebug( 'Deleted: ' . $startPath );
                }
            }
        }
    }
}
