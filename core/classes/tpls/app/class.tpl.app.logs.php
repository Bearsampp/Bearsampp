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
 * Class TplAppLogs
 *
 * This class provides methods to generate and manage the logs menu within the Bearsampp application.
 * It includes functionalities for creating the logs menu and listing log files.
 */
class TplAppLogs
{
    // Constant for the logs menu identifier
    const MENU = 'logs';

    /**
     * Processes and generates the logs menu.
     *
     * This method generates the logs menu by retrieving the localized string for logs
     * and calling the getMenu method from the TplApp class.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return array The generated logs menu as a string.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::LOGS), self::MENU, get_called_class());
    }

    /**
     * Generates the logs menu content.
     *
     * This method retrieves the list of log files from the logs directory, sorts them,
     * and generates menu items for each log file using the getItemNotepad method from the TplAestan class.
     *
     * @global object $bearsamppRoot Provides access to the root directory of the application.
     *
     * @return string The generated logs menu content as a string.
     */
    public static function getMenuLogs()
    {
        global $bearsamppRoot;

        $files = array();

        // Open the logs directory
        $handle = @opendir($bearsamppRoot->getLogsPath());
        if (!$handle) {
            return '';
        }

        // Read log files from the directory
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && Util::endWith($file, '.log')) {
                $files[] = $file;
            }
        }

        // Close the directory handle
        closedir($handle);
        ksort($files);

        // Generate menu items for each log file
        $result = '';
        foreach ($files as $file) {
            $result .= TplAestan::getItemNotepad(basename($file), $bearsamppRoot->getLogsPath() . '/' . $file) . PHP_EOL;
        }
        return $result;
    }
}
