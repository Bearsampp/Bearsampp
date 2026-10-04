<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ProcessFootprint
 *
 * Attributes working set and CPU time to the individual services that make up
 * the WAMPP stack, so the status page can report what the stack actually costs
 * rather than how many services happen to be up.
 *
 * Design constraints this class exists to respect:
 *
 *  - Attribution is by process tree, never by executable name alone. MySQL and
 *    MariaDB both run mysqld.exe, so only the SCM-reported PID chain can tell
 *    their processes apart. Walking from each service PID also picks up every
 *    worker: Apache's children, PostgreSQL's backends, both of a MySQL pair.
 *
 *  - Wrapper processes are excluded implicitly rather than by a denylist. The
 *    tree contains nssm.exe and conhost.exe noise that would otherwise inflate
 *    the numbers, but neither name ever appears in a service's accepted list,
 *    so matching against that list is enough to skip them.
 *
 *  - Exactly one WMI query is issued for the whole stack, unfiltered, and the
 *    attribution is done in PHP. This is deliberate. WQL IN clauses are not
 *    reliable against these classes on this platform: an IN list returns no
 *    rows at all, including for PIDs that provably exist, so the obvious
 *    "query just my processes" approach silently reports everything as zero.
 *    Per-name LIKE filtering does work but costs ~23ms per name, which for six
 *    stack executables is worse than the single ~80ms sweep. Measured against
 *    a live stack of 268 processes, the unfiltered sweep returns everything
 *    needed for less overhead than two filtered queries would.
 *
 *  - CPU needs a previous sample, because Windows exposes cumulative processor
 *    time and not an instantaneous percentage. Instantaneous figures are
 *    available from Win32_PerfFormattedData_*, but that query measured
 *    300-460ms, which is far too slow for a poll. Win32_Process carries
 *    KernelModeTime and UserModeTime, so a single sample is cheap and the
 *    delta between two samples yields the rate. Each poll is a fresh PHP
 *    process, so the previous sample is persisted to a small file.
 */
class ProcessFootprint
{
    /**
     * Where the previous CPU sample is persisted, relative to the install root.
     *
     * Lives in core/tmp, which is already the application's scratch area and is
     * excluded from version control. Losing the file is harmless: the next poll
     * simply reports no CPU figure until a second sample exists.
     */
    const SAMPLE_PATH = '/core/tmp/stack-cpu-sample.json';

    /**
     * Longest gap between two samples that still yields a meaningful rate, in
     * seconds. Beyond this the average says more about the idle gap than about
     * current activity, so the figure is dropped instead of shown.
     */
    const MAX_SAMPLE_AGE = 60;

    /** Windows reports processor time in 100-nanosecond units. */
    const TICKS_PER_SECOND = 10000000;

    /**
     * Columns read from Win32_Process. Working set and processor time come from
     * the same class, which is what makes the one-query design possible.
     */
    const PROCESS_PROPERTIES = [
        'ProcessId',
        'ParentProcessId',
        'Name',
        'WorkingSetSize',
        'KernelModeTime',
        'UserModeTime',
        'ThreadCount',
        'HandleCount',
        'CreationDate',
    ];

    /**
     * Captures per-service working set and CPU for every running stack service.
     *
     * @param   array  $roots  serviceName => ['pid' => int, 'names' => string[]].
     *                        The pid is the SCM ProcessId and names come from
     *                        ServiceHelper::getProcessNamesForService().
     *
     * @return array {
     *     @type array $services serviceName => metric map.
     *     @type array $total    Metric map summed across the stack.
     *     @type array $host     Physical memory totals for the machine.
     *     @type array $disk     Capacity of the volume holding the install.
     *     @type int   $cores    Logical processor count, 0 when unknown.
     * }
     */
    public static function capture(array $roots): array
    {
        $cores     = self::countCores();
        $host      = self::collectHostMemory();
        $result    = self::emptyMetrics();
        $result['services'] = [];
        $result['total']    = self::emptyMetrics();
        $result['host']     = $host;
        $result['disk']     = self::collectDiskSpace();
        $result['cores']    = $cores;

        $attributable = [];
        foreach ($roots as $serviceName => $root) {
            $pid = (int) ($root['pid'] ?? 0);

            if ($pid > 0 && !empty($root['names'])) {
                $attributable[$serviceName] = $root;
            }
        }

        if (empty($attributable)) {
            // Nothing is running, so the process sweep would find nothing worth
            // attributing. Skipping it keeps an idle poll at SCM cost only.
            return $result;
        }

        $processes = Win32Native::getProcessList(self::PROCESS_PROPERTIES);

        if (empty($processes)) {
            return $result;
        }

        $index  = self::indexProcesses($processes);
        $cpuMap = self::resolveCpuPercentages($index, $cores);

        foreach ($attributable as $serviceName => $root) {
            $matched = self::collectMatchingDescendants($index, (int) $root['pid'], $root['names']);

            $result['services'][$serviceName] = self::summarize($matched, $cpuMap, $cores);
            $result['total']                   = self::merge($result['total'], $result['services'][$serviceName]);
        }

        return $result;
    }

