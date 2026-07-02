/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
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
