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
class ActionDebugPostgresql
{
    /**
     * Constructor for ActionDebugPostgresql.
     *
     * @param array $args An array of arguments where the first element is expected to be a PostgreSQL command.
     *
     * This constructor initializes the debugging process for PostgreSQL. It checks the provided arguments,
     * retrieves the command line output for the specified PostgreSQL command, and displays it in a message box.
     *
     * Global variables used:
     * - $bearsamppLang: Provides language-specific strings.
     * - $bearsamppBins: Provides access to Bearsampp binaries, including PostgreSQL.
     * - $bearsamppWinbinder: Handles the display of message boxes.
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $editor = false;
            $msgBoxError = false;
            $caption = $bearsamppLang->getValue(Lang::DEBUG) . ' ' . $bearsamppLang->getValue(Lang::POSTGRESQL) . ' - ';
            if ($args[0] == BinPostgresql::CMD_VERSION) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_POSTGRESQL_VERSION);
            }
            $caption .= ' (' . $args[0] . ')';

            $debugOutput = $bearsamppBins->getPostgresql()->getCmdLineOutput($args[0]);

            if ($editor) {
                Util::openFileContent($caption, $debugOutput);
            } else {
                if ($msgBoxError) {
                    $bearsamppWinbinder->messageBoxError(
                        $debugOutput,
                        $caption
                    );
                } else {
                    $bearsamppWinbinder->messageBoxInfo(
                        $debugOutput,
                        $caption
                    );
                }
            }
        }
    }
}
