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
 * Cryptographically secure random string, token, and byte generators.
 *
 * Usage:
 * ```
 * Random::random(16);                 // cryptographically secure random string
 * Random::generateSecureToken(32);    // hex token for sessions / CSRF
 * Random::generateSecureBytes(32);    // raw bytes for keys / IVs
 * ```
 */
class Random
{
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
