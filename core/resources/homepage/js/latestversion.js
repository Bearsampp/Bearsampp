/*$(document).ready(function() {
  if ($('.latestversion').length) {
    $.ajax({
      data: {
        proc: 'latestversion'
      },
      success: function(data) {
        if (data.display) {
          $('.latestversion-download').append(data.download);
          $('.latestversion-changelog').append(data.changelog);
          $('.latestversion').show();
        }
      }
    });
  }
});*/

async function getLatestVersionStatus() {
  const url = ajax_url;
  let data = new URLSearchParams();
  const proc = 'latestversion';
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
    if (data.display) {
      let q = document.querySelector('.latestversion-download');
      q.insertAdjacentHTML('beforeend',data.download);

      q = document.querySelector('.latestversion-changelog');
      q.insertAdjacentHTML('beforeend',data.changelog);

      q = document.querySelector('.latestversion');
      q.style.display = 'block';
    }

  }
}
document.addEventListener("DOMContentLoaded", function() {
  if (document.querySelector('.latestversion').name === 'latestversion') {
    getLatestVersionStatus();
  }
})

