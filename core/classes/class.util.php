<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Utility class providing a wide range of static methods for various purposes including:
 * - Cleaning and retrieving command line, GET, and POST variables based on type specifications.
 * - String manipulation methods to check if strings contain, start with, or end with specified substrings.
 * - File and directory management functions for deleting, clearing, or finding files and directories.
 * - Logging functionalities tailored for different levels of verbosity (ERROR, WARNING, INFO, DEBUG, TRACE).
 * - System utilities for handling registry operations, managing environment variables, and executing system commands.
 * - Network utilities to validate IPs, domains, and manage HTTP requests.
 * - Helper functions for encoding, decoding, and file operations.
 *
 * This class is designed to be used as a helper or utility class where methods are accessed statically.
 * This means you do not need to instantiate it to use the methods, but can simply call them using the Util::methodName() syntax.
 *
 * Usage Example:
 * ```
 * $cleanedData = Util::cleanGetVar('data', 'text');
 * Util::logError('An error occurred');
 * $isAvailable = Util::isValidIp('192.168.1.1');
 * ```
 *
 * Each method is self-contained and provides specific functionality, making this class a central point for
 * common utility operations needed across a PHP application, especially in environments like web servers or command-line interfaces.
 */
class Util
{
    /**
     * This code snippet defines constants for logging levels.
     */
    const LOG_ERROR = 'ERROR';
    const LOG_WARNING = 'WARNING';
    const LOG_INFO = 'INFO';
    const LOG_DEBUG = 'DEBUG';
    const LOG_TRACE = 'TRACE';

    /**
     * Cleans and returns a specific command line argument based on the type specified.
     *
     * @param   string  $name  The index of the argument in the $_SERVER['argv'] array.
     * @param   string  $type  The type of the argument to return: 'text', 'numeric', 'boolean', or 'array'.
     *
     * @return mixed Returns the cleaned argument based on the type or false if the argument is not set.
     */
    public static function cleanArgv($name, $type = 'text')
    {
        if ( isset( $_SERVER['argv'] ) ) {
            if ( $type == 'text' ) {
                return (isset( $_SERVER['argv'][$name] ) && !empty( $_SERVER['argv'][$name] )) ? trim( $_SERVER['argv'][$name] ) : '';
            }
            elseif ( $type == 'numeric' ) {
                return (isset( $_SERVER['argv'][$name] ) && is_numeric( $_SERVER['argv'][$name] )) ? intval( $_SERVER['argv'][$name] ) : '';
            }
            elseif ( $type == 'boolean' ) {
                return (isset( $_SERVER['argv'][$name] )) ? true : false;
            }
            elseif ( $type == 'array' ) {
                return (isset( $_SERVER['argv'][$name] ) && is_array( $_SERVER['argv'][$name] )) ? $_SERVER['argv'][$name] : array();
            }
        }

        return false;
    }

    /**
     * Cleans and returns a specific $_GET variable based on the type specified.
     *
     * @param   string  $name  The name of the $_GET variable.
     * @param   string  $type  The type of the variable to return: 'text', 'numeric', 'boolean', or 'array'.
     *
     * @return mixed Returns the cleaned $_GET variable based on the type or false if the variable is not set.
     */
    public static function cleanGetVar($name, $type = 'text')
    {
        if ( is_string( $name ) ) {
            if ( $type == 'text' ) {
                return (isset( $_GET[$name] ) && !empty( $_GET[$name] )) ? stripslashes( $_GET[$name] ) : '';
            }
            elseif ( $type == 'numeric' ) {
                return (isset( $_GET[$name] ) && is_numeric( $_GET[$name] )) ? intval( $_GET[$name] ) : '';
            }
            elseif ( $type == 'boolean' ) {
                return (isset( $_GET[$name] )) ? true : false;
            }
            elseif ( $type == 'array' ) {
                return (isset( $_GET[$name] ) && is_array( $_GET[$name] )) ? $_GET[$name] : array();
            }
        }

        return false;
    }

    /**
     * Cleans and returns a specific $_POST variable based on the type specified.
     *
     * @param   string  $name  The name of the $_POST variable.
     * @param   string  $type  The type of the variable to return: 'text', 'number', 'float', 'boolean', 'array', or 'content'.
     *
     * @return mixed Returns the cleaned $_POST variable based on the type or false if the variable is not set.
     */
    public static function cleanPostVar($name, $type = 'text')
    {
        if ( is_string( $name ) ) {
            if ( $type == 'text' ) {
                return (isset( $_POST[$name] ) && !empty( $_POST[$name] )) ? stripslashes( trim( $_POST[$name] ) ) : '';
            }
            elseif ( $type == 'number' ) {
                return (isset( $_POST[$name] ) && is_numeric( $_POST[$name] )) ? intval( $_POST[$name] ) : '';
            }
            elseif ( $type == 'float' ) {
                return (isset( $_POST[$name] ) && is_numeric( $_POST[$name] )) ? floatval( $_POST[$name] ) : '';
            }
            elseif ( $type == 'boolean' ) {
                return (isset( $_POST[$name] )) ? true : false;
            }
            elseif ( $type == 'array' ) {
                return (isset( $_POST[$name] ) && is_array( $_POST[$name] )) ? $_POST[$name] : array();
            }
            elseif ( $type == 'content' ) {
                return (isset( $_POST[$name] ) && !empty( $_POST[$name] )) ? trim( $_POST[$name] ) : '';
            }
        }

        return false;
    }

    /**
     * Checks if a string contains a specified substring.
     *
     * @param   string  $string  The string to search in.
     * @param   string  $search  The substring to search for.
     *
     * @return bool Returns true if the substring is found in the string, otherwise false.
     */
    public static function contains($string, $search)
    {
        if ( !empty( $string ) && !empty( $search ) ) {
            $result = stripos( $string, $search );
            if ( $result !== false ) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Checks if a string starts with a specified substring.
     *
     * @param   string  $string  The string to check.
     * @param   string  $search  The substring to look for at the start of the string.
     *
     * @return bool Returns true if the string starts with the search substring, otherwise false.
     */
    public static function startWith($string, $search)
    {
        $length = strlen( $search );

        return (substr( $string, 0, $length ) === $search);
    }

    /**
     * Checks if a string ends with a specified substring.
     *
     * This method trims the right side whitespace of the input string before checking
     * if it ends with the specified search substring.
     *
     * @param   string  $string  The string to check.
     * @param   string  $search  The substring to look for at the end of the string.
     *
     * @return bool Returns true if the string ends with the search substring, otherwise false.
     */
    public static function endWith($string, $search)
    {
        $length = strlen( $search );
        $start  = $length * -1;

        return (substr( $string, $start ) === $search);
    }

    /**
     * Generates a random string of specified length and character set.
     *
     * @param   int   $length       The length of the random string to generate.
     * @param   bool  $withNumeric  Whether to include numeric characters in the random string.
     *
     * @return string Returns the generated random string.
     */
    public static function random($length = 32, $withNumeric = true)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ( $withNumeric ) {
            $characters .= '0123456789';
        }

        $randomString = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $randomString .= $characters[rand( 0, strlen( $characters ) - 1 )];
        }

        return $randomString;
    }

    /**
     * Recursively deletes files from a specified directory while excluding certain files.
     *
     * @param   string  $path     The path to the directory to clear.
     * @param   array   $exclude  An array of filenames to exclude from deletion.
     *
     * @return array Returns an array with the status of the operation and the number of files deleted.
     */
    public static function clearFolders($paths, $exclude = array())
    {
        $result = array();
        foreach ( $paths as $path ) {
            $result[$path] = self::clearFolder( $path, $exclude );
        }

        return $result;
    }