    /**
     * Builds the lookup tables the tree walk needs.
     *
     * Two indexes are required because the interesting processes are reached
     * from the service PID downwards: by PID for identity, and by parent PID to
     * expand the tree.
     *
     * @param   array  $processes  Rows from Win32Native::getProcessList().
     *
     * @return array {
     *     @type array $byPid    pid => metric map.
     *     @type array $children parentPid => list of pids.
     * }
     */
    private static function indexProcesses(array $processes): array
    {
        $byPid    = [];
        $children = [];

        foreach ($processes as $process) {
            $pid = (int) ($process['ProcessId'] ?? 0);

            if ($pid <= 0) {
                continue;
            }

            $byPid[$pid] = [
                'pid'         => $pid,
                'parentPid'   => (int) ($process['ParentProcessId'] ?? 0),
                'name'        => (string) ($process['Name'] ?? ''),
                'workingSet'  => (int) ($process['WorkingSetSize'] ?? 0),
                'cpuTicks'    => (int) ($process['KernelModeTime'] ?? 0) + (int) ($process['UserModeTime'] ?? 0),
                'threads'     => (int) ($process['ThreadCount'] ?? 0),
                'handles'     => (int) ($process['HandleCount'] ?? 0),
                'created'     => (string) ($process['CreationDate'] ?? ''),
            ];

            $parent = $byPid[$pid]['parentPid'];
            if ($parent > 0) {
                $children[$parent][] = $pid;
            }
        }

        return ['byPid' => $byPid, 'children' => $children];
    }

    /**
     * Collects every descendant of a PID whose executable name is accepted.
     *
     * The root itself is tested too, because for MySQL and MariaDB the service
     * PID is the server itself. The walk is iterative rather than recursive so
     * that a malformed or cyclic parent chain cannot exhaust the stack, and it
     * tracks visited PIDs for the same reason.
     *
     * @param   array   $index      Index from indexProcesses().
     * @param   int     $rootPid    The SCM ProcessId for the service.
     * @param   array   $names      Accepted executable names.
     *
     * @return array List of process maps, in walk order.
     */
    private static function collectMatchingDescendants(array $index, int $rootPid, array $names): array
    {
        $accepted = array_map('strtolower', $names);
        $byPid    = $index['byPid'];
        $children = $index['children'];
        $matched  = [];
        $visited  = [$rootPid => true];
        $queue    = [$rootPid];

        while (!empty($queue)) {
            $pid = array_shift($queue);

            if (isset($byPid[$pid]) && in_array(strtolower($byPid[$pid]['name']), $accepted, true)) {
                $matched[] = $byPid[$pid];
            }

            foreach ($children[$pid] ?? [] as $childPid) {
                if (!isset($visited[$childPid])) {
                    $visited[$childPid] = true;
                    $queue[]            = $childPid;
                }
            }
        }

        return $matched;
    }

