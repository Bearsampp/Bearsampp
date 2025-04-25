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
 * Class Config
 *
 * This class handles the configuration settings for the Bearsampp application.
 * It reads the configuration from an INI file and provides methods to access and modify these settings.
 */
class Config
{
    const CFG_MAX_LOGS_ARCHIVES = 'maxLogsArchives';
    const CFG_LOGS_VERBOSE = 'logsVerbose';
    const CFG_LANG = 'lang';
    const CFG_TIMEZONE = 'timezone';
    const CFG_NOTEPAD = 'notepad';
    const CFG_SCRIPTS_TIMEOUT = 'scriptsTimeout';
    const DOWNLOAD_ID = 'DownloadId';
    const INCLUDE_PR = 'IncludePR';
    const INCLUDE_PR_CACHE_TIME = 'IncludePRCacheTime';

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
     * Constructs a Config object and initializes the configuration settings.
     * Reads the configuration from the INI file and sets the default timezone.
     */
    public function __construct()
    {
        global $bearsamppRoot;

        // Set current timezone to match whats in .conf
        $this->raw = parse_ini_file($bearsamppRoot->getConfigFilePath());
        date_default_timezone_set($this->getTimezone());
    }

    /**
     * Retrieves the raw configuration value for the specified key.
     *
     * @param string $key The configuration key.
     * @return mixed The configuration value.
     */
    public function getRaw($key)
    {
        return $this->raw[$key];
    }

    /**
     * Replaces a single configuration value with the specified key and value.
     *
     * @param string $key The configuration key.
     * @param mixed $value The new configuration value.
     */
    public function replace($key, $value)
    {
        $this->replaceAll(array($key => $value));
    }

    /**
     * Replaces multiple configuration values with the specified key-value pairs.
     *
     * @param array $params An associative array of key-value pairs to replace.
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
     * Retrieves the language setting from the configuration.
     *
     * @return string The language setting.
     */
    public function getLang()
    {
        return $this->raw[self::CFG_LANG];
    }

    /**
     * Retrieves the default language setting from the configuration.
     *
     * @return string The default language setting.
     */
    public function getDefaultLang()
    {
        return $this->raw[self::CFG_DEFAULT_LANG];
    }

    /**
     * Retrieves the timezone setting from the configuration.
     *
     * @return string The timezone setting.
     */
    public function getTimezone()
    {
        return $this->raw[self::CFG_TIMEZONE];
    }

    /**
     * Retrieves the license key from the configuration.
     *
     * @return string The license key.
     */
    public function getDownloadId()
    {
        return $this->raw[self::DOWNLOAD_ID];
    }

    /**
     * Retrieves the license key from the configuration.
     *
     * @return string The license key.
     */
    public function getIncludePr()
    {
        return $this->raw[self::INCLUDE_PR];
    }

    /**
     * Retrieves the IncludePRCacheTime value from the configuration.
     *
     * @return int The number of minutes to wait before reloading prerelease methods.
     */
    public function getIncludePrCacheTime()
    {
        return intval($this->raw[self::INCLUDE_PR_CACHE_TIME]);
    }

    /**
     * Checks if the application is set to be online.
     *
     * @return bool True if online, false otherwise.
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
     * Retrieves the browser setting from the configuration.
     *
     * @return string The browser setting.
     */
    public function getBrowser()
    {
        return $this->raw[self::CFG_BROWSER];
    }

    /**
     * Retrieves the hostname setting from the configuration.
     *
     * @return string The hostname setting.
     */
    public function getHostname()
    {
        return $this->raw[self::CFG_HOSTNAME];
    }

    /**
     * Retrieves the scripts timeout setting from the configuration.
     *
     * @return int The scripts timeout setting.
     */
    public function getScriptsTimeout()
    {
        return intval($this->raw[self::CFG_SCRIPTS_TIMEOUT]);
    }

    /**
     * Retrieves the notepad setting from the configuration.
     *
     * @return string The notepad setting.
     */
    public function getNotepad()
    {
        return $this->raw[self::CFG_NOTEPAD];
    }

    /**
     * Retrieves the logs verbosity setting from the configuration.
     *
     * @return int The logs verbosity setting.
     */
    public function getLogsVerbose()
    {
        return intval($this->raw[self::CFG_LOGS_VERBOSE]);
    }

    /**
     * Retrieves the maximum logs archives setting from the configuration.
     *
     * @return int The maximum logs archives setting.
     */
    public function getMaxLogsArchives()
    {
        return intval($this->raw[self::CFG_MAX_LOGS_ARCHIVES]);
    }
}
