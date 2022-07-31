<?php

class Util
{
    const LOG_ERROR = 'ERROR';
    const LOG_WARNING = 'WARNING';
    const LOG_INFO = 'INFO';
    const LOG_DEBUG = 'DEBUG';
    const LOG_TRACE = 'TRACE';

    public static function cleanArgv($name, $type = 'text')
    {
        if (isset($_SERVER['argv'])) {
            if ($type == 'text') {
                return (isset($_SERVER['argv'][$name]) && !empty($_SERVER['argv'][$name])) ? trim($_SERVER['argv'][$name]) : '';
            } elseif ($type == 'numeric') {
                return (isset($_SERVER['argv'][$name]) && is_numeric($_SERVER['argv'][$name])) ? intval($_SERVER['argv'][$name]) : '';
            } elseif ($type == 'boolean') {
                return (isset($_SERVER['argv'][$name])) ? true : false;
            } elseif ($type == 'array') {
                return (isset($_SERVER['argv'][$name]) && is_array($_SERVER['argv'][$name])) ? $_SERVER['argv'][$name] : array();
            }
        }

        return false;
    }

    public static function cleanGetVar($name, $type = 'text')
    {
        if (is_string($name)) {
            if ($type == 'text') {
                return (isset($_GET[$name]) && !empty($_GET[$name])) ? stripslashes($_GET[$name]) : '';
            } elseif ($type == 'numeric') {
                return (isset($_GET[$name]) && is_numeric($_GET[$name])) ? intval($_GET[$name]) : '';
            } elseif ($type == 'boolean') {
                return (isset($_GET[$name])) ? true : false;
            } elseif ($type == 'array') {
                return (isset($_GET[$name]) && is_array($_GET[$name])) ? $_GET[$name] : array();
            }
        }

        return false;
    }

    public static function cleanPostVar($name, $type = 'text')
    {
        if (is_string($name)) {
            if ($type == 'text') {
                return (isset($_POST[$name]) && !empty($_POST[$name])) ? stripslashes(trim($_POST[$name])) : '';
            } elseif ($type == 'number') {
                return (isset($_POST[$name]) && is_numeric($_POST[$name])) ? intval($_POST[$name]) : '';
            } elseif ($type == 'float') {
                return (isset($_POST[$name]) && is_numeric($_POST[$name])) ? floatval($_POST[$name]) : '';
            } elseif ($type == 'boolean') {
                return (isset($_POST[$name])) ? true : false;
            } elseif ($type == 'array') {
                return (isset($_POST[$name]) && is_array($_POST[$name])) ? $_POST[$name] : array();
            } elseif ($type == 'content') {
                return (isset($_POST[$name]) && !empty($_POST[$name])) ? trim($_POST[$name]) : '';
            }
        }

        return false;
    }

