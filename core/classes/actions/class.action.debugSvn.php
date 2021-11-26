<?php

class ActionDebugSvn
{
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $editor = false;
            $msgBoxError = false;
            $caption = $bearsamppLang->getValue(Lang::DEBUG) . ' ' . $bearsamppLang->getValue(Lang::SVN) . ' - ';
            if ($args[0] == BinSvn::CMD_VERSION) {
                $caption .= $bearsamppLang->getValue(Lang::DEBUG_SVN_VERSION);
            }
            $caption .= ' (' . $args[0] . ')';

            $debugOutput = $bearsamppBins->getSvn()->getCmdLineOutput($args[0]);

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
