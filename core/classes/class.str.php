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
 * String predicate utilities.
 *
 * Provides case-insensitive substring checks, prefix/suffix tests, and
 * alphanumeric validation.
 *
 * Note: This class is required directly by the bootstrap (class.root.php) because
 * the autoloader itself depends on Str::startsWith().
 *
 * Usage:
 * ```
 * Str::startsWith('foobar', 'foo'); // true
 * ```
 */
class Str
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
    public static function startsWith($string, $search)
    {
        if ($string === null || $string === '') {
            return false;
        }

        return str_starts_with((string) $string, (string) $search);
    }

    /**
     * Checks if a string ends with a specified substring.
     *
     * @param   string  $string  The string to check.
     * @param   string  $search  The substring to look for at the end of the string.
     *
     * @return bool Returns true if the string ends with the search substring, otherwise false.
     */
    public static function endsWith($string, $search)
    {
        if ($string === null || $string === '') {
            return false;
        }

        return str_ends_with((string) $string, (string) $search);
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
}
