<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ServiceStatus
 *
 * Canonical status vocabulary for the WAMPP stack.
 *
 * Bearsampp historically reported status through two unrelated vocabularies that
 * could contradict each other:
 *
 *  - the tray icon asked the Windows Service Control Manager (SCM) directly, and
 *  - the homepage derived a badge from a TCP/HTTP liveness probe.
 *
 * A service whose port is blocked therefore showed red on the homepage while the
 * tray showed it running, and Apache reported "warning" whenever HTTP answered but
 * SSL did not, even though that is the normal state for a stack with no SSL vhost.
 *
 * To remove that ambiguity this class defines a single vocabulary split across two
 * orthogonal dimensions, so no information has to be discarded to reconcile them:
 *
 *  - STATUS_* describes the service lifecycle as the SCM reports it.
 *  - HEALTH_* describes whether the service actually answers on its port.
 *
 * A service is "running but unreachable" as a pair of facts, not as a lossy single
 * value. Presentation layers combine them (see getBadgeClass()) instead of the
 * collector guessing on their behalf.
 */
class ServiceStatus
{
    // ---------------------------------------------------------------
    // Lifecycle status (SCM view)
    // ---------------------------------------------------------------

    /** Bin is not enabled in bearsampp.conf, so it is not expected to be running. */
    const STATUS_DISABLED = 'disabled';

    /** Expected and installed, but the SCM reports it as stopped. */
    const STATUS_STOPPED = 'stopped';

    /** SCM reports start-pending. */
    const STATUS_STARTING = 'starting';

    /** SCM reports running. */
    const STATUS_RUNNING = 'running';

    /** SCM reports paused. */
    const STATUS_PAUSED = 'paused';

    /** SCM reports stop-pending. */
    const STATUS_STOPPING = 'stopping';

    /** Expected but not installed in the SCM at all. */
    const STATUS_NOT_INSTALLED = 'not_installed';

    /** The collector could not determine a state. */
    const STATUS_UNKNOWN = 'unknown';

    /** The collector failed outright (COM error, timeout, exception). */
    const STATUS_ERROR = 'error';

    /**
     * Aggregate only: expected bins are up, but not all of them. A stack in
     * this state is healthy in parts, which is distinct from a single service
     * reporting a partial health result.
     */
    const STATUS_PARTIAL_STACK = 'partial_stack';

    /**
     * Not a Windows service. PHP and Node.js are invoked on demand, so a
     * running/stopped distinction is meaningless for them.
     */
    const STATUS_AVAILABLE = 'available';

    // ---------------------------------------------------------------
    // Health (probe view)
    // ---------------------------------------------------------------

    /** The service answered on its port. */
    const HEALTH_OK = 'ok';

    /** The service answered on some of its ports but not all (e.g. HTTP up, SSL down). */
    const HEALTH_PARTIAL = 'partial';

    /** The service is running but nothing answered on any of its ports. */
    const HEALTH_UNREACHABLE = 'unreachable';

    /** The service exposes no probeable port (PHP, Node.js). */
    const HEALTH_NOT_APPLICABLE = 'not_applicable';

    /** Health was not probed. */
    const HEALTH_UNKNOWN = 'unknown';

    /**
     * Maps a Win32_Service State string to a lifecycle status.
     *
     * Win32Native::getServiceState() and listServices() return the human readable
     * WMI state ("Running", "Start Pending", ...) rather than the numeric codes
     * used by Win32Service, so this normalises the string form.
     *
     * @param   string|null  $state  The WMI state string.
     *
     * @return string One of the STATUS_* constants.
     */
    public static function fromWin32State(?string $state): string
    {
        if ($state === null || $state === '') {
            return self::STATUS_UNKNOWN;
        }

        switch (strtolower(trim($state))) {
            case 'running':
                return self::STATUS_RUNNING;

            case 'stopped':
                return self::STATUS_STOPPED;

            case 'start pending':
            case 'startpending':
                return self::STATUS_STARTING;

            case 'stop pending':
            case 'stoppending':
                return self::STATUS_STOPPING;

            case 'paused':
                return self::STATUS_PAUSED;

            case 'continue pending':
            case 'continuepending':
                return self::STATUS_RUNNING;
        }

        return self::STATUS_UNKNOWN;
    }

