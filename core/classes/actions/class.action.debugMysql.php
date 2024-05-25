<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class ActionDebugMysql
{
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $editor = false;
            $msgBoxError = false;
            $caption = $bearsamppLang->getValue(Lang::DEBUG) . ' ' . $bearsamppLang->getValue(Lang::MYSQL) . ' - ';
            if ($args[0] == BinMysql::CMD_VERSION) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MYSQL_VERSION);
            } elseif ($args[0] == BinMysql::CMD_VARIABLES) {
                $editor = true;
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MYSQL_VARIABLES);
            } elseif ($args[0] == BinMysql::CMD_SYNTAX_CHECK) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MYSQL_SYNTAX_CHECK);
            }
            $caption .= ' (' . $args[0] . ')';

            $debugOutput = $bearsamppBins->getMysql()->getCmdLineOutput($args[0]);

            if ($args[0] == BinMysql::CMD_SYNTAX_CHECK) {
                $msgBoxError = !$debugOutput['syntaxOk'];
                $debugOutput['content'] = $debugOutput['syntaxOk'] ? 'Syntax OK !' : $debugOutput['content'];
            }

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
