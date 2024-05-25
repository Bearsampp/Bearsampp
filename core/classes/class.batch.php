<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Batch provides utility functions for executing batch operations, managing services,
 * and interacting with the operating system. It includes methods for executing external commands,
 * managing Filezilla and PostgreSQL services, handling symlinks, and retrieving system information.
 * It utilizes global configurations and paths defined in the $bearsamppRoot and $bearsamppCore global variables.
 */
class Batch
{
    const END_PROCESS_STR = 'FINISHED!';
    const CATCH_OUTPUT_FALSE = 'bearsamppCatchOutputFalse';

    public function __construct()
    {
    }

    /**
     * Writes a log message to the batch log file.
     *
     * @param string $log The log message to write.
     */
    private static function writeLog($log)
    {
        global $bearsamppRoot;
        Util::logDebug($log, $bearsamppRoot->getBatchLogFilePath());
    }

    /**
     * Finds the executable name associated with a given process ID.
     *
     * @param int $pid The process ID.
     * @return string|false The executable name if found, false otherwise.
     */
    public static function findExeByPid($pid)
    {
        $result = self::exec('findExeByPid', 'TASKLIST /FO CSV /NH /FI "PID eq ' . $pid . '"', 5);
        if ($result !== false) {
            $expResult = explode('","', $result[0]);
            if (is_array($expResult) && count($expResult) > 2 && isset($expResult[0]) && !empty($expResult[0])) {
                return substr($expResult[0], 1);
            }
        }

        return false;
    }

    /**
     * Retrieves the process name and ID using a specific port.
     *
     * @param int $port The port number.
     * @return string|null The process name and ID if found, null otherwise.
     */
    public static function getProcessUsingPort($port)
    {
        $result = self::exec('getProcessUsingPort', 'NETSTAT -aon', 4);
        if ($result !== false) {
            foreach ($result as $row) {
                if (!Util::startWith($row, 'TCP')) {
                    continue;
                }
                $rowExp = explode(' ', preg_replace('/\s+/', ' ', $row));
                if (count($rowExp) == 5 && Util::endWith($rowExp[1], ':' . $port) && $rowExp[3] == 'LISTENING') {
                    $pid = intval($rowExp[4]);
                    $exe = self::findExeByPid($pid);
                    if ($exe !== false) {
                        return $exe . ' (' . $pid . ')';
                    }
                    return $pid;
                }
            }
        }

        return null;
    }

    /**
     * Exits the application and optionally restarts it.
     *
     * @param bool $restart Whether to restart the application after exiting.
     */
    public static function exitApp($restart = false)
    {
        global $bearsamppRoot, $bearsamppCore;

        $content = 'PING 1.1.1.1 -n 1 -w 2000 > nul' . PHP_EOL;
        $content .= '"' . $bearsamppRoot->getExeFilePath() . '" -quit -id={bearsampp}' . PHP_EOL;
        if ($restart) {
            $basename = 'restartApp';
            Util::logInfo('Restart App');
            $content .= '"' . $bearsamppCore->getPhpExe() . '" "' . Core::isRoot_FILE . '" "' . Action::RESTART . '"' . PHP_EOL;
        } else {
            $basename = 'exitApp';
            Util::logInfo('Exit App');
        }

        Win32Ps::killBins();
        self::execStandalone($basename, $content);
    }

    /**
     * Restarts the application.
     */
    public static function restartApp()
    {
        self::exitApp(true);
    }

