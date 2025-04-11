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
 * Class Registry
 *
 * This class provides methods to interact with the Windows Registry using VBScript.
 * It includes functionalities to check the existence of registry keys, get and set values,
 * and delete registry entries. The class also logs operations and errors.
 */
class Registry
{
    const END_PROCESS_STR = 'FINISHED!';

    const HKEY_CLASSES_ROOT = 'HKCR';
    const HKEY_CURRENT_USER = 'HKCU';
    const HKEY_LOCAL_MACHINE = 'HKLM';
    const HKEY_USERS = 'HKEY_USERS';

    const REG_SZ = 'REG_SZ';
    const REG_EXPAND_SZ = 'REG_EXPAND_SZ';
    const REG_BINARY = 'REG_BINARY';
    const REG_DWORD = 'REG_DWORD';
    const REG_MULTI_SZ = 'REG_MULTI_SZ';

    const REG_ERROR_ENTRY = 'REG_ERROR_ENTRY';
    const REG_ERROR_SET = 'REG_ERROR_SET';
    const REG_NO_ERROR = 'REG_NO_ERROR';

    const ENV_KEY = 'SYSTEM\CurrentControlSet\Control\Session Manager\Environment';

    // App bins entry
    const APP_BINS_REG_ENTRY = 'BEARSAMPP_BINS';

    // App path entry
    const APP_PATH_REG_ENTRY = 'BEARSAMPP_PATH';

    // System path entry
    const SYSPATH_REG_ENTRY = 'Path';

    // Processor architecture
    const PROCESSOR_REG_SUBKEY = 'HARDWARE\DESCRIPTION\System\CentralProcessor\0';
    const PROCESSOR_REG_ENTRY = 'Identifier';

    private $latestError;

    /**
     * Registry constructor.
     * Initializes the Registry class and logs the initialization.
     */
    public function __construct()
    {
        Util::logInitClass($this);
        $this->latestError = null;
    }

    /**
     * Writes a log entry.
     *
     * @param string $log The log message to write.
     */
    private function writeLog($log)
    {
        global $bearsamppRoot;
        Util::logDebug($log, $bearsamppRoot->getRegistryLogFilePath());
    }

    /**
     * Checks if a registry key or entry exists.
     *
     * @param string $key The root key (e.g., HKEY_LOCAL_MACHINE).
     * @param string $subkey The subkey path.
     * @param string|null $entry The entry name (optional).
     * @return bool True if the key or entry exists, false otherwise.
     */
    public function exists($key, $subkey, $entry = null)
    {
        $basename = 'registryExists';
        $resultFile = Vbs::getResultFile($basename);

        $scriptContent = 'On Error Resume Next' . PHP_EOL;
        $scriptContent .= 'Err.Clear' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'Dim objShell, objFso, objFile, outFile, bExists' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'outFile = "' . $resultFile . '"' . PHP_EOL;
        $scriptContent .= 'Set objShell = WScript.CreateObject("WScript.Shell")' . PHP_EOL;
        $scriptContent .= 'Set objFso = CreateObject("Scripting.FileSystemObject")' . PHP_EOL;
        $scriptContent .= 'Set objFile = objFso.CreateTextFile(outFile, True)' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'strKey = "' . $key . '\\' . $subkey . '\\' . $entry . '"' . PHP_EOL;
        $scriptContent .= 'entryValue = objShell.RegRead(strKey)' . PHP_EOL;
        $scriptContent .= 'If Err.Number <> 0 Then' . PHP_EOL;
        $scriptContent .= '    If Right(strKey,1) = "\" Then' . PHP_EOL;
        $scriptContent .= '        If Instr(1, Err.Description, ssig, 1) <> 0 Then' . PHP_EOL;
        $scriptContent .= '            bExists = true' . PHP_EOL;
        $scriptContent .= '        Else' . PHP_EOL;
        $scriptContent .= '            bExists = false' . PHP_EOL;
        $scriptContent .= '        End If' . PHP_EOL;
        $scriptContent .= '    Else' . PHP_EOL;
        $scriptContent .= '        bExists = false' . PHP_EOL;
        $scriptContent .= '    End If' . PHP_EOL;
        $scriptContent .= '    Err.Clear' . PHP_EOL;
        $scriptContent .= 'Else' . PHP_EOL;
        $scriptContent .= '    bExists = true' . PHP_EOL;
        $scriptContent .= 'End If' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'On Error Goto 0' . PHP_EOL;
        $scriptContent .= 'If bExists = vbFalse Then' . PHP_EOL;
        $scriptContent .= '    objFile.Write "0"' . PHP_EOL;
        $scriptContent .= 'Else' . PHP_EOL;
        $scriptContent .= '    objFile.Write "1"' . PHP_EOL;
        $scriptContent .= 'End If' . PHP_EOL;
        $scriptContent .= 'objFile.Close' . PHP_EOL;

        $result = Vbs::exec($basename, $resultFile, $scriptContent);
        $result = isset($result[0]) ? $result[0] : null;

        $this->writeLog('Exists ' . $key . '\\' . $subkey . '\\' . $entry);
        $this->writeLog('-> result: ' . $result);

        return !empty($result) && intval($result) == 1;
    }