    /**
     * Recursively clears all files and directories within a specified directory, excluding specified items.
     *
     * @param   string  $path     The path of the directory to clear.
     * @param   array   $exclude  An array of filenames to exclude from deletion.
     *
     * @return array|null Returns an array with the operation status and count of files deleted, or null if the directory cannot be opened.
     */
    public static function clearFolder($path, $exclude = array())
    {
        $result             = array();
        $result['return']   = true;
        $result['nb_files'] = 0;

        $handle = @opendir( $path );
        if ( !$handle ) {
            return null;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file == '.' || $file == '..' || in_array( $file, $exclude ) ) {
                continue;
            }
            if ( is_dir( $path . '/' . $file ) ) {
                $r = self::clearFolder( $path . '/' . $file );
                if ( !$r ) {
                    $result['return'] = false;

                    return $result;
                }
            }
            else {
                $r = @unlink( $path . '/' . $file );
                if ( $r ) {
                    $result['nb_files']++;
                }
                else {
                    $result['return'] = false;

                    return $result;
                }
            }
        }

        closedir( $handle );

        return $result;
    }

    /**
     * Recursively deletes a directory and all its contents.
     *
     * @param   string  $path  The path of the directory to delete.
     */
    public static function deleteFolder($path)
    {
        if ( is_dir( $path ) ) {
            if ( substr( $path, strlen( $path ) - 1, 1 ) != '/' ) {
                $path .= '/';
            }
            $files = glob( $path . '*', GLOB_MARK );
            foreach ( $files as $file ) {
                if ( is_dir( $file ) ) {
                    self::deleteFolder( $file );
                }
                else {
                    unlink( $file );
                }
            }
            rmdir( $path );
        }
    }

    /**
     * Recursively searches for a file starting from a specified directory.
     *
     * @param   string  $startPath  The directory path to start the search.
     * @param   string  $findFile   The filename to search for.
     *
     * @return string|false Returns the path to the file if found, or false if not found.
     */
    private static function findFile($startPath, $findFile)
    {
        $result = false;

        $handle = @opendir( $startPath );
        if ( !$handle ) {
            return false;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file == '.' || $file == '..' ) {
                continue;
            }
            if ( is_dir( $startPath . '/' . $file ) ) {
                $result = self::findFile( $startPath . '/' . $file, $findFile );
                if ( $result !== false ) {
                    break;
                }
            }
            elseif ( $file == $findFile ) {
                $result = self::formatUnixPath( $startPath . '/' . $file );
                break;
            }
        }

        closedir( $handle );

        return $result;
    }

    /**
     * Validates an IP address.
     *
     * @param   string  $ip  The IP address to validate.
     *
     * @return bool Returns true if the IP address is valid, otherwise false.
     */
    public static function isValidIp($ip)
    {
        return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
            || filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 );
    }

    /**
     * Validates a port number.
     *
     * @param   int  $port  The port number to validate.
     *
     * @return bool Returns true if the port number is valid and within the range of 1 to 65535, otherwise false.
     */
    public static function isValidPort($port)
    {
        return is_numeric( $port ) && ($port > 0 || $port <= 65535);
    }

    /**
     * Replaces a defined constant in a file with a new value.
     *
     * @param   string  $path   The file path where the constant is defined.
     * @param   string  $var    The name of the constant.
     * @param   mixed   $value  The new value for the constant.
     */
    public static function replaceDefine($path, $var, $value)
    {
        self::replaceInFile( $path, array(
            '/^define\((.*?)' . $var . '(.*?),/' => 'define(\'' . $var . '\', ' . (is_int( $value ) ? $value : '\'' . $value . '\'') . ');'
        ) );
    }

    /**
     * Performs replacements in a file based on a list of regular expression patterns.
     *
     * @param   string  $path         The path to the file where replacements are to be made.
     * @param   array   $replaceList  An associative array where keys are regex patterns and values are replacement strings.
     */
    public static function replaceInFile($path, $replaceList)
    {
        if ( file_exists( $path ) ) {
            $lines = file( $path );
            $fp    = fopen( $path, 'w' );
            foreach ( $lines as $nb => $line ) {
                $replaceDone = false;
                foreach ( $replaceList as $regex => $replace ) {
                    if ( preg_match( $regex, $line, $matches ) ) {
                        $countParams = preg_match_all( '/{{(\d+)}}/', $replace, $paramsMatches );
                        if ( $countParams > 0 && $countParams <= count( $matches ) ) {
                            foreach ( $paramsMatches[1] as $paramsMatch ) {
                                $replace = str_replace( '{{' . $paramsMatch . '}}', $matches[$paramsMatch], $replace );
                            }
                        }
                        self::logTrace( 'Replace in file ' . $path . ' :' );
                        self::logTrace( '## line_num: ' . trim( $nb ) );
                        self::logTrace( '## old: ' . trim( $line ) );
                        self::logTrace( '## new: ' . trim( $replace ) );
                        fwrite( $fp, $replace . PHP_EOL );

                        $replaceDone = true;
                        break;
                    }
                }
                if ( !$replaceDone ) {
                    fwrite( $fp, $line );
                }
            }
            fclose( $fp );
        }
    }

    /**
     * Retrieves a list of version directories within a specified path.
     *
     * @param   string  $path  The path to search for version directories.
     *
     * @return array|false Returns a sorted array of version names, or false if the directory cannot be opened.
     */
    public static function getVersionList($path)
    {
        $result = array();

        $handle = @opendir( $path );
        if ( !$handle ) {
            return false;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            $filePath = $path . '/' . $file;
            if ( $file != "." && $file != ".." && is_dir( $filePath ) && $file != 'current' ) {
                $result[] = str_replace( basename( $path ), '', $file );
            }
        }

        closedir( $handle );
        natcasesort( $result );

        return $result;
    }

    /**
     * Gets the current Unix timestamp with microseconds.
     *
     * @return float Returns the current Unix timestamp combined with microseconds.
     */
    public static function getMicrotime()
    {
        list( $usec, $sec ) = explode( " ", microtime() );

        return ((float) $usec + (float) $sec);
    }

    public static function getAppBinsRegKey($fromRegistry = true)
    {
        global $bearsamppRegistry;

        if ( $fromRegistry ) {
            $value = $bearsamppRegistry->getValue(
                Registry::HKEY_LOCAL_MACHINE,
                Registry::ENV_KEY,
                Registry::APP_BINS_REG_ENTRY
            );
            self::logDebug( 'App reg key from registry: ' . $value );
        }
        else {
            global $bearsamppBins, $bearsamppTools;
            $value = '';
            if ( $bearsamppBins->getApache()->isEnable() ) {
                $value .= $bearsamppBins->getApache()->getSymlinkPath() . '/bin;';
            }
            if ( $bearsamppBins->getPhp()->isEnable() ) {
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . ';';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/pear;';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/deps;';
                $value .= $bearsamppBins->getPhp()->getSymlinkPath() . '/imagick;';
            }
            if ( $bearsamppBins->getNodejs()->isEnable() ) {
                $value .= $bearsamppBins->getNodejs()->getSymlinkPath() . ';';
            }
            if ( $bearsamppTools->getComposer()->isEnable() ) {
                $value .= $bearsamppTools->getComposer()->getSymlinkPath() . ';';
                $value .= $bearsamppTools->getComposer()->getSymlinkPath() . '/vendor/bin;';
            }
            if ( $bearsamppTools->getGhostscript()->isEnable() ) {
                $value .= $bearsamppTools->getGhostscript()->getSymlinkPath() . '/bin;';
            }
            if ( $bearsamppTools->getGit()->isEnable() ) {
                $value .= $bearsamppTools->getGit()->getSymlinkPath() . '/bin;';
            }
            if ( $bearsamppTools->getNgrok()->isEnable() ) {
                $value .= $bearsamppTools->getNgrok()->getSymlinkPath() . ';';
            }
            if ( $bearsamppTools->getPerl()->isEnable() ) {
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/perl/site/bin;';
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/perl/bin;';
                $value .= $bearsamppTools->getPerl()->getSymlinkPath() . '/c/bin;';
            }
            if ( $bearsamppTools->getPython()->isEnable() ) {
                $value .= $bearsamppTools->getPython()->getSymlinkPath() . '/bin;';
            }
            if ( $bearsamppTools->getRuby()->isEnable() ) {
                $value .= $bearsamppTools->getRuby()->getSymlinkPath() . '/bin;';
            }
            if ( $bearsamppTools->getYarn()->isEnable() ) {
                $value .= $bearsamppTools->getYarn()->getSymlinkPath() . ';';
                $value .= $bearsamppTools->getYarn()->getSymlinkPath() . '/global/bin;';
            }
            $value = self::formatWindowsPath( $value );
            self::logDebug( 'Generated app bins reg key: ' . $value );
        }

        return $value;
    }

    /**
     * Retrieves or generates the application binaries registry key.
     *
     * @param   bool  $fromRegistry  Determines whether to retrieve the key from the registry or generate it.
     *
     * @return string Returns the application binaries registry key.
     */
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

    /**
     * Retrieves the application path from the registry.
     *
     * @return mixed The value of the application path registry key or false on error.
     */
    public static function getAppPathRegKey()
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::APP_PATH_REG_ENTRY
        );
    }

    /**
     * Sets the application path in the registry.
     *
     * @param   string  $value  The new value for the application path.
     *
     * @return bool True on success, false on failure.
     */
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

    /**
     * Retrieves the system path from the registry.
     *
     * @return mixed The value of the system path registry key or false on error.
     */
    public static function getSysPathRegKey()
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::ENV_KEY,
            Registry::SYSPATH_REG_ENTRY
        );
    }

    /**
     * Sets the system path in the registry.
     *
     * @param   string  $value  The new value for the system path.
     *
     * @return bool True on success, false on failure.
     */
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

    /**
     * Retrieves the processor identifier from the registry.
     *
     * @return mixed The value of the processor identifier registry key or false on error.
     */
    public static function getProcessorRegKey()
    {
        global $bearsamppRegistry;

        return $bearsamppRegistry->getValue(
            Registry::HKEY_LOCAL_MACHINE,
            Registry::PROCESSOR_REG_SUBKEY,
            Registry::PROCESSOR_REG_ENTRY
        );
    }

    /**
     * Retrieves the path for the startup link file.
     *
     * @return string The full path to the startup link file.
     */
    public static function getStartupLnkPath()
    {
        return Vbs::getStartupPath( APP_TITLE . '.lnk' );
    }

    /**
     * Checks if the application is set to launch at startup.
     *
     * @return bool True if the startup link exists, false otherwise.
     */
    public static function isLaunchStartup()
    {
        return file_exists( self::getStartupLnkPath() );
    }

    /**
     * Enables launching the application at startup by creating a shortcut in the startup folder.
     *
     * @return bool True on success, false on failure.
     */
    public static function enableLaunchStartup()
    {
        return Vbs::createShortcut( self::getStartupLnkPath() );
    }

    /**
     * Disables launching the application at startup by removing the shortcut from the startup folder.
     *
     * @return bool True on success, false on failure.
     */
    public static function disableLaunchStartup()
    {
        return @unlink( self::getStartupLnkPath() );
    }

    /**
     * Logs a message to a specified file or default log file based on the log type.
     *
     * @param   string       $data  The message to log.
     * @param   string       $type  The type of log message: 'ERROR', 'WARNING', 'INFO', 'DEBUG', or 'TRACE'.
     * @param   string|null  $file  The file path to write the log message to. If null, uses default log file based on type.
     */
    private static function log($data, $type, $file = null)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppConfig;
        $file = $file == null ? ($type == self::LOG_ERROR ? $bearsamppRoot->getErrorLogFilePath() : $bearsamppRoot->getLogFilePath()) : $file;
        if ( !$bearsamppRoot->isRoot() ) {
            $file = $bearsamppRoot->getHomepageLogFilePath();
        }

        $verbose                         = array();
        $verbose[Config::VERBOSE_SIMPLE] = $type == self::LOG_ERROR || $type == self::LOG_WARNING;
        $verbose[Config::VERBOSE_REPORT] = $verbose[Config::VERBOSE_SIMPLE] || $type == self::LOG_INFO;
        $verbose[Config::VERBOSE_DEBUG]  = $verbose[Config::VERBOSE_REPORT] || $type == self::LOG_DEBUG;
        $verbose[Config::VERBOSE_TRACE]  = $verbose[Config::VERBOSE_DEBUG] || $type == self::LOG_TRACE;

        $writeLog = false;
        if ( $bearsamppConfig->getLogsVerbose() == Config::VERBOSE_SIMPLE && $verbose[Config::VERBOSE_SIMPLE] ) {
            $writeLog = true;
        }
        elseif ( $bearsamppConfig->getLogsVerbose() == Config::VERBOSE_REPORT && $verbose[Config::VERBOSE_REPORT] ) {
            $writeLog = true;
        }
        elseif ( $bearsamppConfig->getLogsVerbose() == Config::VERBOSE_DEBUG && $verbose[Config::VERBOSE_DEBUG] ) {
            $writeLog = true;
        }
        elseif ( $bearsamppConfig->getLogsVerbose() == Config::VERBOSE_TRACE && $verbose[Config::VERBOSE_TRACE] ) {
            $writeLog = true;
        }

        if ( $writeLog ) {
            file_put_contents(
                $file,
                '[' . date( 'Y-m-d H:i:s', time() ) . '] # ' . APP_TITLE . ' ' . $bearsamppCore->getAppVersion() . ' # ' . $type . ': ' . $data . PHP_EOL,
                FILE_APPEND
            );
        }
    }

    /**
     * Appends a separator line to multiple log files if they do not already end with it.
     * This function ensures that each log file ends with a clear separator for better readability.
     *
     * @global object $bearsamppRoot An object that provides paths to various log files.
     */
    public static function logSeparator()
    {
        global $bearsamppRoot;

        $logs = array(
            $bearsamppRoot->getLogFilePath(),
            $bearsamppRoot->getErrorLogFilePath(),
            $bearsamppRoot->getServicesLogFilePath(),
            $bearsamppRoot->getRegistryLogFilePath(),
            $bearsamppRoot->getStartupLogFilePath(),
            $bearsamppRoot->getBatchLogFilePath(),
            $bearsamppRoot->getVbsLogFilePath(),
            $bearsamppRoot->getWinbinderLogFilePath(),
        );

        $separator = '========================================================================================' . PHP_EOL;
        foreach ( $logs as $log ) {
            if ( !file_exists( $log ) ) {
                continue; // Skip to the next iteration if the file does not exist
            }
            $logContent = @file_get_contents( $log );
            if ( $logContent !== false && !self::endWith( $logContent, $separator ) ) {
                file_put_contents( $log, $separator, FILE_APPEND );
            }
        }
    }

    /**
     * Logs trace information.
     * This function is a wrapper around the generic log function for trace-level messages.
     *
     * @param   mixed        $data  The data to log.
     * @param   string|null  $file  Optional. The file path to log to. If not provided, a default path is used.
     */
    public static function logTrace($data, $file = null)
    {
        self::log( $data, self::LOG_TRACE, $file );
    }

    /**
     * Logs debug information.
     * This function is a wrapper around the generic log function for debug-level messages.
     *
     * @param   mixed        $data  The data to log.
     * @param   string|null  $file  Optional. The file path to log to. If not provided, a default path is used.
     */
    public static function logDebug($data, $file = null)
    {
        self::log( $data, self::LOG_DEBUG, $file );
    }

    /**
     * Logs informational messages.
     * This function is a wrapper around the generic log function for informational messages.
     *
     * @param   mixed        $data  The data to log.
     * @param   string|null  $file  Optional. The file path to log to. If not provided, a default path is used.
     */
    public static function logInfo($data, $file = null)
    {
        self::log( $data, self::LOG_INFO, $file );
    }

    /**
     * Logs warning messages.
     * This function is a wrapper around the generic log function for warning-level messages.
     *
     * @param   mixed        $data  The data to log.
     * @param   string|null  $file  Optional. The file path to log to. If not provided, a default path is used.
     */
    public static function logWarning($data, $file = null)
    {
        self::log( $data, self::LOG_WARNING, $file );
    }

    /**
     * Logs error messages.
     * This function is a wrapper around the generic log function for error-level messages.
     *
     * @param   mixed        $data  The data to log.
     * @param   string|null  $file  Optional. The file path to log to. If not provided, a default path is used.
     */
    public static function logError($data, $file = null)
    {
        self::log( $data, self::LOG_ERROR, $file );
    }

    /**
     * Logs the initialization of a class instance.
     *
     * @param   object  $classInstance  The instance of the class to log.
     */
    public static function logInitClass($classInstance)
    {
        self::logTrace( 'Init ' . get_class( $classInstance ) );
    }

    /**
     * Logs the reloading of a class instance.
     *
     * @param   object  $classInstance  The instance of the class to log.
     */
    public static function logReloadClass($classInstance)
    {
        self::logTrace( 'Reload ' . get_class( $classInstance ) );
    }

    /**
     * Finds the path to the PowerShell executable in the Windows System32 directory.
     *
     * @return string|false Returns the path to powershell.exe if found, otherwise false.
     */
    public static function getPowerShellPath()
    {
        if ( is_dir( 'C:\Windows\System32\WindowsPowerShell' ) ) {
            return self::findFile( 'C:\Windows\System32\WindowsPowerShell', 'powershell.exe' );
        }

        return false;
    }

    /**
     * Recursively searches for repositories starting from a given path up to a specified depth.
     *
     * @param   string  $initPath   The initial path from where the search begins.
     * @param   string  $startPath  The current path from where to search.
     * @param   string  $checkFile  The file name to check for in the directory to consider it a repository.
     * @param   int     $maxDepth   The maximum depth of directories to search into.
     *
     * @return array Returns an array of paths that contain the specified file.
     */
    public static function findRepos($initPath, $startPath, $checkFile, $maxDepth = 1)
    {
        $depth  = substr_count( str_replace( $initPath, '', $startPath ), '/' );
        $result = array();

        $handle = @opendir( $startPath );
        if ( !$handle ) {
            return $result;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file == '.' || $file == '..' ) {
                continue;
            }
            if ( is_dir( $startPath . '/' . $file ) && ($initPath == $startPath || $depth <= $maxDepth) ) {
                $tmpResults = self::findRepos( $initPath, $startPath . '/' . $file, $checkFile, $maxDepth );
                foreach ( $tmpResults as $tmpResult ) {
                    $result[] = $tmpResult;
                }
            }
            elseif ( is_file( $startPath . '/' . $checkFile ) && !in_array( $startPath, $result ) ) {
                $result[] = self::formatUnixPath( $startPath );
            }
        }

        closedir( $handle );

        return $result;
    }

    /**
     * Converts a Unix-style path to a Windows-style path.
     *
     * @param   string  $path  The Unix-style path to convert.
     *
     * @return string Returns the converted Windows-style path.
     */
    public static function formatWindowsPath($path)
    {
        return str_replace( '/', '\\', $path );
    }

    /**
     * Converts a Windows-style path to a Unix-style path.
     *
     * @param   string  $path  The Windows-style path to convert.
     *
     * @return string Returns the converted Unix-style path.
     */
    public static function formatUnixPath($path)
    {
        return str_replace( '\\', '/', $path );
    }

    /**
     * Converts an image file to a base64 encoded string.
     *
     * @param   string  $path  The path to the image file.
     *
     * @return string Returns the base64 encoded string of the image.
     */
    public static function imgToBase64($path)
    {
        $type = pathinfo( $path, PATHINFO_EXTENSION );
        $data = file_get_contents( $path );

        return 'data:image/' . $type . ';base64,' . base64_encode( $data );
    }

    /**
     * Converts UTF-8 encoded data to Windows-1252 encoding.
     *
     * @param   string  $data  The UTF-8 encoded data.
     *
     * @return string Returns the data encoded in Windows-1252.
     */
    public static function utf8ToCp1252($data)
    {
        return iconv( "UTF-8", "WINDOWS-1252//IGNORE", $data );
    }

    /**
     * Converts Windows-1252 encoded data to UTF-8 encoding.
     *
     * @param   string  $data  The Windows-1252 encoded data.
     *
     * @return string Returns the data encoded in UTF-8.
     */
    public static function cp1252ToUtf8($data)
    {
        return iconv( "WINDOWS-1252", "UTF-8//IGNORE", $data );
    }

    /**
     * Initiates a loading process using external components.
     */
    public static function startLoading()
    {
        global $bearsamppCore, $bearsamppWinbinder;
        $bearsamppWinbinder->exec( $bearsamppCore->getPhpExe(), Core::isRoot_FILE . ' ' . Action::LOADING );
    }

    /**
     * Stops a previously started loading process and cleans up related resources.
     */
    public static function stopLoading()
    {
        global $bearsamppCore;
        if ( file_exists( $bearsamppCore->getLoadingPid() ) ) {
            $pids = file( $bearsamppCore->getLoadingPid() );
            foreach ( $pids as $pid ) {
                Win32Ps::kill( $pid );
            }
            @unlink( $bearsamppCore->getLoadingPid() );
        }
    }

    /**
     * Retrieves a list of files to scan from specified paths or default paths.
     *
     * @param   string|null  $path  Optional. The path to start scanning from. If null, uses default paths.
     *
     * @return array Returns an array of files found during the scan.
     */
    public static function getFilesToScan($path = null)
    {
        $result      = array();
        $pathsToScan = !empty( $path ) ? $path : self::getPathsToScan();
        foreach ( $pathsToScan as $pathToScan ) {
            $startTime = self::getMicrotime();
            $findFiles = self::findFiles( $pathToScan['path'], $pathToScan['includes'], $pathToScan['recursive'] );
            foreach ( $findFiles as $findFile ) {
                $result[] = $findFile;
            }
            self::logDebug( $pathToScan['path'] . ' scanned in ' . round( self::getMicrotime() - $startTime, 3 ) . 's' );
        }

        return $result;
    }

    /**
     * Retrieves a list of directories and file types to scan within the BEARSAMPP environment.
     *
     * This method compiles an array of paths from various components of the BEARSAMPP stack,
     * including Apache, PHP, MySQL, MariaDB, PostgreSQL, Node.js, Filezilla, Composer, ConsoleZ,
     * Python, Ruby, and Yarn. Each path entry includes the directory path, file types to include
     * in the scan, and whether the scan should be recursive.
     *
     * The method uses global variables to access the root paths of each component. It then
     * dynamically fetches specific subdirectories using the `getFolderList` method (which is
     * assumed to be defined elsewhere in this class or in the global scope) and constructs
     * an array of path specifications.
     *
     * Each path specification is an associative array with the following keys:
     * - 'path': The full directory path to scan.
     * - 'includes': An array of file extensions or filenames to include in the scan.
     * - 'recursive': A boolean indicating whether the scan should include subdirectories.
     *
     * The method is designed to be used for setting up scans of configuration files and other
     * important files within the BEARSAMPP environment, possibly for purposes like configuration
     * management, backup, or security auditing.
     *
     * @return array An array of associative arrays, each containing 'path', 'includes', and 'recursive' keys.
     */
    private static function getPathsToScan()
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppBins, $bearsamppApps, $bearsamppTools;
        $paths = array();

        // Alias
        $paths[] = array(
            'path'      => $bearsamppRoot->getAliasPath(),
            'includes'  => array(''),
            'recursive' => false
        );

        // Vhosts
        $paths[] = array(
            'path'      => $bearsamppRoot->getVhostsPath(),
            'includes'  => array(''),
            'recursive' => false
        );

        // OpenSSL
        $paths[] = array(
            'path'      => $bearsamppCore->getOpenSslPath(),
            'includes'  => array('openssl.cfg'),
            'recursive' => false
        );

        // Homepage
        $paths[] = array(
            'path'      => $bearsamppCore->getResourcesPath() . '/homepage',
            'includes'  => array('alias.conf'),
            'recursive' => false
        );

        // Apache
        $folderList = self::getFolderList( $bearsamppBins->getApache()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppBins->getApache()->getRootPath() . '/' . $folder,
                'includes'  => array('.ini', '.conf'),
                'recursive' => true
            );
        }

        // PHP
        $folderList = self::getFolderList( $bearsamppBins->getPhp()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppBins->getPhp()->getRootPath() . '/' . $folder,
                'includes'  => array('.php', '.bat', '.ini', '.reg', '.inc'),
                'recursive' => true
            );
        }

        // MySQL
        $folderList = self::getFolderList( $bearsamppBins->getMysql()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppBins->getMysql()->getRootPath() . '/' . $folder,
                'includes'  => array('my.ini'),
                'recursive' => false
            );
        }

        // MariaDB
        $folderList = self::getFolderList( $bearsamppBins->getMariadb()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppBins->getMariadb()->getRootPath() . '/' . $folder,
                'includes'  => array('my.ini'),
                'recursive' => false
            );
        }

        // PostgreSQL
        $folderList = self::getFolderList( $bearsamppBins->getPostgresql()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppBins->getPostgresql()->getRootPath() . '/' . $folder,
                'includes'  => array('.ber', '.conf', '.bat'),
                'recursive' => true
            );
        }

        // Node.js
        $folderList = self::getFolderList( $bearsamppBins->getNodejs()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppBins->getNodejs()->getRootPath() . '/' . $folder . '/etc',
                'includes'  => array('npmrc'),
                'recursive' => true
            );
            $paths[] = array(
                'path'      => $bearsamppBins->getNodejs()->getRootPath() . '/' . $folder . '/node_modules/npm',
                'includes'  => array('npmrc'),
                'recursive' => false
            );
        }

        // Filezilla
        $folderList = self::getFolderList( $bearsamppBins->getFilezilla()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppBins->getFilezilla()->getRootPath() . '/' . $folder,
                'includes'  => array('.xml'),
                'recursive' => true
            );
        }

        // Composer
        $folderList = self::getFolderList( $bearsamppTools->getComposer()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppTools->getComposer()->getRootPath() . '/' . $folder,
                'includes'  => array('giscus.json'),
                'recursive' => false
            );
        }

        // ConsoleZ
        $folderList = self::getFolderList( $bearsamppTools->getConsoleZ()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppTools->getConsoleZ()->getRootPath() . '/' . $folder,
                'includes'  => array('console.xml', '.ini', '.btm'),
                'recursive' => true
            );
        }

        // Python
        $folderList = self::getFolderList( $bearsamppTools->getPython()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppTools->getPython()->getRootPath() . '/' . $folder . '/bin',
                'includes'  => array('.bat'),
                'recursive' => false
            );
            $paths[] = array(
                'path'      => $bearsamppTools->getPython()->getRootPath() . '/' . $folder . '/settings',
                'includes'  => array('winpython.ini'),
                'recursive' => false
            );
        }

        // Ruby
        $folderList = self::getFolderList( $bearsamppTools->getRuby()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppTools->getRuby()->getRootPath() . '/' . $folder . '/bin',
                'includes'  => array('!.dll', '!.exe'),
                'recursive' => false
            );
        }

        // Yarn
        $folderList = self::getFolderList( $bearsamppTools->getYarn()->getRootPath() );
        foreach ( $folderList as $folder ) {
            $paths[] = array(
                'path'      => $bearsamppTools->getYarn()->getRootPath() . '/' . $folder,
                'includes'  => array('yarn.bat'),
                'recursive' => false
            );
            $paths[] = array(
                'path'      => $bearsamppTools->getYarn()->getRootPath() . '/' . $folder . '/global/bin',
                'includes'  => array('.bat'),
                'recursive' => false
            );
            $paths[] = array(
                'path'      => $bearsamppTools->getYarn()->getRootPath() . '/' . $folder . '/nodejs/etc',
                'includes'  => array('npmrc'),
                'recursive' => true
            );
            $paths[] = array(
                'path'      => $bearsamppTools->getYarn()->getRootPath() . '/' . $folder . '/nodejs/node_modules/npm',
                'includes'  => array('npmrc'),
                'recursive' => false
            );
        }

        return $paths;
    }

    /**
     * Recursively finds files in a directory that match a set of inclusion patterns.
     *
     * @param   string  $startPath  The directory path to start the search from.
     * @param   array   $includes   An array of file patterns to include in the search. Patterns starting with '!' are excluded.
     * @param   bool    $recursive  Determines whether the search should be recursive.
     *
     * @return array An array of files that match the inclusion patterns.
     */
    private static function findFiles($startPath, $includes = array(''), $recursive = true)
    {
        $result = array();

        $handle = @opendir( $startPath );
        if ( !$handle ) {
            return $result;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file == '.' || $file == '..' ) {
                continue;
            }
            if ( is_dir( $startPath . '/' . $file ) && $recursive ) {
                $tmpResults = self::findFiles( $startPath . '/' . $file, $includes );
                foreach ( $tmpResults as $tmpResult ) {
                    $result[] = $tmpResult;
                }
            }
            elseif ( is_file( $startPath . '/' . $file ) ) {
                foreach ( $includes as $include ) {
                    if ( self::startWith( $include, '!' ) ) {
                        $include = ltrim( $include, '!' );
                        if ( self::startWith( $file, '.' ) && !self::endWith( $file, $include ) ) {
                            $result[] = self::formatUnixPath( $startPath . '/' . $file );
                        }
                        elseif ( $file != $include ) {
                            $result[] = self::formatUnixPath( $startPath . '/' . $file );
                        }
                    }
                    elseif ( self::endWith( $file, $include ) || $file == $include || empty( $include ) ) {
                        $result[] = self::formatUnixPath( $startPath . '/' . $file );
                    }
                }
            }
        }

        closedir( $handle );

        return $result;
    }

    /**
     * Replaces old path references with new path references in the specified files.
     *
     * @param   array        $filesToScan  Array of file paths to scan and modify.
     * @param   string|null  $rootPath     The new root path to replace the old one. If null, uses a default root path.
     *
     * @return array Returns an array with the count of occurrences changed and the count of files changed.
     */
    public static function changePath($filesToScan, $rootPath = null)
    {
        global $bearsamppRoot, $bearsamppCore;

        $result = array(
            'countChangedOcc'   => 0,
            'countChangedFiles' => 0
        );

        $rootPath           = $rootPath != null ? $rootPath : $bearsamppRoot->getRootPath();
        $unixOldPath        = self::formatUnixPath( $bearsamppCore->getLastPathContent() );
        $windowsOldPath     = self::formatWindowsPath( $bearsamppCore->getLastPathContent() );
        $unixCurrentPath    = self::formatUnixPath( $rootPath );
        $windowsCurrentPath = self::formatWindowsPath( $rootPath );

        foreach ( $filesToScan as $fileToScan ) {
            $tmpCountChangedOcc = 0;
            $fileContentOr      = file_get_contents( $fileToScan );
            $fileContent        = $fileContentOr;

            // old path
            preg_match( '#' . $unixOldPath . '#i', $fileContent, $unixMatches );
            if ( !empty( $unixMatches ) ) {
                $fileContent        = str_replace( $unixOldPath, $unixCurrentPath, $fileContent, $countChanged );
                $tmpCountChangedOcc += $countChanged;
            }
            preg_match( '#' . str_replace( '\\', '\\\\', $windowsOldPath ) . '#i', $fileContent, $windowsMatches );
            if ( !empty( $windowsMatches ) ) {
                $fileContent        = str_replace( $windowsOldPath, $windowsCurrentPath, $fileContent, $countChanged );
                $tmpCountChangedOcc += $countChanged;
            }

            // placeholders
            preg_match( '#' . Core::PATH_LIN_PLACEHOLDER . '#i', $fileContent, $unixMatches );
            if ( !empty( $unixMatches ) ) {
                $fileContent        = str_replace( Core::PATH_LIN_PLACEHOLDER, $unixCurrentPath, $fileContent, $countChanged );
                $tmpCountChangedOcc += $countChanged;
            }
            preg_match( '#' . Core::PATH_WIN_PLACEHOLDER . '#i', $fileContent, $windowsMatches );
            if ( !empty( $windowsMatches ) ) {
                $fileContent        = str_replace( Core::PATH_WIN_PLACEHOLDER, $windowsCurrentPath, $fileContent, $countChanged );
                $tmpCountChangedOcc += $countChanged;
            }

            if ( $fileContentOr != $fileContent ) {
                $result['countChangedOcc']   += $tmpCountChangedOcc;
                $result['countChangedFiles'] += 1;
                file_put_contents( $fileToScan, $fileContent );
            }
        }

        return $result;
    }

    /**
     * Fetches the latest version information from a given URL.
     *
     * @param   string  $url  The URL to fetch version information from.
     *
     * @return array|null Returns an array with 'version' and 'url' if successful, null otherwise.
     */
    public static function getLatestVersion($url)
    {
        $result = self::getApiJson( $url );
        if ( empty( $result ) ) {
            self::logError( 'Cannot retrieve latest github info for: ' . $result . ' RESULT' );

            return null;
        }

        $resultArray = json_decode( $result, true );
        if ( isset( $resultArray['tag_name'] ) && isset( $resultArray['assets'][0]['browser_download_url'] ) ) {
            $tagName     = $resultArray['tag_name'];
            $downloadUrl = $resultArray['assets'][0]['browser_download_url'];
            $name        = $resultArray['name'];
            self::logDebug( 'Latest version tag name: ' . $tagName );
            self::logDebug( 'Download URL: ' . $downloadUrl );
            self::logDebug( 'Name: ' . $name );

            return ['version' => $tagName, 'html_url' => $downloadUrl, 'name' => $name];
        }
        else {
            self::logError( 'Tag name, download URL, or name not found in the response: ' . $result );

            return null;
        }
    }

    /**
     * Constructs a website URL without UTM parameters.
     *
     * @param   string  $path      Optional path to append to the base URL.
     * @param   string  $fragment  Optional fragment to append to the URL.
     *
     * @return string The constructed URL without UTM parameters.
     */
    public static function getWebsiteUrlNoUtm($path = '', $fragment = '')
    {
        return self::getWebsiteUrl( $path, $fragment, false );
    }

    /**
     * Constructs a complete website URL with optional path, fragment, and UTM source parameters.
     *
     * @param   string  $path       Optional path to append to the base URL.
     * @param   string  $fragment   Optional fragment to append to the URL.
     * @param   bool    $utmSource  Whether to include UTM source parameters.
     *
     * @return string The constructed URL.
     */
    public static function getWebsiteUrl($path = '', $fragment = '', $utmSource = true)
    {
        global $bearsamppCore;

        $url = APP_WEBSITE;
        if ( !empty( $path ) ) {
            $url .= '/' . ltrim( $path, '/' );
        }
        if ( $utmSource ) {
            $url = rtrim( $url, '/' ) . '/?utm_source=bearsampp-' . $bearsamppCore->getAppVersion();
        }
        if ( !empty( $fragment ) ) {
            $url .= $fragment;
        }

        return $url;
    }

    /**
     * Constructs the URL to the changelog page, optionally including UTM parameters.
     *
     * @param   bool  $utmSource  Whether to include UTM source parameters.
     *
     * @return string The URL to the changelog page.
     */
    public static function getChangelogUrl($utmSource = true)
    {
        return self::getWebsiteUrl( 'doc/changelog', null, $utmSource );
    }

    /**
     * Retrieves the file size of a remote file.
     *
     * @param   string  $url            The URL of the remote file.
     * @param   bool    $humanFileSize  Whether to return the size in a human-readable format.
     *
     * @return mixed The file size, either in bytes or as a formatted string.
     */
    public static function getRemoteFilesize($url, $humanFileSize = true)
    {
        $size = 0;

        $data = get_headers( $url, true );
        if ( isset( $data['Content-Length'] ) ) {
            $size = intval( $data['Content-Length'] );
        }

        return $humanFileSize ? self::humanFileSize( $size ) : $size;
    }

    /**
     * Converts a file size in bytes to a human-readable format.
     *
     * @param   int     $size  The file size in bytes.
     * @param   string  $unit  The unit to convert to ('GB', 'MB', 'KB', or ''). If empty, auto-selects the unit.
     *
     * @return string The formatted file size.
     */
    public static function humanFileSize($size, $unit = '')
    {
        if ( (!$unit && $size >= 1 << 30) || $unit == 'GB' ) {
            return number_format( $size / (1 << 30), 2 ) . 'GB';
        }
        if ( (!$unit && $size >= 1 << 20) || $unit == 'MB' ) {
            return number_format( $size / (1 << 20), 2 ) . 'MB';
        }
        if ( (!$unit && $size >= 1 << 10) || $unit == 'KB' ) {
            return number_format( $size / (1 << 10), 2 ) . 'KB';
        }

        return number_format( $size ) . ' bytes';
    }

    /**
     * Checks if the operating system is 32-bit.
     *
     * @return bool True if the OS is 32-bit, false otherwise.
     */
    public static function is32BitsOs()
    {
        $processor = self::getProcessorRegKey();

        return self::contains( $processor, 'x86' );
    }

    /**
     * Retrieves HTTP headers from a given URL using either cURL or fopen, depending on availability.
     *
     * @param   string  $pingUrl  The URL to ping for headers.
     *
     * @return array An array of HTTP headers.
     */
    public static function getHttpHeaders($pingUrl)
    {
        if ( function_exists( 'curl_version' ) ) {
            $result = self::getCurlHttpHeaders( $pingUrl );
        }
        else {
            $result = self::getFopenHttpHeaders( $pingUrl );
        }

        if ( !empty( $result ) ) {
            $rebuildResult = array();
            foreach ( $result as $row ) {
                $row = trim( $row );
                if ( !empty( $row ) ) {
                    $rebuildResult[] = $row;
                }
            }
            $result = $rebuildResult;

            self::logDebug( 'getHttpHeaders:' );
            foreach ( $result as $header ) {
                self::logDebug( '-> ' . $header );
            }
        }

        return $result;
    }

    /**
     * Retrieves HTTP headers from a given URL using the fopen function.
     *
     * This method creates a stream context to disable SSL peer and peer name verification,
     * which allows self-signed certificates. It attempts to open the URL and read the HTTP
     * response headers.
     *
     * @param   string  $url  The URL from which to fetch the headers.
     *
     * @return array An array of headers if successful, otherwise an empty array.
     */
    public static function getFopenHttpHeaders($url)
    {
        $result = array();

        $context = stream_context_create( array(
                                              'ssl' => array(
                                                  'verify_peer'       => false,
                                                  'verify_peer_name'  => false,
                                                  'allow_self_signed' => true,
                                              )
                                          ) );

        $fp = @fopen( $url, 'r', false, $context );
        if ( $fp ) {
            $meta   = stream_get_meta_data( $fp );
            $result = isset( $meta['wrapper_data'] ) ? $meta['wrapper_data'] : $result;
            fclose( $fp );
        }

        return $result;
    }

    /**
     * Retrieves HTTP headers from a given URL using cURL.
     *
     * This method initializes a cURL session, sets various options to fetch headers
     * including disabling SSL peer verification, and executes the request. It logs
     * the raw response for debugging purposes and parses the headers from the response.
     *
     * @param   string  $url  The URL from which to fetch the headers.
     *
     * @return array An array of headers if successful, otherwise an empty array.
     */
    public static function getCurlHttpHeaders($url)
    {
        $result = array();

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_VERBOSE, true );
        curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

        $response = @curl_exec( $ch );
        if ( empty( $response ) ) {
            return $result;
        }

        self::logTrace( 'getCurlHttpHeaders:' . $response );
        $responseHeaders = explode( "\r\n\r\n", $response, 2 );
        if ( !isset( $responseHeaders[0] ) || empty( $responseHeaders[0] ) ) {
            return $result;
        }

        return explode( "\n", $responseHeaders[0] );
    }

    /**
     * Retrieves the initial response line from a specified host and port using a socket connection.
     *
     * This method optionally uses SSL and creates a stream context similar to `getFopenHttpHeaders`.
     * It attempts to connect to the host and port, reads the first line of the response, and parses it.
     * Detailed debug information is logged for each header line received.
     *
     * @param   string  $host  The host name or IP address to connect to.
     * @param   int     $port  The port number to connect to.
     * @param   bool    $ssl   Whether to use SSL (defaults to false).
     *
     * @return array An array containing the first line of the response, split into parts, or an empty array if unsuccessful.
     */
    public static function getHeaders($host, $port, $ssl = false)
    {
        $result  = array();
        $context = stream_context_create( array(
                                              'ssl' => array(
                                                  'verify_peer'       => false,
                                                  'verify_peer_name'  => false,
                                                  'allow_self_signed' => true,
                                              )
                                          ) );

        $fp = @stream_socket_client( ($ssl ? 'ssl://' : '') . $host . ':' . $port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context );
        if ( $fp ) {
            $out    = fgets( $fp );
            $result = explode( PHP_EOL, $out );
            @fclose( $fp );
        }

        if ( !empty( $result ) ) {
            $rebuildResult = array();
            foreach ( $result as $row ) {
                $row = trim( $row );
                if ( !empty( $row ) ) {
                    $rebuildResult[] = $row;
                }
            }
            $result = $rebuildResult;

            self::logDebug( 'getHeaders:' );
            foreach ( $result as $header ) {
                self::logDebug( '-> ' . $header );
            }
        }

        return $result;
    }

    /**
     * Sends a GET request to the specified URL and returns the response.
     *
     * @param   string  $url  The URL to send the GET request to.
     *
     * @return string The trimmed response data from the URL.
     */
    public static function getApiJson($url)
    {
        $header = self::setupCurlHeaderWithToken();

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_VERBOSE, true );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
        $data = curl_exec( $ch );
        if ( curl_errno( $ch ) ) {
            Util::logError( 'CURL Error: ' . curl_error( $ch ) );
        }
        curl_close( $ch );

        return trim( $data );

    }

    /**
     * Checks if a specific port on localhost is in use and returns the process using it if available.
     *
     * @param   int  $port  The port number to check.
     *
     * @return mixed Returns the process using the port if in use, 'N/A' if the port is open but no specific process can be identified, or false if the port is not in use.
     */
    public static function isPortInUse($port)
    {
        // Declaring a variable to hold the IP
        // address getHostName() gets the name
        // of the local machine getHostByName()
        // gets the corresponding IP
        $localIP = getHostByName( getHostName() );

        $connection = @fsockopen( $localIP, $port );
        if ( is_resource( $connection ) ) {
            fclose( $connection );
            $process = Batch::getProcessUsingPort( $port );

            return $process != null ? $process : 'N/A';
        }

        return false;
    }

    /**
     * Validates a domain name based on specific criteria.
     *
     * @param   string  $domainName  The domain name to validate.
     *
     * @return bool Returns true if the domain name is valid, false otherwise.
     */
    public static function isValidDomainName($domainName)
    {
        return preg_match( '/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $domainName )
            && preg_match( '/^.{1,253}$/', $domainName )
            && preg_match( '/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $domainName );
    }

    /**
     * Checks if a string is alphanumeric.
     *
     * @param   string  $string  The string to check.
     *
     * @return bool Returns true if the string is alphanumeric, false otherwise.
     */
    public static function isAlphanumeric($string)
    {
        return ctype_alnum( $string );
    }

    /**
     * Attempts to install and start a service on a specific port, with optional syntax checking and user notifications.
     *
     * @param   object  $bin             An object containing the binary information and methods related to the service.
     * @param   int     $port            The port number on which the service should run.
     * @param   string  $syntaxCheckCmd  The command to execute for syntax checking of the service configuration.
     * @param   bool    $showWindow      Optional. Whether to show message boxes for information, warnings, and errors. Defaults to false.
     *
     * @return bool Returns true if the service is successfully installed and started, false otherwise.
     */
    public static function installService($bin, $port, $syntaxCheckCmd, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $name     = $bin->getName();
        $service  = $bin->getService();
        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::INSTALL_SERVICE_TITLE ), $name );

        $isPortInUse = self::isPortInUse( $port );
        if ( $isPortInUse === false ) {
            if ( !$service->isInstalled() ) {
                $service->create();
                if ( $service->start() ) {
                    self::logInfo( sprintf( '%s service successfully installed. (name: %s ; port: %s)', $name, $service->getName(), $port ) );
                    if ( $showWindow ) {
                        $bearsamppWinbinder->messageBoxInfo(
                            sprintf( $bearsamppLang->getValue( Lang::SERVICE_INSTALLED ), $name, $service->getName(), $port ),
                            $boxTitle
                        );
                    }

                    return true;
                }
                else {
                    $serviceError    = sprintf( $bearsamppLang->getValue( Lang::SERVICE_INSTALL_ERROR ), $name );
                    $serviceErrorLog = sprintf( 'Error during the installation of %s service', $name );
                    if ( !empty( $syntaxCheckCmd ) ) {
                        $cmdSyntaxCheck = $bin->getCmdLineOutput( $syntaxCheckCmd );
                        if ( !$cmdSyntaxCheck['syntaxOk'] ) {
                            $serviceError    .= PHP_EOL . sprintf( $bearsamppLang->getValue( Lang::STARTUP_SERVICE_SYNTAX_ERROR ), $cmdSyntaxCheck['content'] );
                            $serviceErrorLog .= sprintf( ' (conf errors detected : %s)', $cmdSyntaxCheck['content'] );
                        }
                    }
                    self::logError( $serviceErrorLog );
                    if ( $showWindow ) {
                        $bearsamppWinbinder->messageBoxError( $serviceError, $boxTitle );
                    }
                }
            }
            else {
                self::logWarning( sprintf( '%s service already installed', $name ) );
                if ( $showWindow ) {
                    $bearsamppWinbinder->messageBoxWarning(
                        sprintf( $bearsamppLang->getValue( Lang::SERVICE_ALREADY_INSTALLED ), $name ),
                        $boxTitle
                    );
                }

                return true;
            }
        }
        elseif ( $service->isRunning() ) {
            self::logWarning( sprintf( '%s service already installed and running', $name ) );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf( $bearsamppLang->getValue( Lang::SERVICE_ALREADY_INSTALLED ), $name ),
                    $boxTitle
                );
            }

            return true;
        }
        else {
            self::logError( sprintf( 'Port %s is used by an other application : %s', $name ) );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf( $bearsamppLang->getValue( Lang::PORT_NOT_USED_BY ), $port, $isPortInUse ),
                    $boxTitle
                );
            }
        }

        return false;
    }

    /**
     * Removes a service if it is installed.
     *
     * @param   Win32Service  $service  The service object to be removed.
     * @param   string        $name     The name of the service.
     *
     * @return bool Returns true if the service is successfully removed, false otherwise.
     */
    public static function removeService($service, $name)
    {
        if ( !($service instanceof Win32Service) ) {
            self::logError( '$service not an instance of Win32Service' );

            return false;
        }

        if ( $service->isInstalled() ) {
            if ( $service->delete() ) {
                self::logInfo( sprintf( '%s service successfully removed', $name ) );

                return true;
            }
            else {
                self::logError( sprintf( 'Error during the uninstallation of %s service', $name ) );

                return false;
            }
        }
        else {
            self::logWarning( sprintf( '%s service does not exist', $name ) );
        }

        return true;
    }

    /**
     * Attempts to start a service and performs a syntax check if required.
     *
     * @param   object  $bin             An object containing service details.
     * @param   string  $syntaxCheckCmd  Command to check syntax errors.
     * @param   bool    $showWindow      Whether to show error messages in a window.
     *
     * @return bool Returns true if the service starts successfully, false otherwise.
     */
    public static function startService($bin, $syntaxCheckCmd, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $name     = $bin->getName();
        $service  = $bin->getService();
        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::START_SERVICE_TITLE ), $name );

        if ( !$service->start() ) {
            $serviceError    = sprintf( $bearsamppLang->getValue( Lang::START_SERVICE_ERROR ), $name );
            $serviceErrorLog = sprintf( 'Error while starting the %s service', $name );
            if ( !empty( $syntaxCheckCmd ) ) {
                $cmdSyntaxCheck = $bin->getCmdLineOutput( $syntaxCheckCmd );
                if ( !$cmdSyntaxCheck['syntaxOk'] ) {
                    $serviceError    .= PHP_EOL . sprintf( $bearsamppLang->getValue( Lang::STARTUP_SERVICE_SYNTAX_ERROR ), $cmdSyntaxCheck['content'] );
                    $serviceErrorLog .= sprintf( ' (conf errors detected : %s)', $cmdSyntaxCheck['content'] );
                }
            }
            self::logError( $serviceErrorLog );
            if ( $showWindow ) {
                $bearsamppWinbinder->messageBoxError( $serviceError, $boxTitle );
            }

            return false;
        }

        return true;
    }

    /**
     * Constructs a GitHub user URL with an optional path.
     *
     * @param   string|null  $part  Optional path to append to the URL.
     *
     * @return string The full GitHub user URL.
     */
    public static function getGithubUserUrl($part = null)
    {
        $part = !empty( $part ) ? '/' . $part : null;

        return 'https://github.com/' . APP_GITHUB_USER . $part;
    }

    /**
     * Constructs a GitHub repository URL with an optional path.
     *
     * @param   string|null  $part  Optional path to append to the URL.
     *
     * @return string The full GitHub repository URL.
     */
    public static function getGithubUrl($part = null)
    {
        $part = !empty( $part ) ? '/' . $part : null;

        return self::getGithubUserUrl( APP_GITHUB_REPO . $part );
    }

    /**
     * Constructs a URL for raw content from a GitHub repository.
     *
     * @param   string  $file  The file path to append to the base URL.
     *
     * @return string The full URL to the raw content on GitHub.
     */
    public static function getGithubRawUrl($file)
    {
        $file = !empty( $file ) ? '/' . $file : null;

        return 'https://raw.githubusercontent.com/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . '/main' . $file;
    }

    /**
     * Retrieves a list of folders from a specified directory, excluding certain directories.
     *
     * @param   string  $path  The directory path from which to list folders.
     *
     * @return array|bool An array of folder names, or false if the directory cannot be opened.
     */
    public static function getFolderList($path)
    {
        $result = array();

        $handle = @opendir( $path );
        if ( !$handle ) {
            return false;
        }

        while ( false !== ($file = readdir( $handle )) ) {
            $filePath = $path . '/' . $file;
            if ( $file != "." && $file != ".." && is_dir( $filePath ) && $file != 'current' ) {
                $result[] = $file;
            }
        }

        closedir( $handle );

        return $result;
    }

    /**
     * Retrieves and formats environment paths from a data file.
     * Paths are verified to be directories and formatted to Unix style.
     * Warnings are logged for paths that do not exist.
     *
     * @return string A semicolon-separated string of formatted environment paths.
     * @global object $bearsamppRoot Global object containing root path methods.
     */
    public static function getNssmEnvPaths()
    {
        global $bearsamppRoot;

        $result           = '';
        $nssmEnvPathsFile = $bearsamppRoot->getRootPath() . '/nssmEnvPaths.dat';

        if ( is_file( $nssmEnvPathsFile ) ) {
            $paths = explode( PHP_EOL, file_get_contents( $nssmEnvPathsFile ) );
            foreach ( $paths as $path ) {
                $path = trim( $path );
                if ( stripos( $path, ':' ) === false ) {
                    $path = $bearsamppRoot->getRootPath() . '/' . $path;
                }
                if ( is_dir( $path ) ) {
                    $result .= self::formatUnixPath( $path ) . ';';
                }
                else {
                    self::logWarning( 'Path not found in nssmEnvPaths.dat: ' . $path );
                }
            }
        }

        return $result;
    }

    /**
     * Opens a file with a given caption and content in the default text editor.
     * The file is created in a temporary directory with a unique name.
     *
     * @param   string  $caption            The filename to use when saving the content.
     * @param   string  $content            The content to write to the file.
     *
     * @global object   $bearsamppRoot      Global object to access temporary path.
     * @global object   $bearsamppConfig    Global configuration object.
     * @global object   $bearsamppWinbinder Global object to execute external programs.
     */
    public static function openFileContent($caption, $content)
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppWinbinder;

        $folderPath = $bearsamppRoot->getTmpPath() . '/openFileContent-' . self::random();
        if ( !is_dir( $folderPath ) ) {
            mkdir( $folderPath, 0777, true );
        }

        $filepath = self::formatWindowsPath( $folderPath . '/' . $caption );
        file_put_contents( $filepath, $content );

        $bearsamppWinbinder->exec( $bearsamppConfig->getNotepad(), '"' . $filepath . '"' );
    }

    /**
     * Decrypts a file encrypted with a specified method and returns the content.
     *
     * @param   string  $encryptedFile  Path to the encrypted file.
     * @param   string  $password       Password used for decryption.
     * @param   string  $method         Encryption method used (e.g., AES-256-CBC).
     *
     * @return string|false Decrypted content or false on failure.
     */
    public static function decryptFile()
    {
        global $bearsamppCore;

        $stringfile    = $bearsamppCore->getResourcesPath() . '/string.dat';
        $encryptedFile = $bearsamppCore->getResourcesPath() . '/github.dat';
        $method        = 'AES-256-CBC'; // The same encryption method used

        // Get key string
        $stringPhrase = file_get_contents( $stringfile );
        if ( $stringPhrase === false ) {
            Util::logDebug( "Failed to read the file at path: {$stringfile}" );

            return false;
        }

        $stringKey = convert_uudecode( $stringPhrase );

        // Read the encrypted data from the file
        $encryptedData = file_get_contents( $encryptedFile );
        if ( $encryptedData === false ) {
            Util::logDebug( "Failed to read the file at path: {$encryptedFile}" );

            return false;
        }

        // Decode the base64 encoded data
        $data = base64_decode( $encryptedData );
        if ( $data === false ) {
            Util::logDebug( "Failed to decode the data from path: {$encryptedFile}" );

            return false;
        }

        // Extract the IV which was prepended to the encrypted data
        $ivLength  = openssl_cipher_iv_length( $method );
        $iv        = substr( $data, 0, $ivLength );
        $encrypted = substr( $data, $ivLength );

        // Decrypt the data
        $decrypted = openssl_decrypt( $encrypted, $method, $stringKey, 0, $iv );
        if ( $decrypted === false ) {
            Util::logDebug( "Decryption failed for data from path: {$encryptedFile}" );

            return false;
        }

        return $decrypted;
    }

    /**
     * Sets up a cURL header array using a decrypted GitHub Personal Access Token.
     *
     * @return array The header array for cURL with authorization and other necessary details.
     */
    public static function setupCurlHeaderWithToken()
    {

        // Usage
        global $bearsamppCore, $bearsamppConfig;
        $Token = self::decryptFile();

        return [
            'Accept: application/vnd.github+json',
            'Authorization: Token ' . $Token,
            'User-Agent: ' . APP_GITHUB_USERAGENT,
            'X-GitHub-Api-Version: 2022-11-28'
        ];
    }

    public static function rebuildIni()
    {

    }
}
