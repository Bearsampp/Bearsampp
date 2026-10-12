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
 * Input validation helpers.
 *
 * Validates IP addresses, port numbers, and domain names.
 */
class Validator
{
    /**
     * Validates an IP address.
     *
     * @param   string  $ip  The IP address to validate.
     *
     * @return bool Returns true if the IP address is valid, otherwise false.
     */
    public static function isValidIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Validates a port number.
     *
     * A padded setting such as '080' is a valid port and is accepted, because
     * module configuration already tolerates it: the Apache bin validates its
     * own configured port with is_numeric() > 0, which accepts leading zeros,
     * so a stricter check here used to reject that port and abort the update.
     * FILTER_VALIDATE_INT cannot be used for this because it reads a leading
     * zero as an octal literal and rejects '080' outright.
     *
     * The value is therefore validated as decimal digits and normalized to an
     * integer before the range check, so the padding is discarded rather than
     * carried into any comparison. Callers that persist the port should cast
     * with intval(), which is base 10 and keeps '080' -> 80.
     *
     * The (string) cast is required before ctype_digit(): given an int, PHP
     * reads the value as a character code instead, so ctype_digit(80) would
     * test 'P' and wrongly report false (and is deprecated since PHP 8.1).
     *
     * @param   mixed  $port  The port number to validate.
     *
     * @return bool Returns true if the port number is valid and within the range of 1 to 65535, otherwise false.
     */
    public static function isValidPort($port)
    {
        $portStr = trim((string) $port);

        if (!ctype_digit($portStr)) {
            return false;
        }

        $port = (int) $portStr;

        return $port >= 1 && $port <= 65535;
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
        return filter_var($domainName, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }
}
