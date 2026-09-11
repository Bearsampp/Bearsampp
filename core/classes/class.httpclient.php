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
    const PROXY_CONNECT_TIMEOUT = 30;
    const PROXY_LOW_SPEED_LIMIT = 1024;
    const PROXY_LOW_SPEED_TIME = 30;

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

            Log::trace('getHttpHeaders: ' . count($result) . ' header(s)');
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

            Log::trace('getHeaders: ' . count($result) . ' header(s)');
        }

        return $result;
    }

    /**
     * Sends a GET request to the specified URL and returns the trimmed response body.
     *
     * This is the generic non-proxied GET method. For GitHub-hosted URLs, prefer
     * {@see proxyFetch()} which routes through the proxy and never exposes a token
     * to the client.
     *
     * The peer certificate is verified against the bundled CA bundle unless $verify is
     * false (used only for local/self-signed endpoints).
     *
     * @param   string  $url     The URL to send the GET request to.
     * @param   bool    $verify  Whether to verify the peer certificate. Defaults to true.
     *
     * @return string The trimmed response body, or empty string on failure.
     */
    public static function fetchGet($url, $verify = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: ' . APP_GITHUB_USERAGENT . ' (https://github.com/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . ')',
            'Accept: application/json, text/plain, */*',
        ));
        self::applyCurlSslOptions($ch, $verify);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('CURL Error: ' . curl_error($ch));
        }

        return trim((string)$data);
    }

    /**
     * Sends a GET request to the specified URL and returns the response.
     *
     * GitHub-hosted URLs are fetched through the GitHub proxy so no token is ever
     * sent by the client. Non-GitHub URLs are fetched directly over verified TLS.
     *
     * @param   string  $url     The URL to send the GET request to.
     * @param   bool    $verify  Whether to verify the peer certificate. Defaults to true.
     *
     * @return string The trimmed response data from the URL.
     */
    public static function getApiJson($url, $verify = true)
    {
        Log::trace('[VCHK-3] getApiJson() sending GET request to: ' . $url);

        if (self::isGithubHost($url)) {
            $result = self::proxyFetch($url, 'GET', $verify);
            if ($result === false || (int)$result['status'] !== 200) {
                Log::error('GitHub request via proxy failed for: ' . $url);
                Log::trace('[VCHK-3] getApiJson() EXIT - proxy request failed');

                return '';
            }

            Log::trace('[VCHK-3] getApiJson() response length: ' . strlen((string)$result['body']));

            return trim($result['body']);
        }

        $data = self::fetchGet($url, $verify);

        Log::trace('[VCHK-3] getApiJson() response length: ' . strlen($data));

        return $data;
    }

    /**
     * Determines whether a URL is hosted on GitHub.
     *
     * Used to decide which requests must be routed through the GitHub proxy.
     * Only these hosts are ever sent to the proxy; everything else (e.g. the
     * QuickPick license API or mirror servers) is fetched directly.
     *
     * @param   string  $url  The URL to check.
     *
     * @return bool True when the URL host is a GitHub endpoint.
     */
    public static function isGithubHost($url)
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');

        return in_array($host, array(
            'github.com',
            'api.github.com',
            'raw.githubusercontent.com',
            'objects.githubusercontent.com',
            'codeload.github.com',
            'gist.github.com',
        ), true);
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
     * Returns a log-safe representation of a URL, keeping only scheme + host + path.
     *
     * Query strings, fragments, and any embedded credentials are stripped so
     * sensitive tokens never appear in log files.
     *
     * @param   string  $url  The URL to sanitise.
     * @return  string        The safe representation (e.g. "https://github.com/foo/bar").
     */
    private static function safeUrlForLog($url)
    {
        $url = (string)$url;
        $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
        $host   = parse_url($url, PHP_URL_HOST)   ?: '';
        $path   = parse_url($url, PHP_URL_PATH)   ?: '';

        if ($host === '') {
            return '(invalid-url)';
        }

        return $scheme . '://' . $host . $path;
    }

    /**
     * Builds a stream context that verifies the peer certificate against the bundled CA bundle.
     *
     * GitHub-hosted content is fetched through the GitHub proxy (APP_GITHUB_PROXY_URL)
     * instead of directly, so no token is ever attached to fopen-based requests here.
     * The $url parameter is kept for API compatibility.
     *
     * @param   bool         $verify  Whether to verify the peer certificate. Defaults to true.
     * @param   string|null  $url     Optional target URL (unused; kept for compatibility).
     *
     * @return resource The stream context.
     */
    public static function getSslStreamContext($verify = true, $url = null)
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
     * Fetches a resource through the GitHub proxy.
     *
     * Sends a POST to APP_GITHUB_PROXY_URL carrying the target URL and method as
     * a JSON body, authenticated with the shared public proxy key. The GitHub PAT
     * never leaves the proxy server, so the client holds no token at all.
     *
     * The proxy key is never logged.
     *
     * @param   string  $url     The target GitHub URL to retrieve.
     * @param   string  $method  The upstream method: GET, HEAD or POST. Defaults to GET.
     * @param   bool    $verify  Whether to verify the peer certificate. Defaults to true.
     *
     * @return array|false An array with 'status', 'headers' (associative) and 'body',
     *                     or false when the proxy request itself failed.
     *                     Header name keys are normalized to lowercase; the first
     *                     occurrence of a duplicate header name wins.
     */
    public static function proxyFetch($url, $method = 'GET', $verify = true)
    {
        if (!self::isGithubHost($url)) {
            Log::error('[PROXY] proxyFetch() blocked non-GitHub URL: ' . self::safeUrlForLog($url));
            return false;
        }

        $method = strtoupper($method);
        Log::trace('[PROXY] proxyFetch() ' . $method . ' target: ' . self::safeUrlForLog($url));

        $payload = array(
            'url'    => (string)$url,
            'method' => $method,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, APP_GITHUB_PROXY_URL);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-Bearsampp-Key: ' . APP_GITHUB_PROXY_KEY,
            'User-Agent: ' . APP_GITHUB_USERAGENT . ' (https://github.com/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . ')',
        ));
        self::applyCurlSslOptions($ch, $verify);

        $response = curl_exec($ch);
        if ($response === false) {
            Log::error('Proxy request failed: ' . curl_error($ch));
            Log::trace('[PROXY] proxyFetch() FAILED - target: ' . self::safeUrlForLog($url) . ' - ' . curl_error($ch));

            return false;
        }

        $status     = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $response = (string)$response;

        $headers = array();
        $body    = '';

        if ($headerSize > 0 && strlen($response) >= $headerSize) {
            // CURLINFO_HEADER_SIZE includes every received header block, so the
            // body is exactly the bytes after it - even when the response contains
            // interim header sections (e.g. "100 Continue" or redirects) that make
            // a naive "first \r\n\r\n" split corrupt.
            $headerData = substr($response, 0, $headerSize);
            $body       = substr($response, $headerSize);

            // Keep only the LAST header block (the final status line and headers).
            $blocks = preg_split('/\r?\n\r?\n/', trim($headerData));
            $headerBlock = end($blocks);

            foreach (explode("\r\n", $headerBlock) as $line) {
                if (strpos($line, ':') === false) {
                    continue;
                }
                list($name, $value) = explode(':', $line, 2);
                // HTTP header names are case-insensitive, so normalize to a
                // consistent lowercase key. The first occurrence of a duplicate
                // name still wins (matching the previous behaviour).
                $name  = strtolower(trim($name));
                $value = trim($value);
                if ($name !== '' && !isset($headers[$name])) {
                    $headers[$name] = $value;
                }
            }
        } else {
            // Defensive fallback: no usable header size reported. Keep the whole
            // body, but still surface any headers found after the first separator.
            $separatorPos = strpos($response, "\r\n\r\n");
            $body = ($separatorPos === false) ? $response : substr($response, $separatorPos + 4);
            if ($separatorPos !== false) {
                foreach (explode("\r\n", substr($response, 0, $separatorPos)) as $line) {
                    if (strpos($line, ':') === false) {
                        continue;
                    }
                    list($name, $value) = explode(':', $line, 2);
                    $name  = strtolower(trim($name));
                    $value = trim($value);
                    if ($name !== '' && !isset($headers[$name])) {
                        $headers[$name] = $value;
                    }
                }
            }
        }

        Log::trace('[PROXY] proxyFetch() END - status ' . $status . ', body length: ' . strlen($body));

        return array('status' => $status, 'headers' => $headers, 'body' => $body);
    }

    /**
     * Downloads a resource through the GitHub proxy, streaming the body to a file.
     *
     * Used for large payloads (module archives) fetched from GitHub hosts. The
     * response body is streamed in chunks to the given file so it is never held
     * in memory, and an optional progress bar emits one JSON progress line per
     * 8KB chunk, matching the behaviour of the legacy stream download.
     *
     * @param   string  $url          The target GitHub URL to download.
     * @param   string  $filePath     Local path to write the body to.
     * @param   bool    $progressBar  Whether to emit progress lines. Defaults to false.
     * @param   bool    $verify       Whether to verify the peer certificate. Defaults to true.
     *                                Pass false only for local/self-signed endpoints.
     *
     * @return bool True when the download completed with a 2xx status, false otherwise.
     */
    public static function proxyDownload($url, $filePath, $progressBar = false, $verify = true)
    {
        if (!self::isGithubHost($url)) {
            Log::error('[PROXY] proxyDownload() blocked non-GitHub URL: ' . self::safeUrlForLog($url));
            return false;
        }

        Log::trace('[PROXY] proxyDownload() START target: ' . self::safeUrlForLog($url) . ' -> ' . $filePath);

        $outputStream = @fopen($filePath, 'wb');
        if ($outputStream === false) {
            Log::trace('[PROXY] proxyDownload() FAILED - cannot open output file: ' . $filePath);

            return false;
        }

        $status     = 0;
        $chunksRead = 0;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, APP_GITHUB_PROXY_URL);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            'url'    => (string)$url,
            'method' => 'GET',
        )));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-Bearsampp-Key: ' . APP_GITHUB_PROXY_KEY,
            'User-Agent: ' . APP_GITHUB_USERAGENT . ' (https://github.com/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . ')',
        ));
        self::applyCurlSslOptions($ch, $verify);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::PROXY_CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, self::PROXY_LOW_SPEED_LIMIT);
        curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, self::PROXY_LOW_SPEED_TIME);

        // Capture the status line from the response headers without writing them to the file.
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $line) use (&$status) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $match) === 1) {
                $status = (int)$match[1];
            }

            return strlen($line);
        });

        // Stream the body to the output file in chunks, mirroring the legacy
        // 8KB chunk loop and progress reporting.
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$outputStream, &$chunksRead, $progressBar) {
            if ($outputStream === false) {
                return -1; // abort the transfer
            }

            $len = fwrite($outputStream, $data);
            if ($len === false) {
                return -1;
            }

            $chunksRead++;
            if ($progressBar) {
                echo json_encode(array('progress' => $chunksRead)) . PHP_EOL;

                if (ob_get_length() !== false) {
                    ob_flush();
                }
                flush();
            }

            return $len;
        });

        $success = (curl_exec($ch) !== false);
        $error   = curl_error($ch);

        // CURLINFO_RESPONSE_CODE is the authoritative status: it works even when
        // the raw header lines cannot be parsed (e.g. HTTP/2 ":status" pseudo
        // headers, interim 1xx blocks, or a proxy omitting a status line), which
        // the header callback above may silently miss. The callback capture is
        // only kept as a fallback for the rare case cURL reports no code.
        $curlStatus = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($curlStatus > 0) {
            $status = $curlStatus;
        }

        fclose($outputStream);

        if (!$success) {
            Log::error('Proxy download failed: ' . $error);
            Log::trace('[PROXY] proxyDownload() FAILED - status ' . $status . ', target: ' . self::safeUrlForLog($url) . ' - ' . $error);
        } else {
            Log::trace('[PROXY] proxyDownload() END - status ' . $status . ', chunks ' . $chunksRead . ', target: ' . self::safeUrlForLog($url));
        }

        return $success && $status >= 200 && $status < 300;
    }

    /**
     * Downloads a file from a given URL and saves it to a specified file path.
     *
     * GitHub-hosted archives are downloaded through the GitHub proxy. Non-GitHub
     * URLs are streamed over a verified TLS connection in 8KB chunks to avoid
     * loading the whole file into memory, emitting one JSON progress line per
     * chunk when $progressBar is enabled.
     *
     * @param   string  $url          The URL from which to fetch the file content.
     * @param   string  $filePath     The path where the file content should be saved.
     * @param   bool    $progressBar  Optional. Whether to display a progress bar during the download process. Default is false.
     * @param   bool    $verify       Whether to verify the peer certificate. Defaults to true.
     *                                Pass false only for local/self-signed endpoints.
     *
     * @return array Returns the file path if successful, or an array with an error message if an error occurs.
     */
    public static function downloadFile(string $url, string $filePath, $progressBar = false, $verify = true)
    {
        // GitHub-hosted module archives are downloaded through the GitHub proxy so
        // the client never holds or transmits a GitHub token. The body is streamed
        // in 8KB chunks to avoid loading the whole archive into memory.
        if (self::isGithubHost($url)) {
            Log::trace('downloadFile() downloading via GitHub proxy: ' . $url);
            $downloaded = self::proxyDownload($url, $filePath, $progressBar, $verify);
            if (!$downloaded) {
                Log::error('Error fetching content from URL: ' . $url);

                return ['error' => 'Error fetching module'];
            }

            return ['success' => true];
        }

        Log::trace('downloadFile() downloading directly (non-GitHub): ' . $url);

        // Open the URL for reading. The verified SSL context makes sure the file is
        // fetched over a properly authenticated HTTPS connection.
        $inputStream = @fopen( $url, 'rb', false, self::getSslStreamContext(true, $url) );
        if ( $inputStream === false ) {
            Log::error( 'Error fetching content from URL: ' . $url );

            return ['error' => 'Error fetching module'];
        }

        // Open the file for writing
        $outputStream = @fopen( $filePath, 'wb' );
        if ( $outputStream === false ) {
            Log::error( 'Error opening file for writing: ' . $filePath );
            fclose( $inputStream );

            return ['error' => 'Error saving module'];
        }

        // Read and write in chunks to avoid memory overload
        $bufferSize = 8096; // 8KB
        $chunksRead = 0;

        while ( !feof( $inputStream ) ) {
            $buffer = fread( $inputStream, $bufferSize );
            fwrite( $outputStream, $buffer );
            $chunksRead++;

            // Send progress update
            if ( $progressBar ) {
                $progress = $chunksRead;
                echo json_encode( ['progress' => $progress] ) . PHP_EOL;

                // Check if output buffering is active before calling ob_flush()
                if ( ob_get_length() !== false ) {
                    ob_flush();
                }
                flush();
            }
        }

        fclose( $inputStream );
        fclose( $outputStream );

        return ['success' => true];
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

