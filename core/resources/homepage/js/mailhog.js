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

