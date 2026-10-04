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
 * CLI collector for the on-disk size of the Bearsampp install.
 *
 * This is the disk-space half of status-disk-usage.ps1. It exists as a separate
 * script because the measurement is far too slow to run inside a page load: a
 * 230k file install takes about 7s to walk, against roughly 270ms for a full
 * status snapshot. Callers must therefore treat it as an explicit, cached query
 * rather than as part of the homepage poll. See DiskUsage::captureForWeb().
 *
 * The traversal itself lives in PowerShell. That is a deliberate choice rather
 * than an accident of convenience:
 *
 *   1. In PHP, stat-ing every file to read its size costs around 32s for the
 *      same tree. Traversing the directories without reading sizes costs 1.4s,
 *      so the size calls are the whole cost and no amount of restructuring in
 *      PHP avoids them.
 *   2. cmd's dir /s would be faster still, but it follows directory junctions
 *      and reported 26.34 GB against the true 18.86 GB by counting the version
 *      symlinks twice.
 *   3. Get-ChildItem -Recurse does not follow them, which is what the .ps1
 *      relies on.
 *
 * Two invariants match status-snapshot.php: stdout carries the JSON and nothing
 * else, and this file is never reachable over HTTP because it lives in core/
 * rather than under the Apache alias.
 *
 * @see DiskUsage::captureForWeb() for the calling side.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain');

    exit('This script may only be run from the command line.');
}

// Same ordering constraint as status-snapshot.php: a notice printed mid-walk
// would prepend itself to the JSON and turn a good measurement into an
// undecodable one, so PHP's own diagnostics are diverted to stderr.
ini_set('display_errors', 'stderr');
ini_set('log_errors', '0');

// Log is required on its own first, before root.php, because entering silent mode
// has to happen before root.php logs its TRACE bootstrap lines: at TRACE
// verbosity each entry is flushed as it is written, so anything buffered first
// has already reached disk.
require_once __DIR__ . '/classes/class.log.php';

Log::startSilentBuffer();

require_once __DIR__ . '/root.php';

/**
 * Locates the PowerShell interpreter that performs the walk.
 *
 * The bundled Path helper resolves Windows PowerShell 5.1, which is what the
 * traversal was measured on and what supplies the non-symlink-following
 * behaviour. PowerShell 7 is accepted as a fallback because a machine without
 * 5.1 is unusual but not impossible, and 7 behaves the same for this script.
 *
 * @return string|null Absolute path to the interpreter, or null when none is found.
 */
function bearsamppDiskUsagePowerShell()
{
    $candidates = [Path::getPowerShellPath()];

    // The bundled PowerShell 7 install, when the user has one, is a reasonable
    // second choice: it is guaranteed to exist in the install being measured.
    $bundled = Path::getToolsPath() . '/powershell/current/pwsh.exe';
    if (is_file($bundled)) {
        $candidates[] = $bundled;
    }

    foreach ($candidates as $candidate) {
        if (is_string($candidate) && $candidate !== '' && is_file($candidate)) {
            return $candidate;
        }
    }

    return null;
}

try {
    $root   = Path::getRootPath();
    $script = __DIR__ . '/status-disk-usage.ps1';
    $shell  = bearsamppDiskUsagePowerShell();

    if (!is_file($script)) {
        throw new RuntimeException('The disk usage script is missing: ' . $script);
    }

    if ($shell === null) {
        throw new RuntimeException('No PowerShell interpreter was found to measure the install with.');
    }

    // A generous ceiling. The measurement is dominated by a single slow
    // traversal, so a timeout here would truncate the walk rather than produce
    // a smaller but valid answer. scriptsTimeout in bearsampp.conf is 120s and
    // the worst case measured here is well under half of that.
    $command = escapeshellarg($shell)
        . ' -NoProfile -NonInteractive -ExecutionPolicy Bypass -File ' . escapeshellarg($script)
        . ' -Root ' . escapeshellarg($root)
        . ' 2>NUL';

    $output = shell_exec($command);

    if (!is_string($output) || trim($output) === '') {
        throw new RuntimeException('The disk usage script produced no output.');
    }

    $decoded = json_decode(trim($output), true);

    if (!is_array($decoded) || !isset($decoded['total'], $decoded['parts'])) {
        throw new RuntimeException('The disk usage script returned unusable output.');
    }

    echo json_encode([
        'root'        => $root,
        'parts'       => $decoded['parts'],
        'total'       => $decoded['total'],
        'generatedAt' => time(),
    ]);
} catch (Throwable $e) {
    // Logged before the rollback below, because rollback discards the silent
    // buffer. This path is exceptional: a routine failure surfaces as the
    // 'error' key below rather than as an exception.
    Log::error('Disk usage collection failed: ' . $e->getMessage());

    // The caller decodes unconditionally, so the failure still has to be valid
    // JSON.
    echo json_encode([
        'error' => $e->getMessage(),
    ]);
}

// As in status-snapshot.php: a successful measurement is noise in a log that a
// dashboard reads constantly, so the buffer is discarded. A failure logged above
// survives, because Log::error() flushes immediately even in silent mode.
Log::rollbackSilentBuffer();
