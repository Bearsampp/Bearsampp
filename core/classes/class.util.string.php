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
 * String utility methods and cryptographically secure random generators.
 *
 * Provides case-insensitive substring checks, prefix/suffix tests, alphanumeric
 * validation, and secure random string/token/bytes generation.
 *
 * Note: This class is required directly by the bootstrap (class.root.php) because
 * the autoloader itself depends on UtilString::startWith().
 *
 * Usage:
 * ```
 * UtilString::startWith('foobar', 'foo'); // true
 * UtilString::random(16);                 // cryptographically secure random string
 * ```
 */
class UtilString
{
    /**
     * Checks if a string contains a specified substring (case-insensitive).
     *
     * @param   string  $string  The string to search in.
     * @param   string  $search  The substring to search for.
     *
     * @return bool Returns true if the substring is found, otherwise false.
     */
    public static function contains($string, $search)
    {
        if (!empty($string) && !empty($search)) {
            return stripos($string, $search) !== false;
        }

        return false;
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
        if ($string === null || $string === '') {
            return false;
        }

        return (substr($string, 0, strlen($search)) === $search);
    }

    /**
     * Checks if a string ends with a specified substring.
     *
     * @param   string  $string  The string to check.
     * @param   string  $search  The substring to look for at the end of the string.
     *
     * @return bool Returns true if the string ends with the search substring, otherwise false.
     */
    public static function endWith($string, $search)
    {
        $length = strlen($search);
        $start  = $length * -1;

        return (substr($string, $start) === $search);
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
        return ctype_alnum($string);
    }

    /**
     * Generates a cryptographically secure random string of specified length and character set.
     *
     * @param   int   $length       The length of the random string to generate.
     * @param   bool  $withNumeric  Whether to include numeric characters in the random string.
     *
     * @return string Returns the generated random string.
     * @throws Exception If an appropriate source of randomness cannot be found.
     */
    public static function random($length = 32, $withNumeric = true)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($withNumeric) {
            $characters .= '0123456789';
        }

        $charactersLength = strlen($characters);
        $randomString     = '';

        try {
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
        } catch (Exception $e) {
            Log::error('Failed to generate cryptographically secure random string: ' . $e->getMessage());
            throw $e;
        }

        return $randomString;
    }

    /**
     * Generates a cryptographically secure random token as a hexadecimal string.
     * Ideal for security tokens, session IDs, CSRF tokens, etc.
     *
     * @param   int  $length  The length in bytes (output will be double this in hex characters).
     *
     * @return string Returns a hexadecimal string of cryptographically secure random bytes.
     * @throws Exception If an appropriate source of randomness cannot be found.
     */
    public static function generateSecureToken($length = 32)
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (Exception $e) {
            Log::error('Failed to generate secure token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generates a cryptographically secure random bytes string.
     * Useful for encryption keys, initialization vectors, etc.
     *
     * @param   int  $length  The length in bytes.
     *
     * @return string Returns raw binary random bytes.
     * @throws Exception If an appropriate source of randomness cannot be found.
     */
    public static function generateSecureBytes($length = 32)
    {
        try {
            return random_bytes($length);
        } catch (Exception $e) {
            Log::error('Failed to generate secure bytes: ' . $e->getMessage());
            throw $e;
        }
    }
}
