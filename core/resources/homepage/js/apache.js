/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

async function getApacheStatus() {
  const url = AJAX_URL;
  const proc = 'apache';
  const senddata = new URLSearchParams();
  senddata.append(`proc`, proc);
  const options = {
    method: 'POST',
    body: senddata
  }
  let response = await fetch(url, options);
  if (!response.ok) {
    console.log('Error receiving from ajax.php');
  } else {
    let myajaxresponse = await response.text();
    let data;
    try {
      data = JSON.parse(myajaxresponse);
    } catch (error) {
      console.error('Failed to parse response:', error);
      return;
    }

    const updateElement = (selector, content) => {
      const element = document.querySelector(selector);
      if (element) {
        const loader = element.querySelector('.loader');
        if (loader) loader.remove();
        element.insertAdjacentHTML('beforeend', content);
      } else {
        console.warn(`Element not found: ${selector}`);
      }
    };

    updateElement('.apache-checkport', data.checkport);
    updateElement('.apache-version-list', data.versions);
    updateElement('.apache-modulescount', data.modulescount);
    updateElement('.apache-aliasescount', data.aliasescount);
    updateElement('.apache-vhostscount', data.vhostscount);
    updateElement('.apache-moduleslist', data.moduleslist);
    updateElement('.apache-aliaseslist', data.aliaseslist);
    updateElement('.apache-wwwdirectory', data.wwwdirectory);
    updateElement('.apache-vhostslist', data.vhostslist);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=apache]').name === 'apache') {
    getApacheStatus();
  }
});
