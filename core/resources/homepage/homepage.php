<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * This script sets up the homepage for the Bearsampp application, including loading necessary resources,
 * setting up the navigation bar, and including dynamic content based on the application's state.
 * It utilizes global variables to access application settings and paths.
 */

/**
 * Include the main root.php file which initializes the application environment.
 */
require_once __DIR__ . '/../../root.php';
require_once __DIR__ . '/../../classes/actions/class.action.quickPick.php';

/**
 * Set security headers to protect against common web vulnerabilities
 */
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https://api.github.com https://bearsampp.com;");

/**
 * Declare global variables to access various parts of the application such as language settings,
 * core functionalities, homepage configurations, and more.
 */
global $bearsamppLang, $bearsamppCore, $bearsamppHomepage, $bearsamppConfig, $bearsamppRoot;

/**
 * Set the base path for resources, ensuring there is a trailing slash.
 */
$resourcesPath = rtrim( $bearsamppHomepage->getResourcesPath(), '/' ) . '/';

/**
 * Define paths for icons and images used in the homepage.
 */
$iconsPath  = $bearsamppHomepage->getIconsPath();
$imagesPath = $bearsamppHomepage->getImagesPath();

// Instantiate the QuickPick class
$quickPick = new QuickPick();

$ajaxUrl = $bearsamppCore->getAjaxPath() . '/ajax.getmodule_versions.php';


/**
 * Retrieve and store the localized string for the 'Download More' label.
 */
$downloadTitle = $bearsamppLang->getValue( Lang::DOWNLOAD_MORE );

/**
 * HTML snippet for a loading spinner image.
 */
$getLoader = '<span class = "loader float-end"><img src = "' . $imagesPath . 'loader.gif" alt="spinner"></span>';

/**
 * HTML structure defining the head of the document, including meta tags, CSS and JS resource inclusion,
 * and the document title.
 */
?>
<!DOCTYPE html>
<html lang = "<?php echo htmlspecialchars($bearsamppLang->getValue( Lang::LOCALE ), ENT_QUOTES, 'UTF-8') ?>">

<head>
    <meta charset = "utf-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <meta name = "description" content = "Localhost Dashboard">
    <meta name = "author" content = "Bearsampp">

    <?php
    /**
     * Arrays of CSS and JS files to be included in the page.
     */
    $cssFiles = [
        "/css/app.css",
        "/libs/bootstrap/css/bootstrap.min.css",
        "/libs/fontawesome/css/all.min.css",
    ];
    $jsFiles  = [

        "/libs/bootstrap/js/bootstrap.bundle.min.js",
        "/libs/fontawesome/js/all.min.js",
        "/js/_commons.js",
        "/js/latestversion.js",
        "/js/summary.js",
        "/js/apache.js",
        '/js/mailpit.js',
        "/js/mariadb.js",
        "/js/memcached.js",
        "/js/mysql.js",
        "/js/nodejs.js",
        "/js/php.js",
        "/js/postgresql.js",
        "/js/xlight.js",
        "/js/quickpick.js",
	    "/js/loading-cursor.js"
    ];

    /**
     * Loop through CSS files and include them in the page.
     */
    foreach ( $cssFiles as $file ) {
        echo '<link href="' . $resourcesPath . $file . '" rel="stylesheet">' . PHP_EOL;
    }
    ?>

    <link href = "<?php echo $iconsPath . 'favicon.ico'; ?>" rel = "icon" />
    <title><?php echo APP_TITLE . ' ' . $bearsamppCore->getAppVersion(); ?></title>

    <!-- Inline script to set loading cursor immediately -->
    <script>
        // Set loading cursor immediately
        document.documentElement.classList.add('loading-cursor');

        // Create and show loading overlay
        window.addEventListener('DOMContentLoaded', function() {
            // Remove loading cursor when page is fully loaded
            window.addEventListener('load', function() {
                document.documentElement.classList.remove('loading-cursor');

                // If there's an overlay, remove it
                const existingOverlay = document.querySelector('.loading-overlay');
                if (existingOverlay) {
                    existingOverlay.parentNode.removeChild(existingOverlay);
                }
            });
        });
    </script>
