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
 * Class Win32Ps
 *
 * This class provides various utility functions for interacting with Windows processes.
 * It includes methods for retrieving process information, checking process existence,
 * finding processes by PID or path, and terminating processes.
 */
class Win32Ps
{
    const NAME = 'Name';
    const PROCESS_ID = 'ProcessID';
    const EXECUTABLE_PATH = 'ExecutablePath';
    const CAPTION = 'Caption';
    const COMMAND_LINE = 'CommandLine';

    public function __construct()
    {
    }

    /**
     * Calls a specified function if it exists.
     *
     * @param string $function The name of the function to call.
     * @return mixed The result of the function call, or false if the function does not exist.
     */
    private static function callWin32Ps($function)
    {
        $result = false;

        if (function_exists($function)) {
            $result = @call_user_func($function);
        }

        return $result;
    }

    /**
     * Retrieves the keys used for process information.
     *
     * @return array An array of keys used for process information.
     */
    public static function getKeys()
    {
        return array(
            self::NAME,
            self::PROCESS_ID,
            self::EXECUTABLE_PATH,
            self::CAPTION,
            self::COMMAND_LINE
        );
    }

    /**
     * Retrieves the current process ID.
     *
     * @return int The current process ID, or 0 if not found.
     */
    public static function getCurrentPid()
    {
        $procInfo = self::getStatProc();
        return isset($procInfo[self::PROCESS_ID]) ? intval($procInfo[self::PROCESS_ID]) : 0;
    }

    /**
     * Retrieves a list of running processes.
     *
     * @return array|false An array of process information, or false on failure.
     */
    public static function getListProcs()
    {
        return Vbs::getListProcs(self::getKeys());
    }

    /**
     * Retrieves the status of the current process.
     *
     * @return array|null An array containing the process ID and executable path, or null on failure.
     */
    public static function getStatProc()
    {
        $statProc = self::callWin32Ps('win32_ps_stat_proc');

        if ($statProc !== false) {
            return array(
                self::PROCESS_ID => $statProc['pid'],
                self::EXECUTABLE_PATH => $statProc['exe']
            );
        }

        return null;
    }

    /**
     * Checks if a process with the specified PID exists.
     *
     * @param int $pid The process ID to check.
     * @return bool True if the process exists, false otherwise.
     */
    public static function exists($pid)
    {
        return self::findByPid($pid) !== false;
    }

    /**
     * Finds a process by its PID.
     *
     * @param int $pid The process ID to find.
     * @return array|false An array of process information, or false if not found.
     */
    public static function findByPid($pid)
    {
        if (!empty($pid)) {
            $procs = self::getListProcs();
            if ($procs !== false) {
                foreach ($procs as $proc) {
                    if ($proc[self::PROCESS_ID] == $pid) {
                        return $proc;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Finds a process by its executable path.
     *
     * @param string $path The path to the executable.
     * @return array|false An array of process information, or false if not found.
     */
    public static function findByPath($path)
    {
        $path = Util::formatUnixPath($path);
        if (!empty($path) && is_file($path)) {
            $procs = self::getListProcs();
            if ($procs !== false) {
                foreach ($procs as $proc) {
                    $unixExePath = Util::formatUnixPath($proc[self::EXECUTABLE_PATH]);
                    if ($unixExePath == $path) {
                        return $proc;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Terminates a process by its PID.
     *
     * @param int $pid The process ID to terminate.
     */
    public static function kill($pid)
    {
        $pid = intval($pid);
        if (!empty($pid)) {
            Vbs::killProc($pid);
        }
    }

    /**
     * Terminates all Bearsampp-related processes except the current one.
     *
     * @param bool $refreshProcs Whether to refresh the list of processes before terminating.
     * @return array An array of terminated processes.
     */
    public static function killBins($refreshProcs = false)
    {
        global $bearsamppRoot;
        $killed = array();

        $procs = $bearsamppRoot->getProcs();
        if ($refreshProcs) {
            $procs = self::getListProcs();
        }

        if ($procs !== false) {
            foreach ($procs as $proc) {
                $unixExePath = Util::formatUnixPath($proc[self::EXECUTABLE_PATH]);
                $unixCommandPath = Util::formatUnixPath($proc[self::COMMAND_LINE]);

                // Not kill current PID (PHP)
                if ($proc[self::PROCESS_ID] == self::getCurrentPid()) {
                    continue;
                }

                // Not kill bearsampp
                if ($unixExePath == $bearsamppRoot->getExeFilePath()) {
                    continue;
                }

                // Not kill inside www
                if (Util::startWith($unixExePath, $bearsamppRoot->getWwwPath() . '/') || Util::contains($unixCommandPath, $bearsamppRoot->getWwwPath() . '/')) {
                    continue;
                }

                // Not kill external process
                if (!Util::startWith($unixExePath, $bearsamppRoot->getRootPath() . '/') && !Util::contains($unixCommandPath, $bearsamppRoot->getRootPath() . '/')) {
                    continue;
                }

                self::kill($proc[self::PROCESS_ID]);
                $killed[] = $proc;
            }
        }

        return $killed;
    }
}
