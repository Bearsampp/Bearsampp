/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * @fileoverview Memcached service status display for the Bearsampp homepage.
 * Sets up a StatusFetcher to keep the Memcached check port and version list up to date.
 */

// Memcached status fetcher
// Maps 'versions' data key to 'version-list' selector
createStatusFetcher('memcached', [
  'checkport',
  { data: 'versions', selector: 'version-list' }
]);
