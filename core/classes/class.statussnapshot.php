<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class StatusSnapshot
 *
 * Builds a single point-in-time picture of the whole WAMPP stack in the canonical
 * vocabulary defined by ServiceStatus.
 *
 * Design constraints this class exists to respect:
 *
 *  - The homepage polls every couple of seconds, and each poll is a fresh PHP
 *    process, so nothing survives in memory between requests. A full collection
 *    costs roughly 200ms: about 12ms for the SCM query, and most of the rest for
 *    the single unfiltered Win32_Process walk that ProcessFootprint needs. That
 *    is why the SCM is queried once for all services
 *    (Win32Native::getServicesByNames) rather than once per service. The web
 *    tier's only reuse is the short on-disk cache in captureForWeb().
 *
 *  - Win32Service::status() and Nssm::status() both retry with sleeps and a 10s
 *    cap, which would stall a poll. This class never calls them; it reads SCM
 *    state through a single non-blocking WMI query instead.
 *
 *  - Status and health are reported as separate dimensions. Nothing here
 *    collapses them into one value, so a service that is running but
 *    unreachable stays visible as exactly that.
 *
 *  - The homepage is served by the user's PHP runtime, which has no COM, so
 *    capture() cannot run there. captureForWeb() bridges that gap by delegating
 *    to the internal engine; see its docblock for the details.
 */
class StatusSnapshot
{
    /** Connect timeout for the TCP liveness probe, in seconds. */
    const PROBE_TIMEOUT = 0.25;

    /**
     * Collector script executed by the internal engine, relative to the core
     * directory. It is deliberately outside the Apache alias and the
     * DocumentRoot so that it cannot be requested over HTTP.
     */
    const COLLECTOR_SCRIPT = '/status-snapshot.php';

    /**
     * Short-lived cache for the bridged snapshot, relative to the core
     * directory. Lives in core/tmp so it is ignored by git and wiped with the
     * other scratch data.
     */
    const WEBCACHE_PATH = '/tmp/stack-status-cache.json';

    /** How old a cached bridged snapshot may be, in seconds. */
    const WEBCACHE_TTL = 2;

    /**
     * Returns a snapshot from a runtime that may not have COM.
     *
     * Apache serves the homepage with the user's PHP (bin/php/php<version>),
     * which loads neither com_dotnet nor the win32ps helper extension, so every
     * WMI call in this class is unreachable from the web tier. That is also why
     * the pre-existing homepage status code probes TCP ports instead of asking
     * the SCM.
     *
     * When COM is present the snapshot is taken directly. When it is not, the
     * collection is delegated to the internal engine (core/libs/php), which does
     * have COM, and its JSON is decoded here. This keeps every WMI call on the
     * engine side of the runtime split and leaves the user's runtime, and the
     * user's terminal, untouched.
     *
     * Results are cached for a couple of seconds so that two browser tabs, or a
     * page load immediately followed by its first poll, do not each pay for a
     * full collection. The cache is safe to share because a snapshot is a
     * read-only observation of the machine.
     *
     * @param   Bins  $bearsamppBins  The bins registry.
     *
     * @return array A snapshot array, or one carrying an 'error' key when the
     *               collector could not be run or produced unusable output.
     */
    public static function captureForWeb(Bins $bearsamppBins): array
    {
        if (class_exists('COM')) {
            return self::capture($bearsamppBins);
        }

        $cachePath = Path::getCorePath() . self::WEBCACHE_PATH;
        $cached    = self::readWebCache($cachePath, self::WEBCACHE_TTL);

        if ($cached !== null) {
            return $cached;
        }

        $snapshot = self::decodeCollectorOutput(self::runCollector());

        if (!isset($snapshot['error'])) {
            self::writeWebCache($cachePath, $snapshot);
        }

        return $snapshot;
    }

    /**
     * Runs the collector with the internal engine and returns its raw stdout.
     *
     * stderr is discarded so that a PHP notice or warning cannot be mistaken for
     * payload, and so that a failure stays quiet instead of leaking paths into
     * the HTTP response.
     *
     * @return string The collector's stdout, or an empty string on failure.
     */
    private static function runCollector(): string
    {
        $engine = Path::getPhpPath() . '/php.exe';
        $script = Path::getCorePath() . self::COLLECTOR_SCRIPT;

        if (!is_file($engine) || !is_file($script)) {
            Log::error('Status snapshot collector is missing: ' . $engine . ' or ' . $script);

            return '';
        }

        $command = escapeshellarg($engine) . ' ' . escapeshellarg($script) . ' 2>NUL';

        try {
            $output = shell_exec($command);
        } catch (Throwable $e) {
            Log::error('Status snapshot collector failed to start: ' . $e->getMessage());

            return '';
        }

        return is_string($output) ? trim($output) : '';
    }

