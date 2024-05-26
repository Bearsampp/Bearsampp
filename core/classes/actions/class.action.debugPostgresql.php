<?php

class ActionDebugPostgresql
{
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
