/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

async function getMailpitStatus() {
  const url = AJAX_URL;
  const proc = 'mailpit';
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
    console.log(myajaxresponse);
    let data;
try {
  data = JSON.parse(myajaxresponse);
} catch (error) {
  console.error('Failed to parse response:', error);
}

    let q = document.querySelector('.mailpit-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.checkport);

    q = document.querySelector('.mailpit-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.versions);
  }
}
document.addEventListener("DOMContentLoaded", function() {
  if (document.querySelector('a[name=mailpit]').name === 'mailpit') {
    getMailpitStatus();
  }
})
