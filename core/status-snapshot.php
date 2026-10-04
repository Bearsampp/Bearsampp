<?php
/*
 *
 *  * Copyright (c) 2021-2026 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * CLI collector for the status snapshot.
 *
 * The homepage runs on the user's PHP runtime (bin/php/php<version>), which does
 * not load com_dotnet, so it cannot reach the SCM or the process table directly.
 * This script is the other half of that split: it is executed by the internal
 * engine (core/libs/php), which does have COM, and it prints the snapshot as
 * JSON on stdout for the web tier to decode.
 *
 * Two invariants matter here:
 *
 *   1. Nothing but the JSON may reach stdout, because the caller parses stdout
 *      verbatim. Diagnostics go to stderr.
 *   2. This file must never be served over HTTP. It lives in core/ rather than
 *      under core/resources/homepage so that it is outside the Apache alias and
 *      the DocumentRoot, and the CLI guard below is a second line of defence.
 *
 * @see StatusSnapshot::captureForWeb() for the calling side.
 */

// Refuse to run under any web SAPI. core/ is not web-reachable today, so this
// only fires if the alias or the DocumentRoot is ever widened to cover it.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain');

    exit('This script may only be run from the command line.');
}

// stdout is the JSON payload and the caller parses it verbatim, so PHP's own
// diagnostics must not be able to land there: a notice printed mid-capture would
// prepend itself to the JSON and turn a good snapshot into an undecodable
// response. Sending them to stderr keeps stdout parseable no matter what fails,
// and the web tier discards stderr anyway.
ini_set('display_errors', 'stderr');
ini_set('log_errors', '0');

// Enter silent mode *before* bootstrapping, which is the only ordering that
// works: root.php logs TRACE lines while it initialises, and at TRACE verbosity
// every entry is flushed the moment it is written, so anything buffered first
// has already reached disk by the time the collector itself starts running.
// Log is loaded on its own first because root.php has not run yet.
require_once __DIR__ . '/classes/class.log.php';

Log::startSilentBuffer();

require_once __DIR__ . '/root.php';

global $bearsamppBins;

try {
    echo json_encode(StatusSnapshot::capture($bearsamppBins));
} catch (Throwable $e) {
    // Logged before the rollback below, because rollback discards the silent
    // buffer. The message can contain local paths, which is acceptable here: the
    // log stays on this machine and the web tier discards stderr anyway. This
    // only runs on an exceptional failure, not on a routine collection error,
    // which StatusSnapshot reports through the 'error' key instead.
    Log::error('Status snapshot collection failed: ' . $e->getMessage());

    // Still emit valid JSON: the caller decodes unconditionally and treats a
    // missing 'error' key as success.
    echo json_encode([
        'error' => $e->getMessage(),
    ]);
}

// From here on this process is a data source, not an interactive session: a
// successful collection is noise in a log that a dashboard reads constantly, so
// the buffer is discarded. A failure logged above survives that discard, because
// Log::error() is flushed immediately even in silent mode.
Log::rollbackSilentBuffer();
