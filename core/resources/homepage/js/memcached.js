$(document).ready(function() {
  if ($('a[name=memcached]').length) {
    $.ajax({
      data: {
        proc: 'memcached'
      },
      success: function(data) {
        $('.memcached-checkport').append(data.checkport);
        $('.memcached-checkport').find('.loader').remove();

        $('.memcached-version-list').append(data.versions);
        $('.memcached-version-list').find('.loader').remove();
      }
    });
  }
});
