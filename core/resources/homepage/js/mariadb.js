/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * @fileoverview MariaDB service status display for the Bearsampp homepage.
 * Sets up a StatusFetcher to keep the MariaDB check port and version list up to date.
 */

// MariaDB status fetcher
// Maps 'versions' data key to 'version-list' selector
createStatusFetcher('mariadb', [
  'checkport',
  { data: 'versions', selector: 'version-list' }
]);
