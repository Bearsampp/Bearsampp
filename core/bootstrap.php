<?php

define('APP_TITLE', 'bearsampp');
define('APP_WEBSITE', 'https://bearsampp.com');
define('APP_UPDATE_URL', 'http://pastebin.com/raw/cPewk367');
define('APP_GITHUB_USER', 'bearsampp');
define('APP_GITHUB_REPO', 'bearsampp');
define('APP_AUTHOR_NAME', '/bearsampp');

define('RETURN_TAB', '	');

// Bootstrap
require_once dirname(__FILE__) . '/classes/class.bootstrap.php';
$bearsamppBs = new Bootstrap(dirname(__FILE__));
$bearsamppBs->register();

// Process action
$bearsamppAction = new Action();
$bearsamppAction->process();

// Stop loading
if ($bearsamppBs->isBootstrap()) {
    Util::stopLoading();
}
