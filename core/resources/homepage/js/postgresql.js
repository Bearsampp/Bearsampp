$(document).ready(function() {
  if ($('a[name=postgresql]').length) {
    $.ajax({
      data: {
        proc: 'postgresql'
      },
      success: function(data) {
        $('.postgresql-checkport').append(data.checkport);
        $('.postgresql-checkport').find('.loader').remove();

        $('.postgresql-version-list').append(data.versions);
        $('.postgresql-version-list').find('.loader').remove();
      }
    });
  }
});
