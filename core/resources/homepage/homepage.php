<?php
/*
 * Copyright (c) 2022 - 2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */
include __DIR__ . '/../../root.php';
global $bearsamppLang, $bearsamppCore, $bearsamppHomepage, $bearsamppConfig, $locale, $bearsamppRoot;

/* Set the resource path homepage dir */
$resourcesPath = rtrim($bearsamppHomepage->getResourcesPath(), '/') . '/';
/* $bearsamppHomepage->getResourcesPath() . '/img/icons/pear-icon.gif'; */
/* Set reusable variables */
$iconsPath = $bearsamppHomepage->getIconsPath();
$imagesPath = $bearsamppHomepage->getImagesPath();
$downloadTitle = $bearsamppLang->getValue(Lang::DOWNLOAD_MORE);
$getLoader = '<span class = "loader float-end"><img src = "' . $imagesPath . 'loader.gif' . '" alt="spinner" /></span>';
?>

<!DOCTYPE html>
<html lang="<?php echo $locale ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Localhost Dashboard">
    <meta name="author" content="Bearsampp">

    <?php
    $cssFiles = [
        "/css/app.css",
        "/libs/bootstrap/bootstrap.min.css",
        "/libs/fontawesome/css/all.css",
    ];
    $jsFiles = [
        "/libs/jquery/jquery-3.7.1.min.js",
        "/libs/jquery/jquery-migrate-3.4.0.min.js",
        "/libs/bootstrap/popper.min.js",
        "/libs/bootstrap/bootstrap.min.js",
        "/libs/fontawesome/js/all.js",
        "/js/_commons.js",
        "/js/latestversion.js",
        "/js/summary.js",
        "/js/apache.js",
        "/js/filezilla.js",
        "/js/mailhog.js",
        "/js/mariadb.js",
        "/js/memcached.js",
        "/js/mysql.js",
        "/js/nodejs.js",
        "/js/php.js",
        "/js/postgresql.js"
    ];

    foreach ($cssFiles as $file) {
        echo '<link href="' . $resourcesPath . $file . '" rel="stylesheet">' . PHP_EOL;
    }

    foreach ($jsFiles as $file) {
        echo '<script src="' . $resourcesPath . $file . '"></script>' . PHP_EOL;
    }
    ?>

    <link href="<?php echo $bearsamppRoot->getRootPath() . '/favicon.ico'; ?>" rel="icon"/>
    <title><?php echo APP_TITLE . ' ' . $bearsamppCore->getAppVersion(); ?></title>
</head>

<body>
<nav class="navbar navbar-expand-md navbar-light bg-dark fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="d-inline-block">
            <a class="navbar-brand" href="<?php echo Util::getWebsiteUrl(); ?>">
                <img class="p-1" alt="<?php echo APP_TITLE . ' ' . $bearsamppCore->getAppVersion(); ?>"
                     src="<?php echo $imagesPath . 'header-logo.png'; ?>"/></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <div class="collapse navbar-collapse icons" id="navbarSupportedContent">
        <ul class="d-flex flex-row justify-content-space-between align-items-center flex-fill mb-0">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php echo $bearsamppLang->getValue(Lang::DISCORD); ?>" target="_blank"
                   href="https://discord.gg/AgwVNAzV" alt='Discord Icon'><i class = 'fa-brands fa-discord'></i></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php echo $bearsamppLang->getValue(Lang::FACEBOOK); ?>" target="_blank"
                   href="https://www.facebook.com/groups/bearsampp" alt="Facebook icon"><i class="fa-brands fa-facebook"></i></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php echo $bearsamppLang->getValue(Lang::GITHUB); ?>" target="_blank"
                   href="<?php echo Util::getGithubUrl(); ?>" alt="Github icon"><i class="fa-brands fa-github"></i></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php echo $bearsamppLang->getValue(Lang::DONATE); ?>" target="_blank"
                   href="<?php echo Util::getWebsiteUrl('donate'); ?>"><img class="donate" src="<?php echo $imagesPath . 'donate.png'; ?>" alt='Donation Icon'/></a>
            </li>
        </ul>
    </div>
</nav>

<div id="page-wrapper">
    <?php include 'tpls/hp.latestversion.html'; ?>
    <?php include 'tpls/hp.' . $bearsamppHomepage->getPage() . '.html'; ?>
</div>

</body>
</html>
