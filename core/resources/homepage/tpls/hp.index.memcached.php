<a class="anchor" name="memcached"></a>
<div class="row-fluid">
  <div class="col-lg-12">
    <h1><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/memcached.png'; ?>" /> <?php echo $bearsamppLang->getValue(Lang::MEMCACHED); ?> <small></small></h1>
  </div>
</div>
<div class="row-fluid">
  <div class="col-lg-6">
    <div class="list-group">
      <span class="list-group-item memcached-checkport">
        <span class="loader" style="float:right"><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        <i class="fa fa-bar-chart-o"></i> <?php echo $bearsamppLang->getValue(Lang::STATUS); ?>
      </span>
      <span class="list-group-item memcached-versions">
              <span class="label-left col-1">
                <i class="fa fa-puzzle-piece"></i> <?php echo $bearsamppLang->getValue(Lang::VERSIONS); ?>
              </span>
              <span class="memcached-version-list float-right col-11">
                <span class="loader" style="float:right">
                  <img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>"/>
                </span>
              </span>
      </span>
    </div>
  </div>
</div>
