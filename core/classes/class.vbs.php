<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Vbs
 *
 * This class provides various utility functions for interacting with the Windows operating system
 * using VBScript. It includes methods for counting files and folders, retrieving default and installed browsers,
 * managing processes, and creating shortcuts.
 */
class Vbs
{
    const END_PROCESS_STR = 'FINISHED!';
    const STR_SEPARATOR = ' || ';

    const DESKTOP_PATH = 'objShell.SpecialFolders("Desktop")';
    const ALL_DESKTOP_PATH = 'objShell.SpecialFolders("AllUsersDesktop")';
    const STARTUP_PATH = 'objShell.SpecialFolders("Startup")';
    const ALL_STARTUP_PATH = 'objShell.SpecialFolders("AllUsersStartup")';

    public function __construct()
    {
    }

    /**
     * Writes a log entry to the VBS log file.
     *
     * @param   string  $log  The log message to write.
     */
    private static function writeLog($log)
    {
        global $bearsamppRoot;
        Util::logDebug( $log, $bearsamppRoot->getVbsLogFilePath() );
    }

    /**
     * Counts the number of files and folders in the specified path.
     *
     * @param   string  $path  The path to count files and folders in.
     *
     * @return int|false The count of files and folders, or false on failure.
     */
    public static function countFilesFolders($path)
    {
        $basename   = 'countFilesFolders';
        $resultFile = self::getResultFile( $basename );

        $content = 'Dim objFso, objResultFile, objCheckFile' . PHP_EOL . PHP_EOL;
        $content .= 'Set objFso = CreateObject("scripting.filesystemobject")' . PHP_EOL;
        $content .= 'Set objResultFile = objFso.CreateTextFile("' . $resultFile . '", True)' . PHP_EOL;
        $content .= 'count = 0' . PHP_EOL;
        $content .= 'CountFiles("' . $path . '")' . PHP_EOL . PHP_EOL;
        $content .= 'Function CountFiles(ByVal path)' . PHP_EOL;
        $content .= '    Dim parentFld, subFld' . PHP_EOL;
        $content .= '    Set parentFld = objFso.GetFolder(path)' . PHP_EOL . PHP_EOL;
        $content .= '    count = count + parentFld.Files.Count + parentFld.SubFolders.Count' . PHP_EOL;
        $content .= '    For Each subFld In parentFld.SubFolders' . PHP_EOL;
        $content .= '        count = count + CountFiles(subFld.Path)' . PHP_EOL;
        $content .= '    Next' . PHP_EOL . PHP_EOL;
        $content .= 'End Function' . PHP_EOL . PHP_EOL;
        $content .= 'objResultFile.Write count' . PHP_EOL;
        $content .= 'objResultFile.Close' . PHP_EOL;

        $result = self::exec( $basename, $resultFile, $content );

        return isset( $result[0] ) && is_numeric( $result[0] ) ? intval( $result[0] ) : false;
    }

    /**
     * Retrieves the default browser's executable path.
     *
     * @return string|false The path to the default browser executable, or false on failure.
     */
    public static function getDefaultBrowser()
    {
        $basename   = 'getDefaultBrowser';
        $resultFile = self::getResultFile( $basename );

        $content = 'On Error Resume Next' . PHP_EOL;
        $content .= 'Err.Clear' . PHP_EOL . PHP_EOL;
        $content .= 'Dim objShell, objFso, objFile' . PHP_EOL . PHP_EOL;
        $content .= 'Set objShell = WScript.CreateObject("WScript.Shell")' . PHP_EOL;
        $content .= 'Set objFso = CreateObject("scripting.filesystemobject")' . PHP_EOL;
        $content .= 'Set objFile = objFso.CreateTextFile("' . $resultFile . '", True)' . PHP_EOL . PHP_EOL;
        $content .= 'objFile.Write objShell.RegRead("HKLM\SOFTWARE\Classes\http\shell\open\command\")' . PHP_EOL;
        $content .= 'objFile.Close' . PHP_EOL;

        $result = self::exec( $basename, $resultFile, $content );
        if ( $result !== false && !empty( $result ) ) {
            if ( preg_match( '/"([^"]+)"/', $result[0], $matches ) ) {
                return $matches[1];
            }
            else {
                return str_replace( '"', '', $result[0] );
            }
        }
        else {
            return false;
        }
    }

