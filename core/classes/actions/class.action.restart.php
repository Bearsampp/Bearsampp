<?php

class ActionRestart
{
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        $bearsamppWinbinder->messageBoxInfo(
            sprintf($bearsamppLang->getValue(Lang::RESTART_TEXT), APP_TITLE),
            $bearsamppLang->getValue(Lang::RESTART_TITLE));
    }
}
