/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * @fileoverview NodeJS service status display for the Bearsampp homepage.
 * Sets up a StatusFetcher to keep the NodeJS status and version list up to date.
 */

// NodeJS status fetcher (uses 'status' field instead of 'checkport')
// Maps 'versions' data key to 'version-list' selector
createStatusFetcher('nodejs', [
  'status',
  { data: 'versions', selector: 'version-list' }
]);
