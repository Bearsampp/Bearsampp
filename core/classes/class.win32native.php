<?php
/*
 * Copyright (c) 2022-2025 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Win32Native
 *
 * This class provides native Windows operations using PHP COM extension.
 * Replaces VBScript operations with direct COM/WMI access from PHP.
 * Uses Windows Management Instrumentation (WMI) and WScript.Shell COM objects.
 */
class Win32Native
{
    // ========================================================================
    // COM Connection Cache
    // Each COM object is created once per PHP process and reused across calls.
    // On a COM/WMI failure the catching method calls resetConnections() so the
    // next call gets a fresh object instead of reusing a broken cached instance.
    // ========================================================================

    /** @var COM|null Cached WMI connection to root/cimv2 (process/service queries) */
    private static ?COM $wmiCimv2 = null;

    /** @var COM|null Cached WMI connection to root/default:StdRegProv (registry key checks) */
    private static ?COM $wmiStdRegProv = null;

    /** @var COM|null Cached WScript.Shell object (registry values, shortcuts, special folders) */
    private static ?COM $wscriptShell = null;

    /**
     * Returns the cached WMI cimv2 connection, creating it on first use.
     */
    private static function getWmiCimv2(): COM
    {
        if (self::$wmiCimv2 === null) {
            self::$wmiCimv2 = new COM("winmgmts://./root/cimv2");
        }
        return self::$wmiCimv2;
    }

    /**
     * Returns the cached WMI StdRegProv connection, creating it on first use.
     */
    private static function getWmiStdRegProv(): COM
    {
        if (self::$wmiStdRegProv === null) {
            self::$wmiStdRegProv = new COM("winmgmts://./root/default:StdRegProv");
        }
        return self::$wmiStdRegProv;
    }

    /**
     * Returns the cached WScript.Shell object, creating it on first use.
     */
    private static function getWscriptShell(): COM
    {
        if (self::$wscriptShell === null) {
            self::$wscriptShell = new COM("WScript.Shell");
        }
        return self::$wscriptShell;
    }

    /**
     * Clears all cached COM connections.
     * Call this after a COM operation fails so the next call gets a fresh connection.
     */
    public static function resetConnections(): void
    {
        self::$wmiCimv2      = null;
        self::$wmiStdRegProv = null;
        self::$wscriptShell  = null;
    }

