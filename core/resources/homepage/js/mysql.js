/*$(document).ready(function() {
  if ($('a[name=mysql]').length) {
    $.ajax({
      data: {
        proc: 'mysql'
      },
      success: function(data) {
        $('.mysql-checkport').append(data.checkport);
        $('.mysql-checkport').find('.loader').remove();

        $('.mysql-version-list').append(data.versions);
        $('.mysql-version-list').find('.loader').remove();
      }
    });
  }
});*/

async function getMySQLStatus() {
  const url = ajax_url;
  const proc = 'mysql';
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

    let q = document.querySelector('.mysql-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.checkport);

    q = document.querySelector('.mysql-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=mysql]').name === 'mysql') {
    getMySQLStatus();
  }
})
