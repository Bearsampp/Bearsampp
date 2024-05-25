<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Manages configuration settings for the Bearsampp application.
 *
 * This class provides methods to access and manipulate configuration settings stored in an INI file.
 * It supports fetching individual configuration values, updating them, and handling specific application
 * settings like language, timezone, and system behaviors.
 *
 * Usage:
 * - Access configuration values using get methods like getLang(), getTimezone(), etc.
 * - Update configuration values using replace() or replaceAll() methods.
 *
 * @package Bearsampp\Config
 */
class Config
{
    // Constants definitions...

    /**
     * @var array Holds the configuration values after parsing the INI file.
     */
    const CFG_MAX_LOGS_ARCHIVES = 'maxLogsArchives';
    const CFG_LOGS_VERBOSE = 'logsVerbose';
    const CFG_LANG = 'lang';
    const CFG_TIMEZONE = 'timezone';
    const CFG_NOTEPAD = 'notepad';
    const CFG_SCRIPTS_TIMEOUT = 'scriptsTimeout';

    const CFG_DEFAULT_LANG = 'defaultLang';
    const CFG_HOSTNAME = 'hostname';
    const CFG_BROWSER = 'browser';
    const CFG_ONLINE = 'online';
    const CFG_LAUNCH_STARTUP = 'launchStartup';

    const ENABLED = 1;
    const DISABLED = 0;

    const VERBOSE_SIMPLE = 0;
    const VERBOSE_REPORT = 1;
    const VERBOSE_DEBUG = 2;
    const VERBOSE_TRACE = 3;

    private $raw;

    /**
     * Constructor for the Config class.
     * Initializes the configuration by loading settings from an INI file.
     */
    public function __construct()
    {
        global $bearsamppRoot;

        $this->raw = parse_ini_file($bearsamppRoot->getConfigFilePath());
        /*if (!$bearsamppRoot->isRoot()) {
            $this->raw[self::CFG_LOGS_VERBOSE] = 0;
        }*/
// TODO set to use TZ from config see https://github.com/Bearsampp/.teams/issues/24
        date_default_timezone_set($this->getTimezone());
    }

    /**
     * Retrieves a configuration value by key.
     *
     * @param string $key The key of the configuration item.
     * @return mixed The value of the configuration item.
     */
    public function getRaw($key)
    {
        return $this->raw[$key];
    }

    /**
     * Replaces a single configuration value.
     *
     * @param string $key The configuration key to replace.
     * @param mixed $value The new value for the configuration key.
     */
    public function replace($key, $value)
    {
        $this->replaceAll(array($key => $value));
    }

    /**
     * Replaces multiple configuration values.
     *
     * @param array $params An associative array of configuration keys and their new values.
     */
    public function replaceAll($params)
    {
        global $bearsamppRoot;

        Util::logTrace('Replace config:');
        $content = file_get_contents($bearsamppRoot->getConfigFilePath());
        foreach ($params as $key => $value) {
            $content = preg_replace('/^' . $key . '\s=\s.*/m', $key . ' = ' . '"' . $value.'"', $content, -1, $count);
            Util::logTrace('## ' . $key . ': ' . $value . ' (' . $count . ' replacements done)');
            $this->raw[$key] = $value;
        }

        file_put_contents($bearsamppRoot->getConfigFilePath(), $content);
    }

    /**
     * Gets the current language setting.
     *
     * @return string The current language.
     */
    public function getLang()
    {
        return $this->raw[self::CFG_LANG];
    }

    /**
     * Gets the default language setting.
     *
     * @return string The default language.
     */
    public function getDefaultLang()
    {
        return $this->raw[self::CFG_DEFAULT_LANG];
    }

    /**
     * Gets the configured timezone.
     *
     * @return string The timezone setting.
     */
    public function getTimezone()
    {
        return $this->raw[self::CFG_TIMEZONE];
    }

    /**
     * Checks if the online mode is enabled.
     *
     * @return bool True if online mode is enabled, false otherwise.
     */
    public function isOnline()
    {
        return $this->raw[self::CFG_ONLINE] == self::ENABLED;
    }

    /**
     * Checks if the application is set to launch at startup.
     *
     * @return bool True if set to launch at startup, false otherwise.
     */
    public function isLaunchStartup()
    {
        return $this->raw[self::CFG_LAUNCH_STARTUP] == self::ENABLED;
    }

    /**
     * Gets the configured browser.
     *
     * @return string The browser setting.
     */
    public function getBrowser()
    {
        return $this->raw[self::CFG_BROWSER];
    }

    /**
     * Gets the configured hostname.
     *
     * @return string The hostname setting.
     */
    public function getHostname()
    {
        return $this->raw[self::CFG_HOSTNAME];
    }

    /**
     * Gets the configured script timeout value.
     *
     * @return int The script timeout in seconds.
     */
    public function getScriptsTimeout()
    {
        return intval($this->raw[self::CFG_SCRIPTS_TIMEOUT]);
    }

    /**
     * Gets the configured notepad application.
     *
     * @return string The notepad setting.
     */
    public function getNotepad()
    {
        return $this->raw[self::CFG_NOTEPAD];
    }

    /**
     * Gets the verbosity level for logs.
     *
     * @return int The verbosity level.
     */
    public function getLogsVerbose()
    {
        return intval($this->raw[self::CFG_LOGS_VERBOSE]);
    }

    /**
     * Gets the maximum number of log archives.
     *
     * @return int The maximum number of log archives.
     */
    public function getMaxLogsArchives()
    {
        return intval($this->raw[self::CFG_MAX_LOGS_ARCHIVES]);
    }
}
