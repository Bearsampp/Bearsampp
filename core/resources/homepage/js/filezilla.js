/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/*$(document).ready(function() {
  if ($('a[name=filezilla]').length) {
    $.ajax({
      data: {
        proc: 'filezilla'
      },
      success: function(data) {
        $('.filezilla-checkport').append(data.checkport);
        $('.filezilla-checkport').find('.loader').remove();

        $('.filezilla-version-list').append(data.versions);
        $('.filezilla-version-list').find('.loader').remove();
      }
    });
  }
});*/
/**
 * Asynchronous function to fetch FileZilla status using AJAX.
 * It sends a POST request to the specified URL with process 'filezilla'.
 * Handles the response and updates the DOM with the received data.
 * It removes loaders and appends the checkport and versions to respective elements.
 * This function is triggered on DOMContentLoaded event if an anchor element with name 'filezilla' exists.
 */
async function getFileZillaStatus() {
  const url = ajax_url;
  const proc = 'filezilla';
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

    let q = document.querySelector('.filezilla-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.checkport);

    q = document.querySelector('.filezilla-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=filezilla]').name === 'filezilla') {
    getFileZillaStatus();
  }
})