</head>

<body>
<nav class = "navbar navbar-expand-md navbar-light bg-dark fixed-top" role = "navigation">
    <div class = "container-fluid">
        <div class = "d-inline-block">
            <a class = "navbar-brand" href = "<?php echo Util::getWebsiteUrl(); ?>" aria-label = 'Home'>
                <img class = "p-1" alt = "<?php echo APP_TITLE . ' ' . $bearsamppCore->getAppVersion(); ?>"
                     src = "<?php echo $imagesPath . 'header-logo.png'; ?>">
            </a>
            <button class = "navbar-toggler" type = "button" data-bs-toggle = "collapse" data-bs-target = "#navbarSupportedContent" aria-controls = "navbarSupportedContent"
                    aria-expanded = "false" aria-label = "Toggle navigation">
                <span class = "navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <?php
    try {
        echo $quickPick->loadQuickpick($imagesPath);
    } catch (Exception $e) {
        // Log the error but continue with the page
        error_log('Error loading QuickPick: ' . $e->getMessage());
        echo '<div id="quickPickError" class="text-center mt-3 pe-3">
            <span>QuickPick unavailable</span>
        </div>';
    }
    ?>

    <div class = "collapse navbar-collapse icons" id = "navbarSupportedContent">
        <div class = "d-flex flex-row justify-content-space-between align-items-center flex-fill mb-0">
            <a data-bs-toggle = "tooltip" data-bs-placement = "top" data-bs-title = "<?php echo $bearsamppLang->getValue( Lang::DISCORD ); ?>" target = "_blank"
               href = "https://discord.gg/AgwVNAzV" aria-label = "Discord">
                <i class = 'fa-brands fa-discord'></i>
            </a>
            <a data-bs-toggle = "tooltip" data-bs-placement = "top" data-bs-title = "<?php echo $bearsamppLang->getValue( Lang::FACEBOOK ); ?>" target = "_blank"
               href = "https://www.facebook.com/groups/bearsampp" aria-label = "Facebook">
                <i class = "fa-brands fa-facebook"></i>
            </a>
            <a data-bs-toggle = "tooltip" data-bs-placement = "top" data-bs-title = "<?php echo $bearsamppLang->getValue( Lang::GITHUB ); ?>" target = "_blank"
               href = "<?php echo Util::getGithubUrl(); ?>" aria-label = "GitHub">
                <i class = "fa-brands fa-github"></i>
            </a>
            <a data-bs-toggle = "tooltip" data-bs-placement = "top" data-bs-title = "<?php echo $bearsamppLang->getValue( Lang::DONATE ); ?>" target = "_blank"
               href = "<?php echo Util::getWebsiteUrl( 'donate' ); ?>"><img class = "donate" src = "<?php echo $imagesPath . 'donate.png'; ?>" alt = 'Donation Icon' />
            </a>
        </div>
    </div>
</nav>

<div id = "page-wrapper">
    <?php
    try {
        include __DIR__ . '/tpls/hp.latestversion.html';
    } catch (Exception $e) {
        error_log('Error including latest version template: ' . $e->getMessage());
        echo '<div class="alert alert-warning">Latest version information unavailable</div>';
    }

    try {
        $page = preg_replace('/[^a-z0-9_-]/i', '', (string) $bearsamppHomepage->getPage());
        $pagePath = __DIR__ . '/tpls/hp.' . $page . '.html';
        if (is_file($pagePath)) {
            include $pagePath;
        } else {
            include __DIR__ . '/tpls/hp.index.html';
        }
    } catch (Exception $e) {
        error_log('Error including page template: ' . $e->getMessage());
        echo '<div class="alert alert-warning">Page content unavailable</div>';
    }
    ?>
</div>

<?php
foreach ( $jsFiles as $file ) {
    echo '<script src="' . $resourcesPath . $file . '"></script>' . PHP_EOL;
}
?>
</body>
</html>
