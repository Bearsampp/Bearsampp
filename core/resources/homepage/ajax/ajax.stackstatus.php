<?php
/*
 * Copyright (c) 2026 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * AJAX handler that returns the current status of every bin together with its
 * per-service resource footprint.
 *
 * The payload is the raw array produced by StatusSnapshot::captureForWeb(), which
 * combines three independent sources:
 *
 *   1. the Windows SCM, queried once for the state and PID of all services;
 *   2. a short TCP liveness probe against each running service's ports;
 *   3. ProcessFootprint, which attributes working set and CPU to a service by
 *      walking the process tree from the SCM PID.
 *
 * This handler runs under the user's PHP, which has no COM, so the collection
 * itself happens in the internal engine; see StatusSnapshot::captureForWeb().
 *
 * Step 3 is what makes this endpoint different from the other homepage polls:
 * the response changes on almost every call (memory and CPU always move), so
 * callers must treat it as live telemetry rather than as state to diff.
 *
 * Responds with a JSON object and exits.
 */
header('Content-Type: application/json');
header('Cache-Control: no-store');

global $bearsamppBins;

$snapshot = StatusSnapshot::captureForWeb($bearsamppBins);

// Only a fallback: the collection stamps this itself so that a snapshot served
// from the web cache reports when it was actually taken, not when it was asked
// for. The client uses it for the "updated" label.
if (!isset($snapshot['generatedAt'])) {
    $snapshot['generatedAt'] = time();
}

echo json_encode($snapshot);
exit;