    public static function contains($string, $search)
    {
        if (!empty($string) && !empty($search)) {
            $result = stripos($string, $search);
            if ($result !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function startWith($string, $search)
    {
        $length = strlen($search);
        return (substr($string, 0, $length) === $search);
    }

    public static function endWith($string, $search)
    {
        $length = strlen($search);
        $start  = $length * -1;
        return (substr($string, $start) === $search);
    }

    public static function random($length = 32, $withNumeric = true)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($withNumeric) {
            $characters .= '0123456789';
        }

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public static function clearFolders($paths, $exclude = array())
    {
        $result = array();
        foreach ($paths as $path) {
            $result[$path] = self::clearFolder($path, $exclude);
        }

        return $result;
    }

    public static function clearFolder($path, $exclude = array())
    {
        $result = array();
        $result['return'] = true;
        $result['nb_files'] = 0;

        $handle = @opendir($path);
        if (!$handle) {
            return null;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..' || in_array($file, $exclude)) {
                continue;
            }
            if (is_dir($path . '/' . $file)) {
                $r = self::clearFolder($path . '/' . $file);
                if (!$r) {
                    $result['return'] = false;
                    return $result;
                }
            } else {
                $r = @unlink($path . '/' . $file);
                if ($r) {
                    $result['nb_files']++;
                } else {
                    $result['return'] = false;
                    return $result;
                }
            }
        }

        closedir($handle);
        return $result;
    }

    public static function deleteFolder($path)
    {
        if (is_dir($path)) {
            if (substr($path, strlen($path) - 1, 1) != '/') {
                $path .= '/';
            }
            $files = glob($path . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::deleteFolder($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($path);
        }
    }

    private static function findFile($startPath, $findFile)
    {
        $result = false;

        $handle = @opendir($startPath);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($startPath . '/' . $file)) {
                $result = self::findFile($startPath . '/' . $file, $findFile);
                if ($result !== false) {
                    break;
                }
            } elseif ($file == $findFile) {
                $result = self::formatUnixPath($startPath . '/' . $file);
                break;
            }
        }

        closedir($handle);
        return $result;
    }

    public static function isValidIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    public static function isValidPort($port)
    {
        return is_numeric($port) && ($port > 0 || $port <= 65535);
    }

    public static function replaceDefine($path, $var, $value)
    {
        self::replaceInFile($path, array(
            '/^define\((.*?)' . $var . '(.*?),/' => 'define(\'' . $var . '\', ' . (is_int($value) ? $value : '\'' . $value . '\'') . ');'
        ));
    }

    public static function replaceInFile($path, $replaceList)
    {
        if (file_exists($path)) {
            $lines = file($path);
            $fp = fopen($path, 'w');
            foreach ($lines as $nb => $line) {
                $replaceDone = false;
                foreach ($replaceList as $regex => $replace) {
                    if (preg_match($regex, $line, $matches)) {
                        $countParams = preg_match_all('/{{(\d+)}}/', $replace, $paramsMatches);
                        if ($countParams > 0 && $countParams <= count($matches)) {
                            foreach ($paramsMatches[1] as $paramsMatch) {
                                $replace = str_replace('{{' . $paramsMatch . '}}', $matches[$paramsMatch], $replace);
                            }
                        }
                        self::logTrace('Replace in file ' . $path . ' :');
                        self::logTrace('## line_num: ' . trim($nb));
                        self::logTrace('## old: ' . trim($line));
                        self::logTrace('## new: ' . trim($replace));
                        fwrite($fp, $replace . PHP_EOL);

                        $replaceDone = true;
                        break;
                    }
                }
                if (!$replaceDone) {
                    fwrite($fp, $line);
                }
            }
            fclose($fp);
        }
    }

    public static function getVersionList($path)
    {
        $result = array();

        $handle = @opendir($path);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            $filePath = $path . '/' . $file;
            if ($file != "." && $file != ".." && is_dir($filePath) && $file != 'current') {
                $result[] = str_replace(basename($path), '', $file);
            }
        }

        closedir($handle);
        return $result;
    }

    public static function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public static function getAppBinsRegKey($fromRegistry = true)
    {
        global $bearsamppRegistry;

        if ($fromRegistry) {
            $value = $bearsamppRegistry->getValue(
                Registry::HKEY_LOCAL_MACHINE,
                Registry::ENV_KEY,
                Registry::APP_BINS_REG_ENTRY
            );
            self::logDebug('App reg key from registry: ' . $value);
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
            if ($bearsamppBins->getSvn()->isEnable()) {
                $value .= $bearsamppBins->getSvn()->getSymlinkPath() . ';';
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
            if ($bearsamppTools->getYarn()->isEnable()) {
                $value .= $bearsamppTools->getYarn()->getSymlinkPath() . ';';
                $value .= $bearsamppTools->getYarn()->getSymlinkPath() . '/global/bin;';
            }
            $value = self::formatWindowsPath($value);
            self::logDebug('Generated app bins reg key: ' . $value);
        }

        return $value;
    }

    public static function setAppBinsRegKey($value)
    {
        global $bearsamppRegistry;
        return $bearsamppRegistry->setStringValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::APP_BINS_REG_ENTRY,
            $value
        );
    }

    public static function getAppPathRegKey()
    {
        global $bearsamppRegistry;
        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::APP_PATH_REG_ENTRY
        );
    }