    /**
     * Maps a Win32Service hexadecimal state code to a lifecycle status.
     *
     * Win32Service uses the SCM numeric codes as hex strings; see the constant
     * block in class.win32service.php. Provided so both existing backends can
     * feed the same vocabulary without each translating on its own.
     *
     * @param   string|null  $hexState  The hexadecimal state code, e.g. '4'.
     *
     * @return string One of the STATUS_* constants.
     */
    public static function fromWin32Code(?string $hexState): string
    {
        if ($hexState === null || $hexState === '') {
            return self::STATUS_UNKNOWN;
        }

        switch (strtolower(trim($hexState))) {
            case '1':
                return self::STATUS_STOPPED;

            case '2':
                return self::STATUS_STARTING;

            case '3':
                return self::STATUS_STOPPING;

            case '4':
                return self::STATUS_RUNNING;

            case '5':
            case '7':
                return self::STATUS_PAUSED;
        }

        // '0' is N/A, and '6' (pause-pending) has no distinct badge.
        return self::STATUS_UNKNOWN;
    }

    /**
     * Reports whether a status counts towards the "expected services" set.
     *
     * This is the same rule the tray uses when Bearsampp writes the [Services]
     * section of bearsampp.ini: a bin participates only when isEnable() is true.
     * Keeping it here means the tray, the dashboard and the stack status all
     * agree on the denominator.
     *
     * @param   string  $status  A STATUS_* constant.
     *
     * @return bool True when the bin is expected to be up.
     */
    public static function isExpected(string $status): bool
    {
        return $status !== self::STATUS_DISABLED;
    }

    /**
     * Reports whether a status means the service is up and serving.
     *
     * @param   string  $status  A STATUS_* constant.
     *
     * @return bool True for running and available bins.
     */
    public static function isUp(string $status): bool
    {
        return $status === self::STATUS_RUNNING || $status === self::STATUS_AVAILABLE;
    }

    /**
     * Reports whether a status is a transitional state.
     *
     * Transitional states should not be aggregated as failures: a stack that is
     * mid-startup is not broken.
     *
     * @param   string  $status  A STATUS_* constant.
     *
     * @return bool True for starting, stopping and paused.
     */
    public static function isTransitional(string $status): bool
    {
        return $status === self::STATUS_STARTING
            || $status === self::STATUS_STOPPING
            || $status === self::STATUS_PAUSED;
    }

    /**
     * Returns the Bootstrap badge class for a status.
     *
     * The mapping is deliberately limited to the four classes the dashboard
     * already uses (bg-secondary, bg-success, bg-warning, bg-danger) so a stack
     * status component renders consistently with the existing summary card
     * without introducing new colours.
     *
     * @param   string  $status  A STATUS_* constant.
     *
     * @return string A Bootstrap background utility class.
     */
    public static function getBadgeClass(string $status): string
    {
        switch ($status) {
            case self::STATUS_RUNNING:
            case self::STATUS_AVAILABLE:
                return 'bg-success';

            case self::STATUS_STARTING:
            case self::STATUS_STOPPING:
            case self::STATUS_PAUSED:
                return 'bg-warning';

            case self::STATUS_STOPPED:
            case self::STATUS_NOT_INSTALLED:
            case self::STATUS_ERROR:
                return 'bg-danger';

            case self::STATUS_PARTIAL_STACK:
                return 'bg-warning';
        }

        return 'bg-secondary';
    }

    /**
     * Returns the Font Awesome icon for a status.
     *
     * @param   string  $status  A STATUS_* constant.
     *
     * @return string A Font Awesome class name.
     */
    public static function getIconClass(string $status): string
    {
        switch ($status) {
            case self::STATUS_RUNNING:
            case self::STATUS_AVAILABLE:
                return 'fa-circle-check';

            case self::STATUS_STARTING:
            case self::STATUS_STOPPING:
                return 'fa-circle-notch';

            case self::STATUS_PAUSED:
                return 'fa-circle-pause';

            case self::STATUS_STOPPED:
                return 'fa-circle-stop';

            case self::STATUS_NOT_INSTALLED:
                return 'fa-circle-question';

            case self::STATUS_ERROR:
                return 'fa-triangle-exclamation';

            case self::STATUS_PARTIAL_STACK:
                return 'fa-circle-half-stroke';
        }

        return 'fa-circle-question';
    }

