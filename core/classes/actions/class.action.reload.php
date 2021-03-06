<?php

class ActionReload
{
    public function __construct($args)
    {
        global $bearsamppBs, $bearsamppCore, $bearsamppConfig, $bearsamppBins, $bearsamppHomepage;

        if (file_exists($bearsamppCore->getExec())) {
            return;
        }

        // Start loading
        Util::startLoading();

        // Refresh hostname
        $bearsamppConfig->replace(Config::CFG_HOSTNAME, gethostname());

        // Refresh launch startup
        $bearsamppConfig->replace(Config::CFG_LAUNCH_STARTUP, Util::isLaunchStartup() ? Config::ENABLED : Config::DISABLED);

        // Check browser
        $currentBrowser = $bearsamppConfig->getBrowser();
        if (empty($currentBrowser) || !file_exists($currentBrowser)) {
            $bearsamppConfig->replace(Config::CFG_BROWSER, Vbs::getDefaultBrowser());
        }

        // Process bearsampp.ini
        file_put_contents($bearsamppBs->getIniFilePath(), Util::utf8ToCp1252(TplApp::process()));

        // Process ConsoleZ config
        TplConsoleZ::process();

        // Process Websvn config
        TplWebsvn::process();

        // Process Gitlist config
        TplGitlist::process();

        // Refresh PEAR version cache file
        $bearsamppBins->getPhp()->getPearVersion();

        // Rebuild alias homepage
        $bearsamppHomepage->refreshAliasContent();

        // Rebuild _commons.js
        $bearsamppHomepage->refreshCommonsJsContent();
    }
}
