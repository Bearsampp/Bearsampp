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
 * Input cleaning and sanitization utilities.
 *
 * Provides safe access to command-line arguments, GET, and POST variables, plus
 * sanitizers for PIDs, ports, service names, file paths, and HTML output.
 *
 * Usage:
 * ```
 * $action = UtilInput::cleanArgv(1);
 * $page   = UtilInput::cleanGetVar('p');
 * $pid    = UtilInput::sanitizePID($rawPid);
 * ```
 */
class UtilInput
{
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
        if (is_string($name)) {
            if ($type == 'text') {
                $value = (isset($_GET[$name]) && $_GET[$name] !== '') ? (string)$_GET[$name] : '';
                $value = str_replace("\0", '', $value);
                $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
                $value = trim($value);
                return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
        if (is_string($name)) {
            if ($type == 'text') {
                $value = (isset($_POST[$name]) && $_POST[$name] !== '') ? (string)$_POST[$name] : '';
                $value = str_replace("\0", '', $value);
                $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
                $value = trim($value);
                return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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

    /**
     * Sanitizes a process ID (PID) by removing all non-numeric characters.
     * This prevents command injection through PID parameters.
     *
     * @param   mixed  $pid  The PID to sanitize.
     *
     * @return int|false Returns the sanitized PID as integer, or false if invalid.
     */
    public static function sanitizePID($pid)
    {
        $sanitized = preg_replace('/[^0-9]/', '', (string)$pid);

        if (empty($sanitized)) {
            Log::warning('Invalid PID provided: ' . var_export($pid, true));
            return false;
        }

        $pidInt = (int)$sanitized;

        if ($pidInt <= 0 || $pidInt > 2147483647) {
            Log::warning('PID out of valid range: ' . $pidInt);
            return false;
        }

        return $pidInt;
    }

    /**
     * Sanitizes a port number by ensuring it's a valid integer in the correct range.
     * This prevents command injection through port parameters.
     *
     * @param   mixed  $port  The port to sanitize.
     *
     * @return int|false Returns the sanitized port as integer, or false if invalid.
     */
    public static function sanitizePort($port)
    {
        $portStr = trim((string)$port);

        if ($portStr === '' || !preg_match('/^\d+$/', $portStr)) {
            Log::warning('Invalid port provided: ' . var_export($port, true));
            return false;
        }

        $portInt = (int)$portStr;

        if ($portInt < 1 || $portInt > 65535) {
            Log::warning('Port out of valid range: ' . $portInt);
            return false;
        }

        return $portInt;
    }

    /**
     * Sanitizes a service name by removing dangerous characters.
     * Allows only alphanumeric characters, underscores, and hyphens.
     *
     * @param   string  $serviceName  The service name to sanitize.
     *
     * @return string|false Returns the sanitized service name, or false if invalid.
     */
    public static function sanitizeServiceName($serviceName)
    {
        if (!is_string($serviceName) || empty($serviceName)) {
            Log::warning('Invalid service name: not a string or empty');
            return false;
        }

        $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '', $serviceName);

        if (empty($sanitized)) {
            Log::warning('Service name became empty after sanitization: ' . $serviceName);
            return false;
        }

        // Limit length to 256 characters (Windows service name limit)
        if (strlen($sanitized) > 256) {
            $sanitized = substr($sanitized, 0, 256);
        }

        return $sanitized;
    }

    /**
     * Sanitizes a file path by removing null bytes and checking for path traversal attempts.
     * This is a basic sanitization — paths should still be validated before use.
     *
     * @param   string  $path  The path to sanitize.
     *
     * @return string|false Returns the sanitized path, or false if dangerous patterns detected.
     */
    public static function sanitizePath($path)
    {
        if (!is_string($path) || empty($path)) {
            return false;
        }

        $sanitized = str_replace("\0", '', $path);

        // Check for path traversal attempts (but allow environment variables)
        $pathWithoutEnvVars = preg_replace('/%[^%]+%/', '', $sanitized);
        if (strpos($pathWithoutEnvVars, '..') !== false) {
            Log::warning('Path traversal attempt detected: ' . $path);
            return false;
        }

        // Remove dangerous characters — preserve : for drive letters and ; for PATH
        // Also strip common cmd.exe metacharacters to reduce command-injection risk when paths are interpolated.
        $sanitized = preg_replace('/[<>"|?*&^`\x00-\x1F]/', '', $sanitized);

        return $sanitized;
    }

    /**
     * Sanitizes output for display to prevent XSS attacks.
     * Escapes HTML special characters.
     *
     * @param   string  $output  The output to sanitize.
     *
     * @return string Returns the sanitized output safe for HTML display.
     */
    public static function sanitizeOutput($output)
    {
        if (!is_string($output)) {
            return '';
        }

        $output = str_replace("\0", '', $output);

        return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
