<?php include __DIR__ . '\..\..\root.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset = "utf-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <meta name = "description" content = "">
    <meta name = "author" content = "">
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/jquery/jquery-3.6.1.min.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/jquery/jquery-migrate-3.4.0.min.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/bootstrap/popper.min.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/bootstrap/bootstrap.min.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/fontawesome/js/all.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/fontawesome/js/v4-shims.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/_commons.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/latestversion.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/summary.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/apache.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/filezilla.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/mailhog.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/mariadb.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/memcached.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/mysql.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/nodejs.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/php.js"></script>
    <script src = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/js/postgresql.js"></script>
    <link href = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/bootstrap/bootstrap.min.css" rel = "stylesheet">
    <link href = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/fontawesome/css/all.css" rel = "stylesheet">
    <link href = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/libs/fontawesome/css/v4-shims.css" rel = "stylesheet">
    <link href = "<?php echo $bearsamppHomepage->getResourcesPath(); ?>/css/app.css" rel = "stylesheet">
    <link href = "<?php echo Util::imgToBase64( $bearsamppCore->getResourcesPath() . '/bearsampp.ico' ); ?>" rel = "icon" />
    <title><?php echo APP_TITLE . ' ' . $bearsamppCore->getAppVersion(); ?></title>
</head>

<body>
<nav class = "navbar navbar-expand-md navbar-light bg-dark fixed-top" role = "navigation">
    <div class = "container-fluid">
        <div class = "d-inline-block"
        <a class = "navbar-brand" href = "<?php echo Util::getWebsiteUrl(); ?>">
            <img class="p-1" alt = "<?php echo APP_TITLE . ' ' . $bearsamppCore->getAppVersion(); ?>"
                 src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/header-logo.png'; ?>" /></a>
        <button class = "navbar-toggler" type = "button" data-bs-toggle = "collapse" data-bs-target = "#navbarSupportedContent" aria-controls = "navbarSupportedContent"
                aria-expanded = "false" aria-label = "Toggle navigation">
            <span class = "navbar-toggler-icon"></span>
        </button>
    </div>
    <div class = "collapse navbar-collapse" id = "navbarSupportedContent">
        <ul class = "d-flex flex-row justify-content-end flex-fill mb-0">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title = "<?php echo $bearsamppLang->getValue( Lang::GITHUB ); ?>" target = "_blank" href = "<?php echo Util::getGithubUrl(); ?>"><img
                        src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/github.png'; ?>" /></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title = "<?php echo $bearsamppLang->getValue( Lang::DONATE ); ?>" target = "_blank" href = "<?php echo Util::getWebsiteUrl( 'donate' ); ?>"><img
                        src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/heart.png'; ?>" /></a>
            </li>
        </ul>
    </div>
</nav>

<div id = "page-wrapper">
    <?php include 'tpls/hp.latestversion.php'; ?>
    <?php include 'tpls/hp.' . $bearsamppHomepage->getPage() . '.php'; ?>
</div>

</body>
</html>
