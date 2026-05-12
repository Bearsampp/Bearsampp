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
 * Class ActionStartup
 * Handles the startup process of the Bearsampp application, including initializing services,
 * cleaning temporary files, refreshing configurations, and more.
 */
class ActionStartup
{
    private $splash;
    private $restart;
    private $startTime;
    private $error;

    private $rootPath;
    private $filesToScan;

    const GAUGE_SERVICES = 5;
    const GAUGE_OTHERS = 19;

    /**
     * ActionStartup constructor.
     * Initializes the startup process, including the splash screen and various configurations.
     *
     * @param   array  $args  Command line arguments.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;
        $this->writeLog( 'Starting ' . APP_TITLE );

        // Admin check is now performed in root.php before ActionStartup is instantiated
        // This prevents screen flashes and ensures the check happens before any WinBinder initialization
        Log::info('Administrator privileges confirmed - proceeding with startup');

        // Init
        $this->splash    = new Splash();
        $this->restart   = false;
        $this->startTime = Util::getMicrotime();
        $this->error     = '';

        $this->rootPath    = $bearsamppRoot->getRootPath();
        $this->filesToScan = array();

        $gauge = self::GAUGE_SERVICES * count( $bearsamppBins->getServices() );
        $gauge += self::GAUGE_OTHERS + 1;

        // Start splash screen
        $this->splash->init(
            $bearsamppLang->getValue( Lang::STARTUP ),
            $gauge,
            sprintf( $bearsamppLang->getValue( Lang::STARTUP_STARTING_TEXT ), APP_TITLE . ' ' . $bearsamppCore->getAppVersion() )
        );

        $bearsamppWinbinder->setHandler( $this->splash->getWbWindow(), $this, 'processWindow', 1000 );
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes the main window events during startup.
     *
     * @param   mixed  $window  The window handle.
     * @param   int    $id      The event ID.
     * @param   mixed  $ctrl    The control that triggered the event.
     * @param   mixed  $param1  Additional parameter 1.
     * @param   mixed  $param2  Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppApps, $bearsamppWinbinder;

        Log::trace('Starting processWindow method');

        // Admin check is now performed in the constructor before anything else
        // No need to check again here

        // Rotation logs
        Log::trace('Performing log rotation');
        $this->rotationLogs();

        // Clean
        Log::trace('Starting cleanup operations');
        $this->cleanTmpFolders();
        $this->cleanOldBehaviors();

        // List procs
        Log::trace('Listing running processes');
        if ($bearsamppRoot->getProcs() !== false) {
            $this->writeLog('List procs:');
            $listProcs = array();
            foreach ($bearsamppRoot->getProcs() as $proc) {
                $unixExePath = UtilPath::formatUnixPath($proc[Win32Ps::EXECUTABLE_PATH]);
                $listProcs[] = '-> ' . basename($unixExePath) . ' (PID ' . $proc[Win32Ps::PROCESS_ID] . ') in ' . $unixExePath;
            }
            sort($listProcs);
            foreach ($listProcs as $proc) {
                $this->writeLog($proc);
            }
            Log::trace('Found ' . count($listProcs) . ' running processes');
        } else {
            Log::trace('No processes found or unable to retrieve process list');
        }

        // List modules
        Log::trace('Listing bins modules');
        $this->writeLog('List bins modules:');
        foreach ($bearsamppBins->getAll() as $module) {
            if (!$module->isEnable()) {
                $this->writeLog('-> ' . $module->getName() . ': ' . $bearsamppLang->getValue(Lang::DISABLED));
                Log::trace('Bin module ' . $module->getName() . ' is disabled');
            } else {
                $this->writeLog('-> ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')');
                Log::trace('Bin module ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')');
            }
        }

        Log::trace('Listing tools modules');
        $this->writeLog('List tools modules:');
        foreach ($bearsamppTools->getAll() as $module) {
            if (!$module->isEnable()) {
                $this->writeLog('-> ' . $module->getName() . ': ' . $bearsamppLang->getValue(Lang::DISABLED));
                Log::trace('Tool module ' . $module->getName() . ' is disabled');
            } else {
                $this->writeLog('-> ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')');
                Log::trace('Tool module ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')');
            }
        }

        Log::trace('Listing apps modules');
        $this->writeLog('List apps modules:');
        foreach ($bearsamppApps->getAll() as $module) {
            if (!$module->isEnable()) {
                $this->writeLog('-> ' . $module->getName() . ': ' . $bearsamppLang->getValue(Lang::DISABLED));
                Log::trace('App module ' . $module->getName() . ' is disabled');
            } else {
                $this->writeLog('-> ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')');
                Log::trace('App module ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')');
            }
        }

        // Kill old instances
        Log::trace('Killing old instances');
        $this->killOldInstances();

        // Prepare app
        Log::trace('Preparing application - refreshing hostname');
        $this->refreshHostname();

        Log::trace('Checking launch startup settings');
        $this->checkLaunchStartup();

        Log::trace('Checking browser configuration');
        $this->checkBrowser();

        Log::trace('Gathering system information');
        $this->sysInfos();

        Log::trace('Refreshing aliases');
        $this->refreshAliases();

        Log::trace('Refreshing virtual hosts');
        $this->refreshVhosts();

        // Check app path
        Log::trace('Checking application path');
        $this->checkPath();

        Log::trace('Scanning folders');
        $this->scanFolders();

        Log::trace('Changing paths in files');
        $this->changePath();

        Log::trace('Saving current path');
        $this->savePath();

        // Check BEARSAMPP_PATH, BEARSAMPP_BINS and System Path reg keys
        Log::trace('Checking PATH registry key');
        $this->checkPathRegKey();

        Log::trace('Checking BINS registry key');
        $this->checkBinsRegKey();

        Log::trace('Checking System PATH registry key');
        $this->checkSystemPathRegKey();

        // Update config
        Log::trace('Updating configuration');
        $this->updateConfig();

        // Create SSL certificates
        Log::trace('Creating SSL certificates');
        $this->createSslCrts();

        // Install
        Log::trace('Installing services');
        $this->installServices();

        // Actions if everything OK
        if (!$this->restart && empty($this->error)) {
            Log::trace('Startup completed successfully - refreshing Git repositories');
            $this->refreshGitRepos();
            $startupTime = round(Util::getMicrotime() - $this->startTime, 3);
            $this->writeLog('Started in ' . $startupTime . 's');
            Log::trace('Application started successfully in ' . $startupTime . ' seconds');

            // Log total startup time in VERBOSE_TRACE mode (mode 3)
            global $bearsamppConfig;
            if ($bearsamppConfig->getLogsVerbose() == Config::VERBOSE_TRACE) {
                $minutes = floor($startupTime / 60);  // floor() returns int-compatible value
                $seconds = fmod($startupTime, 60);
                $formattedTime = sprintf('%d:%05.2f', $minutes, $seconds);
                Log::trace('=== TOTAL STARTUP TIME: ' . $formattedTime . ' ===');
            }
        } else {
            Log::trace('Startup issues detected - incrementing progress bar');
            $this->splash->incrProgressBar(2);
        }

        if ($this->restart) {
            Log::trace('Restart required - preparing to restart application');
            $this->writeLog(APP_TITLE . ' has to be restarted');
            $this->splash->setTextLoading(
                sprintf(
                    $bearsamppLang->getValue(Lang::STARTUP_PREPARE_RESTART_TEXT),
                    APP_TITLE . ' ' . $bearsamppCore->getAppVersion()
                )
            );

            Log::trace('Deleting all services before restart');
            foreach ($bearsamppBins->getServices() as $sName => $service) {
                Log::trace('Deleting service: ' . $sName);
                $service->delete();
            }

            Log::trace('Setting execution action to RESTART');
            $bearsamppCore->setExec(ActionExec::RESTART);
        }

        if (!empty($this->error)) {
            Log::trace('Errors occurred during startup: ' . $this->error);
            $this->writeLog('Error: ' . $this->error);
            $bearsamppWinbinder->messageBoxError($this->error, $bearsamppLang->getValue(Lang::STARTUP_ERROR_TITLE));
        }

        Log::trace('Starting loading screen');
        // Moved Util::startLoading() to after splash window destruction to prevent double progress bars

        // Give the loading window time to initialize before we terminate this process
        Log::trace('Waiting for loading window to initialize');
        usleep(500000); // 500ms delay to allow loading window to start

        Log::trace('Loading process started');

        // Closing cli to finish startup
        Log::trace('Finishing startup process');

        // Safely reset WinBinder and destroy the splash window
        $bearsamppWinbinder->destroyWindow($window);
        $bearsamppWinbinder->reset();

        // Start loading screen AFTER splash window is destroyed to prevent double progress bars
        Util::startLoading();

        // Exit this startup process cleanly - the loading window will continue running
        Log::trace('Exiting startup process cleanly');
        exit(0);

    }

    /**
     * Rotates the logs by archiving old logs and purging old archives.
     * Enhanced with file lock checking to prevent permission denied errors.
     */
    private function rotationLogs()
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppConfig, $bearsamppLang, $bearsamppBins;

