<?php
/*
 * Copyright (c) 2026 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Reports how much disk space the Bearsampp install occupies, broken down by part.
 *
 * This is deliberately separate from StatusSnapshot, which is polled every few
 * seconds. Measuring the install means stat-ing roughly 230,000 files, which
 * costs about 7s here, against roughly 270ms for an entire status snapshot. The
 * two figures differ by more than an order of magnitude, so a poll that included
 * this would spend nearly all of its time in the disk walk. A user who installs
 * or removes a bin changes the answer, but only rarely, so it is offered as an
 * explicit request and then cached.
 *
 * Because that request is expensive, starting one is not left to the caller's
 * judgement. Caching a result is not the same as limiting how often a new one
 * may be produced, and a caller able to ask for a refresh can ask on every
 * request. So a walk takes a lock, making it single-flight, and a forced refresh
 * is refused while the measurement it would replace is still newer than
 * MIN_FORCED_REFRESH_INTERVAL. The refusal is not an error: the cached
 * measurement is returned instead, marked 'cached' as it already is elsewhere.
 *
 * The measurement itself, including why it runs through PowerShell and why the
 * version symlinks are not followed, is documented in status-disk-usage.php.
 *
 * @see ajax.stackdisk.php for the endpoint.
 */
class DiskUsage
{
    /**
     * On-disk cache for a computed breakdown.
     */
    const CACHE_PATH = '/tmp/stack-disk-usage.json';

    /**
     * How long a computed breakdown may be reused, in seconds.
     *
     * Five minutes. A walk costs several seconds, so this is what keeps a reload
     * of the page from re-running one, while still letting an install or removal
     * show up on its own soon afterwards rather than after a long wait. The
     * endpoint also accepts a forced refresh, so a user who has just changed
     * something never has to wait out a stale entry.
     */
    const CACHE_TTL = 300;

    /**
     * Shortest gap between two walks started by a forced refresh, in seconds.
     *
     * CACHE_TTL says how long a result may be *reused*; this says how often a
     * walk may be *started*. Without it the two collapse into one another: a
     * fresh cache is exactly the state in which a forced refresh is willing to
     * walk, so a caller that asks repeatedly gets a walk per request and the
     * cost is paid again every time, the cache never being fast enough to help.
     * Ten seconds is below the time it takes a person to read the button again
     * and far above nothing, so a deliberate "Measure again" still measures.
     */
    const MIN_FORCED_REFRESH_INTERVAL = 10;

    /**
     * Advisory lock held for the duration of a walk.
     *
     * The lock is what makes the cooldown a guarantee rather than a hope. Two
     * requests arriving together both see a stale cache, so a check of the
     * cache alone cannot tell the second one that the first is already walking:
     * the only thing that can is the walk itself, and it takes seconds. Held
     * across the collector run, so a loser waits for the winner's result instead
     * of piling a second worker onto the same 230,000 files.
     */
    const LOCK_PATH = '/tmp/stack-disk-usage.lock';

    /**
     * How long a request waits for an in-flight walk before giving up, in seconds.
     *
     * Long enough to outlast a walk, which is about 7s here, so that a second
     * request arriving during one normally ends up serving the first one's
     * result instead of erroring or starting a duplicate. Bounded regardless,
     * because a walk that has not returned in this long is not one this request
     * should sit behind, and a dead worker's lock is not guaranteed to be
     * released quickly on every failure mode.
     */
    const LOCK_WAIT_SECONDS = 15;

    /**
     * Gap between lock attempts while waiting, in microseconds.
     *
     * 100ms. Fine enough that a request joins a walk which is about to publish,
     * coarse enough that waiting costs no measurable CPU.
     */
    const LOCK_POLL_MICROSECONDS = 100000;

    /**
     * Path to the CLI collector that performs the walk.
     */
    const COLLECTOR_SCRIPT = '/status-disk-usage.php';

    /**
     * Part name used for files sitting directly in the install root.
     *
     * Grouping them keeps the reported total equal to the sum of the listed
     * parts. A total that cannot be checked against its own rows is a number
     * worth trusting less, and these files are otherwise silently missing.
     */
    const ROOT_FILES_PART = '(root files)';

