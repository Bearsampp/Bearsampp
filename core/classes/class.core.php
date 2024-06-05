<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Core
 *
 * This class provides core functionalities and constants for the Bearsampp application.
 * It includes methods for retrieving paths, managing application versions, and handling
 * various executable files and configurations.
 */
class Core
{
    // Constants for various file names and versions
    const isRoot_FILE = 'root.php';
    const PATH_WIN_PLACEHOLDER = '~BEARSAMPP_WIN_PATH~';
    const PATH_LIN_PLACEHOLDER = '~BEARSAMPP_LIN_PATH~';

    const PHP_VERSION = '5.4.23';
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

    const PWGEN_VERSION = '3.5.4';
    const PWGEN_EXE = "PWGenPortable.exe";

    const APP_VERSION = 'version.dat';
    const LAST_PATH = 'lastPath.dat';
    const EXEC = 'exec.dat';
    const LOADING_PID = 'loading.pid';

    const SCRIPT_EXEC_SILENT = 'execSilent.vbs';

    /**
     * Core constructor.
     *
     * Loads the WinBinder extension if available.
     */
    public function __construct()
    {
        if (extension_loaded('winbinder')) {
            require_once $this->getLibsPath() . '/winbinder/winbinder.php';
        }
    }

    /**
     * Retrieves the path to the language files.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the language files.
     */
    public function getLangsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath($aetrayPath) . '/langs';
    }

    /**
     * Retrieves the path to the libraries.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the libraries.
     */
    public function getLibsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath($aetrayPath) . '/libs';
    }

    /**
     * Retrieves the path to the resources.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the resources.
     */
    public function getResourcesPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath($aetrayPath) . '/resources';
    }

    /**
     * Retrieves the path to the icons.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the icons.
     */
    public function getIconsPath($aetrayPath = false)
    {
        global $bearsamppCore;

        return $bearsamppCore->getResourcesPath($aetrayPath) . '/icons';
    }

    /**
     * Retrieves the path to the scripts.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the scripts.
     */
    public function getScriptsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath($aetrayPath) . '/scripts';
    }

    /**
     * Retrieves the path to a specific script.
     *
     * @param string $type The type of script.
     * @return string The path to the script.
     */
    public function getScript($type)
    {
        return $this->getScriptsPath() . '/' . $type;
    }

    /**
     * Retrieves the path to the temporary directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the temporary directory.
     */
    public function getTmpPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath($aetrayPath) . '/tmp';
    }

    /**
     * Retrieves the path to the root file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the root file.
     */
    public function getisRootFilePath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath($aetrayPath) . '/' . self::isRoot_FILE;
    }

    /**
     * Retrieves the application version.
     *
     * @return string|null The application version or null if not found.
     */
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

    /**
     * Retrieves the path to the last path file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the last path file.
     */
    public function getLastPath($aetrayPath = false)
    {
        return $this->getResourcesPath($aetrayPath) . '/' . self::LAST_PATH;
    }

    /**
     * Retrieves the content of the last path file.
     *
     * @return string|false The content of the last path file or false on failure.
     */
    public function getLastPathContent()
    {
        return @file_get_contents($this->getLastPath());
    }

    /**
     * Retrieves the path to the exec file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the exec file.
     */
    public function getExec($aetrayPath = false)
    {
        return $this->getTmpPath($aetrayPath) . '/' . self::EXEC;
    }

    /**
     * Sets the content of the exec file.
     *
     * @param string $action The content to set in the exec file.
     */
    public function setExec($action)
    {
        file_put_contents($this->getExec(), $action);
    }

    /**
     * Retrieves the path to the loading PID file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the loading PID file.
     */
    public function getLoadingPid($aetrayPath = false)
    {
        return $this->getResourcesPath($aetrayPath) . '/' . self::LOADING_PID;
    }

    /**
     * Adds a PID to the loading PID file.
     *
     * @param int $pid The PID to add.
     */
    public function addLoadingPid($pid)
    {
        file_put_contents($this->getLoadingPid(), $pid . PHP_EOL, FILE_APPEND);
    }

    /**
     * Retrieves the path to the PHP directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the PHP directory.
     */
    public function getPhpPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/php';
    }

    /**
     * Retrieves the path to the PHP executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the PHP executable.
     */
    public function getPhpExe($aetrayPath = false)
    {
        return $this->getPhpPath($aetrayPath) . '/' . self::PHP_EXE;
    }

    /**
     * Retrieves the path to the SetEnv directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the SetEnv directory.
     */
    public function getSetEnvPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/setenv';
    }

    /**
     * Retrieves the path to the SetEnv executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the SetEnv executable.
     */
    public function getSetEnvExe($aetrayPath = false)
    {
        return $this->getSetEnvPath($aetrayPath) . '/' . self::SETENV_EXE;
    }

    /**
     * Retrieves the path to the NSSM directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the NSSM directory.
     */
    public function getNssmPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/nssm';
    }

    /**
     * Retrieves the path to the NSSM executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the NSSM executable.
     */
    public function getNssmExe($aetrayPath = false)
    {
        return $this->getNssmPath($aetrayPath) . '/' . self::NSSM_EXE;
    }

    /**
     * Retrieves the path to the OpenSSL directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the OpenSSL directory.
     */
    public function getOpenSslPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/openssl';
    }

    /**
     * Retrieves the path to the OpenSSL executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the OpenSSL executable.
     */
    public function getOpenSslExe($aetrayPath = false)
    {
        return $this->getOpenSslPath($aetrayPath) . '/' . self::OPENSSL_EXE;
    }

    /**
     * Retrieves the path to the OpenSSL configuration file.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the OpenSSL configuration file.
     */
    public function getOpenSslConf($aetrayPath = false)
    {
        return $this->getOpenSslPath($aetrayPath) . '/' . self::OPENSSL_CONF;
    }

    /**
     * Retrieves the path to the HostsEditor directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the HostsEditor directory.
     */
    public function getHostsEditorPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/hostseditor';
    }

    /**
     * Retrieves the path to the HostsEditor executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the HostsEditor executable.
     */
    public function getHostsEditorExe($aetrayPath = false)
    {
        return $this->getHostsEditorPath($aetrayPath) . '/' . self::HOSTSEDITOR_EXE;
    }

    /**
     * Retrieves the path to the LN directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the LN directory.
     */
    public function getLnPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/ln';
    }

    /**
     * Retrieves the path to the LN executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the LN executable.
     */
    public function getLnExe($aetrayPath = false)
    {
        return $this->getLnPath($aetrayPath) . '/' . self::LN_EXE;
    }

    /**
     * Retrieves the path to the PWGen directory.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the PWGen directory.
     */
    public function getPwgenPath($aetrayPath = false)
    {
        return $this->getLibsPath($aetrayPath) . '/pwgen';
    }

    /**
     * Retrieves the path to the PWGen executable.
     *
     * @param bool $aetrayPath Whether to format the path for AeTrayMenu.
     * @return string The path to the PWGen executable.
     */
    public function getPwgenExe($aetrayPath = false)
    {
        return $this->getPwgenPath($aetrayPath) . '/' . self::PWGEN_EXE;
    }

    /**
     * Provides a string representation of the core object.
     *
     * @return string A string describing the core object.
     */
    public function __toString()
    {
        return 'core object';
    }

    /**
     * Unzips a file to a specified destination.
     *
     * @param string $zipFilePath The path to the zip file.
     * @param string $destinationPath The path where the contents should be extracted.
     *
     * @return bool True on success, false on failure.
     */
    public function unzipFile($zipFilePath, $destinationPath)
    {
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) === true) {
            $zip->extractTo($destinationPath);
            $zip->close();

            Util::logError("source: {$zipFilePath}");
            Util::logError("destination: {$destinationPath}");

            return true;
        } else {
            Util::logError('Failed to open zip file: ' . $zipFilePath);

            return false;
        }
    }
}
