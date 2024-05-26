<?php

class TplAppPython
{
    const MENU = 'python';

    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::PYTHON), self::MENU, get_called_class());
    }

    public static function getMenuPython()
    {
        global $bearsamppLang, $bearsamppTools;

        $resultItems = TplAestan::getItemConsoleZ(
            $bearsamppLang->getValue(Lang::PYTHON_CONSOLE),
            TplAestan::GLYPH_PYTHON,
            $bearsamppTools->getConsoleZ()->getTabTitlePython()
        ) . PHP_EOL;

        $resultItems .= TplAestan::getItemExe(
            $bearsamppLang->getValue(Lang::PYTHON) . ' IDLE',
            $bearsamppTools->getPython()->getIdleExe(),
            TplAestan::GLYPH_PYTHON
        ) . PHP_EOL;

        $resultItems .= TplAestan::getItemExe(
            $bearsamppLang->getValue(Lang::PYTHON_CP),
            $bearsamppTools->getPython()->getCpExe(),
            TplAestan::GLYPH_PYTHON_CP
        ) . PHP_EOL;

        return $resultItems;
    }
}