    /**
     * Decodes collector output, normalising any failure into a snapshot-shaped
     * array so that callers never have to special-case a missing 'entries' key.
     *
     * @param   string  $output  Raw collector stdout.
     *
     * @return array A snapshot array, carrying 'error' when decoding failed.
     */
    private static function decodeCollectorOutput(string $output): array
    {
        $decoded = $output === '' ? null : json_decode($output, true);

        if (!is_array($decoded) || !isset($decoded['entries']) || !is_array($decoded['entries'])) {
            Log::error('Status snapshot collector returned unusable output');

            return [
                'error'     => 'The status collector returned an unusable response.',
                'status'    => ServiceStatus::STATUS_UNKNOWN,
                'expected'  => 0,
                'running'   => 0,
                'entries'   => [],
                'resources' => [
                    'stack'        => ProcessFootprint::emptyMetrics(),
                    // Shaped like a real reading rather than left empty. The
                    // collector-failure path has no figures to report, and a bare
                    // [] here would leave every consumer that reads totalBytes
                    // indexing a key that was never there. Zeros read as "no
                    // measurement", which is what this actually is.
                    'host'         => ['totalBytes' => 0, 'freeBytes' => 0],
                    'disk'         => [],
                    'cores'        => 0,
                    'serviceCount' => 0,
                ],
            ];
        }

        return $decoded;
    }

    /**
     * Reads a cached bridged snapshot, provided it is recent enough to be useful.
     *
     * @param   string  $path  Absolute cache file path.
     * @param   int     $ttl   Maximum acceptable age, in seconds.
     *
     * @return array|null The cached snapshot, or null when absent, unreadable or
     *                    stale.
     */
    private static function readWebCache(string $path, int $ttl): ?array
    {
        if ($ttl <= 0 || !is_file($path)) {
            return null;
        }

        $age = time() - (int) @filemtime($path);

        if ($age < 0 || $age > $ttl) {
            return null;
        }

        $raw  = @file_get_contents($path);
        $data = $raw === false ? null : json_decode($raw, true);

        if (!is_array($data) || !isset($data['entries']) || isset($data['error'])) {
            return null;
        }

        return $data;
    }

    /**
     * Stores a bridged snapshot for reuse by near-simultaneous requests.
     *
     * A failed write is not an error worth reporting: the only consequence is
     * that the next request collects again.
     *
     * @param   string  $path      Absolute cache file path.
     * @param   array   $snapshot  The snapshot to store.
     *
     * @return void
     */
    private static function writeWebCache(string $path, array $snapshot): void
    {
        $dir = dirname($path);

        if (!is_dir($dir)) {
            return;
        }

        @file_put_contents($path, json_encode($snapshot), LOCK_EX);
    }

    /**
     * Captures the current state of every bin in the stack.
     *
     * @param   Bins  $bearsamppBins  The bins registry.
     *
     * @return array A snapshot array. See build() for the shape.
     */
    public static function capture(Bins $bearsamppBins): array
    {
        $snapshot = self::build($bearsamppBins, self::collectScmState($bearsamppBins));

        return self::attachFootprint($snapshot);
    }

    /**
     * Adds per-service resource usage to an already built snapshot.
     *
     * Kept apart from build() so that the status and health logic stays testable
     * against a hand-written SCM map, with no live process table involved.
     *
     * @param   array  $snapshot  A snapshot from build().
     *
     * @return array The snapshot, with per-entry 'footprint' and a top level
     *               'resources' block.
     */
    private static function attachFootprint(array $snapshot): array
    {
        $roots = [];

        foreach ($snapshot['entries'] as $index => $entry) {
            $serviceName = $entry['serviceName'] ?? null;
            $pid         = (int) ($entry['pid'] ?? 0);

            if ($serviceName !== null && $pid > 0) {
                $names = ServiceHelper::getProcessNamesForService($serviceName);

                if (!empty($names)) {
                    $roots[$serviceName] = ['pid' => $pid, 'names' => $names];
                }
            }

            // Present but empty for everything else, so consumers never have to
            // test for the key's existence.
            $snapshot['entries'][$index]['footprint'] = null;
        }

        $footprint = ProcessFootprint::capture($roots);

        foreach ($snapshot['entries'] as $index => $entry) {
            $serviceName = $entry['serviceName'] ?? null;

            if ($serviceName !== null && isset($footprint['services'][$serviceName])) {
                $snapshot['entries'][$index]['footprint'] = $footprint['services'][$serviceName];
            }
        }

        $snapshot['resources'] = [
            'stack'   => $footprint['total'],
            'host'    => $footprint['host'],
            'disk'    => $footprint['disk'] ?? [],
            'cores'   => $footprint['cores'],
            'serviceCount' => count($footprint['services']),
        ];

        return $snapshot;
    }

