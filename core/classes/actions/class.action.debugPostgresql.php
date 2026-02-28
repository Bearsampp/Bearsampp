<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionDebugPostgresql
 *
 * This class handles the debugging actions for PostgreSQL within the Bearsampp application.
 * It retrieves and displays PostgreSQL command line output based on the provided arguments.
 */
class ActionDebugPostgresql extends ActionDebugBase
{
    /**
     * Get the service name for language strings
     *
     * @return string The language constant name for PostgreSQL
     */
    protected function getServiceLangConstant()
    {
        return 'POSTGRESQL';
    }

    /**
     * Get the PostgreSQL binary instance
     *
     * @param object $bearsamppBins The bins object containing all service binaries
     * @return BinPostgresql The PostgreSQL binary instance
     */
    protected function getBinInstance($bearsamppBins)
    {
        return $bearsamppBins->getPostgresql();
    }

    /**
     * Get the command-to-caption mapping for PostgreSQL
     *
     * @return array Command mapping configuration
     */
    protected function getCommandMapping()
    {
        return [
            BinPostgresql::CMD_VERSION => [
                'lang' => Lang::DEBUG_POSTGRESQL_VERSION,
                'editor' => false
            ]
        ];
    }

    /**
     * PostgreSQL returns output as a direct string, not an array with 'content' key
     *
     * @return bool False to indicate direct string output
     */
    protected function hasContentKey()
    {
        return false;
    }
}
