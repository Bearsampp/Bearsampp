<a class="anchor" name="php"></a>
<div class="row-fluid">
  <div class="col-lg-12">
    <h1><img
        src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/php.png'; ?>"/> <?php echo $bearsamppLang->getValue(Lang::PHP); ?>
      <small></small></h1>
  </div>
</div>
<div class="row-fluid">
  <div class="col-lg-6">
    <div class="list-group">
      <span class="list-group-item php-status">
        <span class="loader" style="float:right"><img
            src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>"/></span>
        <i class="fa fa-bar-chart-o"></i> <?php echo $bearsamppLang->getValue(Lang::STATUS); ?>
      </span>
      <span class="list-group-item php-versions col-12">
              <span class="label-left col-1">
                <i class="fa fa-puzzle-piece"></i> <?php echo $bearsamppLang->getValue(Lang::VERSIONS); ?>
              </span>
              <span class="php-version-list float-right col-11">
                <span class="loader" style="float:right">
                  <img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>"/>
                </span>
              </span>
      </span>
      <span class="list-group-item php-extscount">
        <span class="loader" style="float:right"><img
            src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>"/></span>
        <i class="fa fa-gear"></i> <?php echo $bearsamppLang->getValue(Lang::EXTENSIONS); ?>
      </span>
      <span class="list-group-item php-pearversion">
        <span class="loader" style="float:right"><img
            src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>"/></span>
        <i class="fa fa-puzzle-piece"></i> <?php echo $bearsamppLang->getValue(Lang::PEAR); ?>
      </span>
      <span class="list-group-item">
        <i class="fa fa-info-circle"></i> <a
          href="<?php echo $bearsamppHomepage->getPageQuery(Homepage::PAGE_PHPINFO); ?>"><?php echo $bearsamppLang->getValue(Lang::HOMEPAGE_PHPINFO_TEXT); ?></a>
      </span>
      <?php if (function_exists('apc_add') && function_exists('apc_exists')) {
        ?>
        <span class="list-group-item">
        <i class="fa fa-info-circle"></i> <a
            href="<?php echo $bearsamppHomepage->getPageQuery(Homepage::PAGE_STDL_APC); ?>"
            target="_blank"><?php echo $bearsamppLang->getValue(Lang::HOMEPAGE_APC_TEXT); ?></a>
      </span>
        <?php
      } ?>
    </div>
  </div>
</div>
<div class="border grid-list mt-3">
  <div class="row-fluid mt-2">
    <div class="col-lg-12 section-header">
      <h3><i class="fa fa-gear"></i> <?php echo $bearsamppLang->getValue(Lang::EXTENSIONS); ?> <small></small></h3>
    </div>
  </div>
  <div class="row-fluid">
    <div class="col-lg-12 php-extslist d-flex flex-wrap mb-2">
      <span class="loader"><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>"/></span>
    </div>
  </div>
</div>
