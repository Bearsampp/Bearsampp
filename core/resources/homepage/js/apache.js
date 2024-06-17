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
    }
    let q = document.querySelector('.apache-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.checkport);

    q = document.querySelector('.apache-versions');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);

    q = document.querySelector('.apache-modulescount');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.modulescount);

    q = document.querySelector('.apache-aliasescount');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.aliasescount);

    q = document.querySelector('.apache-vhostscount');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.vhostscount);

    q = document.querySelector('.apache-moduleslist');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.moduleslist);

    q = document.querySelector('.apache-aliaseslist');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.aliaseslist);

    q = document.querySelector('.apache-wwwdirectory');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.wwwdirectory);

    q = document.querySelector('.apache-vhostslist');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.vhostslist);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=apache]').name === 'apache') {
    getApacheStatus();
  }
})
