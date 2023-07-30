$(document).ready(function() {
  if ($('a[name=mariadb]').length) {
    $.ajax({
      data: {
        proc: 'mariadb'
      },
      success: function(data) {
        $('.mariadb-checkport').append(data.checkport);
        $('.mariadb-checkport').find('.loader').remove();

        $('.mariadb-version-list').append(data.versions);
        $('.mariadb-version-list').find('.loader').remove();
      }
    });
  }
});
