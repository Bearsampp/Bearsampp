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
    const url = AJAX_URL; // Ensure this variable is defined and points to your server-side script handling the AJAX requests.
    let data = new URLSearchParams();
    data.append('proc', 'latestversion'); // Setting 'proc' to 'latestversion'

    const options = {
        method: 'POST',
        body: data
    };

    try {
        let response = await fetch(url, options);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        let responseData = await response.json(); // Assuming the server responds with JSON data

        if (responseData.display) {
            document.querySelector('.latestversion-download').insertAdjacentHTML('beforeend', responseData.download);
            document.querySelector('.latestversion-changelog').insertAdjacentHTML('beforeend', responseData.changelog);
            document.getElementById("latestversionnotify").style.display = 'block';
        }
    } catch (error) {
        console.error('Failed to fetch latest version status:', error);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    getLatestVersionStatus();
});
