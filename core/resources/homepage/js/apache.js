$(document).ready(function() {
  if ($('a[name=apache]').length) {
    $.ajax({
      data: {
        proc: 'apache'
      },
      success: function(data) {
        $('.apache-checkport').append(data.checkport);
        $('.apache-checkport').find('.loader').remove();

        $('.apache-version-list').append(data.versions);
        $('.apache-version-list').find('.loader').remove();

        $('.apache-modulescount').append(data.modulescount);
        $('.apache-modulescount').find('.loader').remove();

        $('.apache-aliasescount').append(data.aliasescount);
        $('.apache-aliasescount').find('.loader').remove();

        $('.apache-vhostscount').append(data.vhostscount);
        $('.apache-vhostscount').find('.loader').remove();

        $('.apache-moduleslist').append(data.moduleslist);
        $('.apache-moduleslist').find('.loader').remove();

        $('.apache-aliaseslist').append(data.aliaseslist);
        $('.apache-aliaseslist').find('.loader').remove();

        $('.apache-wwwdirectory').append(data.wwwdirectory);
        $('.apache-wwwdirectory').find('.loader').remove();

        $('.apache-vhostslist').append(data.vhostslist);
        $('.apache-vhostslist').find('.loader').remove();
      }
    });
  }
});
