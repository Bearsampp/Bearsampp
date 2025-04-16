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
class ActionDebugMariadb
{
    /**
     * ActionDebugMariadb constructor.
     *
     * @param array $args Command-line arguments specifying the debugging action to perform.
     *
     * This constructor initializes the debugging process for MariaDB based on the provided arguments.
     * It supports three types of debugging actions: version check, variables display, and syntax check.
     * The output of the debugging action is displayed either in an editor or a message box.
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $editor = false;
            $msgBoxError = false;
            $caption = $bearsamppLang->getValue(Lang::DEBUG) . ' ' . $bearsamppLang->getValue(Lang::MARIADB) . ' - ';

            // Determine the type of debugging action based on the first argument
            if ($args[0] == BinMariadb::CMD_VERSION) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MARIADB_VERSION);
            } elseif ($args[0] == BinMariadb::CMD_VARIABLES) {
                $editor = true;
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MARIADB_VARIABLES);
            } elseif ($args[0] == BinMariadb::CMD_SYNTAX_CHECK) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MARIADB_SYNTAX_CHECK);
            }
            $caption .= ' (' . $args[0] . ')';

            // Retrieve the command line output for the specified debugging action
            $debugOutput = $bearsamppBins->getMariadb()->getCmdLineOutput($args[0]);

            // Handle syntax check results
            if ($args[0] == BinMariadb::CMD_SYNTAX_CHECK) {
                $msgBoxError = !$debugOutput['syntaxOk'];
                $debugOutput['content'] = $debugOutput['syntaxOk'] ? 'Syntax OK !' : $debugOutput['content'];
            }

            // Display the debugging output
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
