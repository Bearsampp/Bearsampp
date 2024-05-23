/*$(document).ready(function() {
  if ($('a[name=nodejs]').length) {
    $.ajax({
      data: {
        proc: 'nodejs'
      },
      success: function(data) {
        $('.nodejs-status').append(data.status);
        $('.nodejs-status').find('.loader').remove();

        $('.nodejs-version-list').append(data.versions);
        $('.nodejs-version-list').find('.loader').remove();
      }
    });
  }
});*/

async function getNodeJSStatus() {
  const url = ajax_url;
  const proc = 'nodejs';
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

    let q = document.querySelector('.nodejs-status');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.status);

    q = document.querySelector('.nodejs-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=nodejs]').name === 'nodejs') {
    getNodeJSStatus();
  }
})
