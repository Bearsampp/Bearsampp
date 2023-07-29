$(document).ready(function() {
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
});
