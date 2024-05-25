<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class Core
{
    const isRoot_FILE = 'root.php';
    const PATH_WIN_PLACEHOLDER = '~BEARSAMPP_WIN_PATH~';
    const PATH_LIN_PLACEHOLDER = '~BEARSAMPP_LIN_PATH~';

    const PHP_VERSION = '5.6.40';
    const PHP_EXE = 'php-win.exe';
    const PHP_CONF = 'php.ini';

    const SETENV_VERSION = '1.09';
    const SETENV_EXE = 'SetEnv.exe';

    const NSSM_VERSION = '2.24';
    const NSSM_EXE = 'nssm.exe';

    const OPENSSL_VERSION = '1.1.0c';
    const OPENSSL_EXE = 'openssl.exe';
    const OPENSSL_CONF = 'openssl.cfg';

    const HOSTSEDITOR_VERSION = '1.3';
    const HOSTSEDITOR_EXE = 'hEdit_x64.exe';

    const LN_VERSION = '2.928';
    const LN_EXE = 'ln.exe';

    const APP_VERSION = 'version.dat';
    const LAST_PATH = 'lastPath.dat';
    const EXEC = 'exec.dat';
    const LOADING_PID = 'loading.pid';

    const SCRIPT_EXEC_SILENT = 'execSilent.vbs';

    public function __construct()
    {
        if (extension_loaded('winbinder')) {
            require_once $this->getLibsPath() . '/winbinder/winbinder.php';
        }
    }

    public function getLangsPath($aetrayPath = false)
    {
        global $bearsamppRoot;
        return $bearsamppRoot->getCorePath($aetrayPath) . '/langs';
    }

    public function getLibsPath($aetrayPath = false)
    {
        global $bearsamppRoot;
        return $bearsamppRoot->getCorePath($aetrayPath) . '/libs';
    }

    public function getResourcesPath($aetrayPath = false)
    {
        global $bearsamppRoot;
        return $bearsamppRoot->getCorePath($aetrayPath) . '/resources';
    }

    public function getIconsPath($aetrayPath = false)
    {
        global $bearsamppCore;
        return $bearsamppCore->getResourcesPath($aetrayPath) . '/icons';
    }

    public function getScriptsPath($aetrayPath = false)
    {
        global $bearsamppRoot;
        return $bearsamppRoot->getCorePath($aetrayPath) . '/scripts';
    }

    public function getScript($type)
    {
        return $this->getScriptsPath() . '/' . $type;
    }

    public function getTmpPath($aetrayPath = false)
    {
        global $bearsamppRoot;
        return $bearsamppRoot->getCorePath($aetrayPath) . '/tmp';
    }

    public function getisRootFilePath($aetrayPath = false)
    {
        global $bearsamppRoot;
        return $bearsamppRoot->getCorePath($aetrayPath) . '/' . self::isRoot_FILE;
    }

    public function getAppVersion()
    {
        global $bearsamppLang;

        $filePath = $this->getResourcesPath() . '/' . self::APP_VERSION;
        if (!is_file($filePath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), APP_TITLE, $filePath));
            return null;
        }

        return trim(file_get_contents($filePath));
    }

    public function getLastPath($aetrayPath = false)
    {
        return $this->getResourcesPath($aetrayPath) . '/' . self::LAST_PATH;
    }

    public function getLastPathContent()
    {
        return @file_get_contents($this->getLastPath());
    }

    public function getExec($aetrayPath = false)
    {
        return $this->getTmpPath($aetrayPath) . '/' . self::EXEC;
    }

    public function setExec($action)
    {
        file_put_contents($this->getExec(), $action);
    }

    public function getLoadingPid($aetrayPath = false)
    {
        return $this->getResourcesPath($aetrayPath) . '/' . self::LOADING_PID;
    }

    public function addLoadingPid($pid)
    {
        file_put_contents($this->getLoadingPid(), $pid . PHP_EOL, FILE_APPEND);
    }

    public function getPhpPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/php';
    }

    public function getPhpExe($aetrayPath = false)
    {
        return $this->getPhpPath($aetrayPath) . '/' . self::PHP_EXE;
    }

    public function getSetEnvPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/setenv';
    }

    public function getSetEnvExe($aetrayPath = false)
    {
        return $this->getSetEnvPath($aetrayPath) . '/' . self::SETENV_EXE;
    }

    public function getNssmPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/nssm';
    }

    public function getNssmExe($aetrayPath = false)
    {
        return $this->getNssmPath($aetrayPath) . '/' . self::NSSM_EXE;
    }

    public function getOpenSslPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/openssl';
    }

    public function getOpenSslExe($aetrayPath = false)
    {
        return $this->getOpenSslPath($aetrayPath) . '/' . self::OPENSSL_EXE;
    }

    public function getOpenSslConf($aetrayPath = false)
    {
        return $this->getOpenSslPath($aetrayPath) . '/' . self::OPENSSL_CONF;
    }

    public function getHostsEditorPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/hostseditor';
    }

    public function getHostsEditorExe($aetrayPath = false)
    {
        return $this->getHostsEditorPath($aetrayPath) . '/' . self::HOSTSEDITOR_EXE;
    }

    public function getLnPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/ln';
    }

    public function getLnExe($aetrayPath = false)
    {
        return $this->getLnPath($aetrayPath) . '/' . self::LN_EXE;
    }
}