    /**
     * Queries the SCM once for the state and PID of every enabled service.
     *
     * StartMode is deliberately not requested. It is the one Win32_Service
     * property that is expensive here, costing about 166ms on its own against
     * roughly 12ms for State and ProcessId together, and nothing in this class
     * reads it.
     *
     * @param   Bins  $bearsamppBins  The bins registry.
     *
     * @return array|null Service name => [State, ProcessId], or null when the
     *                    query failed. The failure is passed through rather than
     *                    flattened into an empty map, because build() reports a
     *                    service that is missing from a successful query as not
     *                    installed, and that reading would be wrong for a query
     *                    that never ran. Null is not turned into an empty array
     *                    here, so no intermediate step can lose the distinction.
     */
    private static function collectScmState(Bins $bearsamppBins): ?array
    {
        $names = array_keys($bearsamppBins->getServices());

        if (empty($names)) {
            return [];
        }

        return Win32Native::getServicesByNames($names, ['Name', 'State', 'ProcessId']);
    }

    /**
     * Assembles the snapshot from bin configuration and SCM state.
     *
     * Split out from capture() so the assembly logic stays testable without a
     * live SCM.
     *
     * @param   Bins       $bearsamppBins  The bins registry.
     * @param   array|null $scm            Service name => SCM property map, or
     *                                     null when the query failed.
     *
     * @return array {
     *     @type string $status     Aggregate STATUS_* constant.
     *     @type int    $expected   Number of expected bins.
     *     @type int    $running    Number of expected bins that are up.
     *     @type array  $entries    Per-bin entries, ordered as Bins::getAll().
     * }
     */
    public static function build(Bins $bearsamppBins, ?array $scm): array
    {
        $serviceBins = self::mapServiceBins($bearsamppBins);
        $entries     = [];
        $statuses    = [];
        $expected    = 0;
        $running     = 0;

        foreach ($bearsamppBins->getAll() as $bin) {
            $name      = $bin->getName();
            $enabled   = $bin->isEnable();
            $installed = $bin->isInstalled();

            $entry = [
                'name'    => $name,
                'id'      => $bin->getId(),
                'version' => $bin->getVersion(),
                'enabled' => $enabled,
                'installed' => $installed,
                'isService' => isset($serviceBins[$name]),
                'serviceName' => null,
                'status'  => ServiceStatus::STATUS_UNKNOWN,
                'health'  => ServiceStatus::HEALTH_UNKNOWN,
                'pid'     => null,
                'ports'   => [],
                'detail'  => '',
            ];

            if (!$installed) {
                // Absent from disk: the user has to install it. Deliberately
                // distinct from "disabled", which needs no action.
                $entry['status'] = ServiceStatus::STATUS_NOT_INSTALLED;
                $entry['detail'] = 'Not installed';
                $entries[]       = $entry;
                $statuses[]      = $entry['status'];

                continue;
            }

            if (!$enabled) {
                $entry['status'] = ServiceStatus::STATUS_DISABLED;
                $entry['detail'] = 'Installed but disabled';
                $entries[]       = $entry;

                continue;
            }

            $expected++;

            if (!$entry['isService']) {
                // PHP and Node.js are invoked on demand; there is no lifecycle
                // to report, so being installed is all that matters.
                $entry['status'] = ServiceStatus::STATUS_AVAILABLE;
                $entry['health'] = ServiceStatus::HEALTH_NOT_APPLICABLE;
                $entry['detail'] = 'On-demand runtime';
                $entries[]       = $entry;
                $running++;

                continue;
            }

            $serviceName = self::findServiceName($serviceBins, $name);
            $entry['serviceName'] = $serviceName;

            $scmEntry = $serviceName !== null ? ($scm[$serviceName] ?? null) : null;

            if ($scmEntry === null) {
                if ($scm === null) {
                    // The query failed, so this service being absent from the map
                    // says nothing about whether it is registered. Reporting it as
                    // not installed would blame the user for a dropped SCM
                    // connection and ask them to install a service they already
                    // have. ERROR rather than UNKNOWN because the severity of
                    // ERROR outranks every other status, so a genuine outage
                    // elsewhere in the stack cannot be masked by this one.
                    $entry['status'] = ServiceStatus::STATUS_ERROR;
                    $entry['detail'] = 'Service state unavailable';
                    $entries[]       = $entry;
                    $statuses[]      = $entry['status'];

                    continue;
                }

                // The files are on disk but the SCM has no such service, so the
                // Windows service was never registered. Distinct from a failed
                // query: the first needs a service install, the second needs
                // nothing from the user and must not be reported as an outage.
                $entry['status'] = ServiceStatus::STATUS_NOT_INSTALLED;
                $entry['detail'] = 'Service not registered';
                $entries[]       = $entry;
                $statuses[]      = $entry['status'];

                continue;
            }

            $state = ServiceStatus::fromWin32State((string) ($scmEntry['State'] ?? ''));
            $pid   = (int) ($scmEntry['ProcessId'] ?? 0);

            $entry['status'] = $state;
            $entry['detail'] = (string) ($scmEntry['State'] ?? '');
            $entry['pid']    = $pid > 0 ? $pid : null;

            if ($state === ServiceStatus::STATUS_RUNNING) {
                $entry['ports'] = self::collectPorts($bin);
                $entry['health'] = self::probeHealth($entry['ports']);
                $running++;
            }

            $entries[]  = $entry;
            $statuses[] = $entry['status'];
        }

        return [
            'status'   => ServiceStatus::rollup($statuses),
            'expected' => $expected,
            'running'  => $running,
            'entries'  => $entries,
            // Stamped here, at collection time, rather than by the caller at
            // response time. The web bridge caches a snapshot for a couple of
            // seconds, and a request-time stamp would relabel that reused data as
            // freshly observed, so the client could never tell how old it is.
            'generatedAt' => time(),
        ];
    }

