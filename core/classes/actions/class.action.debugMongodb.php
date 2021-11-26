<?php

class ActionDebugMongodb
{
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $editor = false;
            $msgBoxError = false;
            $caption = $bearsamppLang->getValue(Lang::DEBUG) . ' ' . $bearsamppLang->getValue(Lang::MONGODB) . ' - ';
            if ($args[0] == BinMongodb::CMD_VERSION) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_MONGODB_VERSION);
            }
            $caption .= ' (' . $args[0] . ')';

            $debugOutput = $bearsamppBins->getMongodb()->getCmdLineOutput($args[0]);

            if ($editor) {
                Util::openFileContent($caption, $debugOutput['content']);
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