    /**
     * Retrieves the value of a registry entry.
     *
     * @param string $key The root key (e.g., HKEY_LOCAL_MACHINE).
     * @param string $subkey The subkey path.
     * @param string|null $entry The entry name (optional).
     * @return mixed The value of the registry entry, or false on error.
     */
    public function getValue($key, $subkey, $entry = null)
    {
        global $bearsamppLang;

        $basename = 'registryGetValue';
        $resultFile = Vbs::getResultFile($basename);
        $this->latestError = null;

        $scriptContent = 'On Error Resume Next' . PHP_EOL;
        $scriptContent .= 'Err.Clear' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'Dim objShell, objFso, objFile, outFile, entryValue' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'outFile = "' . $resultFile . '"' . PHP_EOL;
        $scriptContent .= 'Set objShell = WScript.CreateObject("WScript.Shell")' . PHP_EOL;
        $scriptContent .= 'Set objFso = CreateObject("Scripting.FileSystemObject")' . PHP_EOL;
        $scriptContent .= 'Set objFile = objFso.CreateTextFile(outFile, True)' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'entryValue = objShell.RegRead("' . $key . '\\' . $subkey . '\\' . $entry . '")' . PHP_EOL;
        $scriptContent .= 'If Err.Number <> 0 Then' . PHP_EOL;
        $scriptContent .= '    objFile.Write "' . self::REG_ERROR_ENTRY . '" & Err.Number & ": " & Err.Description' . PHP_EOL;
        $scriptContent .= 'Else' . PHP_EOL;
        $scriptContent .= '    objFile.Write entryValue' . PHP_EOL;
        $scriptContent .= 'End If' . PHP_EOL;
        $scriptContent .= 'objFile.Close' . PHP_EOL;

        $result = Vbs::exec($basename, $resultFile, $scriptContent);
        $result = isset($result[0]) ? $result[0] : null;
        $this->writeLog('GetValue ' . $key . '\\' . $subkey . '\\' . $entry);
        $this->writeLog('-> result: ' . $result);
        if (Util::startWith($result, self::REG_ERROR_ENTRY)) {
            $this->latestError = $bearsamppLang->getValue(Lang::ERROR) . ' ' . str_replace(self::REG_ERROR_ENTRY, '', $result);
            return false;
        }

        return $result;
    }

    /**
     * Sets a string value in the registry.
     *
     * @param string $key The root key (e.g., HKEY_LOCAL_MACHINE).
     * @param string $subkey The subkey path.
     * @param string $entry The entry name.
     * @param string $value The value to set.
     * @return bool True if the value was set successfully, false otherwise.
     */
    public function setStringValue($key, $subkey, $entry, $value)
    {
        return $this->setValue($key, $subkey, $entry, $value, 'SetStringValue');
    }

    /**
     * Sets an expanded string value in the registry.
     *
     * @param string $key The root key (e.g., HKEY_LOCAL_MACHINE).
     * @param string $subkey The subkey path.
     * @param string $entry The entry name.
     * @param string $value The value to set.
     * @return bool True if the value was set successfully, false otherwise.
     */
    public function setExpandStringValue($key, $subkey, $entry, $value)
    {
        return $this->setValue($key, $subkey, $entry, $value, 'SetExpandedStringValue');
    }

    /**
     * Deletes a registry entry.
     *
     * @param string $key The root key (e.g., HKEY_LOCAL_MACHINE).
     * @param string $subkey The subkey path.
     * @param string $entry The entry name.
     * @return bool True if the entry was deleted successfully, false otherwise.
     */
    public function deleteValue($key, $subkey, $entry)
    {
        $this->writeLog('delete');
        return $this->setValue($key, $subkey, $entry, null, 'DeleteValue');
    }

