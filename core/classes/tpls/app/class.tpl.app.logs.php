<?php

class TplAppLogs
{
    const MENU = 'logs';

    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::LOGS), self::MENU, get_called_class());
    }

    public static function getMenuLogs()
    {
        global $bearsamppBs;

        $files = array();

        $handle = @opendir($bearsamppBs->getLogsPath());
        if (!$handle) {
            return '';
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && Util::endWith($file, '.log')) {
                $files[] = $file;
            }
        }

        closedir($handle);
        ksort($files);

        $result = '';
        foreach ($files as $file) {
            $result .= TplAestan::getItemNotepad(basename($file), $bearsamppBs->getLogsPath() . '/' . $file) . PHP_EOL;
        }
        return $result;
    }
}
