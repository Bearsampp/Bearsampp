<?php
/*
 * Copyright (c) 2021-2025 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Csrf
 *
 * Provides CSRF (Cross-Site Request Forgery) protection for the Bearsampp application.
 * This class handles token generation, validation, and management to prevent CSRF attacks.
 *
 * Features:
 * - Secure token generation using cryptographically secure random bytes
 * - Session-based token storage
 * - Token expiration (default: 2 hours)
 * - Token regeneration for enhanced security
 * - Automatic cleanup of expired tokens
 *
 * Usage:
 * ```php
 * // Generate and get token for forms/AJAX
 * $token = Csrf::getToken();
 *
 * // Validate token from request
 * if (!Csrf::validateToken($_POST['csrf_token'])) {
 *     die('CSRF validation failed');
 * }
 * ```
 */
class Csrf
{
    /**
     * Session key for storing CSRF tokens
     */
    const SESSION_KEY = 'bearsampp_csrf_tokens';

    /**
     * Token expiration time in seconds (default: 2 hours)
     */
    const TOKEN_EXPIRATION = 7200;

    /**
     * Maximum number of tokens to store per session
     * This prevents session bloat from token accumulation
     */
    const MAX_TOKENS = 10;

    /**
     * Initializes the CSRF protection system.
     * Starts the session if not already started and cleans up expired tokens.
     *
     * @return void
     */
    public static function init()
    {
        // Harden session settings before the session is started
        self::configureSession();

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize token storage if not exists
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        // Clean up expired tokens
        self::cleanupExpiredTokens();
    }

    /**
     * Hardens the session configuration before a session is started.
     * These settings are also present in the bundled php.ini, but are applied
     * here as defense in depth in case that file is customized.
     *
     * @return void
     */
    private static function configureSession()
    {
        // Only relevant before the session has been started
        if (session_status() !== PHP_SESSION_NONE || session_id() !== '') {
            return;
        }

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1');
    }

    /**
     * Generates a new CSRF token and stores it in the session.
     *
     * @return string The generated CSRF token
     * @throws Exception If random_bytes() fails
     */
    public static function generateToken()
    {
        self::init();

        // Fail closed: never fall back to weak randomness. random_bytes()
        // throws if no secure source of entropy is available.
        $token = bin2hex(random_bytes(32));

        // Store token with timestamp
        $_SESSION[self::SESSION_KEY][$token] = time();

        // Limit number of stored tokens
        if (count($_SESSION[self::SESSION_KEY]) > self::MAX_TOKENS) {
            // Remove oldest token
            $oldestToken = array_key_first($_SESSION[self::SESSION_KEY]);
            unset($_SESSION[self::SESSION_KEY][$oldestToken]);
        }

        // Log token generation without exposing token material
        Log::debug('CSRF token generated successfully');

        return $token;
    }

    /**
     * Gets the current CSRF token, generating a new one if none exists.
     *
     * @return string The CSRF token
     */
    public static function getToken()
    {
        self::init();

        // If no tokens exist, generate one
        if (empty($_SESSION[self::SESSION_KEY])) {
            return self::generateToken();
        }

        // Return the most recent token
        $tokens = $_SESSION[self::SESSION_KEY];
        end($tokens);
        $latestToken = key($tokens);

        // Check if latest token is expired
        if (time() - $tokens[$latestToken] > self::TOKEN_EXPIRATION) {
            // Generate new token if expired
            return self::generateToken();
        }

        return $latestToken;
    }

