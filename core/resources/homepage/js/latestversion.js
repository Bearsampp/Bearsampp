/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

createStatusFetcher('latestversion', [], {
  customUpdater: (responseData) => {
    if (responseData.display) {
      const downloadEl = document.querySelector('.latestversion-download');
      const changelogEl = document.querySelector('.latestversion-changelog');
      const notifyEl = document.getElementById("latestversionnotify");

      if (downloadEl) {
          downloadEl.innerHTML = responseData.download;
      }
      if (changelogEl) {
          changelogEl.innerHTML = responseData.changelog;
      }
      if (notifyEl) notifyEl.style.display = 'block';
    }
  }
});