    public static function setAppPathRegKey($value)
    {
        global $bearsamppRegistry;
        return $bearsamppRegistry->setStringValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::APP_PATH_REG_ENTRY,
            $value
        );
    }

    public static function getSysPathRegKey()
    {
        global $bearsamppRegistry;
        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::SYSPATH_REG_ENTRY
        );
    }

    public static function setSysPathRegKey($value)
    {
        global $bearsamppRegistry;
        return $bearsamppRegistry->setExpandStringValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::SYSPATH_REG_ENTRY,
            $value
        );
    }

    public static function getProcessorRegKey()
    {
        global $bearsamppRegistry;
        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::PROCESSOR_REG_SUBKEY,
            Registry::PROCESSOR_REG_ENTRY
        );
    }

    public static function getStartupLnkPath()
    {
        return Vbs::getStartupPath(APP_TITLE . '.lnk');
    }

    public static function isLaunchStartup()
    {
        return file_exists(self::getStartupLnkPath());
    }

    public static function enableLaunchStartup()
    {
        return Vbs::createShortcut(self::getStartupLnkPath());
    }

    public static function disableLaunchStartup()
    {
        return @unlink(self::getStartupLnkPath());
    }

    private static function log($data, $type, $file = null)
    {
        global $bearsamppBs, $bearsamppCore, $bearsamppConfig;
        $file = $file == null ? ($type == self::LOG_ERROR ? $bearsamppBs->getErrorLogFilePath() : $bearsamppBs->getLogFilePath()) : $file;
        if (!$bearsamppBs->isBootstrap()) {
            $file = $bearsamppBs->getHomepageLogFilePath();
        }

        $verbose = array();
        $verbose[Config::VERBOSE_SIMPLE] = $type == self::LOG_ERROR || $type == self::LOG_WARNING;
        $verbose[Config::VERBOSE_REPORT] = $verbose[Config::VERBOSE_SIMPLE] || $type == self::LOG_INFO;
        $verbose[Config::VERBOSE_DEBUG] = $verbose[Config::VERBOSE_REPORT] || $type == self::LOG_DEBUG;
        $verbose[Config::VERBOSE_TRACE] = $verbose[Config::VERBOSE_DEBUG] || $type == self::LOG_TRACE;

        $writeLog = false;
        if ($bearsamppConfig->getLogsVerbose() == Config::VERBOSE_SIMPLE && $verbose[Config::VERBOSE_SIMPLE]) {
            $writeLog = true;
        } elseif ($bearsamppConfig->getLogsVerbose() == Config::VERBOSE_REPORT && $verbose[Config::VERBOSE_REPORT]) {
            $writeLog = true;
        } elseif ($bearsamppConfig->getLogsVerbose() == Config::VERBOSE_DEBUG && $verbose[Config::VERBOSE_DEBUG]) {
            $writeLog = true;
        } elseif ($bearsamppConfig->getLogsVerbose() == Config::VERBOSE_TRACE && $verbose[Config::VERBOSE_TRACE]) {
            $writeLog = true;
        }

        if ($writeLog) {
            file_put_contents(
                $file,
                '[' . date('Y-m-d H:i:s', time()) . '] # ' . APP_TITLE . ' ' . $bearsamppCore->getAppVersion() . ' # ' . $type . ': ' . $data . PHP_EOL,
                FILE_APPEND
            );
        }
    }

    public static function logSeparator()
    {
        global $bearsamppBs;

        $logs = array(
            $bearsamppBs->getLogFilePath(),
            $bearsamppBs->getErrorLogFilePath(),
            $bearsamppBs->getServicesLogFilePath(),
            $bearsamppBs->getRegistryLogFilePath(),
            $bearsamppBs->getStartupLogFilePath(),
            $bearsamppBs->getBatchLogFilePath(),
            $bearsamppBs->getVbsLogFilePath(),
            $bearsamppBs->getWinbinderLogFilePath(),
        );

        $separator = '========================================================================================' . PHP_EOL;
        foreach ($logs as $log) {
            $logContent = @file_get_contents($log);
            if ($logContent !== false && !self::endWith($logContent, $separator)) {
                file_put_contents($log, $separator, FILE_APPEND);
            }
        }
    }

    public static function logTrace($data, $file = null)
    {
        self::log($data, self::LOG_TRACE, $file);
    }

    public static function logDebug($data, $file = null)
    {
        self::log($data, self::LOG_DEBUG, $file);
    }

    public static function logInfo($data, $file = null)
    {
        self::log($data, self::LOG_INFO, $file);
    }

    public static function logWarning($data, $file = null)
    {
        self::log($data, self::LOG_WARNING, $file);
    }

    public static function logError($data, $file = null)
    {
        self::log($data, self::LOG_ERROR, $file);
    }

    public static function logInitClass($classInstance)
    {
        self::logTrace('Init ' . get_class($classInstance));
    }

    public static function logReloadClass($classInstance)
    {
        self::logTrace('Reload ' . get_class($classInstance));
    }

    public static function getPowerShellPath()
    {
        if (is_dir('C:\Windows\System32\WindowsPowerShell')) {
            return self::findFile('C:\Windows\System32\WindowsPowerShell', 'powershell.exe');
        }
        return false;
    }

    public static function findRepos($initPath, $startPath, $checkFile, $maxDepth = 1)
    {
        $depth = substr_count(str_replace($initPath, '', $startPath), '/');
        $result = array();

        $handle = @opendir($startPath);
        if (!$handle) {
            return $result;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($startPath . '/' . $file) && ($initPath == $startPath || $depth <= $maxDepth)) {
                $tmpResults = self::findRepos($initPath, $startPath . '/' . $file, $checkFile, $maxDepth);
                foreach ($tmpResults as $tmpResult) {
                    $result[] = $tmpResult;
                }
            } elseif (is_file($startPath . '/' . $checkFile) && !in_array($startPath, $result)) {
                $result[] = self::formatUnixPath($startPath);
            }
        }

        closedir($handle);
        return $result;
    }

    public static function formatWindowsPath($path)
    {
        return str_replace('/', '\\', $path);
    }

    public static function formatUnixPath($path)
    {
        return str_replace('\\', '/', $path);
    }

    public static function imgToBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public static function utf8ToCp1252($data)
    {
        return iconv("UTF-8", "WINDOWS-1252//IGNORE", $data);
    }

    public static function cp1252ToUtf8($data)
    {
        return iconv("WINDOWS-1252", "UTF-8//IGNORE", $data);
    }

    public static function startLoading()
    {
        global $bearsamppCore, $bearsamppWinbinder;
        $bearsamppWinbinder->exec($bearsamppCore->getPhpExe(), Core::BOOTSTRAP_FILE . ' ' . Action::LOADING);
    }

    public static function stopLoading()
    {
        global $bearsamppCore;
        if (file_exists($bearsamppCore->getLoadingPid())) {
            $pids = file($bearsamppCore->getLoadingPid());
            foreach ($pids as $pid) {
                Win32Ps::kill($pid);
            }
            @unlink($bearsamppCore->getLoadingPid());
        }
    }

    public static function getFilesToScan($path = null)
    {
        $result = array();
        $pathsToScan = !empty($path) ? $path : self::getPathsToScan();
        foreach ($pathsToScan as $pathToScan) {
            $startTime = self::getMicrotime();
            $findFiles = self::findFiles($pathToScan['path'], $pathToScan['includes'], $pathToScan['recursive']);
            foreach ($findFiles as $findFile) {
                $result[] = $findFile;
            }
            self::logDebug($pathToScan['path'] . ' scanned in ' . round(self::getMicrotime() - $startTime, 3) . 's');
        }
        return $result;
    }

    private static function getPathsToScan()
    {
        global $bearsamppBs, $bearsamppCore, $bearsamppBins, $bearsamppApps, $bearsamppTools;
        $paths = array();

        // Alias
        $paths[] = array(
            'path' => $bearsamppBs->getAliasPath(),
            'includes' => array(''),
            'recursive' => false
        );

        // Vhosts
        $paths[] = array(
            'path' => $bearsamppBs->getVhostsPath(),
            'includes' => array(''),
            'recursive' => false
        );

        // OpenSSL
        $paths[] = array(
            'path' => $bearsamppCore->getOpenSslPath(),
            'includes' => array('openssl.cfg'),
            'recursive' => false
        );

        // Homepage
        $paths[] = array(
            'path' => $bearsamppCore->getResourcesPath() . '/homepage',
            'includes' => array('alias.conf'),
            'recursive' => false
        );

        // Apache
        $folderList = self::getFolderList($bearsamppBins->getApache()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppBins->getApache()->getRootPath() . '/' . $folder,
                'includes' => array('.ini', '.conf'),
                'recursive' => true
            );
        }

        // PHP
        $folderList = self::getFolderList($bearsamppBins->getPhp()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppBins->getPhp()->getRootPath() . '/' . $folder,
                'includes' => array('.php', '.bat', '.ini', '.reg', '.inc'),
                'recursive' => true
            );
        }

        // MySQL
        $folderList = self::getFolderList($bearsamppBins->getMysql()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppBins->getMysql()->getRootPath() . '/' . $folder,
                'includes' => array('my.ini'),
                'recursive' => false
            );
        }

        // MariaDB
        $folderList = self::getFolderList($bearsamppBins->getMariadb()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppBins->getMariadb()->getRootPath() . '/' . $folder,
                'includes' => array('my.ini'),
                'recursive' => false
            );
        }

        // PostgreSQL
        $folderList = self::getFolderList($bearsamppBins->getPostgresql()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppBins->getPostgresql()->getRootPath() . '/' . $folder,
                'includes' => array('.ber', '.conf', '.bat'),
                'recursive' => true
            );
        }

        // Node.js
        $folderList = self::getFolderList($bearsamppBins->getNodejs()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppBins->getNodejs()->getRootPath() . '/' . $folder . '/etc',
                'includes' => array('npmrc'),
                'recursive' => true
            );
            $paths[] = array(
                'path' => $bearsamppBins->getNodejs()->getRootPath() . '/' . $folder . '/node_modules/npm',
                'includes' => array('npmrc'),
                'recursive' => false
            );
        }

        // Filezilla
        $folderList = self::getFolderList($bearsamppBins->getFilezilla()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppBins->getFilezilla()->getRootPath() . '/' . $folder,
                'includes' => array('.xml'),
                'recursive' => true
            );
        }

        // WebSVN
        $folderList = self::getFolderList($bearsamppApps->getWebsvn()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppApps->getWebsvn()->getRootPath() . '/' . $folder . '/include',
                'includes' => array('config.php'),
                'recursive' => false
            );
        }

        // GitList
        $folderList = self::getFolderList($bearsamppApps->getGitlist()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppApps->getGitlist()->getRootPath() . '/' . $folder,
                'includes' => array('config.ini'),
                'recursive' => false
            );
        }

        // Composer
        $folderList = self::getFolderList($bearsamppTools->getComposer()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppTools->getComposer()->getRootPath() . '/' . $folder,
                'includes' => array('giscus.json'),
                'recursive' => false
            );
        }

        // ConsoleZ
        $folderList = self::getFolderList($bearsamppTools->getConsoleZ()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppTools->getConsoleZ()->getRootPath() . '/' . $folder,
                'includes' => array('console.xml', '.ini', '.btm'),
                'recursive' => true
            );
        }

        // Python
        $folderList = self::getFolderList($bearsamppTools->getPython()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppTools->getPython()->getRootPath() . '/' . $folder . '/bin',
                'includes' => array('.bat'),
                'recursive' => false
            );
            $paths[] = array(
                'path' => $bearsamppTools->getPython()->getRootPath() . '/' . $folder . '/settings',
                'includes' => array('winpython.ini'),
                'recursive' => false
            );
        }

        // Ruby
        $folderList = self::getFolderList($bearsamppTools->getRuby()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppTools->getRuby()->getRootPath() . '/' . $folder . '/bin',
                'includes' => array('!.dll','!.exe'),
                'recursive' => false
            );
        }

        // Yarn
        $folderList = self::getFolderList($bearsamppTools->getYarn()->getRootPath());
        foreach ($folderList as $folder) {
            $paths[] = array(
                'path' => $bearsamppTools->getYarn()->getRootPath() . '/' . $folder,
                'includes' => array('yarn.bat'),
                'recursive' => false
            );
            $paths[] = array(
                'path' => $bearsamppTools->getYarn()->getRootPath() . '/' . $folder . '/global/bin',
                'includes' => array('.bat'),
                'recursive' => false
            );
            $paths[] = array(
                'path' => $bearsamppTools->getYarn()->getRootPath() . '/' . $folder . '/nodejs/etc',
                'includes' => array('npmrc'),
                'recursive' => true
            );
            $paths[] = array(
                'path' => $bearsamppTools->getYarn()->getRootPath() . '/' . $folder . '/nodejs/node_modules/npm',
                'includes' => array('npmrc'),
                'recursive' => false
            );
        }

        return $paths;
    }

    private static function findFiles($startPath, $includes = array(''), $recursive = true)
    {
        $result = array();

        $handle = @opendir($startPath);
        if (!$handle) {
            return $result;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($startPath . '/' . $file) && $recursive) {
                $tmpResults = self::findFiles($startPath . '/' . $file, $includes);
                foreach ($tmpResults as $tmpResult) {
                    $result[] = $tmpResult;
                }
            } elseif (is_file($startPath . '/' . $file)) {
                foreach ($includes as $include) {
                    if (self::startWith($include, '!')) {
                        $include = ltrim($include, '!');
                        if (self::startWith($file, '.') && !self::endWith($file, $include)) {
                            $result[] = self::formatUnixPath($startPath . '/' . $file);
                        } elseif ($file != $include) {
                            $result[] = self::formatUnixPath($startPath . '/' . $file);
                        }
                    } elseif (self::endWith($file, $include) || $file == $include || empty($include)) {
                        $result[] = self::formatUnixPath($startPath . '/' . $file);
                    }
                }
            }
        }

        closedir($handle);
        return $result;
    }

    public static function changePath($filesToScan, $rootPath = null)
    {
        global $bearsamppBs, $bearsamppCore;

        $result = array(
            'countChangedOcc' => 0,
            'countChangedFiles' => 0
        );

        $rootPath = $rootPath != null ? $rootPath : $bearsamppBs->getRootPath();
        $unixOldPath = self::formatUnixPath($bearsamppCore->getLastPathContent());
        $windowsOldPath = self::formatWindowsPath($bearsamppCore->getLastPathContent());
        $unixCurrentPath = self::formatUnixPath($rootPath);
        $windowsCurrentPath = self::formatWindowsPath($rootPath);

        foreach ($filesToScan as $fileToScan) {
            $tmpCountChangedOcc = 0;
            $fileContentOr = file_get_contents($fileToScan);
            $fileContent = $fileContentOr;

            // old path
            preg_match('#' . $unixOldPath . '#i', $fileContent, $unixMatches);
            if (!empty($unixMatches)) {
                $fileContent = str_replace($unixOldPath, $unixCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }
            preg_match('#' . str_replace('\\', '\\\\', $windowsOldPath) . '#i', $fileContent, $windowsMatches);
            if (!empty($windowsMatches)) {
                $fileContent = str_replace($windowsOldPath, $windowsCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }

            // placeholders
            preg_match('#' . Core::PATH_LIN_PLACEHOLDER . '#i', $fileContent, $unixMatches);
            if (!empty($unixMatches)) {
                $fileContent = str_replace(Core::PATH_LIN_PLACEHOLDER, $unixCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }
            preg_match('#' . Core::PATH_WIN_PLACEHOLDER . '#i', $fileContent, $windowsMatches);
            if (!empty($windowsMatches)) {
                $fileContent = str_replace(Core::PATH_WIN_PLACEHOLDER, $windowsCurrentPath, $fileContent, $countChanged);
                $tmpCountChangedOcc += $countChanged;
            }

            if ($fileContentOr != $fileContent) {
                $result['countChangedOcc'] += $tmpCountChangedOcc;
                $result['countChangedFiles'] += 1;
                file_put_contents($fileToScan, $fileContent);
            }
        }

        return $result;
    }

    public static function getLatestVersion()
    {
        $result = self::getRemoteFile(APP_UPDATE_URL);
        if (empty($result)) {
            self::logError('Cannot retrieve latest version');
            return null;
        }
        self::logDebug("Latest version found: " . $result);
        return $result;
    }

    public static function getWebsiteUrlNoUtm($path = '', $fragment = '')
    {
        return self::getWebsiteUrl($path, $fragment, false);
    }

    public static function getWebsiteUrl($path = '', $fragment = '', $utmSource = true)
    {
        global $bearsamppCore;

        $url = APP_WEBSITE;
        if (!empty($path)) {
            $url .= '/' . ltrim($path, '/');
        }
        if ($utmSource) {
            $url = rtrim($url, '/') . '/?utm_source=bearsampp-' . $bearsamppCore->getAppVersion();
        }
        if (!empty($fragment)) {
            $url .= $fragment;
        }

        return $url;
    }

    public static function getVersionUrl($version)
    {
        return self::getWebsiteUrl('/release/' . $version);
    }

    public static function getChangelogUrl($utmSource = true)
    {
        return self::getWebsiteUrl('doc/changelog', null, $utmSource);
    }

    public static function getRemoteFilesize($url, $humanFileSize = true)
    {
        $size = 0;

        $data = get_headers($url, true);
        if (isset($data['Content-Length'])) {
            $size = intval($data['Content-Length']);
        }

        return $humanFileSize ? self::humanFileSize($size) : $size;
    }

    public static function humanFileSize($size, $unit = '')
    {
        if ((!$unit && $size >= 1 << 30) || $unit == 'GB') {
            return number_format($size / (1 << 30), 2) . 'GB';
        }
        if ((!$unit && $size >= 1 << 20) || $unit == 'MB') {
            return number_format($size / (1 << 20), 2) . 'MB';
        }
        if ((!$unit && $size >= 1 << 10) || $unit == 'KB') {
            return number_format($size / (1 << 10), 2) . 'KB';
        }
        return number_format($size) . ' bytes';
    }

    public static function is32BitsOs()
    {
        $processor = self::getProcessorRegKey();
        return self::contains($processor, 'x86');
    }

    public static function getHttpHeaders($pingUrl)
    {
        if (function_exists('curl_version')) {
            $result = self::getCurlHttpHeaders($pingUrl);
        } else {
            $result = self::getFopenHttpHeaders($pingUrl);
        }

        if (!empty($result)) {
            $rebuildResult = array();
            foreach ($result as $row) {
                $row = trim($row);
                if (!empty($row)) {
                    $rebuildResult[] = $row;
                }
            }
            $result = $rebuildResult;

            self::logDebug('getHttpHeaders:');
            foreach ($result as $header) {
                self::logDebug('-> ' . $header);
            }
        }

        return $result;
    }

    public static function getFopenHttpHeaders($url)
    {
        $result = array();

        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        ));

        $fp = @fopen($url, 'r', false, $context);
        if ($fp) {
            $meta = stream_get_meta_data($fp);
            $result = isset($meta['wrapper_data']) ? $meta['wrapper_data'] : $result;
            fclose($fp);
        }

        return $result;
    }

    public static function getCurlHttpHeaders($url)
    {
        $result = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = @curl_exec($ch);
        if (empty($response)) {
            return $result;
        }

        self::logTrace('getCurlHttpHeaders:' . $response);
        $responseHeaders = explode("\r\n\r\n", $response, 2);
        if (!isset($responseHeaders[0]) || empty($responseHeaders[0])) {
            return $result;
        }

        return explode("\n", $responseHeaders[0]);
    }

    public static function getHeaders($host, $port, $ssl = false)
    {
        $result = array();
        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        ));

        $fp = @stream_socket_client(($ssl ? 'ssl://' : '') . $host . ':' . $port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
        if ($fp) {
            $out = fgets($fp);
            $result = explode(PHP_EOL, $out);
        }
        @fclose($fp);

        if (!empty($result)) {
            $rebuildResult = array();
            foreach ($result as $row) {
                $row = trim($row);
                if (!empty($row)) {
                    $rebuildResult[] = $row;
                }
            }
            $result = $rebuildResult;

            self::logDebug('getHeaders:');
            foreach ($result as $header) {
                self::logDebug('-> ' . $header);
            }
        }

        return $result;
    }

    public static function getRemoteFile($url)
    {
         return file_get_contents($url);
    }

    public static function isPortInUse($port)
    {
        $connection = @fsockopen('127.0.0.1', $port);
        if (is_resource($connection)) {
            fclose($connection);
            $process = Batch::getProcessUsingPort($port);
            return $process != null ? $process : 'N/A';
        }
        return false;
    }

    public static function isValidDomainName($domainName)
    {
        return preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $domainName)
            && preg_match('/^.{1,253}$/', $domainName)
            && preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $domainName);
    }

    public static function isAlphanumeric($string)
    {
        return ctype_alnum($string);
    }

    public static function installService($bin, $port, $syntaxCheckCmd, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $name = $bin->getName();
        $service = $bin->getService();
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::INSTALL_SERVICE_TITLE), $name);

        $isPortInUse = self::isPortInUse($port);
        if ($isPortInUse === false) {
            if (!$service->isInstalled()) {
                $service->create();
                if ($service->start()) {
                    self::logInfo(sprintf('%s service successfully installed. (name: %s ; port: %s)', $name, $service->getName(), $port));
                    if ($showWindow) {
                        $bearsamppWinbinder->messageBoxInfo(
                            sprintf($bearsamppLang->getValue(Lang::SERVICE_INSTALLED), $name, $service->getName(), $port),
                            $boxTitle
                        );
                    }
                    return true;
                } else {
                    $serviceError = sprintf($bearsamppLang->getValue(Lang::SERVICE_INSTALL_ERROR), $name);
                    $serviceErrorLog = sprintf('Error during the installation of %s service', $name);
                    if (!empty($syntaxCheckCmd)) {
                        $cmdSyntaxCheck = $bin->getCmdLineOutput($syntaxCheckCmd);
                        if (!$cmdSyntaxCheck['syntaxOk']) {
                            $serviceError .= PHP_EOL . sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_SYNTAX_ERROR), $cmdSyntaxCheck['content']);
                            $serviceErrorLog .= sprintf(' (conf errors detected : %s)', $cmdSyntaxCheck['content']);
                        }
                    }
                    self::logError($serviceErrorLog);
                    if ($showWindow) {
                        $bearsamppWinbinder->messageBoxError($serviceError, $boxTitle);
                    }
                }
            } else {
                self::logWarning(sprintf('%s service already installed', $name));
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxWarning(
                        sprintf($bearsamppLang->getValue(Lang::SERVICE_ALREADY_INSTALLED), $name),
                        $boxTitle
                    );
                }
                return true;
            }
        } elseif ($service->isRunning()) {
            self::logWarning(sprintf('%s service already installed and running', $name));
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf($bearsamppLang->getValue(Lang::SERVICE_ALREADY_INSTALLED), $name),
                    $boxTitle
                );
            }
            return true;
        } else {
            self::logError(sprintf('Port %s is used by an other application : %s', $name));
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED_BY), $port, $isPortInUse),
                    $boxTitle
                );
            }
        }

        return false;
    }

    public static function removeService($service, $name)
    {
        if (!($service instanceof Win32Service)) {
            self::logError('$service not an instance of Win32Service');
            return false;
        }

        if ($service->isInstalled()) {
            if ($service->delete()) {
                self::logInfo(sprintf('%s service successfully removed', $name));
                return true;
            } else {
                self::logError(sprintf('Error during the uninstallation of %s service', $name));
                return false;
            }
        } else {
            self::logWarning(sprintf('%s service does not exist', $name));
        }

        return true;
    }

    public static function startService($bin, $syntaxCheckCmd, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $name = $bin->getName();
        $service = $bin->getService();
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::START_SERVICE_TITLE), $name);

        if (!$service->start()) {
            $serviceError = sprintf($bearsamppLang->getValue(Lang::START_SERVICE_ERROR), $name);
            $serviceErrorLog = sprintf('Error while starting the %s service', $name);
            if (!empty($syntaxCheckCmd)) {
                $cmdSyntaxCheck = $bin->getCmdLineOutput($syntaxCheckCmd);
                if (!$cmdSyntaxCheck['syntaxOk']) {
                    $serviceError .= PHP_EOL . sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_SYNTAX_ERROR), $cmdSyntaxCheck['content']);
                    $serviceErrorLog .= sprintf(' (conf errors detected : %s)', $cmdSyntaxCheck['content']);
                }
            }
            self::logError($serviceErrorLog);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError($serviceError, $boxTitle);
            }
            return false;
        }

        return true;
    }

    public static function getGithubUserUrl($part = null) {
        $part = !empty($part) ? '/' . $part : null;
        return 'https://github.com/' . APP_GITHUB_USER . $part;
    }

    public static function getGithubUrl($part = null) {
        $part = !empty($part) ? '/' . $part : null;
        return self::getGithubUserUrl(APP_GITHUB_REPO . $part);
    }

    public static function getGithubRawUrl($file) {
        $file = !empty($file) ? '/' . $file : null;
        return 'https://raw.githubusercontent.com/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . '/main' . $file;
    }

    public static function getFolderList($path)
    {
        $result = array();

        $handle = @opendir($path);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            $filePath = $path . '/' . $file;
            if ($file != "." && $file != ".." && is_dir($filePath) && $file != 'current') {
                $result[] = $file;
            }
        }

        closedir($handle);
        return $result;
    }

    public static function getNssmEnvPaths() {
        global $bearsamppBs;

        $result = '';
        $nssmEnvPathsFile = $bearsamppBs->getRootPath() . '/nssmEnvPaths.dat';

        if (is_file($nssmEnvPathsFile)) {
            $paths = explode(PHP_EOL, file_get_contents($nssmEnvPathsFile));
            foreach ($paths as $path) {
                $path = trim($path);
                if (stripos($path, ':') === false) {
                    $path = $bearsamppBs->getRootPath() . '/' . $path;
                }
                if (is_dir($path)) {
                    $result .= self::formatUnixPath($path) . ';';
                } else {
                    self::logWarning('Path not found in nssmEnvPaths.dat: ' . $path);
                }
            }
        }

        return $result;
    }

    public static function openFileContent($caption, $content) {
        global $bearsamppBs, $bearsamppConfig, $bearsamppWinbinder;

        $folderPath = $bearsamppBs->getTmpPath() . '/openFileContent-' . self::random();
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $filepath = self::formatWindowsPath($folderPath . '/' . $caption);
        file_put_contents($filepath, $content);

        $bearsamppWinbinder->exec($bearsamppConfig->getNotepad(), '"' . $filepath . '"');
    }
}
