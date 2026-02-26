<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionDebugMysql
 *
 * This class handles the debugging actions for MySQL within the Bearsampp application.
 * It executes specific MySQL commands and displays the output in a message box or editor.
 */
class ActionDebugMysql extends ActionDebugBase
{
    /**
     * Get the service name for language strings
     *
     * @return string The language constant name for MySQL
     */
    protected function getServiceLangConstant()
    {
        return 'MYSQL';
    }

    /**
     * Get the MySQL binary instance
     *
     * @param object $bearsamppBins The bins object containing all service binaries
     * @return BinMysql The MySQL binary instance
     */
    protected function getBinInstance($bearsamppBins)
    {
        return $bearsamppBins->getMysql();
    }

    /**
     * Get the command-to-caption mapping for MySQL
     *
     * @return array Command mapping configuration
     */
    protected function getCommandMapping()
    {
        return [
            BinMysql::CMD_VERSION => [
                'lang' => Lang::DEBUG_MYSQL_VERSION,
                'editor' => false
            ],
            BinMysql::CMD_VARIABLES => [
                'lang' => Lang::DEBUG_MYSQL_VARIABLES,
                'editor' => true
            ],
            BinMysql::CMD_SYNTAX_CHECK => [
                'lang' => Lang::DEBUG_MYSQL_SYNTAX_CHECK,
                'editor' => false,
                'syntaxCheck' => true
            ]
        ];
    }
}
