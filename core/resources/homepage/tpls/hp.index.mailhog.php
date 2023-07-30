<a class="anchor" name="mailhog"></a>
<div class="row-fluid">
  <div class="col-lg-12">
    <h1><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/mailhog.png'; ?>" /> <?php echo $bearsamppLang->getValue(Lang::MAILHOG); ?> <small></small></h1>
  </div>
</div>
<div class="row-fluid">
  <div class="col-lg-6">
    <div class="list-group">
      <span class="list-group-item mailhog-checkport">
        <span class="loader" style="float:right"><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        <i class="fa fa-bar-chart-o"></i> <?php echo $bearsamppLang->getValue(Lang::STATUS); ?>
      </span>
      <span class="list-group-item mailhog-versions">
              <span class="label-left col-1">
                <i class="fa fa-puzzle-piece"></i> <?php echo $bearsamppLang->getValue(Lang::VERSIONS); ?>
              </span>
              <span class="mailhog-version-list float-right col-11">
                <span class="loader" style="float:right">
                  <img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>"/>
                </span>
              </span>
      </span>
      <span class="list-group-item">
        <i class="fa fa-info-circle"></i> <a href="<?php echo $bearsamppRoot->getLocalUrl() . ':' . $bearsamppBins->getMailhog()->getUiPort(); ?>" target="_blank"><?php echo $bearsamppLang->getValue(Lang::HOMEPAGE_MAILHOG_TEXT); ?></a>
      </span>
    </div>
  </div>
</div>
