$(document).ready(function() {
  if ($('a[name=filezilla]').length) {
    $.ajax({
      data: {
        proc: 'filezilla'
      },
      success: function(data) {
        $('.filezilla-checkport').append(data.checkport);
        $('.filezilla-checkport').find('.loader').remove();

        $('.filezilla-version-list').append(data.versions);
        $('.filezilla-version-list').find('.loader').remove();
      }
    });
  }
});
