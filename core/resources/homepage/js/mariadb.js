/*$(document).ready(function() {
  if ($('a[name=mariadb]').length) {
    $.ajax({
      data: {
        proc: 'mariadb'
      },
      success: function(data) {
        $('.mariadb-checkport').append(data.checkport);
        $('.mariadb-checkport').find('.loader').remove();

        $('.mariadb-version-list').append(data.versions);
        $('.mariadb-version-list').find('.loader').remove();
      }
    });
  }
});*/

async function getMariaDBStatus() {
  const url = ajax_url;
  const proc = 'mariadb';
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

    let q = document.querySelector('.mariadb-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.checkport);

    q = document.querySelector('.mariadb-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=mariadb]').name === 'mariadb') {
    getMariaDBStatus();
  }
})
