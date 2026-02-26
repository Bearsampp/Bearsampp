<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionDebugApache
 *
 * This class handles the debugging of Apache configurations and settings.
 * It retrieves various Apache debug information based on the provided arguments
 * and displays the information in a message box or editor.
 */
class ActionDebugApache extends ActionDebugBase
{
    /**
     * Get the service name for language strings
     *
     * @return string The language constant name for Apache
     */
    protected function getServiceLangConstant()
    {
        return 'APACHE';
    }

    /**
     * Get the Apache binary instance
     *
     * @param object $bearsamppBins The bins object containing all service binaries
     * @return BinApache The Apache binary instance
     */
    protected function getBinInstance($bearsamppBins)
    {
        return $bearsamppBins->getApache();
    }

    /**
     * Get the command-to-caption mapping for Apache
     *
     * @return array Command mapping configuration
     */
    protected function getCommandMapping()
    {
        return [
            BinApache::CMD_VERSION_NUMBER => [
                'lang' => Lang::DEBUG_APACHE_VERSION_NUMBER,
                'editor' => false
            ],
            BinApache::CMD_COMPILE_SETTINGS => [
                'lang' => Lang::DEBUG_APACHE_COMPILE_SETTINGS,
                'editor' => false
            ],
            BinApache::CMD_COMPILED_MODULES => [
                'lang' => Lang::DEBUG_APACHE_COMPILED_MODULES,
                'editor' => false
            ],
            BinApache::CMD_CONFIG_DIRECTIVES => [
                'lang' => Lang::DEBUG_APACHE_CONFIG_DIRECTIVES,
                'editor' => true
            ],
            BinApache::CMD_VHOSTS_SETTINGS => [
                'lang' => Lang::DEBUG_APACHE_VHOSTS_SETTINGS,
                'editor' => true
            ],
            BinApache::CMD_LOADED_MODULES => [
                'lang' => Lang::DEBUG_APACHE_LOADED_MODULES,
                'editor' => true
            ],
            BinApache::CMD_SYNTAX_CHECK => [
                'lang' => Lang::DEBUG_APACHE_SYNTAX_CHECK,
                'editor' => false,
                'syntaxCheck' => true
            ]
        ];
    }
}
