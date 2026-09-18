/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * @fileoverview Apache service status display for the Bearsampp homepage.
 * Sets up a StatusFetcher for the Apache module to keep the check port,
 * installed versions, module/alias/vhost counts and lists up to date.
 */

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=apache]') || document.getElementById('apache')) {
    createStatusFetcher('apache', [
      'checkport',
      { data: 'versions', selector: 'version-list' },
      'modulescount',
      'aliasescount',
      'vhostscount',
      'moduleslist',
      'aliaseslist',
      'wwwdirectory',
      'vhostslist'
    ]);
  }
});