    /**
     * Retrieves the PEAR version from the system.
     *
     * @return string|null The PEAR version if found, null otherwise.
     */
    public static function getPearVersion()
    {
        global $bearsamppBins;

        $result = self::exec('getPearVersion', 'CMD /C "' . $bearsamppBins->getPhp()->getPearExe() . '" -V', 5);
        if (is_array($result)) {
            foreach ($result as $row) {
                if (Util::startWith($row, 'PEAR Version:')) {
                    $expResult = explode(' ', $row);
                    if (count($expResult) == 3) {
                        return trim($expResult[2]);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Refreshes the environment variables.
     */
    public static function refreshEnvVars()
    {
        global $bearsamppRoot, $bearsamppCore;
        self::execStandalone('refreshEnvVars', '"' . $bearsamppCore->getSetEnvExe() . '" -a ' . Registry::APP_PATH_REG_ENTRY . ' "' . Util::formatWindowsPath($bearsamppRoot->getRootPath()) . '"');
    }

    /**
     * Installs the Filezilla service.
     *
     * @return bool True if the service is successfully installed, false otherwise.
     */
    public static function installFilezillaService()
    {
        global $bearsamppBins;

        self::exec('installFilezillaService', '"' . $bearsamppBins->getFilezilla()->getExe() . '" /install', true, false);

        if (!$bearsamppBins->getFilezilla()->getService()->isInstalled()) {
            return false;
        }

        self::setServiceDescription(BinFilezilla::SERVICE_NAME, $bearsamppBins->getFilezilla()->getService()->getDisplayName());

        return true;
    }

    /**
     * Uninstalls the Filezilla service.
     *
     * @return bool True if the service is successfully uninstalled, false otherwise.
     */
    public static function uninstallFilezillaService()
    {
        global $bearsamppBins;

        self::exec('uninstallFilezillaService', '"' . $bearsamppBins->getFilezilla()->getExe() . '" /uninstall', true, false);
        return !$bearsamppBins->getFilezilla()->getService()->isInstalled();
    }

    /**
     * Initializes MySQL with a specific path.
     *
     * @param string $path The path to the MySQL initialization script.
     */
    public static function initializeMysql($path)
    {
        if (!file_exists($path . '/init.bat')) {
            Util::logWarning($path . '/init.bat does not exist');
            return;
        }
        self::exec('initializeMysql', 'CMD /C "' . $path . '/init.bat"', 60);
    }

    /**
     * Installs the PostgreSQL service.
     *
     * @return bool True if the service is successfully installed, false otherwise.
     */
    public static function installPostgresqlService()
    {
        global $bearsamppBins;

        $cmd = '"' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getCtlExe()) . '" register -N "' . BinPostgresql::SERVICE_NAME . '"';
        $cmd .= ' -U "LocalSystem" -D "' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getSymlinkPath()) . '\\data"';
        $cmd .= ' -l "' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getErrorLog()) . '" -w';
        self::exec('installPostgresqlService', $cmd, true, false);

        if (!$bearsamppBins->getPostgresql()->getService()->isInstalled()) {
            return false;
        }

        self::setServiceDisplayName(BinPostgresql::SERVICE_NAME, $bearsamppBins->getPostgresql()->getService()->getDisplayName());
        self::setServiceDescription(BinPostgresql::SERVICE_NAME, $bearsamppBins->getPostgresql()->getService()->getDisplayName());
        self::setServiceStartType(BinPostgresql::SERVICE_NAME, "demand");

        return true;
    }

    /**
     * Uninstalls the PostgreSQL service.
     *
     * @return bool True if the service is successfully uninstalled, false otherwise.
     */
    public static function uninstallPostgresqlService()
    {
        global $bearsamppBins;

        $cmd = '"' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getCtlExe()) . '" unregister -N "' . BinPostgresql::SERVICE_NAME . '"';
        $cmd .= ' -l "' . Util::formatWindowsPath($bearsamppBins->getPostgresql()->getErrorLog()) . '" -w';
        self::exec('uninstallPostgresqlService', $cmd, true, false);
        return !$bearsamppBins->getPostgresql()->getService()->isInstalled();
    }

    /**
     * Initializes PostgreSQL with a specific path.
     *
     * @param string $path The path to the PostgreSQL initialization script.
     */
    public static function initializePostgresql($path)
    {
        if (!file_exists($path . '/init.bat')) {
            Util::logWarning($path . '/init.bat does not exist');
            return;
        }
        self::exec('initializePostgresql', 'CMD /C "' . $path . '/init.bat"', 15);
    }

    /**
     * Creates a symbolic link from a source to a destination.
     *
     * @param string $src The source path.
     * @param string $dest The destination path.
     */
    public static function createSymlink($src, $dest)
    {
        global $bearsamppCore;
        $src = Util::formatWindowsPath($src);
        $dest = Util::formatWindowsPath($dest);
        self::exec('createSymlink', '"' . $bearsamppCore->getLnExe() . '" --absolute --symbolic --traditional --1023safe "' . $src . '" ' . '"' . $dest . '"', true, false);
    }

    /**
     * Removes a symbolic link.
     *
     * @param string $link The link to remove.
     */
    public static function removeSymlink($link)
    {
        self::exec('removeSymlink', 'rmdir /Q "' . Util::formatWindowsPath($link) . '"', true, false);
    }

    /**
     * Retrieves the operating system information.
     *
     * @return string The operating system information.
     */
    public static function getOsInfo()
    {
        $result = self::exec('getOsInfo', 'ver', 5);
        if (is_array($result)) {
            foreach ($result as $row) {
                if (Util::startWith($row, 'Microsoft')) {
                    return trim($row);
                }
            }
        }
        return '';
    }

    /**
     * Sets the display name for a service.
     *
     * @param string $serviceName The service name.
     * @param string $displayName The display name to set.
     */
    public static function setServiceDisplayName($serviceName, $displayName)
    {
        $cmd = 'sc config ' . $serviceName . ' DisplayName= "' . $displayName . '"';
        self::exec('setServiceDisplayName', $cmd, true, false);
    }

    /**
     * Sets the description for a service.
     *
     * @param string $serviceName The service name.
     * @param string $desc The description to set.
     */
    public static function setServiceDescription($serviceName, $desc)
    {
        $cmd = 'sc description ' . $serviceName . ' "' . $desc . '"';
        self::exec('setServiceDescription', $cmd, true, false);
    }

    /**
     * Sets the start type for a service.
     *
     * @param string $serviceName The service name.
     * @param string $startType The start type to set.
     */
    public static function setServiceStartType($serviceName, $startType)
    {
        $cmd = 'sc config ' . $serviceName . ' start= ' . $startType;
        self::exec('setServiceStartType', $cmd, true, false);
    }

    /**
     * Executes a standalone script.
     *
     * @param string $basename The base name for the script.
     * @param string $content The content of the script.
     * @param bool $silent Whether to execute the script silently.
     * @return mixed The result of the execution.
     */
    public static function execStandalone($basename, $content, $silent = true)
    {
        return self::exec($basename, $content, false, false, true, $silent);
    }

    /**
     * Executes a script with optional parameters.
     *
     * @param string $basename The base name for the script.
     * @param string $content The content of the script.
     * @param mixed $timeout The timeout for the script execution.
     * @param bool $catchOutput Whether to catch the output of the script.
     * @param bool $standalone Whether the script is standalone.
     * @param bool $silent Whether to execute the script silently.
     * @param bool $rebuild Whether to rebuild the result.
     * @return mixed The result of the execution.
     */
    public static function exec($basename, $content, $timeout = true, $catchOutput = true, $standalone = false, $silent = true, $rebuild = true)
    {
        global $bearsamppConfig, $bearsamppWinbinder;
        $result = false;

        $resultFile = self::getTmpFile('.tmp', $basename);
        $scriptPath = self::getTmpFile('.bat', $basename);
        $checkFile = self::getTmpFile('.tmp', $basename);

        // Redirect output
        if ($catchOutput) {
            $content .= '> "' . $resultFile . '"' . (!Util::endWith($content, '2') ? ' 2>&1' : '');
        }

        // Header
        $header = '@ECHO OFF' . PHP_EOL . PHP_EOL;

        // Footer
        $footer = PHP_EOL . (!$standalone ? PHP_EOL . 'ECHO ' . self::END_PROCESS_STR . ' > "' . $checkFile . '"' : '');

        // Process
        file_put_contents($scriptPath, $header . $content . $footer);
        $bearsamppWinbinder->exec($scriptPath, null, $silent);

        if (!$standalone) {
            $timeout = is_numeric($timeout) ? $timeout : ($timeout === true ? $bearsamppConfig->getScriptsTimeout() : false);
            $maxtime = time() + $timeout;
            $noTimeout = $timeout === false;
            while ($result === false || empty($result)) {
                if (file_exists($checkFile)) {
                    $check = file($checkFile);
                    if (!empty($check) && trim($check[0]) == self::END_PROCESS_STR) {
                        if ($catchOutput && file_exists($resultFile)) {
                            $result = file($resultFile);
                        } else {
                            $result = self::CATCH_OUTPUT_FALSE;
                        }
                    }
                }
                if ($maxtime < time() && !$noTimeout) {
                    break;
                }
            }
        }

        self::writeLog('Exec:');
        self::writeLog('-> basename: ' . $basename);
        self::writeLog('-> content: ' . str_replace(PHP_EOL, ' \\\\ ', $content));
        self::writeLog('-> checkFile: ' . $checkFile);
        self::writeLog('-> resultFile: ' . $resultFile);
        self::writeLog('-> scriptPath: ' . $scriptPath);

        if ($result !== false && !empty($result) && is_array($result)) {
            if ($rebuild) {
                $rebuildResult = array();
                foreach ($result as $row) {
                    $row = trim($row);
                    if (!empty($row)) {
                        $rebuildResult[] = $row;
                    }
                }
                $result = $rebuildResult;
            }
            self::writeLog('-> result: ' . substr(implode(' \\\\ ', $result), 0, 2048));
        } else {
            self::writeLog('-> result: N/A');
        }

        return $result;
    }

    /**
     * Generates a temporary file path with a specific extension and custom name.
     *
     * @param string $ext The file extension.
     * @param string|null $customName The custom name for the file.
     * @return string The generated file path.
     */
    private static function getTmpFile($ext, $customName = null)
    {
        global $bearsamppCore;
        return Util::formatWindowsPath($bearsamppCore->getTmpPath() . '/' . (!empty($customName) ? $customName . '-' : '') . Util::random() . $ext);
    }
}
