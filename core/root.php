<?php
/*
 * Copyright (c) 2022 - 2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Defines constants used throughout the Bearsampp application.
 */
const APP_AUTHOR_NAME = 'N6REJ';
const APP_TITLE = 'Bearsampp';
const APP_WEBSITE = 'https://bearsampp.com';
const APP_LICENSE = 'GPL3 License';
const APP_GITHUB_USER = 'Bearsampp';
const APP_GITHUB_REPO = 'Bearsampp';
const APP_GITHUB_USERAGENT = 'Bearsampp';
const APP_GITHUB_LATEST_URL = 'https://api.github.com/repos/' . APP_GITHUB_USER . '/' . APP_GITHUB_REPO . '/releases/latest';
const RETURN_TAB = '	';

// Membership Pro API key & URL
const QUICKPICK_API_KEY = '4abe15e5-95f2-4663-ad12-eadb245b28b4';
const QUICKPICK_API_URL = 'https://bearsampp.com/index.php?option=com_osmembership&task=api.get_active_plan_ids&api_key=';

// URL where quickpick-releases.json lives
const QUICKPICK_JSON_URL = 'https://raw.githubusercontent.com/Bearsampp/Bearsampp/main/core/resources/quickpick-releases.json';

/**
 * Includes the Root class file and creates an instance of Root.
 * Registers the root directory of the application.
 */
require_once dirname(__FILE__) . '/classes/class.root.php';
$bearsamppRoot = new Root(dirname(__FILE__));
$bearsamppRoot->register();

/**
 * Creates an instance of the Action class and processes the action based on command line arguments.
 */
$bearsamppAction = new Action();
$bearsamppAction->process();

/**
 * Checks if the current user has root privileges and stops loading if true.
 */
if ($bearsamppRoot->isRoot()) {
    Util::stopLoading();
}

/**
 * Retrieves the locale setting from the global language instance.
 */
global $bearsamppLang;
$locale = $bearsamppLang->getValue('locale');