    /**
     * Retrieves a list of installed browsers' executable paths.
     *
     * @return array|false An array of paths to installed browser executables, or false on failure.
     */
    public static function getInstalledBrowsers()
    {
        $basename   = 'getInstalledBrowsers';
        $resultFile = self::getResultFile( $basename );

        $content = 'On Error Resume Next' . PHP_EOL;
        $content .= 'Err.Clear' . PHP_EOL . PHP_EOL;
        $content .= 'Dim objShell, objRegistry, objFso, objFile' . PHP_EOL . PHP_EOL;
        $content .= 'Set objShell = WScript.CreateObject("WScript.Shell")' . PHP_EOL;
        $content .= 'Set objRegistry = GetObject("winmgmts://./root/default:StdRegProv")' . PHP_EOL;
        $content .= 'Set objFso = CreateObject("scripting.filesystemobject")' . PHP_EOL;
        $content .= 'Set objFile = objFso.CreateTextFile("' . $resultFile . '", True)' . PHP_EOL . PHP_EOL;
        $content .= 'mainKey = "SOFTWARE\WOW6432Node\Clients\StartMenuInternet"' . PHP_EOL;
        $content .= 'checkKey = objShell.RegRead("HKLM\" & mainKey & "\")' . PHP_EOL;
        $content .= 'If Err.Number <> 0 Then' . PHP_EOL;
        $content .= '    Err.Clear' . PHP_EOL;
        $content .= '    mainKey = "SOFTWARE\Clients\StartMenuInternet"' . PHP_EOL;
        $content .= '    checkKey = objShell.RegRead("HKLM\" & mainKey & "\")' . PHP_EOL;
        $content .= '    If Err.Number <> 0 Then' . PHP_EOL;
        $content .= '        mainKey = ""' . PHP_EOL;
        $content .= '    End If' . PHP_EOL;
        $content .= 'End If' . PHP_EOL . PHP_EOL;
        $content .= 'Err.Clear' . PHP_EOL;
        $content .= 'If mainKey <> "" Then' . PHP_EOL;
        $content .= '    objRegistry.EnumKey &H80000002, mainKey, arrSubKeys' . PHP_EOL;
        $content .= '    For Each subKey In arrSubKeys' . PHP_EOL;
        $content .= '        objFile.Write objShell.RegRead("HKLM\SOFTWARE\Clients\StartMenuInternet\" & subKey & "\shell\open\command\") & vbCrLf' . PHP_EOL;
        $content .= '    Next' . PHP_EOL;
        $content .= 'End If' . PHP_EOL;
        $content .= 'objFile.Close' . PHP_EOL;

        $result = self::exec( $basename, $resultFile, $content );
        if ( $result !== false && !empty( $result ) ) {
            $rebuildResult = array();
            foreach ( $result as $browser ) {
                $rebuildResult[] = str_replace( '"', '', $browser );
            }
            $result = $rebuildResult;
        }

        return $result;
    }

    /**
     * Retrieves a list of running processes with specified keys.
     *
     * @param   array  $vbsKeys  The keys to retrieve for each process.
     *
     * @return array|false An array of process information, or false on failure.
     */
    public static function getListProcs($vbsKeys)
    {
        $basename   = 'getListProcs';
        $resultFile = self::getResultFile( $basename );
        $sep        = ' & "' . self::STR_SEPARATOR . '" & _';

        $content = 'Dim objFso, objResultFile, objWMIService' . PHP_EOL . PHP_EOL;
        $content .= 'Set objFso = CreateObject("scripting.filesystemobject")' . PHP_EOL;
        $content .= 'Set objResultFile = objFso.CreateTextFile("' . $resultFile . '", True)' . PHP_EOL;
        $content .= 'strComputer = "."' . PHP_EOL;
        $content .= 'Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\\\" & strComputer & "\root\cimv2")' . PHP_EOL;
        $content .= 'Set listProcess = objWMIService.ExecQuery ("SELECT * FROM Win32_Process")' . PHP_EOL;
        $content .= 'For Each process in listProcess' . PHP_EOL;

        $content .= '    objResultFile.WriteLine(_' . PHP_EOL;
        foreach ( $vbsKeys as $vbsKey ) {
            $content .= '        process.' . $vbsKey . $sep . PHP_EOL;
        }
        $content = substr( $content, 0, strlen( $content ) - strlen( $sep ) - 1 ) . ')' . PHP_EOL;

        $content .= 'Next' . PHP_EOL;
        $content .= 'objResultFile.WriteLine("' . self::END_PROCESS_STR . '")' . PHP_EOL;
        $content .= 'objResultFile.Close' . PHP_EOL;
        $content .= 'Err.Clear' . PHP_EOL;

        $result = self::exec( $basename, $resultFile, $content );
        if ( empty( $result ) ) {
            return false;
        }

        unset( $result[array_search( self::END_PROCESS_STR, $result )] );
        if ( is_array( $result ) && count( $result ) > 0 ) {
            $rebuildResult = array();
            foreach ( $result as $row ) {
                $row = explode( trim( self::STR_SEPARATOR ), $row );
                if ( count( $row ) != count( $vbsKeys ) ) {
                    continue;
                }
                $processInfo = array();
                foreach ( $vbsKeys as $key => $vbsKey ) {
                    $processInfo[$vbsKey] = trim( $row[$key] );
                }
                if ( !empty( $processInfo[Win32Ps::EXECUTABLE_PATH] ) ) {
                    $rebuildResult[] = $processInfo;
                }
            }

            return $rebuildResult;
        }

        return false;
    }