    /**
     * Turns the cumulative processor time of every process into a rate.
     *
     * Reads the previous sample, compares it against the one just taken, and
     * writes the current sample back for the next poll. Two guards keep a
     * recycled or restarted process from producing nonsense: the sample must be
     * recent, and a PID whose creation time changed is treated as a different
     * process and skipped.
     *
     * @param   array  $index  Index from indexProcesses().
     * @param   int    $cores  Logical processor count.
     *
     * @return array pid => percent of the whole machine, for pids with a usable delta.
     */
    private static function resolveCpuPercentages(array $index, int $cores): array
    {
        $current = self::buildSample($index);
        $previous = self::readSample();

        $percentages = [];

        if ($previous !== null && $cores > 0) {
            $elapsed = $current['ts'] - $previous['ts'];

            if ($elapsed > 0 && $elapsed <= self::MAX_SAMPLE_AGE) {
                foreach ($index['byPid'] as $pid => $process) {
                    $old = $previous['procs'][$pid] ?? null;

                    if ($old === null) {
                        // First sighting of this PID: a newly started process has
                        // no baseline, and a reused PID cannot be compared safely.
                        continue;
                    }

                    if ($old['c'] !== $process['created']) {
                        continue;
                    }

                    $delta = $process['cpuTicks'] - $old['t'];

                    if ($delta < 0) {
                        // Counter reset, or the process was replaced under us.
                        continue;
                    }

                    // Ticks are 100ns units across all cores, so this converts to
                    // a share of the whole machine rather than of a single core.
                    $percentages[$pid] = $delta / self::TICKS_PER_SECOND / $elapsed / $cores * 100;
                }
            }
        }

        self::writeSample($current);

        return $percentages;
    }

    /**
     * Reduces a set of processes to the metrics reported per service.
     *
     * cpuPercent is null rather than zero when no baseline exists, so the UI can
     * tell "measuring" apart from "idle".
     *
     * @param   array  $processes  Matched process maps.
     * @param   array  $cpuMap     pid => percent of machine.
     * @param   int    $cores      Logical processor count.
     *
     * @return array Metric map.
     */
    private static function summarize(array $processes, array $cpuMap, int $cores): array
    {
        $metrics = self::emptyMetrics();
        $cpuSum  = 0.0;
        $cpuSeen = false;

        foreach ($processes as $process) {
            $metrics['processes']++;
            $metrics['workingSetBytes'] += $process['workingSet'];
            $metrics['threads']        += $process['threads'];
            $metrics['handles']        += $process['handles'];

            $pid = $process['pid'];
            if (isset($cpuMap[$pid])) {
                $cpuSum += $cpuMap[$pid];
                $cpuSeen = true;
            }
        }

        if ($cpuSeen) {
            $metrics['cpuPercent'] = $cpuSum;
            $metrics['cpuCores']   = $cores > 0 ? $cpuSum / 100 * $cores : null;
        }

        return $metrics;
    }

    /**
     * Adds one service's metrics to the running stack total.
     *
     * CPU is summed only across services that actually produced a figure, so a
     * total is never diluted by services that are still waiting for a baseline.
     *
     * @param   array  $total    Accumulator.
     * @param   array  $metrics  Service metrics.
     *
     * @return array Updated accumulator.
     */
    private static function merge(array $total, array $metrics): array
    {
        $total['processes']        += $metrics['processes'];
        $total['workingSetBytes'] += $metrics['workingSetBytes'];
        $total['threads']        += $metrics['threads'];
        $total['handles']        += $metrics['handles'];

        if ($metrics['cpuPercent'] !== null) {
            $total['cpuPercent'] = ($total['cpuPercent'] ?? 0.0) + $metrics['cpuPercent'];
            $total['cpuCores']   = ($total['cpuCores'] ?? 0.0) + ($metrics['cpuCores'] ?? 0.0);
        }

        return $total;
    }

    /**
     * The shape of a metrics map with no data.
     *
     * Public because StatusSnapshot builds the same shape for its "collector
     * failed" response, and a consumer should never have to care which of the
     * two produced the map it was handed.
     *
     * @return array
     */
    public static function emptyMetrics(): array
    {
        return [
            'processes'       => 0,
            'workingSetBytes' => 0,
            'cpuPercent'      => null,
            'cpuCores'        => null,
            'threads'         => 0,
            'handles'         => 0,
        ];
    }

    /**
     * Assembles the sample to persist for the next poll.
     *
     * @param   array  $index  Index from indexProcesses().
     *
     * @return array
     */
    private static function buildSample(array $index): array
    {
        $procs = [];

        foreach ($index['byPid'] as $pid => $process) {
            $procs[$pid] = ['t' => $process['cpuTicks'], 'c' => $process['created']];
        }

        return ['ts' => microtime(true), 'procs' => $procs];
    }