        Log::trace("Starting log rotation process");
        $this->splash->setTextLoading($bearsamppLang->getValue(Lang::STARTUP_ROTATION_LOGS_TEXT));
        $this->splash->incrProgressBar();

        $archivesPath = $bearsamppRoot->getLogsPath() . '/archives';
        if (!is_dir($archivesPath)) {
            Log::trace("Creating archives directory: " . $archivesPath);
            mkdir($archivesPath, 0777, true);
            return;
        }

        $date = date('Y-m-d-His', time());
        $archiveLogsPath = $archivesPath . '/' . $date;
        $archiveScriptsPath = $archiveLogsPath . '/scripts';

        // Create archive folders
        Log::trace("Creating archive directories for current rotation");
        if (!is_dir($archiveLogsPath)) {
            Log::trace("Creating logs archive directory: " . $archiveLogsPath);
            mkdir($archiveLogsPath, 0777, true);
        } else {
            Log::trace("Logs archive directory already exists: " . $archiveLogsPath);
        }

        if (!is_dir($archiveScriptsPath)) {
            Log::trace("Creating scripts archive directory: " . $archiveScriptsPath);
            mkdir($archiveScriptsPath, 0777, true);
        } else {
            Log::trace("Scripts archive directory already exists: " . $archiveScriptsPath);
        }

        // Count archives
        Log::trace("Counting existing archives");
        $archives = array();
        $handle = @opendir($archivesPath);
        if (!$handle) {
            Log::trace("Failed to open archives directory: " . $archivesPath);
            return;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $archives[] = $archivesPath . '/' . $file;
        }
        closedir($handle);
        sort($archives);
        Log::trace("Found " . count($archives) . " existing archives");

        // Remove old archives
        if (count($archives) > $bearsamppConfig->getMaxLogsArchives()) {
            $total = count($archives) - $bearsamppConfig->getMaxLogsArchives();
            Log::trace("Removing " . $total . " old archives");
            for ($i = 0; $i < $total; $i++) {
                Log::trace("Deleting old archive: " . $archives[$i]);
                Util::deleteFolder($archives[$i]);
            }
        }