    /**
     * Terminates a process by its PID.
     *
     * @param   int  $pid  The process ID to terminate.
     *
     * @return bool True on success, false on failure.
     */
    public static function killProc($pid)
    {
        $basename   = 'killProc';
        $resultFile = self::getResultFile( $basename );

        $content = 'Dim objFso, objResultFile, objWMIService' . PHP_EOL . PHP_EOL;
        $content .= 'Set objFso = CreateObject("scripting.filesystemobject")' . PHP_EOL;
        $content .= 'Set objResultFile = objFso.CreateTextFile("' . $resultFile . '", True)' . PHP_EOL;
        $content .= 'strComputer = "."' . PHP_EOL;
        $content .= 'strProcessKill = "' . $pid . '"' . PHP_EOL;
        $content .= 'Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\\\" & strComputer & "\root\cimv2")' . PHP_EOL;
        $content .= 'Set listProcess = objWMIService.ExecQuery ("Select * from Win32_Process Where ProcessID = " & strProcessKill)' . PHP_EOL;
        $content .= 'For Each objProcess in listProcess' . PHP_EOL;
        $content .= '    objResultFile.WriteLine(objProcess.Name & "' . self::STR_SEPARATOR . '" & objProcess.ProcessID & "' . self::STR_SEPARATOR . '" & objProcess.ExecutablePath)' . PHP_EOL;
        $content .= '    objProcess.Terminate()' . PHP_EOL;
        $content .= 'Next' . PHP_EOL;
        $content .= 'objResultFile.Close' . PHP_EOL;

        $result = self::exec( $basename, $resultFile, $content );
        if ( empty( $result ) ) {
            return true;
        }

        if ( is_array( $result ) && count( $result ) > 0 ) {
            foreach ( $result as $row ) {
                $row = explode( self::STR_SEPARATOR, $row );
                if ( count( $row ) == 3 && !empty( $row[2] ) ) {
                    Util::logDebug( 'Kill process ' . $row[2] . ' (PID ' . $row[1] . ')' );
                }
            }
        }

        return true;
    }

    /**
     * Retrieves a special folder path.
     *
     * @param   string  $path  The VBScript path constant for the special folder.
     *
     * @return string|null The path to the special folder, or null on failure.
     */
    private static function getSpecialPath($path)
    {
        $basename   = 'getSpecialPath';
        $resultFile = self::getResultFile( $basename );

        $content = 'Dim objShell, objFso, objResultFile' . PHP_EOL . PHP_EOL;
        $content .= 'Set objShell = Wscript.CreateObject("Wscript.Shell")' . PHP_EOL;
        $content .= 'Set objFso = CreateObject("scripting.filesystemobject")' . PHP_EOL;
        $content .= 'Set objResultFile = objFso.CreateTextFile("' . $resultFile . '", True)' . PHP_EOL . PHP_EOL;
        $content .= 'objResultFile.WriteLine(' . $path . ')' . PHP_EOL;
        $content .= 'objResultFile.Close' . PHP_EOL;

        $result = self::exec( $basename, $resultFile, $content );
        if ( !empty( $result ) && is_array( $result ) && count( $result ) == 1 ) {
            return Util::formatUnixPath( $result[0] );
        }

        return null;
    }

    /**
     * Retrieves the startup path, optionally appending a file name.
     *
     * @param   string|null  $file  The file name to append to the startup path.
     *
     * @return string The startup path.
     */
    public static function getStartupPath($file = null)
    {
        return self::getSpecialPath( self::STARTUP_PATH ) . ($file != null ? '/' . $file : '');
    }