    /**
     * Reads the previous sample, treating any problem as "no sample".
     *
     * @return array|null
     */
    private static function readSample(): ?array
    {
        $path = Path::getRootPath() . self::SAMPLE_PATH;

        if (!is_file($path)) {
            return null;
        }

        $raw = @file_get_contents($path);

        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true);

        if (!is_array($data) || !isset($data['ts'], $data['procs']) || !is_array($data['procs'])) {
            return null;
        }

        // Validate the per-process entries too, not just the envelope. The file
        // is rewritten on every poll, so a partially written or hand-edited one
        // is possible, and a scalar or truncated entry would otherwise reach the
        // comparison below and raise a warning for a value we are about to
        // discard anyway. Warnings are not free here: this class runs inside
        // the status collector, whose stdout is the JSON payload.
        $procs = [];

        foreach ($data['procs'] as $pid => $entry) {
            if (is_array($entry) && isset($entry['c'], $entry['t'])) {
                $procs[(int) $pid] = $entry;
            }
        }

        $data['procs'] = $procs;

        return $data;
    }

    /**
     * Persists the sample for the next poll.
     *
     * Failures are ignored on purpose. The sample is an optimisation for
     * reporting, and a read-only or full disk must not turn into a failed
     * status poll.
     *
     * @param   array  $sample  Sample from buildSample().
     *
     * @return void
     */
    private static function writeSample(array $sample): void
    {
        $path = Path::getRootPath() . self::SAMPLE_PATH;
        $dir  = dirname($path);

        if (!is_dir($dir)) {
            return;
        }

        @file_put_contents($path, json_encode($sample), LOCK_EX);
    }

    /**
     * Reads physical memory totals for the machine.
     *
     * Included because a footprint figure is hard to interpret without the
     * machine total next to it. This is the only extra query in the collector
     * and it measured 3-4ms, unlike every CPU-percentage alternative.
     *
     * @return array
     */
    private static function collectHostMemory(): array
    {
        $host = ['totalBytes' => 0, 'freeBytes' => 0];

        try {
            $wmi = new COM('winmgmts://./root/cimv2');
            $rows = $wmi->ExecQuery('SELECT FreePhysicalMemory, TotalVisibleMemorySize FROM Win32_OperatingSystem');

            foreach ($rows as $row) {
                // WMI reports kilobytes here.
                $host['freeBytes']  = (int) $row->FreePhysicalMemory * 1024;
                $host['totalBytes'] = (int) $row->TotalVisibleMemorySize * 1024;
                break;
            }
        } catch (Exception $e) {
            Log::debug('ProcessFootprint: could not read host memory: ' . $e->getMessage());
        }

        return $host;
    }

    /**
     * Reads the capacity of the volume that holds the Bearsampp install.
     *
     * Reported next to the footprint figures because a working-set total is hard
     * to act on without knowing how much room the disk has. These two calls need
     * no COM and measure well under a millisecond, which is why this figure can
     * live in the always-on telemetry path while the on-disk size of the install
     * itself, which takes seconds to walk, has to be asked for explicitly.
     *
     * @return array
     */
    private static function collectDiskSpace(): array
    {
        $empty = ['totalBytes' => 0, 'freeBytes' => 0, 'usedBytes' => 0];
        $root  = Path::getRootPath();
        $total = @disk_total_space($root);
        $free  = @disk_free_space($root);

        if ($total === false || $free === false) {
            Log::debug('ProcessFootprint: could not read disk space for ' . $root);

            return $empty;
        }

        $total = (int) $total;
        $free  = (int) $free;

        return [
            'totalBytes' => $total,
            'freeBytes'  => $free,
            'usedBytes'  => $total - $free,
        ];
    }

    /**
     * Counts logical processors, which is what CPU percentages are scaled by.
     *
     * @return int Processor count, or 0 when it cannot be determined.
     */
    private static function countCores(): int
    {        try {
            $wmi  = new COM('winmgmts://./root/cimv2');
            $rows = $wmi->ExecQuery('SELECT NumberOfLogicalProcessors FROM Win32_ComputerSystem');

            foreach ($rows as $row) {
                return (int) $row->NumberOfLogicalProcessors;
            }
        } catch (Exception $e) {
            Log::debug('ProcessFootprint: could not count cores: ' . $e->getMessage());
        }

        return 0;
    }
}
