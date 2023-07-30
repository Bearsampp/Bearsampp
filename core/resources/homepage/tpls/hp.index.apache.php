<a class = "anchor" name = "apache"></a>
<div class = "row-fluid">
    <div class = "col-lg-12">
        <h1><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/apache.png'; ?>" /> <?php echo $bearsamppLang->getValue( Lang::APACHE ); ?> <small></small></h1>
    </div>
</div>
<div class = "row-fluid">
    <div class = "col-lg-6">
        <div class = "list-group">
      <span class = "list-group-item apache-checkport">
        <span class = "loader" style = "float:right"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        <i class = "fa fa-bar-chart-o"></i> <?php echo $bearsamppLang->getValue( Lang::STATUS ); ?>
      </span>
            <span class = "list-group-item apache-versions">
              <span class="label-left col-1">
                <i class="fa fa-puzzle-piece"></i> <?php echo $bearsamppLang->getValue(Lang::VERSIONS); ?>
              </span>
        <span class="apache-version-list col-11">
              <span class = "loader" style = "float:right"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        </span>
      </span>
            <span class = "list-group-item apache-modulescount">
        <span class = "loader" style = "float:right"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        <i class = "fa fa-gear"></i> <?php echo $bearsamppLang->getValue( Lang::MODULES ); ?>
      </span>
            <span class = "list-group-item apache-aliasescount">
        <span class = "loader" style = "float:right"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        <i class = "fa fa-link"></i> <?php echo $bearsamppLang->getValue( Lang::ALIASES ); ?>
      </span>
            <span class = "list-group-item apache-vhostscount">
        <span class = "loader" style = "float:right"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        <i class = "fa fa-globe"></i> <?php echo $bearsamppLang->getValue( Lang::VIRTUAL_HOSTS ); ?>
      </span>
        </div>
    </div>
</div>
<div class = "border grid-list mt-3">
    <div class = "row-fluid mt-2">
        <div class = "col-lg-12 section-top">
            <h3><i class = "fa fa-gear"></i> <?php echo $bearsamppLang->getValue( Lang::MODULES ); ?> <small></small></h3>
        </div>
    </div>
    <div class = "row-fluid">
        <div class = "col-lg-12 apache-moduleslist d-flex flex-wrap mb-2">
            <span class = "loader"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        </div>
    </div>
</div>
<div class = "border grid-list mt-3">
    <div class = "row-fluid mt-2">
        <div class = "col-lg-12 section-top">
            <h3><i class = "fa fa-link"></i> <?php echo $bearsamppLang->getValue( Lang::ALIASES ); ?> <small></small></h3>
        </div>
    </div>
    <div class = "row-fluid">
        <div class = "col-lg-12 apache-aliaseslist d-flex flex-wrap mb-2">
            <span class = "loader"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        </div>
    </div>
</div>
<div class = "border grid-list mt-3">
    <div class = "row-fluid mt-2">
        <div class = "col-lg-12 section-top">
            <h3><i class = "fa fa-folder"></i> <?php echo $bearsamppLang->getValue( Lang::MENU_WWW_DIRECTORY ); ?> <small></small></h3>
        </div>
    </div>
    <div class = "row-fluid">
        <div class = "col-lg-12 apache-wwwdirectory d-flex flex-wrap mb-2">
            <span class = "loader"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        </div>
    </div>
</div>
<div class = "border grid-list mt-3">
    <div class = "row-fluid mt-2">
        <div class = "col-lg-12 section-top">
            <h3><i class = "fa fa-globe"></i> <?php echo $bearsamppLang->getValue( Lang::VIRTUAL_HOSTS ); ?> <small></small></h3>
        </div>
    </div>
    <div class = "row-fluid">
        <div class = "col-lg-12 apache-vhostslist d-flex flex-wrap mb-2">
            <span class = "loader"><img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/loader.gif'; ?>" /></span>
        </div>
    </div>
</div>
