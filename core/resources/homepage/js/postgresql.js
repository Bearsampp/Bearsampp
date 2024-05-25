/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/*$(document).ready(function() {
  if ($('a[name=postgresql]').length) {
    $.ajax({
      data: {
        proc: 'postgresql'
      },
      success: function(data) {
        $('.postgresql-checkport').append(data.checkport);
        $('.postgresql-checkport').find('.loader').remove();

        $('.postgresql-version-list').append(data.versions);
        $('.postgresql-version-list').find('.loader').remove();
      }
    });
  }
});*/
/**
 * Asynchronous function to fetch PostgreSQL status using AJAX.
 * It sends a POST request to the specified URL with process 'postgresql'.
 * If successful, it parses the response as JSON and updates the DOM with the received data.
 * If there is an error in fetching or parsing the response, appropriate error messages are logged.
 * This function is triggered on DOMContentLoaded event if an anchor tag with name 'postgresql' is present.
 */
async function getPostgresStatus() {
  const url = ajax_url;
  const proc = 'postgresql';
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

    let q = document.querySelector('.postgresql-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.checkport);

    q = document.querySelector('.postgresql-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=postgresql]').name === 'postgresql') {
    getPostgresStatus();
  }
})