    /**
     * Gets a list of running processes using COM/WMI.
     * Replaces VBS WMI process query with direct PHP COM access.
     *
     * @param array $properties Optional array of properties to retrieve (e.g., ['Name', 'ProcessID', 'ExecutablePath'])
     * @return array Array of processes with requested information
     */
    public static function getProcessList($properties = [])
    {
        Util::logDebug('getProcessList: Listing processes (COM/WMI)');

        $startTime = microtime(true);

        try {
            // Create WMI connection
            $wmi = self::getWmiCimv2();

            // Build WQL query
            if (empty($properties)) {
                $properties = ['Name', 'ProcessID', 'ExecutablePath'];
            }

            $selectClause = implode(', ', $properties);
            $query = "SELECT {$selectClause} FROM Win32_Process";

            // Execute query
            $processes = $wmi->ExecQuery($query);

            // Convert to array
            $result = [];
            foreach ($processes as $proc) {
                $process = [];
                foreach ($properties as $prop) {
                    // Handle property access
                    try {
                        $value = $proc->$prop;
                        $process[$prop] = $value ?? '';
                    } catch (Exception $e) {
                        $process[$prop] = '';
                    }
                }
                $result[] = $process;
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Util::logDebug('getProcessList: Found ' . count($result) . ' processes in ' . $duration . 'ms (COM/WMI)');

            return $result;

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('getProcessList: COM exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Kills a process by PID using COM/WMI.
     * Replaces VBS WMI process termination with direct PHP COM access.
     *
     * @param int $pid The process ID to kill
     * @return bool True on success, false on failure
     */
    public static function killProcess($pid)
    {
        // Validate PID
        if (!is_numeric($pid) || $pid <= 0) {
            Util::logError('killProcess: Invalid PID: ' . $pid);
            return false;
        }

        Util::logDebug('killProcess: Killing process PID ' . $pid . ' (COM/WMI)');

        $startTime = microtime(true);

        try {
            // Create WMI connection
            $wmi = self::getWmiCimv2();

            // Query for specific process
            $query = "SELECT * FROM Win32_Process WHERE ProcessID = {$pid}";
            $processes = $wmi->ExecQuery($query);

            // Terminate the process
            $found = false;
            $terminateResult = null;
            foreach ($processes as $proc) {
                $terminateResult = $proc->Terminate();
                $found = true;
                break;
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            if (!$found) {
                Util::logDebug('killProcess: Process ' . $pid . ' not found');
                return false;
            }

            // Check if Terminate() returned success (0 = success)
            if ($terminateResult !== 0) {
                Util::logError('killProcess: Terminate() failed with status code: ' . $terminateResult);
                return false;
            }

            // Additional verification: check if process still exists after a short delay
            usleep(100000); // Wait 100ms for termination to take effect
            if (self::processExists($pid)) {
                Util::logError('killProcess: Process ' . $pid . ' still exists after termination attempt');
                return false;
            }

            Util::logDebug('killProcess: Successfully killed process ' . $pid . ' in ' . $duration . 'ms (COM/WMI)');
            return true;

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('killProcess: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if a process with the given PID exists.
     *
     * @param int $pid The process ID to check
     * @return bool True if process exists, false otherwise
     */
    public static function processExists($pid)
    {
        if (!is_numeric($pid) || $pid <= 0) {
            return false;
        }

        try {
            $wmi = self::getWmiCimv2();
            $query = "SELECT ProcessID FROM Win32_Process WHERE ProcessID = {$pid}";
            $processes = $wmi->ExecQuery($query);

            foreach ($processes as $proc) {
                return true;
            }

            return false;

        } catch (Exception $e) {
            self::resetConnections();
            return false;
        }
    }

    /**
     * Gets information about a specific process by PID.
     *
     * @param int $pid The process ID
     * @param array $properties Properties to retrieve
     * @return array|false Process information or false if not found
     */
    public static function getProcessInfo($pid, $properties = [])
    {
        if (!is_numeric($pid) || $pid <= 0) {
            return false;
        }

        if (empty($properties)) {
            $properties = ['Name', 'ProcessID', 'ExecutablePath', 'CommandLine'];
        }

        try {
            $wmi = self::getWmiCimv2();
            $selectClause = implode(', ', $properties);
            $query = "SELECT {$selectClause} FROM Win32_Process WHERE ProcessID = {$pid}";
            $processes = $wmi->ExecQuery($query);

            foreach ($processes as $proc) {
                $result = [];
                foreach ($properties as $prop) {
                    try {
                        $value = $proc->$prop;
                        $result[$prop] = $value ?? '';
                    } catch (Exception $e) {
                        $result[$prop] = '';
                    }
                }
                return $result;
            }

            return false;

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('getProcessInfo: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Finds processes by name.
     *
     * @param string $name Process name (e.g., 'notepad.exe')
     * @param array $properties Properties to retrieve
     * @return array Array of matching processes
     */
    public static function findProcessesByName($name, $properties = [])
    {
        if (empty($name)) {
            return [];
        }

        if (empty($properties)) {
            $properties = ['Name', 'ProcessID', 'ExecutablePath'];
        }

        try {
            $wmi = self::getWmiCimv2();
            $selectClause = implode(', ', $properties);

            // Sanitize the name parameter to prevent WQL injection
            // Escape single quotes by doubling them (WQL standard)
            $safeName = str_replace("'", "''", $name);

            $query = "SELECT {$selectClause} FROM Win32_Process WHERE Name = '{$safeName}'";
            $processes = $wmi->ExecQuery($query);

            $result = [];
            foreach ($processes as $proc) {
                $process = [];
                foreach ($properties as $prop) {
                    try {
                        $value = $proc->$prop;
                        $process[$prop] = $value ?? '';
                    } catch (Exception $e) {
                        $process[$prop] = '';
                    }
                }
                $result[] = $process;
            }

            return $result;

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('findProcessesByName: COM exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Gets the current PHP process ID.
     *
     * @return int Current process ID
     */
    public static function getCurrentPid()
    {
        return getmypid();
    }

    // ========================================================================
    // Registry Operations (COM/WScript.Shell)
    // ========================================================================

    /**
     * Maps registry hive abbreviations to full names for WScript.Shell.
     *
     * @param string $hive The registry hive (HKLM, HKCU, HKCR, HKU)
     * @return string The full hive name
     */
    private static function mapRegistryHive($hive)
    {
        $hiveMap = [
            'HKLM' => 'HKLM',
            'HKCU' => 'HKCU',
            'HKCR' => 'HKCR',
            'HKU' => 'HKU',
            'HKEY_LOCAL_MACHINE' => 'HKLM',
            'HKEY_CURRENT_USER' => 'HKCU',
            'HKEY_CLASSES_ROOT' => 'HKCR',
            'HKEY_USERS' => 'HKU',
        ];

        return isset($hiveMap[$hive]) ? $hiveMap[$hive] : $hive;
    }

    /**
     * Checks if a registry key or value exists using COM.
     * Replaces VBS/reg.exe with direct COM access.
     *
     * Uses StdRegProv for key existence checks (more reliable than WScript.Shell::RegRead).
     * Uses WScript.Shell::RegRead for value existence checks.
     *
     * @param string $hive The registry hive (HKLM, HKCU, etc.)
     * @param string $key The registry key path
     * @param string|null $value The value name (null to check key existence only)
     * @return bool True if exists, false otherwise
     */
    public static function registryExists($hive, $key, $value = null)
    {
        $hive = self::mapRegistryHive($hive);
        $regPath = $hive . '\\' . $key;

        Util::logDebug('registryExists: Checking ' . $regPath . ($value !== null ? '\\' . $value : ' (key)') . ' (COM)');

        try {
            if ($value === null) {
                // Check if the key itself exists
                // Use StdRegProv::EnumKey for reliable key existence checking
                // This avoids false negatives from checking for a default value that may not exist
                try {
                    $wmi = self::getWmiStdRegProv();

                    // Map hive names to WMI constants
                    $hiveConstMap = [
                        'HKCR' => 0x80000000,  // HKEY_CLASSES_ROOT
                        'HKCU' => 0x80000001,  // HKEY_CURRENT_USER
                        'HKLM' => 0x80000002,  // HKEY_LOCAL_MACHINE
                        'HKU'  => 0x80000003,  // HKEY_USERS
                    ];
                    
                    $hConst = $hiveConstMap[$hive] ?? 0x80000002;  // Default to HKLM

                    // EnumKey checks if parent key's subkeys contain the requested key
                    // For root-level checks, pass empty parent
                    $pathParts = explode('\\', $key);
                    
                    if (count($pathParts) === 1) {
                        // Top-level key: parent is root
                        $parentKey = '';
                        $keyName = $pathParts[0];
                    } else {
                        // Nested key: split parent from key name
                        $keyName = array_pop($pathParts);
                        $parentKey = implode('\\', $pathParts);
                    }
                    
                    $subKeys = null;
                    $rc = $wmi->EnumKey($hConst, $parentKey, $subKeys);
                    
                    if ($rc !== 0 || !is_array($subKeys)) {
                        Util::logDebug('registryExists: Key not found (EnumKey failed)');
                        return false;
                    }
                    
                    // Check if our key name is in the list of subkeys
                    $exists = false;
                    foreach ($subKeys as $subKey) {
                        if (strcasecmp($subKey, $keyName) === 0) {
                            $exists = true;
                            break;
                        }
                    }
                    
                    if ($exists) {
                        Util::logDebug('registryExists: Key found');
                        return true;
                    } else {
                        Util::logDebug('registryExists: Key not found');
                        return false;
                    }

                } catch (Exception $e) {
                    self::$wmiStdRegProv = null;
                    Util::logError('registryExists: StdRegProv exception during key check: ' . $e->getMessage());
                    return false;
                }
            } else {
                // Check if a specific value exists within the key
                // Use WScript.Shell::RegRead for value existence
                try {
                    $shell = self::getWscriptShell();
                    $valuePath = $regPath . '\\' . $value;
                    $shell->RegRead($valuePath);
                    Util::logDebug('registryExists: Value found');
                    return true;
                } catch (Exception $e) {
                    Util::logDebug('registryExists: Value not found');
                    return false;
                }
            }

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('registryExists: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gets a value from the Windows registry using COM.
     * Replaces VBS/reg.exe with direct COM access.
     *
     * @param string $hive The registry hive (HKLM, HKCU, etc.)
     * @param string $key The registry key path
     * @param string $value The value name (empty string for default value)
     * @return mixed|null The registry value data, or null if not found
     */
    public static function registryGetValue($hive, $key, $value = '')
    {
        $hive = self::mapRegistryHive($hive);
        $regPath = $hive . '\\' . $key;

        if ($value !== '') {
            $regPath .= '\\' . $value;
        } else {
            // For default value, append backslash
            $regPath .= '\\';
        }

        Util::logDebug('registryGetValue: Reading ' . $regPath . ' (COM)');

        $startTime = microtime(true);

        try {
            $shell = self::getWscriptShell();
            $result = $shell->RegRead($regPath);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            // Convert result to appropriate PHP type
            if (is_object($result)) {
                // COM objects need special handling
                $result = (string)$result;
            }

            Util::logDebug('registryGetValue: Found value in ' . $duration . 'ms (COM)');
            return $result;

        } catch (Exception $e) {
            Util::logDebug('registryGetValue: Value not found');
            return null;
        }
    }

    /**
     * Sets a value in the Windows registry using COM.
     * Replaces VBS/reg.exe with direct COM access.
     *
     * @param string $hive The registry hive (HKLM, HKCU, etc.)
     * @param string $key The registry key path
     * @param string $value The value name
     * @param mixed $data The data to write
     * @param string $type The registry type (REG_SZ, REG_EXPAND_SZ, REG_DWORD, REG_BINARY)
     * @return bool True on success, false on failure
     */
    public static function registrySetValue($hive, $key, $value, $data, $type = 'REG_SZ')
    {
        $hive = self::mapRegistryHive($hive);
        $regPath = $hive . '\\' . $key . '\\' . $value;

        // Validate type
        $validTypes = ['REG_SZ', 'REG_EXPAND_SZ', 'REG_DWORD', 'REG_BINARY'];
        if (!in_array($type, $validTypes)) {
            Util::logError('registrySetValue: Invalid type: ' . $type);
            return false;
        }

        Util::logDebug('registrySetValue: Writing ' . $regPath . ' (' . $type . ') (COM)');

        $startTime = microtime(true);

        try {
            $shell = self::getWscriptShell();

            // Convert data based on type
            if ($type === 'REG_DWORD') {
                $data = (int)$data;
            }

            // Write the value
            $shell->RegWrite($regPath, $data, $type);

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Util::logDebug('registrySetValue: Successfully wrote value in ' . $duration . 'ms (COM)');

            return true;

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('registrySetValue: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a value from the Windows registry using COM.
     * Replaces VBS/reg.exe with direct COM access.
     *
     * @param string $hive The registry hive (HKLM, HKCU, etc.)
     * @param string $key The registry key path
     * @param string $value The value name to delete
     * @return bool True on success, false on failure
     */
    public static function registryDeleteValue($hive, $key, $value)
    {
        $hive = self::mapRegistryHive($hive);
        $regPath = $hive . '\\' . $key . '\\' . $value;

        Util::logDebug('registryDeleteValue: Deleting ' . $regPath . ' (COM)');

        $startTime = microtime(true);

        try {
            $shell = self::getWscriptShell();

            // Delete the value
            $shell->RegDelete($regPath);

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Util::logDebug('registryDeleteValue: Successfully deleted value in ' . $duration . 'ms (COM)');

            return true;

        } catch (Exception $e) {
            // If the value doesn't exist, that's OK
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'Unable to remove') !== false ||
                strpos($errorMsg, 'Invalid root') !== false) {
                Util::logDebug('registryDeleteValue: Value does not exist (already deleted)');
                return true;
            }

            self::resetConnections();
            Util::logError('registryDeleteValue: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a registry key and all its subkeys using COM.
     * Additional helper method for key deletion.
     *
     * @param string $hive The registry hive (HKLM, HKCU, etc.)
     * @param string $key The registry key path to delete
     * @return bool True on success, false on failure
     */
    public static function registryDeleteKey($hive, $key)
    {
        $hive = self::mapRegistryHive($hive);
        $regPath = $hive . '\\' . $key . '\\';

        Util::logDebug('registryDeleteKey: Deleting ' . $regPath . ' (COM)');

        try {
            $shell = self::getWscriptShell();

            // Delete the key (note the trailing backslash)
            $shell->RegDelete($regPath);

            Util::logDebug('registryDeleteKey: Successfully deleted key (COM)');
            return true;

        } catch (Exception $e) {
            // If the key doesn't exist, that's OK
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'Unable to remove') !== false ||
                strpos($errorMsg, 'Invalid root') !== false) {
                Util::logDebug('registryDeleteKey: Key does not exist (already deleted)');
                return true;
            }

            self::resetConnections();
            Util::logError('registryDeleteKey: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================================================
    // Shortcuts & Special Paths (COM/WScript.Shell)
    // ========================================================================

    /**
     * Gets a Windows special folder path using COM.
     * Replaces VBS with direct COM access.
     *
     * @param string $folderName The special folder name (Desktop, Startup, etc.)
     * @return string|false The folder path, or false on failure
     */
    public static function getSpecialFolderPath($folderName)
    {
        Util::logDebug('getSpecialFolderPath: Getting ' . $folderName . ' path (COM)');

        $startTime = microtime(true);

        try {
            $shell = self::getWscriptShell();

            // Get the special folder path
            $path = $shell->SpecialFolders($folderName);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            if ($path && !empty($path)) {
                // Convert to Unix-style path
                $path = str_replace('\\', '/', $path);
                Util::logDebug('getSpecialFolderPath: Found ' . $folderName . ' in ' . $duration . 'ms (COM)');
                return $path;
            } else {
                Util::logDebug('getSpecialFolderPath: ' . $folderName . ' not found');
                return false;
            }

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('getSpecialFolderPath: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a Windows shortcut using COM.
     * Replaces VBS with direct COM access.
     *
     * @param string $shortcutPath Full path where to save the shortcut (.lnk file)
     * @param string $targetPath Path to the target executable
     * @param string $workingDir Working directory for the shortcut
     * @param string $description Shortcut description
     * @param string $iconPath Path to icon file
     * @return bool True on success, false on failure
     */
    public static function createShortcut($shortcutPath, $targetPath, $workingDir = '', $description = '', $iconPath = '')
    {
        Util::logDebug('createShortcut: Creating shortcut at ' . $shortcutPath . ' (COM)');

        $startTime = microtime(true);

        try {
            $shell = self::getWscriptShell();

            // Create the shortcut object
            $shortcut = $shell->CreateShortcut($shortcutPath);

            // Set shortcut properties
            $shortcut->TargetPath = $targetPath;

            if (!empty($workingDir)) {
                $shortcut->WorkingDirectory = $workingDir;
            }

            if (!empty($description)) {
                $shortcut->Description = $description;
            }

            if (!empty($iconPath)) {
                $shortcut->IconLocation = $iconPath;
            }

            // Save the shortcut
            $shortcut->Save();

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Util::logDebug('createShortcut: Successfully created shortcut in ' . $duration . 'ms (COM)');

            return true;

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('createShortcut: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================================================
    // File Operations (COM/FileSystemObject or Native PHP)
    // ========================================================================

    /**
     * Counts files and folders recursively using native PHP.
     * Replaces VBS with native PHP (faster than COM FileSystemObject).
     *
     * @param string $path The path to count files and folders in
     * @return int|false The count of files and folders, or false on failure
     */
    public static function countFilesFolders($path)
    {
        Util::logDebug('countFilesFolders: Counting in ' . $path . ' (Native PHP)');

        $startTime = microtime(true);

        try {
            if (!is_dir($path)) {
                Util::logError('countFilesFolders: Path is not a directory: ' . $path);
                return false;
            }

            $count = 0;

            // Use RecursiveDirectoryIterator for efficient recursive counting
            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $item) {
                    $count++;
                }
            } catch (Exception $e) {
                // If RecursiveIterator fails, fall back to manual recursion
                Util::logDebug('countFilesFolders: RecursiveIterator failed, using manual recursion');
                $count = self::countFilesFoldersManual($path);
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Util::logDebug('countFilesFolders: Counted ' . $count . ' items in ' . $duration . 'ms (Native PHP)');

            return $count;

        } catch (Exception $e) {
            Util::logError('countFilesFolders: Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Manual recursive file/folder counting (fallback method).
     * Helper method for countFilesFolders.
     *
     * @param string $path The path to count
     * @return int The count of files and folders
     */
    private static function countFilesFoldersManual($path)
    {
        $count = 0;

        try {
            $items = @scandir($path);

            if ($items === false) {
                return 0;
            }

            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $fullPath = $path . DIRECTORY_SEPARATOR . $item;
                $count++; // Count this item

                if (is_dir($fullPath)) {
                    // Recursively count subdirectory
                    $count += self::countFilesFoldersManual($fullPath);
                }
            }
        } catch (Exception $e) {
            // Silently handle errors (permission denied, etc.)
        }

        return $count;
    }

    /**
     * Counts files and folders recursively using COM FileSystemObject.
     * Alternative COM-based implementation (slower than native PHP).
     *
     * @param string $path The path to count files and folders in
     * @return int|false The count of files and folders, or false on failure
     */
    public static function countFilesFoldersCOM($path)
    {
        Util::logDebug('countFilesFoldersCOM: Counting in ' . $path . ' (COM/FSO)');

        $startTime = microtime(true);

        try {
            $fso = new COM("Scripting.FileSystemObject");

            if (!$fso->FolderExists($path)) {
                Util::logError('countFilesFoldersCOM: Path does not exist: ' . $path);
                return false;
            }

            $count = self::countFolderItemsCOM($fso, $path);

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Util::logDebug('countFilesFoldersCOM: Counted ' . $count . ' items in ' . $duration . 'ms (COM/FSO)');

            return $count;

        } catch (Exception $e) {
            Util::logError('countFilesFoldersCOM: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Recursive helper for COM-based file/folder counting.
     * Helper method for countFilesFoldersCOM.
     *
     * @param COM $fso FileSystemObject instance
     * @param string $path The path to count
     * @return int The count of files and folders
     */
    private static function countFolderItemsCOM($fso, $path)
    {
        try {
            $folder = $fso->GetFolder($path);

            // Count files and subfolders in this folder
            $count = $folder->Files->Count + $folder->SubFolders->Count;

            // Recursively count subfolders
            foreach ($folder->SubFolders as $subFolder) {
                $count += self::countFolderItemsCOM($fso, $subFolder->Path);
            }

            return $count;

        } catch (Exception $e) {
            // Silently handle errors (permission denied, etc.)
            return 0;
        }
    }

    // ========================================================================
    // Service Operations (COM/WMI)
    // ========================================================================

    /**
     * Gets information about a Windows service using COM/WMI.
     * Replaces VBS with direct COM/WMI access.
     *
     * @param string $serviceName The name of the service
     * @param array $properties Optional array of properties to retrieve
     * @return array|false Service information array, or false on failure
     */
    public static function getServiceInfo($serviceName, $properties = [])
    {
        Util::logDebug('getServiceInfo: Getting info for service ' . $serviceName . ' (COM/WMI)');

        $startTime = microtime(true);

        try {
            // Create WMI connection
            $wmi = self::getWmiCimv2();

            // Default properties if none specified
            if (empty($properties)) {
                $properties = [
                    'Name',
                    'DisplayName',
                    'State',
                    'Status',
                    'StartMode',
                    'PathName',
                    'ProcessId',
                    'Started',
                    'StartName',
                    'Description'
                ];
            }

            // Build WQL query
            $selectClause = implode(', ', $properties);
            $safeServiceName = str_replace("'", "''", $serviceName);
            $query = "SELECT {$selectClause} FROM Win32_Service WHERE Name = '{$safeServiceName}'";

            // Execute query
            $services = $wmi->ExecQuery($query);

            // Get the first (and should be only) result
            foreach ($services as $service) {
                $result = [];
                foreach ($properties as $prop) {
                    try {
                        $value = $service->$prop;
                        $result[$prop] = $value ?? '';
                    } catch (Exception $e) {
                        $result[$prop] = '';
                    }
                }

                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Util::logDebug('getServiceInfo: Found service in ' . $duration . 'ms (COM/WMI)');

                return $result;
            }

            // Service not found
            Util::logDebug('getServiceInfo: Service not found');
            return false;

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('getServiceInfo: COM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lists all Windows services using COM/WMI.
     * Additional helper method.
     *
     * @param array $properties Optional array of properties to retrieve
     * @return array Array of service information
     */
    public static function listServices($properties = [])
    {
        Util::logDebug('listServices: Listing all services (COM/WMI)');

        $startTime = microtime(true);

        try {
            // Create WMI connection
            $wmi = self::getWmiCimv2();

            // Default properties if none specified
            if (empty($properties)) {
                $properties = ['Name', 'DisplayName', 'State', 'StartMode'];
            }

            // Build WQL query
            $selectClause = implode(', ', $properties);
            $query = "SELECT {$selectClause} FROM Win32_Service";

            // Execute query
            $services = $wmi->ExecQuery($query);

            // Convert to array
            $result = [];
            foreach ($services as $service) {
                $serviceInfo = [];
                foreach ($properties as $prop) {
                    try {
                        $value = $service->$prop;
                        $serviceInfo[$prop] = $value ?? '';
                    } catch (Exception $e) {
                        $serviceInfo[$prop] = '';
                    }
                }
                $result[] = $serviceInfo;
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Util::logDebug('listServices: Found ' . count($result) . ' services in ' . $duration . 'ms (COM/WMI)');

            return $result;

        } catch (Exception $e) {
            self::resetConnections();
            Util::logError('listServices: COM exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Checks if a Windows service exists.
     * Additional helper method.
     *
     * @param string $serviceName The name of the service
     * @return bool True if service exists, false otherwise
     */
    public static function serviceExists($serviceName)
    {
        try {
            $wmi = self::getWmiCimv2();
            // Sanitize the serviceName parameter to prevent WQL injection
            // Escape single quotes by doubling them (WQL standard)
            $safeServiceName = str_replace("'", "''", $serviceName);
            $query = "SELECT Name FROM Win32_Service WHERE Name = '{$safeServiceName}'";

            // Execute query
            $services = $wmi->ExecQuery($query);

            foreach ($services as $service) {
                return true;
            }

            return false;

        } catch (Exception $e) {
            self::resetConnections();
            return false;
        }
    }

    /**
     * Gets the state of a Windows service.
     * Additional helper method.
     *
     * @param string $serviceName The name of the service
     * @return string|false The service state (Running, Stopped, etc.), or false if not found
     */
    public static function getServiceState($serviceName)
    {
        try {
            $wmi = self::getWmiCimv2();
            // Sanitize the serviceName parameter to prevent WQL injection
            // Escape single quotes by doubling them (WQL standard)
            $safeServiceName = str_replace("'", "''", $serviceName);
            $query = "SELECT State FROM Win32_Service WHERE Name = '{$safeServiceName}'";

            // Execute query
            $services = $wmi->ExecQuery($query);

            foreach ($services as $service) {
                return $service->State;
            }

            return false;

        } catch (Exception $e) {
            self::resetConnections();
            return false;
        }
    }

    // ========================================================================
    // Browser Detection (COM/Registry)
    // ========================================================================

    /**
     * Gets the default browser's executable path using COM.
     * Replaces VBS with direct COM registry access.
     *
     * @return string|false The path to the default browser executable, or false on failure
     */
    public static function getDefaultBrowser()
    {
        Util::logDebug('getDefaultBrowser: Reading default browser (COM)');

        $startTime = microtime(true);

        // Try to read the default browser from registry
        $browserPath = self::registryGetValue('HKLM', 'SOFTWARE\\Classes\\http\\shell\\open\\command', '');

        if ($browserPath === null) {
            Util::logDebug('getDefaultBrowser: No default browser found');
            return false;
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Extract the executable path from the command
        // Format is usually: "C:\Program Files\Browser\browser.exe" -- "%1"
        if (preg_match('/"([^"]+)"/', $browserPath, $matches)) {
            $path = $matches[1];
        } else {
            // No quotes, take everything before the first space or use as-is
            $path = trim(explode(' ', $browserPath)[0]);
            $path = str_replace('"', '', $path);
        }

        Util::logDebug('getDefaultBrowser: Found browser in ' . $duration . 'ms (COM)');
        return $path;
    }

    /**
     * Gets a list of installed browsers using hybrid approach.
     * Hybrid approach - known browsers + pattern matching (NO VBS!)
     *
     * @return array An array of browser executable paths (empty array if none found)
     */
    public static function getInstalledBrowsers()
    {
        Util::logDebug('getInstalledBrowsers: Enumerating installed browsers (Hybrid - No VBS)');

        $startTime = microtime(true);
        $browsers = [];

        // Known browser registry names (covers 99% of browsers)
        $knownBrowsers = [
            // Major browsers
            'Google Chrome',
            'Microsoft Edge',
            'Opera',
            'Brave',
            'Vivaldi',
            'Chromium',

            // Firefox variants
            'Firefox',
            'Firefox-308046B0AF4A39CB',  // Common Firefox GUID
            'Firefox Developer Edition',
            'Firefox Nightly',

            // Other browsers
            'Safari',
            'Waterfox',
            'Pale Moon',
            'Yandex',
            'IEXPLORE.EXE',
            'Total Browser',
            'Maxthon',
            'Seamonkey',
            'K-Meleon',
            'Basilisk',
            'Tor Browser',
            'Slimjet',
            'Epic Privacy Browser',
            'Comodo Dragon',
            'SRWare Iron',
            'Cent Browser',
            '360 Browser',
            'UC Browser',
            'Avast Secure Browser',
            'AVG Secure Browser',
        ];

        // Registry paths to check
        $registryPaths = [
            ['hive' => 'HKLM', 'key' => 'SOFTWARE\\WOW6432Node\\Clients\\StartMenuInternet'],
            ['hive' => 'HKLM', 'key' => 'SOFTWARE\\Clients\\StartMenuInternet'],
            ['hive' => 'HKCU', 'key' => 'SOFTWARE\\Clients\\StartMenuInternet'],
        ];

        // Check each known browser in each registry path
        foreach ($registryPaths as $regPath) {
            $hive = $regPath['hive'];
            $basePath = $regPath['key'];

            foreach ($knownBrowsers as $browserName) {
                try {
                    // Try to read the browser's command path
                    $commandPath = self::registryGetValue(
                        $hive,
                        $basePath . '\\' . $browserName . '\\shell\\open\\command',
                        ''
                    );

                    if ($commandPath !== null && !empty($commandPath)) {
                        // Extract executable path
                        $exePath = self::extractBrowserExecutablePath($commandPath);

                        if ($exePath && !in_array($exePath, $browsers)) {
                            Util::logDebug('getInstalledBrowsers: Found ' . $browserName . ': ' . $exePath);
                            $browsers[] = $exePath;
                        }
                    }
                } catch (Exception $e) {
                    // Browser not found, continue
                    continue;
                }
            }
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);
        Util::logDebug('getInstalledBrowsers: Found ' . count($browsers) . ' browser(s) in ' . $duration . 'ms (Hybrid - No VBS)');

        // Always return an array (possibly empty) to simplify call sites
        return $browsers;
    }

    /**
     * Extracts the executable path from a browser command string.
     * Helper method for browser detection.
     *
     * @param string $commandPath The command path string from registry
     * @return string|false The extracted executable path, or false on failure
     */
    private static function extractBrowserExecutablePath($commandPath)
    {
        if (empty($commandPath)) {
            return false;
        }

        // Method 1: Extract path from quotes
        // Format: "C:\Program Files\Browser\browser.exe" --arguments
        if (preg_match('/"([^"]+\\.exe)"/i', $commandPath, $matches)) {
            return $matches[1];
        }

        // Method 2: Extract path without quotes but with .exe
        // Format: C:\Program Files\Browser\browser.exe --arguments
        if (preg_match('/^([^\\s]+\\.exe)/i', trim($commandPath), $matches)) {
            return $matches[1];
        }

        // Method 3: Handle paths with spaces but no quotes (rare)
        // Try to find .exe in the string
        if (preg_match('/([A-Z]:\\\\[^"]+\\.exe)/i', $commandPath, $matches)) {
            return $matches[1];
        }

        // Method 4: Fallback - take everything before first space
        $parts = explode(' ', trim($commandPath));
        $path = str_replace('"', '', $parts[0]);

        // Validate it looks like a path
        if (stripos($path, '.exe') !== false) {
            return $path;
        }

        return false;
    }
}