    /**
     * Sets a value in the registry.
     *
     * @param string $key The root key (e.g., HKEY_LOCAL_MACHINE).
     * @param string $subkey The subkey path.
     * @param string $entry The entry name.
     * @param string|null $value The value to set (optional).
     * @param string $type The type of value to set (e.g., SetStringValue).
     * @return bool True if the value was set successfully, false otherwise.
     */
    private function setValue($key, $subkey, $entry, $value, $type)
    {
        global $bearsamppLang;

        $basename = 'registrySetValue';
        $resultFile = Vbs::getResultFile($basename);
        $this->latestError = null;

        $strKey = $key;
        if ($key == self::HKEY_CLASSES_ROOT) {
            $key = '&H80000000';
        } elseif ($key == self::HKEY_CURRENT_USER) {
            $key = '&H80000001';
        } elseif ($key == self::HKEY_LOCAL_MACHINE) {
            $key = '&H80000002';
        } elseif ($key == self::HKEY_LOCAL_MACHINE) {
            $key = '&H80000003';
        }

        $scriptContent = 'On Error Resume Next' . PHP_EOL;
        $scriptContent .= 'Err.Clear' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'Const HKEY = ' . $key . PHP_EOL . PHP_EOL;

        $scriptContent .= 'Dim objShell, objRegistry, objFso, objFile, outFile, entryValue, newValue' . PHP_EOL . PHP_EOL;

        $scriptContent .= 'newValue = "' . (!empty($value) ? str_replace('"', '""', $value) : '') . '"' . PHP_EOL;
        $scriptContent .= 'outFile = "' . $resultFile . '"' . PHP_EOL;
        $scriptContent .= 'Set objShell = WScript.CreateObject("WScript.Shell")' . PHP_EOL;
        $scriptContent .= 'Set objRegistry = GetObject("winmgmts://./root/default:StdRegProv")' . PHP_EOL;
        $scriptContent .= 'Set objFso = CreateObject("Scripting.FileSystemObject")' . PHP_EOL;
        $scriptContent .= 'Set objFile = objFso.CreateTextFile(outFile, True)' . PHP_EOL . PHP_EOL;

        if (!empty($value)) {
            $scriptContent .= 'objRegistry.' . $type . ' HKEY, "' . $subkey . '", "' . $entry . '", newValue' . PHP_EOL;
        } elseif (!empty($entry)) {
            $scriptContent .= 'objRegistry.' . $type . ' HKEY, "' . $subkey . '", "' . $entry . '"' . PHP_EOL;
        } else {
            $scriptContent .= 'objRegistry.' . $type . ' HKEY, "' . $subkey . '"' . PHP_EOL;
        }
        $scriptContent .= 'If Err.Number <> 0 Then' . PHP_EOL;
        $scriptContent .= '    objFile.Write "' . self::REG_ERROR_ENTRY . '" & Err.Number & ": " & Err.Description' . PHP_EOL;
        $scriptContent .= 'Else' . PHP_EOL;
        if (!empty($value)) {
            $scriptContent .= '    entryValue = objShell.RegRead("' . $strKey . '\\' . $subkey . '\\' . $entry . '")' . PHP_EOL;
            $scriptContent .= '    If entryValue = newValue Then' . PHP_EOL;
            $scriptContent .= '        objFile.Write "' . self::REG_NO_ERROR . '"' . PHP_EOL;
            $scriptContent .= '    Else' . PHP_EOL;
            $scriptContent .= '        objFile.Write "' . self::REG_ERROR_SET . '" & newValue' . PHP_EOL;
            $scriptContent .= '    End If' . PHP_EOL;
        } else {
            $scriptContent .= '    objFile.Write "' . self::REG_NO_ERROR . '"' . PHP_EOL;
        }
        $scriptContent .= 'End If' . PHP_EOL;
        $scriptContent .= 'objFile.Close' . PHP_EOL;

        $result = Vbs::exec($basename, $resultFile, $scriptContent);
        $result = isset($result[0]) ? $result[0] : null;

        if ($subkey == self::ENV_KEY) {
            Batch::refreshEnvVars();
        }

        $this->writeLog('SetValue ' . $strKey . '\\' . $subkey . '\\' . $entry);
        $this->writeLog('-> value: ' . $value);
        $this->writeLog('-> result: ' . $result);
        if (Util::startWith($result, self::REG_ERROR_SET)) {
            $this->latestError = sprintf($bearsamppLang->getValue(Lang::REGISTRY_SET_ERROR_TEXT), str_replace(self::REG_ERROR_SET, '', $result));
            return false;
        } elseif (Util::startWith($result, self::REG_ERROR_ENTRY)) {
            $this->latestError = $bearsamppLang->getValue(Lang::ERROR) . ' ' . str_replace(self::REG_ERROR_ENTRY, '', $result);
            return false;
        }

        return $result == self::REG_NO_ERROR;
    }

    /**
     * Retrieves the latest error message.
     *
     * @return string|null The latest error message, or null if no error occurred.
     */
    public function getLatestError()
    {
        return $this->latestError;
    }
}
