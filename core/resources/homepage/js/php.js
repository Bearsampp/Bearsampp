/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

// PHP status fetcher with multiple fields
// Maps 'versions' data key to 'version-list' selector
createStatusFetcher('php', [
  'status',
  { data: 'versions', selector: 'version-list' },
  'extscount',
  'pearversion',
  'extslist'
]);
