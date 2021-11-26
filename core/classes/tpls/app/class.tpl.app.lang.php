<?php

class TplAppLang
{
    const MENU = 'lang';

    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::LANG), self::MENU, get_called_class());
    }

    public static function getMenuLang()
    {
        global $bearsamppLang;
        $items = '';
        $actions = '';

        foreach ($bearsamppLang->getList() as $lang) {
            $tplSwitchLang = TplApp::getActionMulti(
                Action::SWITCH_LANG, array($lang),
                array(ucfirst($lang), $lang == $bearsamppLang->getCurrent() ? TplAestan::GLYPH_CHECK : ''),
                false, get_called_class()
            );

            // Item
            $items .= $tplSwitchLang[TplApp::SECTION_CALL] . PHP_EOL;

            // Action
            $actions .= PHP_EOL . $tplSwitchLang[TplApp::SECTION_CONTENT] .  PHP_EOL;
        }

        return $items . $actions;
    }

    public static function getActionSwitchLang($lang)
    {
        return TplApp::getActionRun(Action::SWITCH_LANG, array($lang)) . PHP_EOL .
            TplAppReload::getActionReload();
    }
}
