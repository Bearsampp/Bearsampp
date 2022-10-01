<?php

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

    private static function callWin32Ps($function)
    {
        $result = false;

        if (function_exists($function)) {
            $result = @call_user_func($function);
        }

        return $result;
    }

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

    public static function getCurrentPid()
    {
        $procInfo = self::getStatProc();
        return isset($procInfo[self::PROCESS_ID]) ? intval($procInfo[self::PROCESS_ID]) : 0;
    }

    public static function getListProcs()
    {
        return Vbs::getListProcs(self::getKeys());
    }

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

    public static function exists($pid)
    {
        return self::findByPid($pid) !== false;
    }

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

    public static function kill($pid)
    {
        $pid = intval($pid);
        if (!empty($pid)) {
            Vbs::killProc($pid);
        }
    }

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