    /**
     * Returns a severity rank for a status, highest first.
     *
     * The ordering drives aggregate rollup. It encodes the distinction that
     * matters operationally: a collector failure outranks a stopped service,
     * which outranks a partially healthy stack, which outranks a healthy one.
     *
     * @param   string  $status  A STATUS_* constant.
     *
     * @return int Severity rank, lower is worse.
     */
    public static function getSeverity(string $status): int
    {
        switch ($status) {
            case self::STATUS_ERROR:
                return 0;

            case self::STATUS_NOT_INSTALLED:
                return 1;

            case self::STATUS_STOPPED:
                return 2;

            case self::STATUS_UNKNOWN:
                return 3;

            case self::STATUS_DISABLED:
                return 4;

            case self::STATUS_PAUSED:
                return 5;

            case self::STATUS_STOPPING:
                return 6;

            case self::STATUS_STARTING:
                return 7;

            case self::STATUS_AVAILABLE:
                return 8;

            case self::STATUS_RUNNING:
                return 9;

            // Aggregate-only: rollup() produces it but never consumes it. Ranked
            // explicitly so feeding it back in is deterministic rather than
            // silently falling through to the unknown default.
            case self::STATUS_PARTIAL_STACK:
                return 8;
        }

        return 3;
    }

    /**
     * Reduces a set of statuses to a single overall status for the stack.
     *
     * Only expected bins take part. A status of STATUS_DISABLED is returned when
     * nothing is enabled, and STATUS_AVAILABLE is returned when every expected
     * bin is a non-service such as PHP, since there is no lifecycle to report.
     *
     * @param   array  $statuses  List of STATUS_* constants.
     *
     * @return string The aggregate STATUS_* constant.
     */
    public static function rollup(array $statuses): string
    {
        $expected = array_values(array_filter($statuses, [self::class, 'isExpected']));

        if (empty($expected)) {
            return self::STATUS_DISABLED;
        }

        // No Windows service in the set: PHP and Node.js only.
        $hasService = false;
        foreach ($expected as $status) {
            if ($status !== self::STATUS_AVAILABLE) {
                $hasService = true;
                break;
            }
        }

        if (!$hasService) {
            return self::STATUS_AVAILABLE;
        }

        $up      = 0;
        $worst   = null;
        $rank    = PHP_INT_MAX;
        $pending = false;

        foreach ($expected as $status) {
            if (self::isUp($status)) {
                $up++;
            }

            if (self::isTransitional($status)) {
                $pending = true;
            }

            $current = self::getSeverity($status);
            if ($current < $rank) {
                $rank  = $current;
                $worst = $status;
            }
        }

        $count = count($expected);

        if ($up === $count) {
            return self::STATUS_RUNNING;
        }

        // A collector failure or a missing service is a fact the user has to
        // see. It outranks the softer "partly up" reading, so it is surfaced
        // rather than averaged away into STATUS_PARTIAL_STACK.
        $actionable = self::getSeverity($worst ?? self::STATUS_UNKNOWN) < self::getSeverity(self::STATUS_STOPPED);
        if ($actionable) {
            return $worst;
        }

        if ($up === 0) {
            // Nothing is up. If a transition is in flight the stack is on its
            // way up rather than down, so report that instead of "stopped".
            if ($pending && $worst === self::STATUS_STOPPED) {
                return self::STATUS_STARTING;
            }

            return $worst ?? self::STATUS_UNKNOWN;
        }

        // Some up, some not. A pending peer is expected to join imminently.
        if ($pending && $worst === self::STATUS_STOPPED) {
            return self::STATUS_STARTING;
        }

        return self::STATUS_PARTIAL_STACK;
    }

    /**
     * Combines a lifecycle status and a health result into a badge class.
     *
     * This is the single place where the two dimensions are allowed to meet for
     * presentation. Keeping the combination here (rather than inside the
     * collector) is what stops a blocked port from being reported as a stopped
     * service, while still showing the user that something is wrong.
     *
     * @param   string  $status  A STATUS_* constant.
     * @param   string  $health  A HEALTH_* constant.
     *
     * @return string A Bootstrap background utility class.
     */
    public static function getCombinedBadgeClass(string $status, string $health): string
    {
        if (!self::isUp($status)) {
            return self::getBadgeClass($status);
        }

        switch ($health) {
            case self::HEALTH_OK:
            case self::HEALTH_NOT_APPLICABLE:
                return self::getBadgeClass($status);

            case self::HEALTH_PARTIAL:
                return 'bg-warning';

            case self::HEALTH_UNREACHABLE:
                return 'bg-danger';
        }

        return 'bg-secondary';
    }
}
