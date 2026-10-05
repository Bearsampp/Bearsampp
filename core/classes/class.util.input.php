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
     * The validation rule itself lives in Util::isValidPort() so that this
     * class and the bin classes cannot drift apart; this method only adds the
     * logging and returns the normalized integer. Leading zeros are accepted
     * and stripped, so '080' becomes 80.
     *
     * @param   mixed  $port  The port to sanitize.
     *
     * @return int|false Returns the sanitized port as integer, or false if invalid.
     */
    public static function sanitizePort($port)
    {
        $portStr = trim((string) $port);

        if (!Util::isValidPort($port)) {
            // The rule is applied only once, in Util::isValidPort(). Here the
            // digit test just separates the two rejection reasons in the log.
            Log::warning(
                ctype_digit($portStr)
                    ? 'Port out of valid range: ' . $portStr
                    : 'Invalid port provided: ' . var_export($port, true)
            );

            return false;
        }

        return (int) $portStr;
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
     * Sanitizes a value that will be embedded in a generated .bat script.
     *
     * Batch scripts are written verbatim to disk by Batch::exec() and then run
     * through cmd /c, so a value must not be able to terminate its own argument
     * or start a new command line. The rules are:
     *   - CR/LF and all other control characters are removed, because a newline
     *     appends an entirely new command to the script.
     *   - "%" is doubled so cmd performs no variable expansion.
     *   - "!" is removed in case delayed expansion is ever enabled.
     *   - The characters cmd treats as command separators or quoting are removed.
     *
     * Quotes are stripped from the returned value, so a caller that wraps the
     * result in its own quotes always produces one well-formed argument.
     * Values that must keep their literal form (identifiers, file names)
     * should be validated with a whitelist instead, see sanitizeServiceName().
     *
     * @param   string|null  $value           The value to sanitize.
     * @param   bool          $preserveQuotes  True when the value legitimately
     *                                         contains quotes that must survive,
     *                                         e.g. --defaults-file="C:\my.ini".
     *
     * @return string Returns the sanitized value, empty string for null/non-string input.
     */
    public static function sanitizeBatchValue($value, $preserveQuotes = false)
    {
        if (!is_string($value)) {
            return '';
        }

        // Strip NUL and every control character, CR/LF included.
        $sanitized = preg_replace('/[\x00-\x1F\x7F]/', '', $value);

        $metacharacters = $preserveQuotes
            ? array('&', '|', '<', '>', '^', '!')
            : array('"', '&', '|', '<', '>', '^', '!');
        $sanitized = str_replace($metacharacters, '', $sanitized);

        // Double "%" so cmd does not expand %VAR%.
        $sanitized = str_replace('%', '%%', $sanitized);

        return $sanitized;
    }

    /**
     * Prepares a value written between double quotes in a batch file (paths,
     * display names). Inside quotes cmd treats & | < > ^ as literals, so they
     * are kept and the filesystem identity of the path is preserved. Only
     * control characters and quotes (invalid in Windows paths) are stripped,
     * and "%" is doubled.
     *
     * @param   string|null  $value  The value to escape.
     *
     * @return string Escaped value, empty string for null/non-string input.
     */
    public static function sanitizeQuotedBatchValue($value)
    {
        if (!is_string($value)) {
            return '';
        }

        $sanitized = preg_replace('/[\x00-\x1F\x7F"]/', '', $value);

        return str_replace('%', '%%', $sanitized);
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

        // ENT_SUBSTITUTE keeps invalid byte sequences visible as U+FFFD. Without it
        // htmlspecialchars() returns an empty string, which would silently blank
        // out any value that is not valid UTF-8.
        return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Sanitizes a URL for use in an href/src attribute.
     *
     * HTML-escaping alone is not enough for a URL: a value such as
     * "javascript:alert(1)" contains no character that htmlspecialchars()
     * touches, yet it executes when the link is clicked. Only http, https and
     * protocol-relative URLs are allowed; anything else (javascript:, data:,
     * vbscript:, file:) yields an empty string so the caller emits a dead link
     * instead of a live one.
     *
     * @param   string  $url  The URL to sanitize.
     *
     * @return string Returns the escaped URL, or an empty string if not allowed.
     */
    public static function sanitizeUrl($url)
    {
        if (!is_string($url)) {
            return '';
        }

        $url = str_replace(array("\0", "\r", "\n", "\t"), '', $url);
        $url = trim($url);

        if ($url === '') {
            return '';
        }

        // Reject any remaining control character: it cannot legitimately appear
        // in a URL, and one could break out of the attribute or smuggle a
        // second scheme past the check below.
        if (preg_match('/[\x00-\x1F\x7F]/', $url)) {
            return '';
        }

        // Protocol-relative URLs inherit the page scheme and are always safe.
        if (strpos($url, '//') === 0) {
            return self::sanitizeOutput($url);
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        // parse_url() returns null when the value carries no scheme at all
        // (a bare relative path, or a malformed string). Reject instead of
        // feeding null into strtolower(), which is deprecated since PHP 8.1.
        if (!is_string($scheme)) {
            return '';
        }

        $scheme = strtolower($scheme);
        if ($scheme !== 'http' && $scheme !== 'https') {
            return '';
        }

        return self::sanitizeOutput($url);
    }
}

