<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Handles the action of clearing specific temporary folders within the application.
 *
 * This class is responsible for clearing out temporary files and directories that are not
 * essential for immediate functionality but may accumulate over time and use disk space.
 * It targets temporary directories used by various components like Composer, OpenSSL, and others.
 */
class ActionClearFolders
{
    /**
     * Constructor for the ActionClearFolders class.
     *
     * Upon instantiation, it clears specified temporary folders in both the root and core temporary paths.
     * It excludes certain files and folders from being deleted to prevent essential data loss.
     *
     * @param array $args Arguments that might be used for further extension of constructor functionality.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppCore;

        /**
         * Clears specific temporary folders in the root temporary path.
         *
         * Util::clearFolder is used to clear the contents of the root temporary path, excluding
         * certain essential items such as 'cachegrind', 'composer', 'openssl', 'mailpit', 'xlight', 'npm-cache',
         * 'pip' and '.gitignore'. This ensures that important data and configurations are not lost.
         *
         * @param string $bearsamppRoot->getTmpPath() The root temporary path to be cleared.
         * @param array $exclusions List of folders and files to be excluded from deletion.
         */
        Util::clearFolder($bearsamppRoot->getTmpPath(), array('cachegrind', 'composer', 'openssl', 'mailpit', 'xlight', 'npm-cache', 'pip', '.gitignore'));

        // Clear logs
        Util::clearFolder($bearsamppRoot->getLogsPath(), array('mailpit.err.log', 'mailpit.out.log', 'memcached.err.log', 'memcached.out.log', 'xlight.err.log', 'xlight.log', '.gitignore') );

        /**
         * Clears the core temporary path.
         *
         * Util::clearFolder is used to clear the contents of the core temporary path, excluding
         * the '.gitignore' file. This ensures that the core temporary path is cleaned without
         * removing the '.gitignore' file which might be necessary for version control.
         *
         * @param string $bearsamppCore->getTmpPath() The core temporary path to be cleared.
         * @param array $exclusions List of folders and files to be excluded from deletion.
         */
        Util::clearFolder($bearsamppCore->getTmpPath(), array('.gitignore'));
    }
}
