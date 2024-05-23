<?php

class LangProc
{
    private $current;
    private $raw;

    public function __construct()
    {
        $this->load();
    }

    public function load()
    {
        global $bearsamppCore, $bearsamppConfig;
        $this->raw = null;

        $this->current = $bearsamppConfig->getDefaultLang();
        if (!empty($this->current) && in_array($this->current, $this->getList())) {
            $this->current = $bearsamppConfig->getLang();
        }

        $this->raw = parse_ini_file($bearsamppCore->getLangsPath() . '/' . $this->current . '.lang');
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getList()
    {
        global $bearsamppCore;
        $result = array();

        $handle = @opendir($bearsamppCore->getLangsPath());
        if (!$handle) {
            return $result;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && Util::endWith($file, '.lang')) {
                $result[] = str_replace('.lang', '', $file);
            }
        }

        closedir($handle);
        return $result;
    }

    public function getValue($key)
    {
        global $bearsamppRoot;

        if (!isset($this->raw[$key])) {
            $content = '[' . date('Y-m-d H:i:s', time()) . '] ';
            $content .= 'ERROR: Lang var missing ' . $key;
            $content .= ' for ' . $this->current . ' language.' . PHP_EOL;
            file_put_contents($bearsamppRoot->getErrorLogFilePath(), $content, FILE_APPEND);
            return $key;
        }

        // Special chars not handled by Aestan Tray Menu
        $replace = array("ő", "Ő", "ű", "Ű");
        $with = array("o", "O", "u", "U");

        return str_replace($replace, $with, $this->raw[$key]);
    }
}
