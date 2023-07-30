$(document).ready(function() {
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
});
