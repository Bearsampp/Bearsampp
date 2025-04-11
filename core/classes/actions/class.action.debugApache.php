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
 * Class ActionDebugApache
 *
 * This class handles the debugging of Apache configurations and settings.
 * It retrieves various Apache debug information based on the provided arguments
 * and displays the information in a message box or editor.
 */
class ActionDebugApache
{
    /**
     * Constructor for ActionDebugApache.
     *
     * @param array $args An array of arguments specifying the type of Apache debug information to retrieve.
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $editor = false;
            $msgBoxError = false;
            $caption = $bearsamppLang->getValue(Lang::DEBUG) . ' ' . $bearsamppLang->getValue(Lang::APACHE) . ' - ';

            // Determine the type of debug information requested and set the caption accordingly
            if ($args[0] == BinApache::CMD_VERSION_NUMBER) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_APACHE_VERSION_NUMBER);
            } elseif ($args[0] == BinApache::CMD_COMPILE_SETTINGS) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_APACHE_COMPILE_SETTINGS);
            } elseif ($args[0] == BinApache::CMD_COMPILED_MODULES) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_APACHE_COMPILED_MODULES);
            } elseif ($args[0] == BinApache::CMD_CONFIG_DIRECTIVES) {
                $editor = true;
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_APACHE_CONFIG_DIRECTIVES);
            } elseif ($args[0] == BinApache::CMD_VHOSTS_SETTINGS) {
                $editor = true;
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_APACHE_VHOSTS_SETTINGS);
            } elseif ($args[0] == BinApache::CMD_LOADED_MODULES) {
                $editor = true;
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_APACHE_LOADED_MODULES);
            } elseif ($args[0] == BinApache::CMD_SYNTAX_CHECK) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_APACHE_SYNTAX_CHECK);
            }
            $caption .= ' (' . $args[0] . ')';

            // Retrieve the debug output from Apache
            $debugOutput = $bearsamppBins->getApache()->getCmdLineOutput($args[0]);

            // Handle syntax check specifically
            if ($args[0] == BinApache::CMD_SYNTAX_CHECK) {
                $msgBoxError = !$debugOutput['syntaxOk'];
                $debugOutput['content'] = $debugOutput['syntaxOk'] ? 'Syntax OK !' : $debugOutput['content'];
            }

            // Display the debug output in an editor or message box
            if ($editor) {
                Util::openFileContent($caption, $debugOutput['content']);
            } else {
                if ($msgBoxError) {
                    $bearsamppWinbinder->messageBoxError(
                        $debugOutput['content'],
                        $caption
                    );
                } else {
                    $bearsamppWinbinder->messageBoxInfo(
                        $debugOutput['content'],
                        $caption
                    );
                }
            }
        }
    }
}
