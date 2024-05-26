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
  const url = AJAX_URL;
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
            if(myajaxresponse.includes("Uncaught mysqli_sql_exception")) {
                console.log("Error occured accessing MySQL - ");
            } else {
      data = JSON.parse(myajaxresponse);
    let q = document.querySelector('.mysql-checkport');
    let ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.checkport);

    q = document.querySelector('.mysql-version-list');
    ql = q.querySelector('.loader');
    ql.remove();
    q.insertAdjacentHTML('beforeend', data.versions);
  }
        } catch (error) {
            console.error('Failed to parse response:', error);
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector('a[name=mysql]').name === 'mysql') {
    getMySQLStatus();
  }
})
