<a class="anchor" name="postgresql"></a>
<div class="row">
  <div class="col-lg-12">
    <h1><img src="<?php echo $neardHomepage->getResourcesPath() . '/img/postgresql.png'; ?>" /> <?php echo $neardLang->getValue(Lang::POSTGRESQL); ?> <small></small></h1>
  </div>
</div>
<div class="row">
  <div class="col-lg-6">
    <div class="list-group">
      <span class="list-group-item postgresql-checkport">
        <span class="loader" style="float:right"><img src="<?php echo $neardHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        <i class="fa fa-bar-chart-o"></i> <?php echo $neardLang->getValue(Lang::STATUS); ?>
      </span>
      <span class="list-group-item postgresql-versions">
        <span class="loader" style="float:right"><img src="<?php echo $neardHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        <i class="fa fa-puzzle-piece"></i> <?php echo $neardLang->getValue(Lang::VERSIONS); ?>
      </span>
    </div>
  </div>
</div>