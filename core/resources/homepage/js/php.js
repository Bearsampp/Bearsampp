/*$(document).ready(function() {
  if ($('a[name=php]').length) {
    $.ajax({
      data: {
        proc: 'php'
      },
      success: function(data) {
        $('.php-status').append(data.status);
        $('.php-status').find('.loader').remove();

        $('.php-version-list').append(data.versions);
        $('.php-version-list').find('.loader').remove();

        $('.php-extscount').append(data.extscount);
        $('.php-extscount').find('.loader').remove();

        $('.php-pearversion').append(data.pearversion);
        $('.php-pearversion').find('.loader').remove();

        $('.php-extslist').append(data.extslist);
        $('.php-extslist').find('.loader').remove();
      }
    });
  }
});*/

async function getPHPStatus() {
  const url = AJAX_URL;
  const proc = 'php';
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

    let q = document.querySelector('.php-status');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.status);

    q = document.querySelector('.php-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);

    q = document.querySelector('.php-extscount');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.extscount);

    q = document.querySelector('.php-pearversion');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.pearversion);

    q = document.querySelector('.php-extslist');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.extslist);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=php]').name === 'php') {
    getPHPStatus();
  }
})
