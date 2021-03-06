<div class="alert alert-dismissable alert-success no-display latestversion">
    <button data-dismiss="alert" class="close" type="button">×</button>
    <h4><?php echo $bearsamppLang->getValue(Lang::CHECK_VERSION_AVAILABLE_TEXT); ?></h4>
    <div class="latestversion-download"></div>
    <div><?php echo sprintf($bearsamppLang->getValue(Lang::READ_CHANGELOG), '<a href="#" data-toggle="modal" data-target=".modal-changelog">', '</a>'); ?></div>
</div>
<div class="modal fade modal-changelog" tabindex="-1" role="dialog" aria-labelledby="modal-changelog-title" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="modal-changelog-title"><?php echo $bearsamppLang->getValue(Lang::CHANGELOG); ?></h4>
      </div>
      <div class="modal-body latestversion-changelog"></div>
    </div>
  </div>
</div>