    /**
     * Builds a map of bin display name => Windows service name.
     *
     * Bins::getServices() only returns enabled bins, so it cannot be used to
     * decide which bins are services; that distinction has to hold for disabled
     * bins too, otherwise toggling a bin off would change its status vocabulary
     * instead of simply reporting it as disabled.
     *
     * @param   Bins  $bearsamppBins  The bins registry.
     *
     * @return array Bin name => service name.
     */
    private static function mapServiceBins(Bins $bearsamppBins): array
    {
        $map = [];
        foreach (ServiceHelper::getAllServiceNames() as $serviceName) {
            $bin = ServiceHelper::getBinFromServiceName($serviceName, $bearsamppBins);
            if ($bin !== null) {
                $map[$bin->getName()] = $serviceName;
            }
        }

        return $map;
    }

    /**
     * Returns the Windows service name backing a bin display name.
     *
     * @param   array   $serviceBins  Bin name => service name map.
     * @param   string  $binName      The bin display name.
     *
     * @return string|null The service name, or null when the bin has none.
     */
    private static function findServiceName(array $serviceBins, string $binName): ?string
    {
        return $serviceBins[$binName] ?? null;
    }

    /**
     * Collects every port a bin is expected to listen on.
     *
     * Bins expose their ports through differently named accessors depending on
     * the service, so the known ones are probed reflectively. Apache is the case
     * that matters most here: it has both an HTTP and an SSL port, and only one
     * of them answering is a partial result rather than a failure.
     *
     * @param   object  $bin  The bin module.
     *
     * @return array List of port numbers.
     */
    private static function collectPorts(object $bin): array
    {
        $ports = [];

        foreach (['getPort', 'getSslPort', 'getSmtpPort'] as $accessor) {
            if (!method_exists($bin, $accessor)) {
                continue;
            }

            $port = (int) $bin->$accessor();
            if ($port > 0) {
                $ports[$port] = $port;
            }
        }

        return array_values($ports);
    }

    /**
     * Probes a set of ports and reduces the results to a single health value.
     *
     * @param   array  $ports  List of port numbers.
     *
     * @return string A HEALTH_* constant.
     */
    private static function probeHealth(array $ports): string
    {
        if (empty($ports)) {
            return ServiceStatus::HEALTH_NOT_APPLICABLE;
        }

        $open = 0;
        foreach ($ports as $port) {
            if (self::probePort($port)) {
                $open++;
            }
        }

        if ($open === 0) {
            return ServiceStatus::HEALTH_UNREACHABLE;
        }

        if ($open < count($ports)) {
            return ServiceStatus::HEALTH_PARTIAL;
        }

        return ServiceStatus::HEALTH_OK;
    }

    /**
     * Attempts a bounded TCP connection to a port on the loopback interface.
     *
     * stream_socket_client() is used rather than fsockopen() because it takes an
     * explicit connect timeout. That matters: a filtered port would otherwise
     * block for the OS default, which on a 2 second poll is a visible stall.
     *
     * @param   int  $port  The port to probe.
     *
     * @return bool True when something accepted the connection.
     */
    private static function probePort(int $port): bool
    {
        $address = sprintf('tcp://%s:%d', APP_LOCALHOST, $port);

        $socket = @stream_socket_client($address, $errno, $errstr, self::PROBE_TIMEOUT);
        if ($socket === false) {
            return false;
        }

        fclose($socket);

        return true;
    }
}
