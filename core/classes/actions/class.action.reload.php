<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionReload
 *
 * This class handles the reloading of various configurations and settings for the Bearsampp application.
 * It performs operations such as refreshing the hostname, updating startup settings, checking and updating
 * the browser configuration, processing configuration files, and rebuilding certain cached contents.
 */
class ActionReload
{
    /**
     * Constructs an ActionReload object and performs various refresh operations.
     *
     * @param array $args The arguments passed to the constructor.
     *
     * @global Root $bearsamppRoot The root object of the Bearsampp application.
     * @global Core $bearsamppCore The core object of the Bearsampp application.
     * @global Config $bearsamppConfig The configuration object of the Bearsampp application.
     * @global Bins $bearsamppBins The bins object containing various binaries used by the Bearsampp application.
     * @global Homepage $bearsamppHomepage The homepage object for managing homepage-related settings and content.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppConfig, $bearsamppBins, $bearsamppHomepage;

        // If the executable file exists, return early.
        if (file_exists($bearsamppCore->getExec())) {
            return;
        }

        // Start loading process
        Util::startLoading();

        // Refresh hostname in the configuration
        $bearsamppConfig->replace(Config::CFG_HOSTNAME, gethostname());

        // Refresh launch startup setting in the configuration
        $bearsamppConfig->replace(Config::CFG_LAUNCH_STARTUP, Util::isLaunchStartup() ? Config::ENABLED : Config::DISABLED);

        // Check and update the browser setting in the configuration
        $currentBrowser = $bearsamppConfig->getBrowser();
        if (empty($currentBrowser) || !file_exists($currentBrowser)) {
            $bearsamppConfig->replace(Config::CFG_BROWSER, Vbs::getDefaultBrowser());
        }

        // Process and update the bearsampp.ini file
        file_put_contents($bearsamppRoot->getIniFilePath(), Util::utf8ToCp1252(TplApp::process()));

        // Process and update the ConsoleZ configuration
        TplConsoleZ::process();

        // Refresh PEAR version cache file
        $bearsamppBins->getPhp()->getPearVersion();

        // Rebuild alias homepage content
        $bearsamppHomepage->refreshAliasContent();

        // Rebuild _commons.js content
        $bearsamppHomepage->refreshCommonsJsContent();
    }
}
