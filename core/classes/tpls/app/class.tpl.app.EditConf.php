<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppEditConf
 *
 * This class provides a method to generate a menu item for editing the Bearsampp
 * configuration file within the Bearsampp application.
 */
class TplAppEditConf
{
    /**
     * Processes the request to edit the configuration file.
     *
     * This method generates a Notepad item string that allows the user to edit the `bearsampp.conf` file.
     * It utilizes global variables to access language settings and the root path of the application.
     *
     * @return string The Notepad item string for editing the configuration file.
     * @global Root     $bearsamppRoot The root object for accessing application paths.
     * @global LangProc $bearsamppLang The language processor for retrieving localized strings.
     */
    public static function process()
    {
        global $bearsamppLang, $bearsamppRoot;

        return TplAestan::getItemNotepad(
                sprintf($bearsamppLang->getValue(Lang::MENU_EDIT_CONF), "bearsampp.conf"),
                Path::getConfigFilePath()
            ) . PHP_EOL;
    }
}

