<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class LangProc
 *
 * This class handles the language processing for the Bearsampp application.
 * It loads language files, retrieves language settings, and provides language-specific values.
 */
class LangProc
{
    /**
     * @var string The current language being used.
     */
    private $current;

    /**
     * @var array The raw language data loaded from the language file.
     */
    private $raw;

    /**
     * LangProc constructor.
     *
     * Initializes the LangProc object and loads the language settings.
     */
    public function __construct()
    {
        $this->load();
    }

    /**
     * Loads the current language settings and data.
     *
     * This method retrieves the default language from the configuration,
     * checks if it is available, and then loads the corresponding language file.
     */
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

    /**
     * Gets the current language being used.
     *
     * @return string The current language.
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Retrieves the list of available languages.
     *
     * This method scans the language directory and returns a list of available language files.
     *
     * @return array The list of available languages.
     */
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

    /**
     * Retrieves the value for a given language key.
     *
     * This method returns the value associated with the specified key in the current language.
     * If the key is not found, it logs an error and returns the key itself.
     *
     * @param string $key The language key to retrieve the value for.
     * @return string The value associated with the key, or the key itself if not found.
     */
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