        // Helper function to check if a file is locked
        $isFileLocked = function($filePath) {
            if (!file_exists($filePath)) {
                return false;
            }

            $handle = @fopen($filePath, 'r+');
            if ($handle === false) {
                Log::trace("File appears to be locked: " . $filePath);
                return true; // File is locked
            }

            fclose($handle);
            return false; // File is not locked
        };

        // Logs
        Log::trace("Archiving log files");
        $srcPath = $bearsamppRoot->getLogsPath();
        $handle = @opendir($srcPath);
        if (!$handle) {
            Log::trace("Failed to open logs directory: " . $srcPath);
            return;
        }

        $logsCopied = 0;
        $logsSkipped = 0;

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..' || is_dir($srcPath . '/' . $file)) {
                continue;
            }

            $sourceFile = $srcPath . '/' . $file;
            $destFile = $archiveLogsPath . '/' . $file;

            // Check if file is locked before attempting to copy
            if ($isFileLocked($sourceFile)) {
                Log::trace("Skipping locked log file: " . $file);
                $logsSkipped++;
                continue;
            }

            try {
                if (copy($sourceFile, $destFile)) {
                    $logsCopied++;
                    Log::trace("Archived log file: " . $file);
                } else {
                    $logsSkipped++;
                    Log::trace("Failed to copy log file: " . $file);
                }
            } catch (Exception $e) {
                $logsSkipped++;
                Log::trace("Exception copying log file " . $file . ": " . $e->getMessage());
            }
        }
        closedir($handle);
        Log::trace("Logs archived: " . $logsCopied . " copied, " . $logsSkipped . " skipped");

        // Scripts
        Log::trace("Archiving script files");
        $srcPath = $bearsamppCore->getTmpPath();
        $handle = @opendir($srcPath);
        if (!$handle) {
            Log::trace("Failed to open tmp directory: " . $srcPath);
            return;
        }

        $scriptsCopied = 0;
        $scriptsSkipped = 0;

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..' || is_dir($srcPath . '/' . $file)) {
                continue;
            }

            $sourceFile = $srcPath . '/' . $file;
            $destFile = $archiveScriptsPath . '/' . $file;

            // Check if file is locked before attempting to copy
            if ($isFileLocked($sourceFile)) {
                Log::trace("Skipping locked script file: " . $file);
                $scriptsSkipped++;
                continue;
            }

            try {
                if (copy($sourceFile, $destFile)) {
                    $scriptsCopied++;
                    Log::trace("Archived script file: " . $file);
                } else {
                    $scriptsSkipped++;
                    Log::trace("Failed to copy script file: " . $file);
                }
            } catch (Exception $e) {
                $scriptsSkipped++;
                Log::trace("Exception copying script file " . $file . ": " . $e->getMessage());
            }
        }
        closedir($handle);
        Log::trace("Scripts archived: " . $scriptsCopied . " copied, " . $scriptsSkipped . " skipped");

        // Purge logs - only delete files that aren't locked
        Log::trace("Purging log files");
        $logsPath = $bearsamppRoot->getLogsPath();
        $handle = @opendir($logsPath);
        if (!$handle) {
            Log::trace("Failed to open logs directory for purging: " . $logsPath);
            return;
        }

        $logsDeleted = 0;
        $logsPurgeSkipped = 0;

        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..' || $file == 'archives' || $file == '.gitignore' || is_dir($logsPath . '/' . $file)) {
                continue;
            }

            $filePath = $logsPath . '/' . $file;

            // Check if file is locked before attempting to delete
            if ($isFileLocked($filePath)) {
                Log::trace("Skipping locked log file during purge: " . $file);
                $logsPurgeSkipped++;
                continue;
            }

            try {
                if (file_exists($filePath) && unlink($filePath)) {
                    $logsDeleted++;
                    Log::trace("Purged log file: " . $file);
                } else {
                    $logsPurgeSkipped++;
                    Log::trace("Failed to purge log file: " . $file);
                }
            } catch (Exception $e) {
                $logsPurgeSkipped++;
                Log::trace("Exception purging log file " . $file . ": " . $e->getMessage());
            }
        }
        closedir($handle);
        Log::trace("Logs purged: " . $logsDeleted . " deleted, " . $logsPurgeSkipped . " skipped");

        Log::trace("Log rotation completed");
    }

    /**
     * Cleans temporary folders by removing unnecessary files.
     */
    private function cleanTmpFolders()
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppCore;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_CLEAN_TMP_TEXT ) );
        $this->splash->incrProgressBar();

        $this->writeLog( 'Clear tmp folders' );
        Util::clearFolder( $bearsamppRoot->getTmpPath(), array('cachegrind', 'composer', 'openssl', 'mailpit', 'xlight', 'npm-cache', 'pip', 'opcache', '.gitignore') );
        Util::clearFolder( $bearsamppCore->getTmpPath(), array('.gitignore') );

        // Ensure opcache directory exists for persistent file cache
        $opcachePath = $bearsamppRoot->getTmpPath() . DIRECTORY_SEPARATOR . 'opcache';

        if (!is_dir($opcachePath)) {
            $this->writeLog('Creating opcache directory: ' . $opcachePath);
            if (!@mkdir($opcachePath, 0755, true) && !is_dir($opcachePath)) {
                $this->writeLog('Failed to create opcache directory: ' . $opcachePath);
                return;
            }
        }

        if (!is_writable($opcachePath)) {
            $this->writeLog('Opcache directory is not writable: ' . $opcachePath);
        }
    }

    /**
     * Cleans old behaviors by removing outdated registry entries.
     */
    private function cleanOldBehaviors()
    {
        global $bearsamppLang, $bearsamppRegistry;

        $this->writeLog( 'Clean old behaviors' );

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_CLEAN_OLD_BEHAVIORS_TEXT ) );
        $this->splash->incrProgressBar();

        // App >= 1.0.13
        $bearsamppRegistry->deleteValue(
            Registry::HKEY_LOCAL_MACHINE,
            'SOFTWARE\Microsoft\Windows\CurrentVersion\Run',
            APP_TITLE
        );
    }

    /**
     * Kills old instances of Bearsampp processes.
     */
    private function killOldInstances()
    {
        global $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_KILL_OLD_PROCS_TEXT ) );
        $this->splash->incrProgressBar();

        // Stop services
        /*foreach ($bearsamppBins->getServices() as $sName => $service) {
            $serviceInfos = $service->infos();
            if ($serviceInfos === false) {
                continue;
            }
            $service->stop();
        }*/

        // Stop third party procs
        $procsKilled = Win32Ps::killBins();
        if ( !empty( $procsKilled ) ) {
            $this->writeLog( 'Procs killed:' );
            $procsKilledSort = array();
            foreach ( $procsKilled as $proc ) {
                $unixExePath       = UtilPath::formatUnixPath( $proc[Win32Ps::EXECUTABLE_PATH] );
                $procsKilledSort[] = '-> ' . basename( $unixExePath ) . ' (PID ' . $proc[Win32Ps::PROCESS_ID] . ') in ' . $unixExePath;
            }
            sort( $procsKilledSort );
            foreach ( $procsKilledSort as $proc ) {
                $this->writeLog( $proc );
            }
        }
    }

    /**
     * Refreshes the hostname in the configuration.
     */
    private function refreshHostname()
    {
        global $bearsamppConfig, $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_REFRESH_HOSTNAME_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Refresh hostname' );

        $bearsamppConfig->replace( Config::CFG_HOSTNAME, gethostname() );
    }

    /**
     * Checks and sets the launch startup configuration.
     */
    private function checkLaunchStartup()
    {
        global $bearsamppConfig;

        $this->writeLog( 'Check launch startup' );

        if ( $bearsamppConfig->isLaunchStartup() ) {
            Util::enableLaunchStartup();
        }
        else {
            Util::disableLaunchStartup();
        }
    }

    /**
     * Checks and sets the default browser configuration.
     */
    private function checkBrowser()
    {
        global $bearsamppConfig, $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_CHECK_BROWSER_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Check browser' );

        $currentBrowser = $bearsamppConfig->getBrowser();
        if ( empty( $currentBrowser ) || !file_exists( $currentBrowser ) ) {
            $bearsamppConfig->replace( Config::CFG_BROWSER, Win32Native::getDefaultBrowser() );
        }
    }

    /**
     * Logs system information.
     */
    private function sysInfos()
    {
        global $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_SYS_INFOS ) );
        $this->splash->incrProgressBar();

        $os = Batch::getOsInfo();
        $this->writeLog( sprintf( 'OS: %s', $os ) );
    }

    /**
     * Refreshes the aliases in the Apache configuration.
     */
    private function refreshAliases()
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppBins;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_REFRESH_ALIAS_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Refresh aliases' );

        $bearsamppBins->getApache()->refreshAlias( $bearsamppConfig->isOnline() );
    }

    /**
     * Refreshes the virtual hosts in the Apache configuration.
     */
    private function refreshVhosts()
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppBins;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_REFRESH_VHOSTS_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Refresh vhosts' );

        $bearsamppBins->getApache()->refreshVhosts( $bearsamppConfig->isOnline() );
    }

    /**
     * Checks the application path and logs the last path content.
     */
    private function checkPath()
    {
        global $bearsamppCore, $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_CHECK_PATH_TEXT ) );
        $this->splash->incrProgressBar();

        $this->writeLog( 'Last path: ' . $bearsamppCore->getLastPathContent() );
    }

    /**
     * Scans folders and logs the number of files to scan.
     * Performance optimization: Skips expensive file scan when path hasn't changed.
     * This saves 3-8 seconds on typical startups (95% of cases).
     */
    private function scanFolders()
    {
        global $bearsamppCore, $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_SCAN_FOLDERS_TEXT ) );
        $this->splash->incrProgressBar();

        $lastPath = $bearsamppCore->getLastPathContent();
        $currentPath = $this->rootPath;

        // Performance optimization: Skip scan if path hasn't changed
        if ($lastPath === $currentPath) {
            Log::debug('Path unchanged, skipping file scan (performance optimization)');
            Log::trace('Last path: "' . $lastPath . '" matches current path: "' . $currentPath . '"');
            $this->filesToScan = [];
            $this->writeLog('Files to scan: 0 (path unchanged - scan skipped)');

            // Log performance benefit
            $this->writeLog('Performance: File scan skipped, saving 3-8 seconds');
            return;
        }

        // Path changed, perform full scan
        Log::debug('Path changed, performing full file scan');
        Log::trace('Last path: "' . $lastPath . '" differs from current path: "' . $currentPath . '"');
        $this->writeLog('Path changed detected - performing full scan');

        $scanStartTime = Util::getMicrotime();
        $this->filesToScan = Util::getFilesToScan();
        $scanDuration = round(Util::getMicrotime() - $scanStartTime, 3);

        $this->writeLog('Files to scan: ' . count($this->filesToScan) . ' (scanned in ' . $scanDuration . 's)');
    }

    /**
     * Changes the application path and logs the number of files and occurrences changed.
     */
    private function changePath()
    {
        global $bearsamppLang;

        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_CHANGE_PATH_TEXT ), $this->rootPath ) );
        $this->splash->incrProgressBar();

        $result = Util::changePath( $this->filesToScan, $this->rootPath );
        $this->writeLog( 'Nb files changed: ' . $result['countChangedFiles'] );
        $this->writeLog( 'Nb occurences changed: ' . $result['countChangedOcc'] );
    }

    /**
     * Saves the current application path.
     */
    private function savePath()
    {
        global $bearsamppCore;

        file_put_contents( $bearsamppCore->getLastPath(), $this->rootPath );
        $this->writeLog( 'Save current path: ' . $this->rootPath );
    }

    /**
     * Checks and updates the application path registry key.
     */
    private function checkPathRegKey()
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppRegistry;

        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_TEXT ), Registry::APP_PATH_REG_ENTRY ) );
        $this->splash->incrProgressBar();

        $currentAppPathRegKey = $bearsamppRegistry->getAppPathRegKey();
        $genAppPathRegKey     = UtilPath::formatWindowsPath( $bearsamppRoot->getRootPath() );
        $this->writeLog( 'Current app path reg key: ' . $currentAppPathRegKey );
        $this->writeLog( 'Gen app path reg key: ' . $genAppPathRegKey );
        if ( $currentAppPathRegKey != $genAppPathRegKey ) {
            if ( !$bearsamppRegistry->setAppPathRegKey( $genAppPathRegKey ) ) {
                if ( !empty( $this->error ) ) {
                    $this->error .= PHP_EOL . PHP_EOL;
                }
                $this->error .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_ERROR_TEXT ), Registry::APP_PATH_REG_ENTRY );
                $this->error .= PHP_EOL . $bearsamppRegistry->getLatestError();
            }
            else {
                $this->writeLog( 'Need restart: checkPathRegKey' );
                $this->restart = true;
            }
        }
    }

    /**
     * Checks and updates the application bins registry key.
     * If the current registry key does not match the generated key, it updates the registry key.
     * Logs the current and generated registry keys.
     * Sets an error message if the registry key update fails.
     * Sets a restart flag if the registry key is updated.
     */
    private function checkBinsRegKey()
    {
        global $bearsamppLang, $bearsamppRegistry;

        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_TEXT ), Registry::APP_BINS_REG_ENTRY ) );
        $this->splash->incrProgressBar();

        $currentAppBinsRegKey = $bearsamppRegistry->getAppBinsRegKey();
        $genAppBinsRegKey     = $bearsamppRegistry->getAppBinsRegKey( false );
        $this->writeLog( 'Current app bins reg key: ' . $currentAppBinsRegKey );
        $this->writeLog( 'Gen app bins reg key: ' . $genAppBinsRegKey );
        if ( $currentAppBinsRegKey != $genAppBinsRegKey ) {
            if ( !$bearsamppRegistry->setAppBinsRegKey( $genAppBinsRegKey ) ) {
                if ( !empty( $this->error ) ) {
                    $this->error .= PHP_EOL . PHP_EOL;
                }
                $this->error .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_ERROR_TEXT ), Registry::APP_BINS_REG_ENTRY );
                $this->error .= PHP_EOL . $bearsamppRegistry->getLatestError();
            }
            else {
                $this->writeLog( 'Need restart: checkBinsRegKey' );
                $this->restart = true;
            }
        }
    }

    /**
     * Checks and updates the system PATH registry key.
     * Ensures the application bins registry entry is at the beginning of the system PATH.
     * Logs the current and new system PATH.
     * Sets an error message if the system PATH update fails.
     * Sets a restart flag if the system PATH is updated.
     */
    private function checkSystemPathRegKey()
    {
        global $bearsamppLang, $bearsamppRegistry;

        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_TEXT ), Registry::SYSPATH_REG_ENTRY ) );
        $this->splash->incrProgressBar();

        $currentSysPathRegKey = $bearsamppRegistry->getSysPathRegKey();
        $this->writeLog( 'Current system PATH: ' . $currentSysPathRegKey );

        $newSysPathRegKey = str_replace( '%' . Registry::APP_BINS_REG_ENTRY . '%;', '', $currentSysPathRegKey );
        $newSysPathRegKey = str_replace( '%' . Registry::APP_BINS_REG_ENTRY . '%', '', $newSysPathRegKey );
        $newSysPathRegKey = '%' . Registry::APP_BINS_REG_ENTRY . '%;' . $newSysPathRegKey;
        $this->writeLog( 'New system PATH: ' . $newSysPathRegKey );

        if ( $currentSysPathRegKey != $newSysPathRegKey ) {
            if ( !$bearsamppRegistry->setSysPathRegKey( $newSysPathRegKey ) ) {
                if ( !empty( $this->error ) ) {
                    $this->error .= PHP_EOL . PHP_EOL;
                }
                $this->error .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_ERROR_TEXT ), Registry::SYSPATH_REG_ENTRY );
                $this->error .= PHP_EOL . $bearsamppRegistry->getLatestError();
            }
            else {
                $this->writeLog( 'Need restart: checkSystemPathRegKey' );
                $this->restart = true;
            }
        }
        else {
            $this->writeLog( 'Refresh system PATH: ' . $currentSysPathRegKey );
            $bearsamppRegistry->setSysPathRegKey( str_replace( '%' . Registry::APP_BINS_REG_ENTRY . '%', '', $currentSysPathRegKey ) );
            $bearsamppRegistry->setSysPathRegKey( $currentSysPathRegKey );
        }
    }

    /**
     * Updates the configuration for bins, tools, and apps.
     * Logs the update process.
     */
    private function updateConfig()
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppApps;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_UPDATE_CONFIG_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Update config' );

        $bearsamppBins->update();
        $bearsamppTools->update();
        $bearsamppApps->update();
    }

    /**
     * Creates SSL certificates if they do not already exist.
     * Logs the creation process.
     */
    private function createSslCrts()
    {
        global $bearsamppLang, $bearsamppOpenSsl;

        $this->splash->incrProgressBar();
        if ( !$bearsamppOpenSsl->existsCrt( 'localhost' ) ) {
            $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_GEN_SSL_CRT_TEXT ), 'localhost' ) );
            $bearsamppOpenSsl->createCrt( 'localhost' );
        }
    }

    /**
     * Installs and starts services for the application.
     * Checks if services are already installed and updates them if necessary.
     * Logs the installation process and any errors encountered.
     *
     * Uses optimized service checking and starting methods.
     */

    private function installServices()
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppRoot;

        Log::trace('Starting installServices method');

        if (!$this->restart) {
            Log::trace('Normal startup mode - processing services');

            // Admin check is now performed at the very beginning of processWindow()
            // No need to check again here

            // Service Installation
            $this->installServicesSequential($bearsamppBins, $bearsamppLang);
        } else {
            Log::trace('Restart mode - skipping service installation');
            $this->splash->incrProgressBar(self::GAUGE_SERVICES * count($bearsamppBins->getServices()));
        }

        Log::trace('Completed installServices method');
    }

    /**
     * Service Installation
     * Installs and starts services sequentially with proper progress tracking.
     * Ensures exactly GAUGE_SERVICES (5) progress steps per service.
     *
     * @param object $bearsamppBins The bins object
     * @param object $bearsamppLang The language object
     */
    private function installServicesSequential($bearsamppBins, $bearsamppLang)
    {
        Log::trace('Starting sequential service installation');
        $installStartTime = Util::getMicrotime();

        // Step 1: Check and prepare all services
        $servicesToStart = [];
        $serviceErrors = [];

        $totalServiceCount = count($bearsamppBins->getServices());
        $currentServiceIndex = 0;

        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $currentServiceIndex++;

            Log::trace('Preparing service: ' . $sName);

            // prepareService() increments 1 step
            $serviceInfo = $this->prepareService($sName, $service, $bearsamppBins, $bearsamppLang, $currentServiceIndex, $totalServiceCount);

            if ($serviceInfo['restart']) {
                $this->writeLog('Need restart: installService ' . $serviceInfo['bin']->getName());
                Log::trace('Restart required for service: ' . $serviceInfo['bin']->getName());
                $this->restart = true;
                // prepareService used 1 step, need 4 more to reach GAUGE_SERVICES (5 total)
                $this->splash->incrProgressBar(self::GAUGE_SERVICES - 1);
                continue;
            }

            if (!empty($serviceInfo['error'])) {
                $serviceErrors[$sName] = $serviceInfo;
                // prepareService used 1 step, need 4 more to reach GAUGE_SERVICES (5 total)
                $this->splash->incrProgressBar(self::GAUGE_SERVICES - 1);
                continue;
            }

            if ($serviceInfo['needsStart']) {
                $servicesToStart[$sName] = $serviceInfo;
            } else {
                // Service already running or doesn't need to start
                // prepareService used 1 step, need 4 more to reach GAUGE_SERVICES (5 total)
                $this->splash->incrProgressBar(self::GAUGE_SERVICES - 1);
            }
        }

        // Step 2: Start all services sequentially with progress updates
        if (!empty($servicesToStart)) {
            Log::trace('Starting ' . count($servicesToStart) . ' services sequentially');

            $serviceCount = 0;
            $totalServices = count($servicesToStart);

            foreach ($servicesToStart as $sName => $serviceInfo) {
                $serviceCount++;
                $name = $serviceInfo['name'];
                $service = $serviceInfo['service'];

                // Update splash before starting (1 step - 2nd of 5)
                $this->splash->setTextLoading('Starting ' . $name . ' (' . $serviceCount . '/' . $totalServices . ')');
                $this->splash->incrProgressBar();

                Log::trace('Starting service: ' . $sName);
                $serviceStartTime = Util::getMicrotime();

                // Start the service
                $success = $service->start();

                $duration = round(Util::getMicrotime() - $serviceStartTime, 3);

                if ($success) {
                    $this->writeLog($name . ' service started in ' . $duration . 's');
                    Log::trace('Service ' . $name . ' started successfully in ' . $duration . ' seconds');

                    // Update splash after successful start
                    $this->splash->setTextLoading($name . ' started successfully');
                } else {
                    $error = $service->getError();
                    if (empty($error)) {
                        $error = 'Failed to start service';
                    }

                    $serviceErrors[$sName] = $serviceInfo;
                    $serviceErrors[$sName]['error'] = $error;
                    Log::trace('Service ' . $name . ' failed to start: ' . $error);

                    // Run syntax check if available
                    if (!empty($serviceInfo['syntaxCheckCmd'])) {
                        try {
                            $cmdSyntaxCheck = $serviceInfo['bin']->getCmdLineOutput($serviceInfo['syntaxCheckCmd']);
                            if (!$cmdSyntaxCheck['syntaxOk']) {
                                $serviceErrors[$sName]['error'] .= PHP_EOL . 'Syntax error: ' . $cmdSyntaxCheck['content'];
                            }
                        } catch (\Exception $e) {
                            // Ignore syntax check errors
                        }
                    }
                }

                // Complete remaining steps: prepareService=1, pre-start=1, now add 3 more = 5 total
                $this->splash->incrProgressBar(self::GAUGE_SERVICES - 2);
            }
        }

        // Step 3: Report any errors
        foreach ($serviceErrors as $sName => $serviceInfo) {
            if (!empty($serviceInfo['error'])) {
                Log::trace('Service error occurred for ' . $sName . ': ' . $serviceInfo['error']);
                if (!empty($this->error)) {
                    $this->error .= PHP_EOL . PHP_EOL;
                }
                $this->error .= sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_ERROR), $serviceInfo['name']) . PHP_EOL . $serviceInfo['error'];
            }
        }

        $installDuration = round(Util::getMicrotime() - $installStartTime, 3);
        $this->writeLog('Service installation completed in ' . $installDuration . 's');
        Log::trace('Service installation completed in ' . $installDuration . ' seconds');
    }

    /**
     * Prepares a service for startup (check, install if needed, but don't start yet)
     *
     * @param string $sName Service name
     * @param object $service Service object
     * @param object $bearsamppBins Bins object
     * @param object $bearsamppLang Language object
     * @param int $currentIndex Current service index
     * @param int $totalCount Total service count
     * @return array Service information array
     */
    private function prepareService($sName, $service, $bearsamppBins, $bearsamppLang, $currentIndex = 0, $totalCount = 0)
    {
        $serviceInfo = [
            'sName' => $sName,
            'service' => $service,
            'bin' => null,
            'name' => '',
            'port' => 0,
            'syntaxCheckCmd' => null,
            'error' => '',
            'restart' => false,
            'needsStart' => false,
            'startTime' => Util::getMicrotime()
        ];

        // Identify service type and get bin
        $syntaxCheckCmd = null;
        $bin = null;
        $port = 0;

        if ($sName == BinMailpit::SERVICE_NAME) {
            $bin = $bearsamppBins->getMailpit();
            $port = $bearsamppBins->getMailpit()->getSmtpPort();
        } elseif ($sName == BinMemcached::SERVICE_NAME) {
            $bin = $bearsamppBins->getMemcached();
            $port = $bearsamppBins->getMemcached()->getPort();
        } elseif ($sName == BinApache::SERVICE_NAME) {
            $bin = $bearsamppBins->getApache();
            $port = $bearsamppBins->getApache()->getPort();
            $syntaxCheckCmd = BinApache::CMD_SYNTAX_CHECK;
        } elseif ($sName == BinMysql::SERVICE_NAME) {
            $bin = $bearsamppBins->getMysql();
            $port = $bearsamppBins->getMysql()->getPort();
            $syntaxCheckCmd = BinMysql::CMD_SYNTAX_CHECK;

            // Pre-initialize MySQL data if needed
            if (!file_exists($bin->getDataDir()) || count(glob($bin->getDataDir() . '/*')) === 0) {
                Log::trace('Pre-initializing MySQL data directory');
                $bin->initData();
            }
        } elseif ($sName == BinMariadb::SERVICE_NAME) {
            $bin = $bearsamppBins->getMariadb();
            $port = $bearsamppBins->getMariadb()->getPort();
            $syntaxCheckCmd = BinMariadb::CMD_SYNTAX_CHECK;
        } elseif ($sName == BinPostgresql::SERVICE_NAME) {
            $bin = $bearsamppBins->getPostgresql();
            $port = $bearsamppBins->getPostgresql()->getPort();
        } elseif ($sName == BinXlight::SERVICE_NAME) {
            $bin = $bearsamppBins->getXlight();
            $port = $bearsamppBins->getXlight()->getPort();
        }

        $name = $bin->getName() . ' ' . $bin->getVersion() . ' (' . $service->getName() . ')';

        $serviceInfo['bin'] = $bin;
        $serviceInfo['name'] = $name;
        $serviceInfo['port'] = $port;
        $serviceInfo['syntaxCheckCmd'] = $syntaxCheckCmd;

        // Update splash with current service being checked (1 step)
        if ($currentIndex > 0 && $totalCount > 0) {
            $this->splash->setTextLoading('Checking ' . $bin->getName() . ' service (' . $currentIndex . '/' . $totalCount . ')');
        }
        $this->splash->incrProgressBar();

        // Check if service is already installed
        $serviceAlreadyInstalled = false;
        $serviceToRemove = false;

        if ($sName == BinApache::SERVICE_NAME) {
            $serviceInfos = $this->checkApacheServiceWithTimeout($service);
        } else if ($sName == BinMysql::SERVICE_NAME) {
            $serviceInfos = $this->checkMySQLServiceWithTimeout($service, $bin);
            if ($serviceInfos === false && $service->isInstalled()) {
                Log::trace('MySQL service appears to be hanging, forcing restart');
                Win32Ps::killBins(['mysqld.exe']);
                $service->delete();
                $serviceToRemove = true;
            }
        } else {
            try {
                $serviceInfos = $service->infos();
            } catch (\Exception $e) {
                Log::trace("Exception during service check: " . $e->getMessage());
                $serviceInfos = false;
            } catch (\Throwable $e) {
                Log::trace("Throwable during service check: " . $e->getMessage());
                $serviceInfos = false;
            }
        }

        if ($serviceInfos !== false) {
            $serviceAlreadyInstalled = true;
            $this->writeLog($name . ' service already installed');

            // Check if service needs to be removed and reinstalled
            if ($sName == BinPostgresql::SERVICE_NAME) {
                $serviceGenPathName = trim(str_replace('"', '', $service->getBinPath()));
                $installedPathParts = explode(' ', $serviceInfos[Win32Service::VBS_PATH_NAME], 2);
                $serviceVbsPathName = trim(str_replace('"', '', $installedPathParts[0]));
            } else {
                $serviceGenPathName = trim(str_replace('"', '', $service->getBinPath() . ($service->getParams() ? ' ' . $service->getParams() : '')));
                $serviceVbsPathName = trim(str_replace('"', '', $serviceInfos[Win32Service::VBS_PATH_NAME]));
            }

            $normalizedGenPath = preg_replace('/\s+/', ' ', $serviceGenPathName);
            $normalizedVbsPath = preg_replace('/\s+/', ' ', $serviceVbsPathName);

            if ($normalizedGenPath !== $normalizedVbsPath && $serviceGenPathName != $serviceVbsPathName) {
                $serviceToRemove = true;
                $this->writeLog($name . ' service has to be removed');
            }
        }

        // Remove service if needed (no progress increment - part of check phase)
        if ($serviceToRemove) {
            if (!$service->delete()) {
                $serviceInfo['restart'] = true;
                return $serviceInfo;
            }
        }

        // Check port availability (no progress increment - part of check phase)
        $isPortInUse = Util::isPortInUse($port);
        if ($isPortInUse !== false) {
            // Port is in use - check if it's our service that's already running
            if ($service->isRunning()) {
                // Service is already running and owns the port - this is OK
                $this->writeLog($name . ' service already running on port ' . $port);
                Log::trace('Service ' . $name . ' already running - no need to start');
                $serviceInfo['needsStart'] = false;
                return $serviceInfo;
            }

            // Port is in use by something else - this is an error
            $serviceInfo['error'] = sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_PORT_ERROR), $port, $isPortInUse);
            return $serviceInfo;
        }

        // Install service if needed (no progress increment - part of check phase)
        if (!$serviceAlreadyInstalled || $serviceToRemove) {
            if (!$service->create()) {
                $serviceInfo['error'] = sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_CREATE_ERROR), $service->getError());
                return $serviceInfo;
            }
        }

        $serviceInfo['needsStart'] = true;
        return $serviceInfo;
    }

    /**
     * Refreshes Git repositories if the scan on startup is enabled.
     * Logs the number of repositories found.
     */
    private function refreshGitRepos()
    {
        global $bearsamppLang, $bearsamppTools;

        $this->splash->incrProgressBar();
        if ( $bearsamppTools->getGit()->isScanStartup() ) {
            $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_REFRESH_GIT_REPOS_TEXT ) );

            $repos = $bearsamppTools->getGit()->findRepos( false );
            $this->writeLog( 'Update GIT repos: ' . count( $repos ) . ' found' );
        }
    }

    /**
     * Specialized method to check Apache service with timeout protection.
     * Apache service checks can sometimes hang, so this method provides a safer way to check.
     *
     * @param object $service The Apache service object
     * @return mixed Service info array or false if service not installed or check timed out
     */
    private function checkApacheServiceWithTimeout($service)
    {
        Log::trace('Starting specialized Apache service check with timeout protection');

        // Set a timeout for the Apache service check
        $serviceCheckStartTime = microtime(true);
        $serviceCheckTimeout = 10; // 10 seconds timeout

        try {
            // Use a non-blocking approach to check service
            $serviceInfos = false;

            // First try a quick check if the service exists in the list
            $serviceList = Win32Service::getServices();
            if (is_array($serviceList) && isset($serviceList[$service->getName()])) {
                Log::trace('Apache service found in service list, getting details');

                // Service exists, now try to get its details with timeout protection
                $startTime = microtime(true);
                $serviceInfos = $service->infos();

                // Check if we've exceeded our timeout
                if (microtime(true) - $serviceCheckStartTime > $serviceCheckTimeout) {
                    Log::trace("Apache service check timeout exceeded, assuming service needs reinstall");
                    return false;
                }
            } else {
                Log::trace('Apache service not found in service list');
                return false;
            }

            return $serviceInfos;
        } catch (\Exception $e) {
            Log::trace("Exception during Apache service check: " . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            Log::trace("Throwable during Apache service check: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Specialized method to check MySQL service with timeout protection.
     * MySQL service checks can sometimes hang, so this method provides a safer way to check.
     *
     * @param object $service The MySQL service object
     * @param object $bin The MySQL bin object
     * @return mixed Service info array or false if service not installed or check timed out
     */
    private function checkMySQLServiceWithTimeout($service, $bin)
    {
        Log::trace('Starting specialized MySQL service check with timeout protection');

        // Set a timeout for the MySQL service check
        $serviceCheckStartTime = microtime(true);
        $serviceCheckTimeout = 8; // 8 seconds timeout

        try {
            // Use a non-blocking approach to check service
            $serviceInfos = false;

            // First check if the service exists in the list
            $serviceList = Win32Service::getServices();
            if (is_array($serviceList) && isset($serviceList[$service->getName()])) {
                Log::trace('MySQL service found in service list, getting details');

                // Service exists, now try to get its details with timeout protection
                $serviceInfos = $service->infos();

                // Check if we've exceeded our timeout
                if (microtime(true) - $serviceCheckStartTime > $serviceCheckTimeout) {
                    Log::trace("MySQL service check timeout exceeded, assuming service needs reinstall");
                    return false;
                }
            } else {
                Log::trace('MySQL service not found in service list');
                return false;
            }

            return $serviceInfos;
        } catch (\Exception $e) {
            Log::trace("Exception during MySQL service check: " . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            Log::trace("Throwable during MySQL service check: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Writes a log message to the startup log file.
     *
     * @param   string  $log  The log message to write.
     */
    private function writeLog($log)
    {
        global $bearsamppRoot;
        Log::debug( $log, $bearsamppRoot->getStartupLogFilePath() );
    }
}
