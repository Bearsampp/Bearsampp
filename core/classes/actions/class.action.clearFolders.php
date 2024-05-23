<?php
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

        // Clear specific temporary folders in the root temporary path, excluding some essential items.
        Util::clearFolder($bearsamppRoot->getTmpPath(), array('cachegrind', 'composer', 'openssl', 'mailhog', 'npm-cache', 'pip', 'yarn', '.gitignore'));

        // Clear the core temporary path, excluding the .gitignore file.
        Util::clearFolder($bearsamppCore->getTmpPath(), array('.gitignore'));
    }
}
