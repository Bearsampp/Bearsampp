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
class ActionDebugMysql
{
    /**
     * Constructor for ActionDebugMysql.
     *
     * @param array $args An array of arguments specifying the MySQL command to execute.
     *
     * This constructor initializes the debugging process for MySQL based on the provided arguments.
     * It supports commands for retrieving the MySQL version, variables, and performing a syntax check.
     * The output is displayed in a message box or editor based on the command.
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $editor = false;
            $msgBoxError = false;
            $caption = $bearsamppLang->getValue(Lang::DEBUG) . ' ' . $bearsamppLang->getValue(Lang::MYSQL) . ' - ';

            // Determine the command and set the caption accordingly
            if ($args[0] == BinMysql::CMD_VERSION) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MYSQL_VERSION);
            } elseif ($args[0] == BinMysql::CMD_VARIABLES) {
                $editor = true;
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MYSQL_VARIABLES);
            } elseif ($args[0] == BinMysql::CMD_SYNTAX_CHECK) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MYSQL_SYNTAX_CHECK);
            }
            $caption .= ' (' . $args[0] . ')';

            // Execute the MySQL command and get the output
            $debugOutput = $bearsamppBins->getMysql()->getCmdLineOutput($args[0]);

            // Handle syntax check results
            if ($args[0] == BinMysql::CMD_SYNTAX_CHECK) {
                $msgBoxError = !$debugOutput['syntaxOk'];
                $debugOutput['content'] = $debugOutput['syntaxOk'] ? 'Syntax OK !' : $debugOutput['content'];
            }

            // Display the output in an editor or message box
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
