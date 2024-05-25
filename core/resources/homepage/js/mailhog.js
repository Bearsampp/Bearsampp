/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/*$(document).ready(function() {
  if ($('a[name=mailhog]').length) {
    $.ajax({
      data: {
        proc: 'mailhog'
      },
      success: function(data) {
        $('.mailhog-checkport').append(data.checkport);
        $('.mailhog-checkport').find('.loader').remove();

        $('.mailhog-version-list').append(data.versions);
        $('.mailhog-version-list').find('.loader').remove();
      }
    });
  }
});*/
/**
 * Asynchronous function to fetch MailHog status using AJAX.
 * It sends a POST request to the specified URL with 'proc' parameter as 'mailhog'.
 * If the response is successful, it parses the JSON response and updates the DOM with the received data.
 * It removes loader elements before inserting the data into the respective elements.
 * This function is triggered when the document content is loaded and if there is an anchor element with name 'mailhog'.
 */
async function getMailHogStatus() {
  const url = ajax_url;
  let data = new URLSearchParams();
  const proc = 'mailhog';
  data.append(`proc`, proc);
  const options = {
    method: 'POST',
    body: data
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

    let q = document.querySelector('.mailhog-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.checkport);

    q = document.querySelector('.mailhog-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend',data.versions);
  }
}
document.addEventListener("DOMContentLoaded", function() {
  if (document.querySelector('a[name=mailhog]').name === 'mailhog') {
    getMailHogStatus();
  }
})
