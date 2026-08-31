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
 * Class HttpClient
 *
 * This class provides methods for making HTTP requests and retrieving HTTP headers.
 * It supports both cURL and fopen for fetching headers and handles API JSON requests.
 *
 * @package Bearsampp
 */
class HttpClient
{
    /**
     * Retrieves HTTP headers from a given URL using either cURL or fopen, depending on availability.
     *
     * @param   string  $pingUrl  The URL to ping for headers.
     * @param   bool    $verify   Whether to verify the peer certificate. Defaults to true.
     *                            Pass false only for local/self-signed endpoints (e.g. the
     *                            app's own Apache SSL on localhost).
     *
     * @return array An array of HTTP headers.
     */
    public static function getHttpHeaders($pingUrl, $verify = true)
    {
        if (function_exists('curl_version')) {
            $result = self::getCurlHttpHeaders($pingUrl, $verify);
        } else {
            $result = self::getFopenHttpHeaders($pingUrl, $verify);
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

            Log::debug('getHttpHeaders:');
            foreach ($result as $header) {
                Log::debug('-> ' . $header);
            }
        }

        return $result;
    }

    /**
     * Retrieves HTTP headers from a given URL using the fopen function.
     *
     * The stream context verifies the peer certificate against the bundled CA bundle
     * unless $verify is false (used only for local/self-signed endpoints).
     *
     * @param   string  $url     The URL from which to fetch the headers.
     * @param   bool    $verify  Whether to verify the peer certificate. Defaults to true.
     *
     * @return array An array of headers if successful, otherwise an empty array.
     */
    public static function getFopenHttpHeaders($url, $verify = true)
    {
        $result = array();

        $fp = @fopen($url, 'r', false, self::getSslStreamContext($verify));
        if ($fp) {
            $meta   = stream_get_meta_data($fp);
            $result = isset($meta['wrapper_data']) ? $meta['wrapper_data'] : $result;
            fclose($fp);
        }

        return $result;
    }

    /**
     * Retrieves HTTP headers from a given URL using cURL.
     *
     * The peer certificate is verified against the bundled CA bundle unless $verify is
     * false (used only for local/self-signed endpoints).
     *
     * @param   string  $url     The URL from which to fetch the headers.
     * @param   bool    $verify  Whether to verify the peer certificate. Defaults to true.
     *
     * @return array An array of headers if successful, otherwise an empty array.
     */
    public static function getCurlHttpHeaders($url, $verify = true)
    {
        $result = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        self::applyCurlSslOptions($ch, $verify);

        $response = @curl_exec($ch);
        if (empty($response)) {
            return $result;
        }

        // Cap the logged response to a small prefix so headers/body never saturate
        // the log and any transient sensitive data in the body is not written verbatim.
        Log::trace('getCurlHttpHeaders: ' . substr($response, 0, 512));
        $responseHeaders = explode("\r\n\r\n", $response, 2);
        if (!isset($responseHeaders[0]) || empty($responseHeaders[0])) {
            return $result;
        }

        return explode("\n", $responseHeaders[0]);
    }

