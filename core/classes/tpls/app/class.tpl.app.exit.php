<?php

class TplAppExit
{
    const ACTION = 'exit';

    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getActionMulti(
            self::ACTION, null,
            array($bearsamppLang->getValue(Lang::QUIT), TplAestan::GLYPH_EXIT),
            false, get_called_class()
        );
    }

    public static function getActionExit()
    {
        return TplApp::getActionRun(Action::QUIT) . PHP_EOL . 'Action: exit';
    }
}
