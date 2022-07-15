<?php include __DIR__ . '\..\..\bootstrap.php'; ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/jquery/jquery-1.10.2.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/jquery/jquery-migrate-1.2.1.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/_commons.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/latestversion.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/summary.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/apache.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/filezilla.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/mailhog.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/mariadb.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/memcached.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/mysql.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/nodejs.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/php.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/postgresql.js"></script>
    <script src="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/svn.js"></script>
    <link href="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo $bearsamppHomepage->getResourcesPath(); ?>/css/app.css" rel="stylesheet">
    <link href="<?php echo Util::imgToBase64($bearsamppCore->getResourcesPath() . '/bearsampp.ico'); ?>" rel="icon" />
    <title><?php echo APP_TITLE . ' ' . $bearsamppCore->getAppVersion(); ?></title>
  </head>

  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav navbar-header">
          <li><a href="<?php echo Util::getWebsiteUrl(); ?>"><img alt="<?php echo APP_TITLE . ' ' . $bearsamppCore->getAppVersion(); ?>" src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/logo.png'; ?>" /></a></li>
        </ul>
        <ul style="margin-right:0;" class="nav navbar-nav navbar-right">
          <li><a class="addtooltip" title="<?php echo $bearsamppLang->getValue(Lang::GITHUB); ?>" target="_blank" href="<?php echo Util::getGithubUrl(); ?>"><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/github.png'; ?>" /></a></li>
          <li><a class="addtooltip" title="<?php echo $bearsamppLang->getValue(Lang::DONATE); ?>" target="_blank" href="<?php echo Util::getWebsiteUrl('donate'); ?>"><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/heart.png'; ?>" /></a></li>
        </ul>
      </div>
    </nav>

    <div id="page-wrapper">
        <?php include 'tpls/hp.latestversion.php'; ?>
        <?php include 'tpls/hp.' . $bearsamppHomepage->getPage() . '.php'; ?>
    </div>

    <script type="text/javascript">
    $('.navbar-nav a.addtooltip[title]').tooltip({ html: true, placement: 'bottom' });
    $('a.addtooltip[title]').tooltip({ html: true });
    </script>

  </body>
</html>