    /**
     * Returns the on-disk breakdown, computing it only when needed.
     *
     * The two booleans are separate on purpose, because "no cache yet" has to be
     * answerable without paying for a walk:
     *
     *   - $force asks for a fresh measurement even when the cache is usable;
     *   - $compute decides whether a missing cache entry may be filled in.
     *
     * A page load passes $compute as false so that arriving at the status page
     * never costs several seconds; the figure appears only once something has
     * already measured it, and the button is what starts that.
     *
     * @param   bool  $force    Recompute even when a usable cache entry exists.
     * @param   bool  $compute  Fill in a missing or stale cache entry.
     *
     * @return array {
     *     @type array  $parts       One entry per top-level part, largest first.
     *     @type array  $total       byte and file count for the whole install.
     *     @type int    $generatedAt When the measurement was taken, 0 if none.
     *     @type bool   $available   Whether a real measurement is being reported.
     *     @type bool   $cached      Whether this response came from the cache.
     *     @type int    $elapsedMs   How long the computation took, 0 when cached.
     *     @type string $error       Present only when the measurement failed.
     * }
     */
    public static function captureForWeb(bool $force = false, bool $compute = true): array
    {
        $cachePath = Path::getCorePath() . self::CACHE_PATH;

        if (!$force) {
            $cached = self::readCache($cachePath);

            if ($cached !== null) {
                $cached['cached']    = true;
                $cached['elapsedMs'] = 0;

                return $cached;
            }
        }

        if (!$compute) {
            // Asked for a reading without authorising the walk that produces one.
            return [
                'parts'       => [],
                'total'       => ['bytes' => 0, 'files' => 0],
                'generatedAt' => 0,
                'available'   => false,
                'cached'      => false,
                'elapsedMs'   => 0,
            ];
        }

        // From here on the answer may cost a full walk: seconds of disk I/O and
        // a PHP worker held for the duration. The cache TTL does not bound that,
        // because TTL decides what may be reused while a forced refresh is by
        // definition the case where the cache is not reused. So the walk is
        // serialised with a lock and rate limited by a cooldown.
        //
        // The lock is waited on rather than refused. A second request arriving
        // mid-walk wants the same number as the first, and the first is already
        // producing it, so making it wait and then read the result turns a
        // duplicate request into a cache hit instead of an error. Waiting is
        // bounded because the walker's own failure must not become this
        // request's hang.
        $lock = self::acquireLock(self::LOCK_WAIT_SECONDS);

        if ($lock === null) {
            // The walker outlived the wait, which means the walk is far slower
            // than the wait assumed. Reporting the last known figures is more
            // useful than an error, and is still true of them.
            $inFlight = self::readCache($cachePath);

            if ($inFlight !== null) {
                $inFlight['cached']    = true;
                $inFlight['elapsedMs'] = 0;

                return $inFlight;
            }

            return [
                'parts'       => [],
                'total'       => ['bytes' => 0, 'files' => 0],
                'generatedAt' => 0,
                'available'   => false,
                'cached'      => false,
                'elapsedMs'   => 0,
                'error'       => 'A disk measurement is already running.',
            ];
        }

        try {
            // Re-read under the lock. A request that queued behind a walk would
            // otherwise have decided above, from the pre-lock cache, that a walk
            // was needed, and would then walk immediately after the one it was
            // waiting for had already produced exactly the result it wants.
            $cached = self::readCache($cachePath);

            if ($cached !== null) {
                if (!$force) {
                    $cached['cached']    = true;
                    $cached['elapsedMs'] = 0;

                    return $cached;
                }

                // Forcing means the caller wants a walk regardless of the TTL, so
                // only the cooldown can refuse. It is measured on the cache file,
                // which is rewritten by every walk, so it answers "how long ago
                // was anything last measured" for both the winner of the lock and
                // the requests that queued behind it.
                if (self::secondsSinceLastMeasurement($cachePath) < self::MIN_FORCED_REFRESH_INTERVAL) {
                    $cached['cached']    = true;
                    $cached['elapsedMs'] = 0;

                    return $cached;
                }
            }

            $started  = microtime(true);
            $measured = self::runCollector();
            $elapsed  = (int) round((microtime(true) - $started) * 1000);

            if (isset($measured['error'])) {
                // A failure is never cached: the next request should be free to
                // succeed, for instance once whatever locked the files has exited.
                return [
                    'parts'       => [],
                    'total'       => ['bytes' => 0, 'files' => 0],
                    'generatedAt' => time(),
                    'available'   => false,
                    'cached'      => false,
                    'elapsedMs'   => $elapsed,
                    'error'       => $measured['error'],
                ];
            }

            $parts = self::normaliseParts($measured['parts'] ?? []);

            $result = [
                'parts'       => $parts,
                'total'       => self::normaliseTotal($measured['total'] ?? [], $parts),
                'generatedAt' => (int) ($measured['generatedAt'] ?? time()),
                'available'   => true,
                'cached'      => false,
                'elapsedMs'   => $elapsed,
            ];

            self::writeCache($cachePath, $result);

            return $result;
        } finally {
            self::releaseLock($lock);
        }
    }

