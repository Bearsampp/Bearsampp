/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * @fileoverview MySQL service status display for the Bearsampp homepage.
 * Sets up a StatusFetcher to keep the MySQL check port and version list up to date.
 */

// MySQL status fetcher with custom validation for mysqli_sql_exception
// Maps 'versions' data key to 'version-list' selector
createStatusFetcher('mysql', [
  'checkport',
  { data: 'versions', selector: 'version-list' }
]);