    /**
     * Creates a shortcut to the Bearsampp executable.
     *
     * @param   string  $savePath  The path to save the shortcut.
     *
     * @return bool True on success, false on failure.
     */
    public static function createShortcut($savePath)
    {
        global $bearsamppRoot, $bearsamppCore;
        $basename   = 'createShortcut';
        $resultFile = self::getResultFile( $basename );

        $content = 'Dim objShell, objFso, objResultFile' . PHP_EOL . PHP_EOL;
        $content .= 'Set objShell = Wscript.CreateObject("Wscript.Shell")' . PHP_EOL;
        $content .= 'Set objFso = CreateObject("scripting.filesystemobject")' . PHP_EOL;
        $content .= 'Set objResultFile = objFso.CreateTextFile("' . $resultFile . '", True)' . PHP_EOL . PHP_EOL;
        $content .= 'Set objShortcut = objShell.CreateShortcut("' . $savePath . '")' . PHP_EOL;
        $content .= 'objShortCut.TargetPath = "' . $bearsamppRoot->getExeFilePath() . '"' . PHP_EOL;
        $content .= 'objShortCut.WorkingDirectory = "' . $bearsamppRoot->getRootPath() . '"' . PHP_EOL;
        $content .= 'objShortCut.Description = "' . APP_TITLE . ' ' . $bearsamppCore->getAppVersion() . '"' . PHP_EOL;
        $content .= 'objShortCut.IconLocation = "' . $bearsamppCore->getResourcesPath() . '/homepage/img/icons/app.ico' . '"' . PHP_EOL;
        $content .= 'objShortCut.Save' . PHP_EOL;
        $content .= 'If Err.Number <> 0 Then' . PHP_EOL;
        $content .= '    objResultFile.Write Err.Number & ": " & Err.Description' . PHP_EOL;
        $content .= 'End If' . PHP_EOL;
        $content .= 'objResultFile.Close' . PHP_EOL;

        $result = self::exec( $basename, $resultFile, $content );
        if ( empty( $result ) ) {
            return true;
        }
        elseif ( isset( $result[0] ) ) {
            Util::logError( 'createShortcut: ' . $result[0] );

            return false;
        }

        return false;
    }

    /**
     * Retrieves information about a Windows service.
     *
     * @param   string  $serviceName  The name of the service to retrieve information about.
     *
     * @return array|false An array of service information, or false on failure.
     */
    public static function getServiceInfos($serviceName)
    {
        $basename   = 'getServiceInfos';
        $resultFile = self::getResultFile( $basename );
        $sep        = ' & "' . self::STR_SEPARATOR . '" & _';
        $vbsKeys    = Win32Service::getVbsKeys();

        $content = 'Dim objFso, objResultFile, objWMIService' . PHP_EOL . PHP_EOL;
        $content .= 'Set objFso = CreateObject("scripting.filesystemobject")' . PHP_EOL;
        $content .= 'Set objResultFile = objFso.CreateTextFile("' . $resultFile . '", True)' . PHP_EOL;
        $content .= 'strComputer = "."' . PHP_EOL;
        $content .= 'Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\\\" & strComputer & "\root\cimv2")' . PHP_EOL;
        $content .= 'Set listServices = objWMIService.ExecQuery ("SELECT * FROM Win32_Service WHERE Name=\'' . $serviceName . '\'")' . PHP_EOL;
        $content .= 'For Each service in listServices' . PHP_EOL;

        $content .= '    objResultFile.WriteLine(_' . PHP_EOL;
        foreach ( $vbsKeys as $vbsKey ) {
            $content .= '        service.' . $vbsKey . $sep . PHP_EOL;
        }
        $content = substr( $content, 0, strlen( $content ) - strlen( $sep ) - 1 ) . ')' . PHP_EOL;

        $content .= 'Next' . PHP_EOL;
        $content .= 'objResultFile.WriteLine("' . self::END_PROCESS_STR . '")' . PHP_EOL;
        $content .= 'objResultFile.Close' . PHP_EOL;

        $result = self::exec( $basename, $resultFile, $content );
        if ( empty( $result ) ) {
            return false;
        }

        unset( $result[array_search( self::END_PROCESS_STR, $result )] );
        if ( is_array( $result ) && count( $result ) == 1 ) {
            $rebuildResult = array();
            $row           = explode( trim( self::STR_SEPARATOR ), $result[0] );
            if ( count( $row ) != count( $vbsKeys ) ) {
                return false;
            }
            foreach ( $vbsKeys as $key => $vbsKey ) {
                $rebuildResult[$vbsKey] = trim( $row[$key] );
            }

            return $rebuildResult;
        }

        return false;
    }

    /**
     * Generates a temporary file path with a given extension and optional custom name.
     *
     * @param   string       $ext         The file extension for the temporary file.
     * @param   string|null  $customName  An optional custom name to include in the file name.
     *
     * @return string The formatted path to the temporary file.
     */
    public static function getTmpFile($ext, $customName = null)
    {
        global $bearsamppCore;

        return Util::formatWindowsPath( $bearsamppCore->getTmpPath() . '/' . (!empty( $customName ) ? $customName . '-' : '') . Util::random() . $ext );
    }

