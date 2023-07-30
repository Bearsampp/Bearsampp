$(document).ready(function() {
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
});
