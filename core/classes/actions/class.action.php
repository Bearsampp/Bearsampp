<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Action handles the execution of various actions based on command line arguments.
 */
class Action
{
    // Constants for different actions
    const ABOUT = 'about';
    const ADD_ALIAS = 'addAlias';
    const ADD_VHOST = 'addVhost';
    const CHANGE_BROWSER = 'changeBrowser';
    const CHANGE_DB_ROOT_PWD = 'changeDbRootPwd';
    const CHANGE_PORT = 'changePort';
    const CHECK_PORT = 'checkPort';
    const CHECK_VERSION = 'checkVersion';
    const CLEAR_FOLDERS = 'clearFolders';
    const DEBUG_APACHE = 'debugApache';
    const DEBUG_MARIADB = 'debugMariadb';
    const DEBUG_MYSQL = 'debugMysql';
    const DEBUG_POSTGRESQL = 'debugPostgresql';
    const EDIT_ALIAS = 'editAlias';
    const EDIT_VHOST = 'editVhost';
    const ENABLE = 'enable';
    const EXEC = 'exec';
    const GEN_SSL_CERTIFICATE = 'genSslCertificate';
    const LAUNCH_STARTUP = 'launchStartup';
    const MANUAL_RESTART = 'manualRestart';
    const LOADING = 'loading';
    const QUIT = 'quit';
    const REFRESH_REPOS = 'refreshRepos';
    const REFRESH_REPOS_STARTUP = 'refreshReposStartup';
    const RELOAD = 'reload';
    const RESTART = 'restart';
    const SERVICE = 'service';
    const STARTUP = 'startup';
    const SWITCH_APACHE_MODULE = 'switchApacheModule';
    const SWITCH_LANG = 'switchLang';
    const SWITCH_LOGS_VERBOSE = 'switchLogsVerbose';
    const SWITCH_PHP_EXTENSION = 'switchPhpExtension';
    const SWITCH_PHP_PARAM = 'switchPhpParam';
    const SWITCH_ONLINE = 'switchOnline';
    const SWITCH_VERSION = 'switchVersion';

    const EXT = 'ext';

    /**
     * @var mixed Holds the current action instance.
     */
    private $current;


    /**
     * Constructor for the Action class.
     */
    public function __construct()
    {
    }

    /**
     * Processes the action based on command line arguments.
     */
    public function process()
    {
        if ($this->exists()) {
            $action = Util::cleanArgv(1);
            $actionClass = 'Action' . ucfirst($action);

            $args = array();
            foreach ($_SERVER['argv'] as $key => $arg) {
                if ($key > 1) {
                    $args[] = $action == self::EXT ? $arg : base64_decode($arg);
                }
            }

            $this->current = null;
            if (class_exists($actionClass)) {
                Util::logDebug('Start ' . $actionClass);
                $this->current = new $actionClass($args);
            }
        }
    }

    /**
     * Calls a specific action by name with optional arguments.
     *
     * @param string $actionName The name of the action to call.
     * @param mixed $actionArgs Optional arguments for the action.
     */
    public function call($actionName, $actionArgs = null)
    {
        $actionClass = 'Action' . ucfirst($actionName);
        if (class_exists($actionClass)) {
            Util::logDebug('Start ' . $actionClass);
            new $actionClass($actionArgs);
        }
    }

    /**
     * Checks if the action exists in the command line arguments.
     *
     * @return bool Returns true if the action exists, false otherwise.
     */
    public function exists()
    {
        return isset($_SERVER['argv'])
            && isset($_SERVER['argv'][1])
            && !empty($_SERVER['argv'][1]);
    }
}
