/*$(document).ready(function() {
  if ($('a[name=apache]').length) {
    $.ajax({
      data: {
        proc: 'apache'
      },
      success: function(data) {
        console.log(data);
        $('.apache-checkport').append(data.checkport);
        $('.apache-checkport').find('.loader').remove();

        $('.apache-version-list').append(data.versions);
        $('.apache-version-list').find('.loader').remove();

        $('.apache-modulescount').append(data.modulescount);
        $('.apache-modulescount').find('.loader').remove();

        $('.apache-aliasescount').append(data.aliasescount);
        $('.apache-aliasescount').find('.loader').remove();

        $('.apache-vhostscount').append(data.vhostscount);
        $('.apache-vhostscount').find('.loader').remove();

        $('.apache-moduleslist').append(data.moduleslist);
        $('.apache-moduleslist').find('.loader').remove();

        $('.apache-aliaseslist').append(data.aliaseslist);
        $('.apache-aliaseslist').find('.loader').remove();

        $('.apache-wwwdirectory').append(data.wwwdirectory);
        $('.apache-wwwdirectory').find('.loader').remove();

        $('.apache-vhostslist').append(data.vhostslist);
        $('.apache-vhostslist').find('.loader').remove();
      }
    });
  }
}); */

async function getApacheStatus() {
  const url = ajax_url;
  const proc = 'apache';
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
    let q = document.querySelector('.apache-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.checkport);

    q = document.querySelector('.apache-versions');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);

    q = document.querySelector('.apache-modulescount');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.modulescount);

    q = document.querySelector('.apache-aliasescount');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.aliasescount);

    q = document.querySelector('.apache-vhostscount');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.vhostscount);

    q = document.querySelector('.apache-moduleslist');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.moduleslist);

    q = document.querySelector('.apache-aliaseslist');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.aliaseslist);

    q = document.querySelector('.apache-wwwdirectory');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.wwwdirectory);

    q = document.querySelector('.apache-vhostslist');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.vhostslist);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=apache]').name === 'apache') {
    getApacheStatus();
  }
})
