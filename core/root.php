<?php

define('APP_TITLE', 'Bearsampp');
define('APP_WEBSITE', 'https://bearsampp.com');
define('APP_UPDATE_URL', 'https://raw.githubusercontent.com/Bearsampp/Bearsampp/main/.latest-release');
define('APP_GITHUB_USER', 'bearsampp');
define('APP_GITHUB_REPO', 'bearsampp');
define('APP_AUTHOR_NAME', 'N6REJ');

define('RETURN_TAB', '	');

// isRoot
require_once dirname(__FILE__) . '/classes/class.root.php';
$bearsamppRoot = new Root(dirname(__FILE__));
$bearsamppRoot->register();

// Process action
$bearsamppAction = new Action();
$bearsamppAction->process();

// Stop loading
if ($bearsamppRoot->isRoot()) {
    Util::stopLoading();
}
