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
     * @global Apps $bearsamppApps The apps object containing various applications used by the Bearsampp application.
     * @global Homepage $bearsamppHomepage The homepage object for managing homepage-related settings and content.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppConfig, $bearsamppBins, $bearsamppApps, $bearsamppHomepage;

        // If the executable file exists, return early.
        if (file_exists($bearsamppCore->getExec())) {
            return;
        }

        // Start loading process
        Util::startLoading();

        // Capture which database services are currently running so we can restart them once
        // the reload has finished. A running database server keeps executing the binary it was
        // launched with, so it keeps reporting the old version to clients such as phpMyAdmin
        // until it is restarted from the updated 'current' symlink. Only the database services
        // are handled here: the rest of the reload already refreshes the other modules, and
        // restarting every service needlessly disrupts them (and makes the tray flash a window
        // per service).
        $dbServiceNames = array(
            BinMysql::SERVICE_NAME,
            BinMariadb::SERVICE_NAME,
            BinPostgresql::SERVICE_NAME,
        );

        $runningServices = array();
        $stoppedServices = array();
        $services = $bearsamppBins->getServices();
        foreach ($dbServiceNames as $serviceName) {
            if (isset($services[$serviceName]) && $services[$serviceName] != null && $services[$serviceName]->isRunning()) {
                $runningServices[] = $serviceName;
                Log::info('Stopping ' . $serviceName . ' before reload');
                if ($services[$serviceName]->stop()) {
                    $stoppedServices[] = $serviceName;
                } else {
                    Log::error('Failed to stop ' . $serviceName . ' before reload');
                }
            }
        }

        try {
            // Scan and update paths in bin configuration files (replaces path placeholders
            // such as ~BEARSAMPP_LIN_PATH~ for the newly selected version, like switchVersion does)
            $pathsToScan = array();

            // MySQL
            $folderList = Util::getFolderList(Path::getModuleRootPath($bearsamppBins->getMysql()));
            if ($folderList === false) {
                Log::error('Failed to scan MySQL module folder list for path updates');
                $folderList = array();
            }
            foreach ($folderList as $folder) {
                $pathsToScan[] = array(
                    'path'      => Path::getModuleRootPath($bearsamppBins->getMysql()) . '/' . $folder,
                    'includes'  => array('my.ini'),
                    'recursive' => false
                );
            }

            // MariaDB
            $folderList = Util::getFolderList(Path::getModuleRootPath($bearsamppBins->getMariadb()));
            if ($folderList === false) {
                Log::error('Failed to scan MariaDB module folder list for path updates');
                $folderList = array();
            }
            foreach ($folderList as $folder) {
                $pathsToScan[] = array(
                    'path'      => Path::getModuleRootPath($bearsamppBins->getMariadb()) . '/' . $folder,
                    'includes'  => array('my.ini'),
                    'recursive' => false
                );
            }

            // Update paths in scanned files
            if (!empty($pathsToScan)) {
                Path::changePath(Util::getFilesToScan($pathsToScan));
            }

            // Reload bins and apps to recreate symlinks if needed
            $bearsamppBins->reload();
            $bearsamppApps->reload();

            // Refresh application configs (ports/credentials) against the reloaded bins
            $bearsamppApps->getPhpmyadmin()->update();
            $bearsamppApps->getPhppgadmin()->update();

            // Refresh hostname in the configuration
            $bearsamppConfig->replace(Config::CFG_HOSTNAME, gethostname());

            // Refresh launch startup setting in the configuration
            $bearsamppConfig->replace(Config::CFG_LAUNCH_STARTUP, Util::isLaunchStartup() ? Config::ENABLED : Config::DISABLED);

            // Check and update the browser setting in the configuration
            $currentBrowser = $bearsamppConfig->getBrowser();
            if (empty($currentBrowser) || !file_exists($currentBrowser)) {
                $bearsamppConfig->replace(Config::CFG_BROWSER, Win32Native::getDefaultBrowser());
            }

            // Process and update the bearsampp.ini file
            file_put_contents(Path::getIniFilePath(), Util::utf8ToCp1252(TplApp::process()));

            // Process and update the PowerShell configuration
            TplPowerShell::process();

            // Refresh PEAR version cache file
            $bearsamppBins->getPhp()->getPearVersion();

            // Rebuild alias homepage content
            $bearsamppHomepage->refreshAliasContent();

            // Rebuild _commons.js content
            $bearsamppHomepage->refreshCommonsJsContent();
        } finally {
            // Restart the services that were successfully stopped. Because the bins have
            // been reloaded and the 'current' symlink now points to the selected version, the
            // service starts from the new binary and reports the new version to clients such
            // as phpMyAdmin. Fresh service references are taken from the reloaded bins.
            $failedServices = array();
            if (!empty($stoppedServices)) {
                $services = $bearsamppBins->getServices();
                foreach ($stoppedServices as $serviceName) {
                    if (isset($services[$serviceName]) && $services[$serviceName] != null) {
                        Log::info('Restarting ' . $serviceName . ' after reload');
                        if (!$services[$serviceName]->start()) {
                            Log::error('Failed to restart ' . $serviceName . ' after reload');
                            $failedServices[] = $serviceName;
                        }
                    }
                }
            }

            // Write reload status file for UI to poll and display results
            $reloadStatusFile = Path::getLogsPath() . '/reload-status.json';
            $reloadStatus = array(
                'timestamp' => time(),
                'stoppedServices' => $stoppedServices,
                'failedServices' => $failedServices,
                'success' => empty($failedServices)
            );

            // Atomic write: write to temp file, then rename to final path to prevent partial reads
            $reloadStatusTmpFile = $reloadStatusFile . '.tmp';
            file_put_contents($reloadStatusTmpFile, json_encode($reloadStatus, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
            rename($reloadStatusTmpFile, $reloadStatusFile);

            // Stop loading process
            Util::stopLoading();
        }
    }
}