    /**
     * Retrieves the path for a result file based on a given basename.
     *
     * @param   string  $basename  The base name to use for the result file.
     *
     * @return string The path to the result file.
     */
    public static function getResultFile($basename)
    {
        return self::getTmpFile( '.vbs', $basename );
    }

    /**
     * Executes a VBScript file and retrieves the result.
     *
     * @param   string    $basename    The base name for the script and result files.
     * @param   string    $resultFile  The path to the result file.
     * @param   string    $content     The VBScript content to execute.
     * @param   int|bool  $timeout     The timeout duration in seconds, or true for default timeout, or false for no timeout.
     *
     * @return array|false The result of the script execution as an array of lines, or false on failure.
     */
    public static function exec($basename, $resultFile, $content, $timeout = true)
    {
        global $bearsamppConfig, $bearsamppWinbinder;
        $result = false;

        $scriptPath       = self::getTmpFile( '.vbs', $basename );
        $checkFile        = self::getTmpFile( '.tmp', $basename );
        $errFile          = self::getTmpFile( '.tmp', $basename );
        $randomVarName    = Util::random( 15, false );
        $randomObjErrFile = Util::random( 15, false );
        $randomObjFile    = Util::random( 15, false );
        $randomObjFso     = Util::random( 15, false );

        // Header
        $header = 'On Error Resume Next' . PHP_EOL .
            'Dim ' . $randomVarName . ', ' . $randomObjFso . ', ' . $randomObjErrFile . ', ' . $randomObjFile . PHP_EOL .
            'Set ' . $randomObjFso . ' = CreateObject("scripting.filesystemobject")' . PHP_EOL .
            'Set ' . $randomObjErrFile . ' = ' . $randomObjFso . '.CreateTextFile("' . $errFile . '", True)' . PHP_EOL .
            'Set ' . $randomObjFile . ' = ' . $randomObjFso . '.CreateTextFile("' . $checkFile . '", True)' . PHP_EOL . PHP_EOL;

        // Footer
        $footer = PHP_EOL . PHP_EOL .
            'If Err.Number <> 0 Then' . PHP_EOL .
            $randomObjErrFile . '.Write Err.Description' . PHP_EOL .
            'End If' . PHP_EOL .
            $randomObjFile . '.Write "' . self::END_PROCESS_STR . '"' . PHP_EOL .
            $randomObjFile . '.Close' . PHP_EOL .
            $randomObjErrFile . '.Close' . PHP_EOL;

        // Process
        file_put_contents( $scriptPath, $header . $content . $footer );
        $bearsamppWinbinder->exec( 'wscript.exe', '"' . $scriptPath . '"' );

        $timeout   = is_numeric( $timeout ) ? $timeout : ($timeout === true ? $bearsamppConfig->getScriptsTimeout() : false);
        $maxtime   = time() + $timeout;
        $noTimeout = $timeout === false;
        while ( $result === false || empty( $result ) ) {
            if ( file_exists( $checkFile ) ) {
                $check = file( $checkFile );
                if ( !empty( $check ) && trim( $check[0] ) == self::END_PROCESS_STR ) {
                    $result = file( $resultFile );
                    break;
                }
            }
            if ( $maxtime < time() && !$noTimeout ) {
                break;
            }
        }

        $err = file_get_contents( $errFile );
        if ( !empty( $err ) ) {
            Util::logError( 'VBS error on ' . $basename . ': ' . $err );
        }

        self::writeLog( 'Exec ' . $basename . ':' );
        self::writeLog( '-> content: ' . str_replace( PHP_EOL, ' \\\\ ', $content ) );
        self::writeLog( '-> errFile: ' . $errFile );
        self::writeLog( '-> checkFile: ' . $checkFile );
        self::writeLog( '-> resultFile: ' . $resultFile );
        self::writeLog( '-> scriptPath: ' . $scriptPath );

        if ( $result !== false && !empty( $result ) ) {
            $rebuildResult = array();
            foreach ( $result as $row ) {
                $row = trim( $row );
                if ( !empty( $row ) ) {
                    $rebuildResult[] = $row;
                }
            }
            $result = $rebuildResult;
            self::writeLog( '-> result: ' . substr( implode( ' \\\\ ', $result ), 0, 2048 ) );
        }
        else {
            self::writeLog( '-> result: N/A' );
        }

        return $result;
    }
}
