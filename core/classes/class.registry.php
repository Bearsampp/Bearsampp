<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Registry
 *
 * This class provides methods to interact with the Windows Registry using COM.
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
        Log::initClass($this);
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
        Log::debug($log, $bearsamppRoot->getRegistryLogFilePath());
    }

    /**
     * Checks if a registry key or entry exists.
     * Now uses Win32Native COM methods instead of VBScript.
     *
     * @param string $key The root key (e.g., HKEY_LOCAL_MACHINE).
     * @param string $subkey The subkey path.
     * @param string|null $entry The entry name (optional).
     * @return bool True if the key or entry exists, false otherwise.
     */
    public function exists($key, $subkey, $entry = null)
    {
        $this->writeLog('Exists ' . $key . '\\' . $subkey . '\\' . $entry);

        // Use Win32Native COM implementation
        $result = Win32Native::registryExists($key, $subkey, $entry);

        $this->writeLog('-> result: ' . ($result ? '1' : '0'));

        return $result;
    }

    /**
     * Retrieves the value of a registry entry.
     * Now uses Win32Native COM methods instead of VBScript.
     *
     * @param string $key The root key (e.g., HKEY_LOCAL_MACHINE).
     * @param string $subkey The subkey path.
     * @param string|null $entry The entry name (optional).
     * @return mixed The value of the registry entry, or false on error.
     */
    public function getValue($key, $subkey, $entry = null)
    {
        global $bearsamppLang;

        $this->latestError = null;

        $this->writeLog('GetValue ' . $key . '\\' . $subkey . '\\' . $entry);

        // Use Win32Native COM implementation
        $result = Win32Native::registryGetValue($key, $subkey, $entry);

        $this->writeLog('-> result: ' . $result);

        if ($result === null) {
            $this->latestError = $bearsamppLang->getValue(Lang::ERROR) . ' Registry value not found';
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
     * Now uses Win32Native COM methods instead of VBScript.
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

        $this->latestError = null;

        $this->writeLog('SetValue ' . $key . '\\' . $subkey . '\\' . $entry);
        $this->writeLog('-> value: ' . $value);
        $this->writeLog('-> type: ' . $type);

        // Map the VBS type to Win32Native registry type
        $regType = self::REG_SZ; // Default
        if ($type == 'SetExpandedStringValue') {
            $regType = self::REG_EXPAND_SZ;
        } elseif ($type == 'SetStringValue') {
            $regType = self::REG_SZ;
        }

        // Handle delete operations
        if ($type == 'DeleteValue' && !empty($entry)) {
            // Delete a value
            $result = Win32Native::registryDeleteValue($key, $subkey, $entry);
        } elseif ($type == 'DeleteValue' && empty($entry)) {
            // Delete a key
            $result = Win32Native::registryDeleteKey($key, $subkey);
        } else {
            // Set a value
            $result = Win32Native::registrySetValue($key, $subkey, $entry, $value, $regType);
        }

        if ($subkey == self::ENV_KEY) {
            Batch::refreshEnvVars();
        }

        $this->writeLog('-> result: ' . ($result ? 'success' : 'failed'));

        if (!$result) {
            $this->latestError = $bearsamppLang->getValue(Lang::ERROR) . ' Registry operation failed';
            return false;
        }

        // Verify the value was set correctly (for set operations)
        if ($type != 'DeleteValue' && !empty($value)) {
            $verifyValue = Win32Native::registryGetValue($key, $subkey, $entry);
            if ($verifyValue != $value) {
                $this->latestError = sprintf($bearsamppLang->getValue(Lang::REGISTRY_SET_ERROR_TEXT), $value);
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieves or generates the application binaries registry key.
     *
     * @param   bool  $fromRegistry  Determines whether to retrieve the key from the registry or generate it.
     *
     * @return string Returns the application binaries registry key.
     */
    public function getAppBinsRegKey($fromRegistry = true)
    {
        if ($fromRegistry) {
            $value = $this->getValue(
                self::HKEY_LOCAL_MACHINE,
                self::ENV_KEY,
                self::APP_BINS_REG_ENTRY
            );
            Log::debug('App reg key from registry: ' . $value);
        } else {
            global $bearsamppBins, $bearsamppTools;
            $value = '';
            if ($bearsamppBins->getApache()->isEnable()) {
                $value .= $bearsamppBins->getApache()->getSymlinkPath() . '/bin;';
            }
            if ($bearsamppBins->getPhp()->isEnable()) {
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . ';';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/pear;';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/deps;';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/imagick;';
            }
            if ($bearsamppBins->getNodejs()->isEnable()) {
                $value .= $bearsamppBins->getNodejs()->getSymlinkPath() . ';';
            }
            if ($bearsamppTools->getComposer()->isEnable()) {
                $value .= $bearsamppTools->getComposer()->getSymlinkPath() . ';';
                $value .= $bearsamppTools->getComposer()->getSymlinkPath() . '/vendor/bin;';
            }
            if ($bearsamppTools->getGhostscript()->isEnable()) {
                $value .= $bearsamppTools->getGhostscript()->getSymlinkPath() . '/bin;';
            }
            if ($bearsamppTools->getGit()->isEnable()) {
                $value .= $bearsamppTools->getGit()->getSymlinkPath() . '/bin;';
            }
            if ($bearsamppTools->getNgrok()->isEnable()) {
                $value .= $bearsamppTools->getNgrok()->getSymlinkPath() . ';';
            }
            if ($bearsamppTools->getPerl()->isEnable()) {
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/perl/site/bin;';
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/perl/bin;';
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/c/bin;';
            }
            if ($bearsamppTools->getPython()->isEnable()) {
                $value .= $bearsamppTools->getPython()->getSymlinkPath() . '/bin;';
            }
            if ($bearsamppTools->getRuby()->isEnable()) {
                $value .= $bearsamppTools->getRuby()->getSymlinkPath() . '/bin;';
            }
            $value = UtilPath::formatWindowsPath($value);
            Log::debug('Generated app bins reg key: ' . $value);
        }

        return $value;
    }

    /**
     * Sets the application binaries registry key.
     *
     * @param   string  $value  The new value for the application binaries.
     *
     * @return bool True on success, false on failure.
     */
    public function setAppBinsRegKey($value)
    {
        return $this->setStringValue(
            self::HKEY_LOCAL_MACHINE,
            self::ENV_KEY,
            self::APP_BINS_REG_ENTRY,
            $value
        );
    }

    /**
     * Retrieves the application path from the registry.
     *
     * @return mixed The value of the application path registry key or false on error.
     */
    public function getAppPathRegKey()
    {
        return $this->getValue(
            self::HKEY_LOCAL_MACHINE,
            self::ENV_KEY,
            self::APP_PATH_REG_ENTRY
        );
    }

    /**
     * Sets the application path in the registry.
     *
     * @param   string  $value  The new value for the application path.
     *
     * @return bool True on success, false on failure.
     */
    public function setAppPathRegKey($value)
    {
        return $this->setStringValue(
            self::HKEY_LOCAL_MACHINE,
            self::ENV_KEY,
            self::APP_PATH_REG_ENTRY,
            $value
        );
    }

    /**
     * Retrieves the system path from the registry.
     *
     * @return mixed The value of the system path registry key or false on error.
     */
    public function getSysPathRegKey()
    {
        return $this->getValue(
            self::HKEY_LOCAL_MACHINE,
            self::ENV_KEY,
            self::SYSPATH_REG_ENTRY
        );
    }

    /**
     * Sets the system path in the registry.
     *
     * @param   string  $value  The new value for the system path.
     *
     * @return bool True on success, false on failure.
     */
    public function setSysPathRegKey($value)
    {
        return $this->setExpandStringValue(
            self::HKEY_LOCAL_MACHINE,
            self::ENV_KEY,
            self::SYSPATH_REG_ENTRY,
            $value
        );
    }

    /**
     * Retrieves the processor identifier from the registry.
     *
     * @return mixed The value of the processor identifier registry key or false on error.
     */
    public function getProcessorRegKey()
    {
        return $this->getValue(
            self::HKEY_LOCAL_MACHINE,
            self::PROCESSOR_REG_SUBKEY,
            self::PROCESSOR_REG_ENTRY
        );
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