    /**
     * Takes the walk lock, waiting up to $waitSeconds for a holder to finish.
     *
     * The lock is a file, polled with LOCK_NB rather than left to block. The
     * difference is who decides how long to wait: a blocking LOCK_EX hands that
     * choice to the operating system and holds a PHP worker for the whole walk,
     * which is the cost this is meant to bound. Polling lets the request give up
     * on its own terms, and lets it read the cache between attempts so it can
     * answer from the walker's result the moment that is published.
     *
     * Returning null is an expected outcome, not an error: it means a walk is
     * taking longer than the caller agreed to wait.
     *
     * @param   int  $waitSeconds  Longest time to wait for the lock.
     *
     * @return resource|null The open handle, to be passed to releaseLock().
     */
    private static function acquireLock(int $waitSeconds)
    {
        $path = Path::getCorePath() . self::LOCK_PATH;

        if (!is_dir(dirname($path))) {
            return null;
        }

        $handle = @fopen($path, 'c');

        if ($handle === false) {
            return null;
        }

        $deadline = microtime(true) + $waitSeconds;

        while (true) {
            if (@flock($handle, LOCK_EX | LOCK_NB)) {
                return $handle;
            }

            if (microtime(true) >= $deadline) {
                @fclose($handle);

                return null;
            }

            // Short enough that a request joins a walk that is about to publish,
            // long enough not to spin a worker for the wait.
            usleep(self::LOCK_POLL_MICROSECONDS);
        }
    }

    /**
     * Releases a lock taken by acquireLock(), keeping a crashed walk recoverable.
     *
     * The contents are only a timestamp, and deliberately not the PID: nothing
     * needs to identify the owner, because the OS releases the lock when the
     * handle closes, including on a fatal error. The timestamp is left behind
     * only so an operator can see when the last walk was started.
     *
     * @param   resource  $handle  Handle returned by acquireLock().
     *
     * @return void
     */
    private static function releaseLock($handle): void
    {
        if (!is_resource($handle)) {
            return;
        }

        @ftruncate($handle, 0);
        @fwrite($handle, (string) time());
        @fflush($handle);
        @flock($handle, LOCK_UN);
        @fclose($handle);
    }

    /**
     * Seconds since anything last wrote a measurement, or a large number if unknown.
     *
     * An unreadable or absent cache file reports PHP_INT_MAX so the caller treats
     * "when was this measured" as unanswerable and walks, which is the safe
     * direction: a missing measurement should be computed, not assumed fresh.
     *
     * @param   string  $path  Absolute cache file path.
     *
     * @return int
     */
    private static function secondsSinceLastMeasurement(string $path): int
    {
        $mtime = @filemtime($path);

        if ($mtime === false) {
            return PHP_INT_MAX;
        }

        return max(0, time() - $mtime);
    }

    /**
     * Runs the collector with the internal engine and decodes its output.
     *
     * The internal engine is used even though the walk needs no COM, because it
     * is the fixed, known-good interpreter for this codebase and keeps the
     * measurement independent of whatever the user has selected in the UI.
     *
     * @return array The measurement, carrying 'error' when it failed.
     */
    private static function runCollector(): array
    {
        $engine = Path::getPhpPath() . '/php.exe';
        $script = Path::getCorePath() . self::COLLECTOR_SCRIPT;

        if (!is_file($engine) || !is_file($script)) {
            Log::error('Disk usage collector is missing: ' . $engine . ' or ' . $script);

            return ['error' => 'The disk usage collector is missing.'];
        }

        $command = escapeshellarg($engine) . ' ' . escapeshellarg($script) . ' 2>NUL';

        try {
            $output = shell_exec($command);
        } catch (Throwable $e) {
            Log::error('Disk usage collector failed to start: ' . $e->getMessage());

            return ['error' => 'The disk usage collector could not be started.'];
        }

        $output = is_string($output) ? trim($output) : '';

        if ($output === '') {
            Log::error('Disk usage collector returned no output');

            return ['error' => 'The disk usage collector returned no response.'];
        }

        $decoded = json_decode($output, true);

        if (!is_array($decoded) || isset($decoded['error'])) {
            Log::error('Disk usage collector returned unusable output');

            return ['error' => 'The disk usage collector returned an unusable response.'];
        }

        return $decoded;
    }

