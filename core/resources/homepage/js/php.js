/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * @fileoverview PHP service status display for the Bearsampp homepage.
 * Sets up a StatusFetcher to keep the PHP status, version list, extension count,
 * PEAR version and extension list up to date.
 */

// PHP status fetcher with multiple fields
// Maps 'versions' data key to 'version-list' selector
createStatusFetcher('php', [
	'status',
	{data: 'versions', selector: 'version-list'},
	'extscount',
	'pearversion',
	'extslist'
]);