    /**
     * Validates a CSRF token.
     *
     * @param string|null $token The token to validate
     * @param bool $removeAfterValidation Whether to remove the token after successful validation (one-time use)
     * @return bool True if token is valid, false otherwise
     */
    public static function validateToken($token, $removeAfterValidation = false)
    {
        self::init();

        // Check if token is provided
        if (empty($token) || !is_string($token)) {
            Log::warning('CSRF validation failed: No token provided');
            return false;
        }

        // Check if token exists in session
        if (!isset($_SESSION[self::SESSION_KEY][$token])) {
            Log::warning('CSRF validation failed: Token not found in session');
            return false;
        }

        // Check if token is expired
        $tokenTimestamp = $_SESSION[self::SESSION_KEY][$token];
        if (time() - $tokenTimestamp > self::TOKEN_EXPIRATION) {
            Log::warning('CSRF validation failed: Token expired');
            unset($_SESSION[self::SESSION_KEY][$token]);
            return false;
        }

        // Token is valid
        Log::debug('CSRF token validated successfully');

        // Remove token if one-time use is requested
        if ($removeAfterValidation) {
            unset($_SESSION[self::SESSION_KEY][$token]);
        }

        return true;
    }

    /**
     * Validates a CSRF token from the request (POST body or header).
     * Checks $_POST['csrf_token'] first, then the X-CSRF-Token header.
     * Also verifies the request is same-origin.
     *
     * @param bool $removeAfterValidation Whether to remove the token after successful validation
     * @return bool True if token is valid, false otherwise
     */
    public static function validateRequest($removeAfterValidation = false)
    {
        // Reject cross-origin requests before checking the token.
        // Combined with SameSite=Strict cookies this blocks classic CSRF and
        // DNS-rebinding attacks.
        if (!self::validateOrigin()) {
            return false;
        }

        // Accept the token from the POST body or a dedicated header only.
        // Tokens in the query string leak via Referer headers, logs and
        // browser history.
        if (isset($_POST['csrf_token'])) {
            return self::validateToken($_POST['csrf_token'], $removeAfterValidation);
        }

        // Check custom header (for AJAX requests)
        $headers = self::getAllHeaders();

        // Check for X-CSRF-Token header (case-insensitive)
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'x-csrf-token') {
                return self::validateToken($value, $removeAfterValidation);
            }
        }

        Log::warning('CSRF validation failed: No token in request');
        return false;
    }

    /**
     * Validates that the request is addressed to an intended/trusted host and
     * originates from that same origin.
     *
     * The request host (HTTP_HOST) must be part of a fixed allowlist of hosts
     * the local server is meant to serve (localhost, 127.0.0.1, ::1, the
     * machine hostname and any configured virtual hosts). Comparing the Origin
     * header against an attacker-influenced HTTP_HOST alone would not stop
     * DNS-rebinding attacks, where a malicious domain is pointed at 127.0.0.1
     * and both the Host and Origin headers are controlled by the attacker.
     *
     * Uses the Origin header when present and falls back to the Referer header.
     * Browsers always send Origin on cross-origin POST requests, so a missing
     * Origin/Referer on a state-changing request is treated as invalid.
     *
     * @return bool True if the request is same-origin, false otherwise
     */
    private static function validateOrigin()
    {
        $httpHost = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : '';
        if ($httpHost === '') {
            Log::warning('CSRF validation failed: HTTP_HOST not available');
            return false;
        }

        $allowedHosts = self::getAllowedHosts();

        // The request must be addressed to an intended host. This is the key
        // defense against DNS rebinding: an attacker's domain, even resolved
        // to 127.0.0.1, will not be in the allowlist.
        $requestHost = self::normalizeHost($httpHost);
        if (!self::isHostAllowed($requestHost, $allowedHosts)) {
            Log::warning('CSRF validation failed: Request host "' . $requestHost . '" is not an allowed host');
            return false;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') ? 'https' : 'http';

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? (string)$_SERVER['HTTP_ORIGIN'] : '';
        if ($origin !== '') {
            return self::isAllowedOrigin($origin, $httpHost, $scheme, $allowedHosts);
        }

        $referer = isset($_SERVER['HTTP_REFERER']) ? (string)$_SERVER['HTTP_REFERER'] : '';
        if ($referer !== '') {
            return self::isAllowedOrigin($referer, $httpHost, $scheme, $allowedHosts);
        }

        Log::warning('CSRF validation failed: Missing Origin and Referer headers');
        return false;
    }

    /**
     * Builds the allowlist of hosts the local server is intended to serve.
     * Keeps DNS-rebinding protection effective while still allowing access
     * through the machine hostname and any user-configured virtual hosts.
     *
     * @return array List of allowed, normalized host names.
     */
    private static function getAllowedHosts()
    {
        $allowed = array('localhost', '127.0.0.1', '[::1]', '::1');

        // The local address this very request was received on. This is the
        // reachable address (e.g. a LAN IP) clients use when the application
        // has been put online, so requests addressed to the server's own
        // reachable addresses must be accepted.
        if (isset($_SERVER['SERVER_ADDR'])) {
            $serverAddr = self::normalizeHost((string)$_SERVER['SERVER_ADDR']);
            if ($serverAddr !== '') {
                $allowed[] = $serverAddr;
            }
        }

        $hostname = function_exists('gethostname') ? @gethostname() : false;
        if ($hostname !== false && $hostname !== '') {
            $allowed[] = self::normalizeHost($hostname);
        }

        global $bearsamppConfig;
        if (is_object($bearsamppConfig) && method_exists($bearsamppConfig, 'getHostname')) {
            $cfgHostname = $bearsamppConfig->getHostname();
            if (is_string($cfgHostname) && $cfgHostname !== '') {
                $allowed[] = self::normalizeHost($cfgHostname);
            }
        }

        global $bearsamppBins;
        if (is_object($bearsamppBins) && method_exists($bearsamppBins, 'getApache')) {
            try {
                $apache = $bearsamppBins->getApache();
                if (is_object($apache) && method_exists($apache, 'getVhosts')) {
                    $vhostNames = array();
                    foreach ($apache->getVhosts() as $vhost) {
                        if (is_string($vhost) && $vhost !== '') {
                            $vhostNames[] = $vhost;
                        }
                    }

                    // Resolve the actual host names each vhost config serves.
                    // Relying on the config file names alone would miss ServerAlias
                    // entries and hosts that differ from the file name, which would
                    // wrongly reject legitimate requests when HTTPS (secure Apache
                    // settings) is used.
                    $parsedHosts = array();
                    $vhostsPath = Path::getVhostsPath();
                    if (is_dir($vhostsPath)) {
                        foreach ($vhostNames as $vhost) {
                            $content = @file_get_contents($vhostsPath . '/' . $vhost . '.conf');
                            if ($content === false) {
                                // Fall back to the config file name when it cannot be read
                                $parsedHosts[] = $vhost;
                                continue;
                            }

                            $found = false;
                            foreach (array('ServerName', 'ServerAlias') as $directive) {
                                if (preg_match_all('/^\s*' . $directive . '\s+([^\s#]+)/mi', $content, $matches)) {
                                    foreach ($matches[1] as $declaredHost) {
                                        $normalized = self::normalizeHost($declaredHost);
                                        if ($normalized !== '') {
                                            $parsedHosts[] = $normalized;
                                            $found = true;
                                        }
                                    }
                                }
                            }

                            // Keep the file name when the config declares no host name
                            if (!$found) {
                                $parsedHosts[] = $vhost;
                            }
                        }
                    }

                    foreach (array_unique($parsedHosts) as $vhost) {
                        $allowed[] = $vhost;
                        // Vhost certificates cover any subdomain (e.g. www.vhost.local),
                        // so those hosts must be accepted as well.
                        $allowed[] = '*.' . $vhost;
                    }
                }
            } catch (\Throwable $e) {
                // Host discovery must never break CSRF validation
                Log::warning('CSRF validation failed: Unable to read virtual hosts: ' . $e->getMessage());
            }
        }

        return array_values(array_unique($allowed));
    }

    /**
     * Checks whether a host is allowed, either by exact match against the
     * allowlist or as a subdomain of an allowed wildcard host (e.g.
     * www.vhost.local matches *.vhost.local). Wildcards mirror the certificate
     * coverage BearSampp generates for virtual hosts.
     *
     * @param string $host The raw host to check.
     * @param array $allowedHosts The allowlist of normalized hosts and wildcards.
     * @return bool True if the host is allowed, false otherwise.
     */
    private static function isHostAllowed($host, array $allowedHosts)
    {
        $host = self::normalizeHost($host);
        if ($host === '') {
            return false;
        }

        if (in_array($host, $allowedHosts, true)) {
            return true;
        }

        foreach ($allowedHosts as $allowedHost) {
            if (strpos($allowedHost, '*.') === 0) {
                $suffix = substr($allowedHost, 1);
                if ($suffix !== '' && substr($host, -strlen($suffix)) === $suffix) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Normalizes a host header/name to a comparable form: lowercased, with any
     * explicit port removed and IPv6 addresses bracketed ([::1]).
     *
     * @param string $host The raw host.
     * @return string The normalized host.
     */
    private static function normalizeHost($host)
    {
        $host = strtolower(trim((string)$host));

        // Strip a trailing port (both host:port and [ipv6]:port forms)
        if (preg_match('/^\[.*\]:\d+$/', $host)) {
            $host = substr($host, 0, strrpos($host, ':'));
        } elseif (preg_match('/^[^:]+:\d+$/', $host)) {
            $host = substr($host, 0, strrpos($host, ':'));
        }

        // Bracket IPv6 loopback so ::1 and [::1] compare equal
        if ($host === '::1') {
            $host = '[::1]';
        }

        return $host;
    }

    /**
     * Checks that an Origin/Referer URL belongs to an allowed host and matches
     * the scheme, host and effective port the request was addressed to.
     *
     * @param string $url The Origin or Referer header value.
     * @param string $httpHost The raw HTTP_HOST header (may include a port).
     * @param string $scheme The request scheme ('http' or 'https').
     * @param array $allowedHosts The allowlist of permitted hosts.
     * @return bool True if the origin is allowed, false otherwise.
     */
    private static function isAllowedOrigin($url, $httpHost, $scheme, array $allowedHosts)
    {
        $parts = parse_url($url);
        if ($parts === false || empty($parts['host'])) {
            Log::warning('CSRF validation failed: Unparseable Origin/Referer header');
            return false;
        }

        // Reject URLs carrying credentials (userinfo). Real browser Origins
        // never contain them, so this is purely defense in depth.
        if (isset($parts['user']) || isset($parts['pass'])) {
            Log::warning('CSRF validation failed: Origin/Referer header must not contain credentials');
            return false;
        }

        $originScheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
        if (!in_array($originScheme, array('http', 'https'), true)) {
            Log::warning('CSRF validation failed: Unsupported Origin/Referer scheme "' . $originScheme . '"');
            return false;
        }

        $originHost = self::normalizeHost($parts['host']);
        if (!self::isHostAllowed($originHost, $allowedHosts)) {
            Log::warning('CSRF validation failed: Origin/Referer host "' . $originHost . '" is not an allowed host');
            return false;
        }

        // The scheme of the origin must match the scheme the request was made over
        if ($originScheme !== $scheme) {
            Log::warning('CSRF validation failed: Origin/Referer scheme "' . $originScheme . '" does not match request scheme "' . $scheme . '"');
            return false;
        }

        // The origin host must match the host the request was addressed to
        $requestHost = self::normalizeHost($httpHost);
        if ($originHost !== $requestHost) {
            Log::warning('CSRF validation failed: Origin/Referer host "' . $originHost . '" does not match request host "' . $requestHost . '"');
            return false;
        }

        // The effective port of the origin must match the request port
        if (self::getOriginPort($parts, $originScheme) !== self::getRequestPort($httpHost, $scheme)) {
            Log::warning('CSRF validation failed: Origin/Referer port mismatch');
            return false;
        }

        return true;
    }

    /**
     * Resolves the effective port of an Origin/Referer URL, falling back to the
     * default port of the scheme when none is explicitly present.
     *
     * @param array $parts The parsed URL components.
     * @param string $scheme The origin scheme ('http' or 'https').
     * @return int The effective port.
     */
    private static function getOriginPort(array $parts, $scheme)
    {
        if (isset($parts['port']) && is_numeric($parts['port'])) {
            return (int)$parts['port'];
        }
        return $scheme === 'https' ? 443 : 80;
    }

    /**
     * Resolves the effective port of the request from the HTTP_HOST header,
     * falling back to the default port of the scheme when none is present.
     *
     * @param string $httpHost The raw HTTP_HOST header (may include a port).
     * @param string $scheme The request scheme ('http' or 'https').
     * @return int The effective port.
     */
    private static function getRequestPort($httpHost, $scheme)
    {
        if (preg_match('/:(\d+)$/', $httpHost, $matches)) {
            return (int)$matches[1];
        }
        return $scheme === 'https' ? 443 : 80;
    }

    /**
     * Gets all HTTP headers in a cross-compatible way.
     * Works with both Apache and FastCGI/CGI environments.
     *
     * @return array Associative array of headers
     */
    private static function getAllHeaders()
    {
        // Use getallheaders() if available (Apache)
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if ($headers !== false) {
                return $headers;
            }
        }

        // Fallback for FastCGI/CGI environments
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            // Extract HTTP headers from $_SERVER
            if (substr($key, 0, 5) === 'HTTP_') {
                // Convert HTTP_X_CSRF_TOKEN to X-Csrf-Token
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$headerName] = $value;
            }
            // Handle CONTENT_TYPE and CONTENT_LENGTH specially
            elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                $headers[$headerName] = $value;
            }
        }

        return $headers;
    }

    /**
     * Removes expired tokens from the session.
     *
     * @return int Number of tokens removed
     */
    private static function cleanupExpiredTokens()
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return 0;
        }

        $removed = 0;
        $currentTime = time();

        foreach ($_SESSION[self::SESSION_KEY] as $token => $timestamp) {
            if ($currentTime - $timestamp > self::TOKEN_EXPIRATION) {
                unset($_SESSION[self::SESSION_KEY][$token]);
                $removed++;
            }
        }

        if ($removed > 0) {
            Log::debug("Cleaned up $removed expired CSRF tokens");
        }

        return $removed;
    }

    /**
     * Regenerates the CSRF token.
     * Useful after sensitive operations or login.
     *
     * @return string The new CSRF token
     */
    public static function regenerateToken()
    {
        self::init();

        // Clear all existing tokens
        $_SESSION[self::SESSION_KEY] = [];

        // Generate new token
        return self::generateToken();
    }

    /**
     * Gets the token as a hidden input field for forms.
     *
     * @return string HTML hidden input field
     */
    public static function getTokenField()
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Gets the token as a meta tag for inclusion in HTML head.
     * Useful for AJAX requests.
     *
     * @return string HTML meta tag
     */
    public static function getTokenMeta()
    {
        $token = self::getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validates request and sends JSON error response if validation fails.
     * This is a convenience method for AJAX endpoints.
     *
     * @param bool $removeAfterValidation Whether to remove the token after successful validation
     * @return void Exits with JSON error if validation fails
     */
    public static function validateOrDie($removeAfterValidation = false)
    {
        if (!self::validateRequest($removeAfterValidation)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'CSRF validation failed',
                'message' => 'Invalid or expired security token. Please refresh the page and try again.'
            ]);
            exit;
        }
    }

    /**
     * Gets statistics about current CSRF tokens.
     * Useful for debugging and monitoring.
     *
     * @return array Statistics about tokens
     */
    public static function getStats()
    {
        self::init();

        $tokens = $_SESSION[self::SESSION_KEY] ?? [];
        $currentTime = time();
        $expired = 0;
        $valid = 0;

        foreach ($tokens as $timestamp) {
            if ($currentTime - $timestamp > self::TOKEN_EXPIRATION) {
                $expired++;
            } else {
                $valid++;
            }
        }

        return [
            'total' => count($tokens),
            'valid' => $valid,
            'expired' => $expired,
            'max_tokens' => self::MAX_TOKENS,
            'expiration_seconds' => self::TOKEN_EXPIRATION
        ];
    }
}