    /**
     * Reads a cached breakdown, provided it is recent enough to be useful.
     *
     * @param   string  $path  Absolute cache file path.
     *
     * @return array|null The cached measurement, or null when absent,
     *                    unreadable or stale.
     */
    private static function readCache(string $path): ?array
    {
        if (!is_file($path)) {
            return null;
        }

        $age = time() - (int) @filemtime($path);

        if ($age < 0 || $age > self::CACHE_TTL) {
            return null;
        }

        $raw  = @file_get_contents($path);
        $data = $raw === false ? null : json_decode($raw, true);

        if (!is_array($data) || !isset($data['parts'], $data['total']) || isset($data['error'])) {
            return null;
        }

        // Older cache entries predate the 'available' flag and are still a real
        // measurement, so they are upgraded rather than discarded.
        if (!isset($data['available'])) {
            $data['available'] = true;
        }

        return $data;
    }

    /**
     * Stores a computed breakdown for reuse.
     *
     * A failed write is not worth reporting: the only consequence is that the
     * next request measures again.
     *
     * @param   string  $path       Absolute cache file path.
     * @param   array   $measurement  The measurement to store.
     *
     * @return void
     */
    private static function writeCache(string $path, array $measurement): void
    {
        if (!is_dir(dirname($path))) {
            return;
        }

        @file_put_contents($path, json_encode($measurement), LOCK_EX);
    }

    /**
     * Cleans and orders the parts reported by the collector.
     *
     * Sorting is repeated here rather than trusted from the script, so the page
     * still shows the largest part first if the collector ever changes.
     *
     * @param   array  $parts  Raw parts from the collector.
     *
     * @return array
     */
    private static function normaliseParts(array $parts): array
    {
        $clean = [];

        foreach ($parts as $part) {
            if (!is_array($part) || !isset($part['name'])) {
                continue;
            }

            $children = [];

            foreach (($part['childrenList'] ?? []) as $child) {
                if (!is_array($child) || !isset($child['name'])) {
                    continue;
                }

                $children[] = [
                    'name'  => (string) $child['name'],
                    'bytes' => max(0, (int) ($child['bytes'] ?? 0)),
                    'files' => max(0, (int) ($child['files'] ?? 0)),
                ];
            }

            usort($children, function (array $a, array $b) {
                return $b['bytes'] <=> $a['bytes'];
            });

            $clean[] = [
                'name'        => (string) $part['name'],
                // Set by the collector for the synthetic group of files sitting
                // directly in the install root, so the page can relabel it without
                // matching on an English name this class knows nothing about.
                'isRootFiles' => !empty($part['isRootFiles']),
                'bytes'       => max(0, (int) ($part['bytes'] ?? 0)),
                'files'       => max(0, (int) ($part['files'] ?? 0)),
                'children'    => $children,
            ];
        }

        usort($clean, function (array $a, array $b) {
            return $b['bytes'] <=> $a['bytes'];
        });

        return $clean;
    }

    /**
     * Derives the headline total from the parts that will be displayed.
     *
     * The total is recomputed here rather than taken from the collector, because
     * that identity is the useful property: the rows a user can see have to add up
     * to the figure printed above them, even if the collector ever reports
     * something else. The collector's own figure is still compared against the
     * derived one and any disagreement is logged, so a future bug in the walk is
     * visible rather than quietly masked.
     *
     * @param   array  $total  Raw total from the collector, for comparison only.
     * @param   array  $parts  Normalised parts, already cleaned and ordered.
     *
     * @return array
     */
    private static function normaliseTotal(array $total, array $parts): array
    {
        $bytes = 0;
        $files = 0;

        foreach ($parts as $part) {
            $bytes += $part['bytes'];
            $files += $part['files'];
        }

        $reportedBytes = max(0, (int) ($total['bytes'] ?? 0));
        $reportedFiles = max(0, (int) ($total['files'] ?? 0));

        if ($parts && ($reportedBytes !== $bytes || $reportedFiles !== $files)) {
            Log::debug(sprintf(
                'Disk usage: collector total (bytes=%d, files=%d) disagrees with the sum of the '
                . 'reported parts (bytes=%d, files=%d). Showing the derived figure.',
                $reportedBytes,
                $reportedFiles,
                $bytes,
                $files
            ));
        }

        return [
            'bytes' => $bytes,
            'files' => $files,
        ];
    }
}