    /**
     * Constructs a complete website URL with optional path, fragment, and UTM source parameters.
     *
     * @param   string  $path       Optional path to append to the base URL.
     * @param   string  $fragment   Optional fragment to append to the URL.
     * @param   bool    $utmSource  Whether to include UTM source parameters. Defaults to true.
     *
     * @return string The constructed URL.
     */
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
        return self::getWebsiteUrl($path, $fragment, false);
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
        return self::getWebsiteUrl('doc/changelog', null, $utmSource);
    }

    /**
     * Generates various GitHub URLs based on the specified type.
     *
     * @param string $type The type of URL ('user', 'repo', 'raw'). Defaults to 'user'.
     * @param string $user The GitHub username. Defaults to 'Bearsampp'.
     * @param string|null $repo The repository name (required for 'repo' and 'raw' types).
     * @param string|null $branch The branch name (required for 'raw' type).
     * @param string|null $path The file path (required for 'raw' type).
     * @return string|false The generated URL or false on invalid input.
     */
    public static function getGithubUrl($type = 'user', $user = APP_GITHUB_USER, $repo = null, $branch = null, $path = null) {
        if (empty($user) || !is_string($user)) {
            return false;
        }

        // Encode as URL path segment (not query encoding)
        $user = rawurlencode($user);

        switch ($type) {
            case 'user':
                return "https://github.com/{$user}";

            case 'repo':
                if (empty($repo) || !is_string($repo)) {
                    return false;
                }
                $repo = rawurlencode($repo);
                return "https://github.com/{$user}/{$repo}";

            case 'issues':
                if (empty($repo) || !is_string($repo)) {
                    return false;
                }
                $repo = rawurlencode($repo);
                return "https://github.com/{$user}/{$repo}/issues";

            case 'raw':
                if (empty($repo) || empty($branch) || empty($path) || !is_string($repo) || !is_string($branch) || !is_string($path)) {
                    return false;
                }
                $repo = rawurlencode($repo);
                $branch = rawurlencode($branch);

                $path = ltrim($path, '/');
                $segments = array_map('rawurlencode', explode('/', $path));
                $pathEncoded = implode('/', $segments);

                return "https://raw.githubusercontent.com/{$user}/{$repo}/{$branch}/{$pathEncoded}";

            default:
                return false;
        }
    }

    /**
     * Gets the GitHub user URL for Bearsampp.
     *
     * @return string The GitHub user URL.
     */
    public static function getGithubUserUrl()
    {
        return self::getGithubUrl('user', APP_GITHUB_USER);
    }

    /**
     * Retrieves the initial response line from a specified host and port using a socket connection.
     *
     * This is a local connectivity probe (used to detect which local service owns a port).
     * Certificate verification is intentionally disabled here: it only reads the first
     * response line from localhost/self-signed services and never processes untrusted
     * content.
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
        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            )
        ));

        $fp = @stream_socket_client(($ssl ? 'ssl://' : '') . $host . ':' . $port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
        if ($fp) {
            $out    = fgets($fp);
            $result = explode(PHP_EOL, $out);
            @fclose($fp);
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

            Log::debug('getHeaders:');
            foreach ($result as $header) {
                Log::debug('-> ' . $header);
            }
        }

        return $result;
    }

    /**
     * Sends a GET request to the specified URL and returns the response.
     *
     * The peer certificate is verified against the bundled CA bundle unless $verify is
     * false (used only for local/self-signed endpoints).
     *
     * @param   string  $url     The URL to send the GET request to.
     * @param   bool    $verify  Whether to verify the peer certificate. Defaults to true.
     *
     * @return string The trimmed response data from the URL.
     */
    public static function getApiJson($url, $verify = true)
    {
        $header = self::setupCurlHeaderWithToken();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false); // Set to false to avoid polluting logs unless needed
        curl_setopt($ch, CURLOPT_URL, $url);
        self::applyCurlSslOptions($ch, $verify);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('CURL Error (' . curl_errno($ch) . '): ' . curl_error($ch) . ' (URL: ' . self::redactUrl($url) . ')');
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            Log::error('HTTP Error ' . $httpCode . ' for URL: ' . self::redactUrl($url));
        }

        // curl_close() is deprecated in PHP 8.5+ as it has no effect since PHP 8.0
        // The resource is automatically closed when it goes out of scope
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }

        return $data === false ? '' : trim($data);
    }

    /**
     * Fetches the latest version information from a given url.
     *
     * @param   string  $url  The URL to fetch version information from.
     *
     * @return array|null Returns an array with 'version' and 'url' if successful, null otherwise.
     */
    public static function getLatestVersion($url)
    {
        $result = self::getApiJson($url);
        if (empty($result)) {
            Log::error('Cannot retrieve latest github info: empty result or error for URL: ' . $url);

            return null;
        }

        $resultArray = json_decode($result, true);
        if ($resultArray === null) {
            Log::error('Failed to decode JSON response from: ' . $url . '. Response snippet: ' . substr($result, 0, 100));
            return null;
        }

        if (isset($resultArray['tag_name']) && isset($resultArray['assets'][0]['browser_download_url'])) {
            $tagName     = $resultArray['tag_name'];
            $downloadUrl = $resultArray['assets'][0]['browser_download_url'];
            $name        = $resultArray['name'];
            Log::debug('Latest version tag name: ' . $tagName);
            Log::debug('Download URL: ' . $downloadUrl);
            Log::debug('Name: ' . $name);

            return ['version' => $tagName, 'html_url' => $downloadUrl, 'name' => $name];
        } else {
            Log::error('Tag name, download URL, or name not found in the response: ' . $result);

            return null;
        }
    }

    /**
     * Sets up cURL headers with token for API requests.
     *
     * @return array The array of cURL headers.
     */
    public static function setupCurlHeaderWithToken()
    {
        // Return headers with User-Agent, which is required by GitHub API
        return array(
            'User-Agent: ' . APP_GITHUB_USERAGENT . ' (https://github.com/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . ')',
            'Accept: application/vnd.github.v3+json'
        );
    }

    /**
     * Returns the path to the CA bundle shipped with the PHP installation.
     *
     * @return string The absolute path to the cacert.pem file.
     */
    public static function getCacertPath()
    {
        return Path::getPhpPath() . '/extras/ssl/cacert.pem';
    }

    /**
     * Redacts sensitive query-string parameters from a URL before it is logged.
     *
     * URLs passed to this class can carry credentials such as an API key or a
     * license/download ID (e.g. `?api_key=...&download_id=...`). Writing those
     * verbatim into log files would leak them, so any matching parameter's value
     * is masked.
     *
     * @param   string  $url  The URL to redact.
     * @return  string        The URL with sensitive parameter values replaced by '******'.
     */
    public static function redactUrl($url)
    {
        $url = (string)$url;
        if ($url === '' || strpos($url, '?') === false) {
            return $url;
        }

        $sensitiveParams = ['api_key', 'download_id', 'token', 'access_token', 'auth', 'password', 'passwd', 'key', 'secret'];

        [$base, $query] = explode('?', $url, 2);
        parse_str($query, $params);

        foreach ($params as $name => &$value) {
            foreach ($sensitiveParams as $sensitive) {
                if (stripos($name, $sensitive) !== false) {
                    $value = '******';
                    break;
                }
            }
        }

        return $base . '?' . http_build_query($params);
    }

    /**
     * Builds a stream context that verifies the peer certificate against the bundled CA bundle.
     *
     * @param   bool  $verify  Whether to verify the peer certificate. Defaults to true.
     *
     * @return resource The stream context.
     */
    public static function getSslStreamContext($verify = true)
    {
        $ssl = array(
            'verify_peer'       => $verify,
            'verify_peer_name'  => $verify,
            'allow_self_signed' => !$verify,
        );

        if ($verify) {
            $cacert = self::getCacertPath();
            if (is_file($cacert)) {
                $ssl['cafile'] = $cacert;
            }
        }

        return stream_context_create(array('ssl' => $ssl));
    }

    /**
     * Applies certificate verification options to a cURL handle.
     *
     * When $verify is true the peer certificate is checked against the bundled CA bundle.
     * Pass false only for local/self-signed endpoints (e.g. the app's own Apache SSL).
     *
     * @param   resource  $ch      The cURL handle.
     * @param   bool      $verify  Whether to verify the peer certificate. Defaults to true.
     *
     * @return void
     */
    public static function applyCurlSslOptions($ch, $verify = true)
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verify ? 2 : 0);

        if ($verify) {
            $cacert = self::getCacertPath();
            if (is_file($cacert)) {
                curl_setopt($ch, CURLOPT_CAINFO, $cacert);
            }
        }
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

        $data = get_headers($url, true, self::getSslStreamContext());
        if (isset($data['Content-Length'])) {
            $size = intval($data['Content-Length']);
        }

        return $humanFileSize ? Util::humanFileSize($size) : $size;
    }

    /**
     * Checks the current state of the internet connection.
     *
     * Opens a verified TLS connection to the project's own website over port 443.
     * This keeps the probe encrypted (no plaintext traffic to a third party) and
     * independent of external hosts such as Google.
     *
     * @return bool True if the internet connection is active, false otherwise.
     */
    public static function checkInternetState()
    {
        $host = str_replace(['https://', 'http://'], '', APP_WEBSITE);
        $host = rtrim(parse_url($host, PHP_URL_HOST) ?: $host, '/');

        // Verified TLS context: we only need to establish a clean TLS handshake, not
        // fetch any content, so a successful connection is enough to confirm internet.
        $connected = @stream_socket_client(
            'tls://' . $host . ':443',
            $errno,
            $errstr,
            5,
            STREAM_CLIENT_CONNECT,
            self::getSslStreamContext()
        );

        if ($connected) {
            fclose($connected);
            return true;
        }

        return false;
    }
}

