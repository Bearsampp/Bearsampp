/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

// MySQL status fetcher with custom validation for mysqli_sql_exception
// Maps 'versions' data key to 'version-list' selector
createStatusFetcher('mysql', [
  'checkport',
  { data: 'versions', selector: 'version-list' }
], {
  responseValidator: (responseText) => {
    if (responseText.includes("Uncaught mysqli_sql_exception")) {
      return {
        valid: false,
        message: "Error occurred accessing MySQL"
      };
    }
    return { valid: true };
  }
});
