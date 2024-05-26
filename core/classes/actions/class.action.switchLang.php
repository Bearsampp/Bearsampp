<?php

class ActionSwitchLang
{
    public function __construct($args)
    {
        global $bearsamppConfig;

        if (isset($args[0]) && !empty($args[0])) {
            $bearsamppConfig->replace(Config::CFG_LANG, $args[0]);
        }
    }
}
