<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionDebugMariadb
 *
 * This class handles debugging actions for MariaDB within the Bearsampp application.
 * It processes command-line arguments to determine the type of debugging action to perform,
 * retrieves the corresponding output from MariaDB, and displays it using the appropriate method.
 */
class ActionDebugMariadb extends ActionDebugBase
{
    /**
     * Get the service name for language strings
     *
     * @return string The language constant name for MariaDB
     */
    protected function getServiceLangConstant()
    {
        return 'MARIADB';
    }

    /**
     * Get the MariaDB binary instance
     *
     * @param object $bearsamppBins The bins object containing all service binaries
     * @return BinMariadb The MariaDB binary instance
     */
    protected function getBinInstance($bearsamppBins)
    {
        return $bearsamppBins->getMariadb();
    }

    /**
     * Get the command-to-caption mapping for MariaDB
     *
     * @return array Command mapping configuration
     */
    protected function getCommandMapping()
    {
        return [
            BinMariadb::CMD_VERSION => [
                'lang' => Lang::DEBUG_MARIADB_VERSION,
                'editor' => false
            ],
            BinMariadb::CMD_VARIABLES => [
                'lang' => Lang::DEBUG_MARIADB_VARIABLES,
                'editor' => true
            ],
            BinMariadb::CMD_SYNTAX_CHECK => [
                'lang' => Lang::DEBUG_MARIADB_SYNTAX_CHECK,
                'editor' => false,
                'syntaxCheck' => true
            ]
        ];
    }
}
