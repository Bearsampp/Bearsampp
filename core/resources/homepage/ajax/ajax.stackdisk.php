<?php
/*
 * Copyright (c) 2026 Bearsampp
 * License: GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * AJAX handler that returns the on-disk size of the install, broken down by part.
 *
 * This endpoint is opt-in, unlike the other homepage polls. Measuring the install
 * costs about 7s, against roughly 270ms for a full status snapshot, so it is only
 * called when the user asks for it and is then served from a cache for
 * DISK_USAGE::CACHE_TTL seconds. The page loads the drive's free space from the
 * ordinary snapshot instead, which costs nothing.
 *
 * Because a forced refresh can start that walk on demand, this proc is listed in
 * ajax.php's CSRF-protected endpoints and is POST-only: it is reached through
 * fetchWithCsrf() like the state-changing actions. DiskUsage additionally
 * serialises walks with a lock and rate limits forced ones, so even a caller
 * holding a valid token cannot turn repeated requests into repeated full scans.
 *
 * The response carries 'cached' and 'elapsedMs' so the page can be honest about
 * whether the numbers were just measured or reused, and 'generatedAt' so a stale
 * figure can be labelled as stale rather than presented as current.
 *
 * Responds with a JSON object and exits.
 */
header('Content-Type: application/json');
header('Cache-Control: no-store');

// The flags arrive by POST alongside 'proc'. GET is deliberately not accepted:
// a forced refresh is the expensive request here, since it starts a full disk
// walk, so it has to travel the same CSRF-validated POST path as every other
// action that costs real work. ajax.php refuses any non-POST call to this proc
// before it gets here, and reading only POST keeps a future caller from
// reintroducing a token-less route to a forced scan. The page already posts
// through fetchWithCsrf(), so nothing legitimate depends on the GET form.
$refresh    = isset($_POST['refresh']) ? $_POST['refresh'] : '';
$cachedOnly = isset($_POST['cached']) ? $_POST['cached'] : '';

// 'cached' asks for a reading without authorising the walk that produces one.
// The page uses it on load, so that arriving at the status page never costs the
// several seconds a fresh measurement takes on a first visit.
echo json_encode(DiskUsage::captureForWeb($refresh === '1', $cachedOnly !== '1'));
exit;
