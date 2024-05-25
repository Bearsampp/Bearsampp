/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/*$(document).ready(function() {
  if ($('a[name=memcached]').length) {
    $.ajax({
      data: {
        proc: 'memcached'
      },
      success: function(data) {
        $('.memcached-checkport').append(data.checkport);
        $('.memcached-checkport').find('.loader').remove();

        $('.memcached-version-list').append(data.versions);
        $('.memcached-version-list').find('.loader').remove();
      }
    });
  }
});*/
/**
 * Asynchronous function to get MemCached status by sending a POST request to the specified URL.
 * If the response is successful, it parses the JSON response and updates the DOM elements with the received data.
 * It removes loader elements after updating the content.
 *
 * This function is triggered on DOMContentLoaded event if an anchor element with name 'memcached' exists.
 */
async function getMemCachedStatus() {
  const url = ajax_url;
  const proc = 'memcached';
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

    let q = document.querySelector('.memcached-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.checkport);

    q = document.querySelector('.memcached-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=memcached]').name === 'memcached') {
    getMemCachedStatus();
  }
})
