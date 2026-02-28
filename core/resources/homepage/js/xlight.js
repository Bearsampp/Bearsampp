/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

// Xlight status fetcher
// Maps 'versions' data key to 'version-list' selector
createStatusFetcher('xlight', [
  'checkport',
  { data: 'versions', selector: 'version-list' }
]);
