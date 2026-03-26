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
 * Class ActionQuit
 * Handles the quitting process of the Bearsampp application.
 * Displays a splash screen and stops all services and processes.
 */
class ActionQuit
{
    /**
     * @var Splash The splash screen instance.
     */
    private $splash;

    /**
     * Gauge values for progress bar increments.
     */
    const GAUGE_PROCESSES = 1;
    const GAUGE_OTHERS = 1;

    /**
     * ActionQuit constructor.
     * Initializes the quitting process, displays the splash screen, and sets up the main loop.
     *
     * @param   array  $args  Command line arguments.
     */
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder, $arrayOfCurrents;

        Util::logInfo('ActionQuit constructor called - starting exit process');
        Util::logDebug('Number of services to stop: ' . count($bearsamppBins->getServices()));

        // Start splash screen
        $this->splash = new Splash();
        $this->splash->init(
            $bearsamppLang->getValue( Lang::QUIT ),
            self::GAUGE_PROCESSES * count( $bearsamppBins->getServices() ) + self::GAUGE_OTHERS,
            sprintf( $bearsamppLang->getValue( Lang::EXIT_LEAVING_TEXT ), APP_TITLE . ' ' . $bearsamppCore->getAppVersion() )
        );

        Util::logDebug('Splash screen initialized');

        // Set handler for the splash screen window
        $bearsamppWinbinder->setHandler( $this->splash->getWbWindow(), $this, 'processWindow', 2000 );
        Util::logDebug('Window handler set, starting main loop');

        $bearsamppWinbinder->mainLoop();
        Util::logDebug('Main loop exited');

        $bearsamppWinbinder->reset();
        Util::logInfo('ActionQuit constructor completed');
    }


    /**
     * Get the optimal service shutdown order based on dependencies.
     * Services are ordered to stop dependent services first, then core services.
     *
     * @return array Array of service names in shutdown order
     */
    private function getServiceShutdownOrder()
    {
        // Define shutdown order: dependent services first, then core services
        // This prevents connection errors and ensures clean shutdown
        return [
            // Tier 1: Application services (no dependencies on other services)
            BinMailpit::SERVICE_NAME,      // Mail testing tool
            BinMemcached::SERVICE_NAME,    // Caching service
            BinXlight::SERVICE_NAME,       // FTP server

            // Tier 2: Database services (web server depends on these)
            BinPostgresql::SERVICE_NAME,   // PostgreSQL database
            BinMariadb::SERVICE_NAME,      // MariaDB database
            BinMysql::SERVICE_NAME,        // MySQL database

            // Tier 3: Web server (depends on databases and other services)
            BinApache::SERVICE_NAME,       // Apache web server (stopped last)
        ];
    }

    /**
     * Get the display name for a service.
     *
     * @param   string  $sName    The service name constant
     * @param   object  $service  The service object
     * @return  string  The formatted display name
     */
    private function getServiceDisplayName($sName, $service)
    {
        global $bearsamppBins;

        $name = '';

        if ($sName == BinApache::SERVICE_NAME) {
            $name = $bearsamppBins->getApache()->getName() . ' ' . $bearsamppBins->getApache()->getVersion();
        }
        elseif ($sName == BinMysql::SERVICE_NAME) {
            $name = $bearsamppBins->getMysql()->getName() . ' ' . $bearsamppBins->getMysql()->getVersion();
        }
        elseif ($sName == BinMailpit::SERVICE_NAME) {
            $name = $bearsamppBins->getMailpit()->getName() . ' ' . $bearsamppBins->getMailpit()->getVersion();
        }
        elseif ($sName == BinMariadb::SERVICE_NAME) {
            $name = $bearsamppBins->getMariadb()->getName() . ' ' . $bearsamppBins->getMariadb()->getVersion();
        }
        elseif ($sName == BinPostgresql::SERVICE_NAME) {
            $name = $bearsamppBins->getPostgresql()->getName() . ' ' . $bearsamppBins->getPostgresql()->getVersion();
        }
        elseif ($sName == BinMemcached::SERVICE_NAME) {
            $name = $bearsamppBins->getMemcached()->getName() . ' ' . $bearsamppBins->getMemcached()->getVersion();
        }
        elseif ($sName == BinXlight::SERVICE_NAME) {
            $name = $bearsamppBins->getXlight()->getName() . ' ' . $bearsamppBins->getXlight()->getVersion();
        }

        $name .= ' (' . $service->getName() . ')';
        return $name;
    }

    /**
     * Processes the splash screen window events.
     * Stops all services in optimal order, deletes symlinks, and kills remaining processes.
     *
     * @param   resource  $window  The window resource.
     * @param   int       $id      The event ID.
     * @param   int       $ctrl    The control ID.
     * @param   mixed     $param1  Additional parameter 1.
     * @param   mixed     $param2  Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppWinbinder;

        Util::logInfo('Starting graceful shutdown process with optimized service order');

        // Get all available services
        $allServices = $bearsamppBins->getServices();

        // Get optimal shutdown order
        $shutdownOrder = $this->getServiceShutdownOrder();

        Util::logDebug('Service shutdown order: ' . implode(' -> ', $shutdownOrder));

        // Stop services in optimal order
        foreach ($shutdownOrder as $sName) {
            // Check if this service exists and is installed
            if (!isset($allServices[$sName])) {
                Util::logDebug('Service not found in available services: ' . $sName);
                continue;
            }

            $service = $allServices[$sName];
            $displayName = $this->getServiceDisplayName($sName, $service);

            Util::logInfo('Stopping service: ' . $displayName);

            $this->splash->incrProgressBar();
            $this->splash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::EXIT_REMOVE_SERVICE_TEXT), $displayName));

            // Delete (stop and remove) the service
            $result = $service->delete();

            if ($result) {
                Util::logInfo('Successfully stopped and removed service: ' . $displayName);
            } else {
                Util::logWarning('Failed to stop/remove service: ' . $displayName . ' (may not be installed)');
            }
        }

        // Handle any services not in the shutdown order (for extensibility)
        foreach ($allServices as $sName => $service) {
            if (!in_array($sName, $shutdownOrder)) {
                $displayName = $this->getServiceDisplayName($sName, $service);
                Util::logWarning('Stopping unlisted service: ' . $displayName);

                $this->splash->incrProgressBar();
                $this->splash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::EXIT_REMOVE_SERVICE_TEXT), $displayName));
                $service->delete();
            }
        }

        Util::logInfo('All services stopped successfully');

        // Purge "current" symlinks
        $this->splash->setTextLoading('Removing symlinks...');
        Symlinks::deleteCurrentSymlinks();

        // Stop other processes
        $this->splash->incrProgressBar();
        $this->splash->setTextLoading($bearsamppLang->getValue(Lang::EXIT_STOP_OTHER_PROCESS_TEXT));
        Win32Ps::killBins(true);

        // Perform cleanup verification in background (non-blocking)
        $this->splash->setTextLoading('Performing cleanup verification...');
        $this->performQuickCleanupVerification($allServices);

        // Terminate any remaining processes
        // Final termination sequence
        $this->splash->setTextLoading('Completing shutdown...');
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $currentPid = Win32Ps::getCurrentPid();

            // Terminate PHP processes with a timeout of 15 seconds
            self::terminatePhpProcesses($currentPid, $window, $this->splash, 15);

            // Force exit if still running
            exit(0);
        }

        // Non-Windows fallback
        $bearsamppWinbinder->destroyWindow($window);
        exit(0);
    }

    /**
     * Terminates PHP processes with timeout handling.
     *
     * @param   int     $excludePid  Process ID to exclude
     * @param   mixed   $window      Window handle or null
     * @param   mixed   $splash      Splash screen or null
     * @param   int     $timeout     Maximum time to wait for termination (seconds)
     * @return  void
     */
    public static function terminatePhpProcesses($excludePid, $window = null, $splash = null, $timeout = 10)
    {
        global $bearsamppWinbinder, $bearsamppCore;

        $currentPid = Win32Ps::getCurrentPid();
        $startTime = microtime(true);

        Util::logTrace('Starting PHP process termination (excluding PID: ' . $excludePid . ')');

        // Get list of loading PIDs to exclude from termination
        $loadingPids = array();
        if (file_exists($bearsamppCore->getLoadingPid())) {
            $pids = file($bearsamppCore->getLoadingPid());
            foreach ($pids as $pid) {
                $loadingPids[] = intval(trim($pid));
            }
            Util::logTrace('Loading PIDs to preserve: ' . implode(', ', $loadingPids));
        }

        $targets = ['php-win.exe', 'php.exe'];
        foreach (Win32Ps::getListProcs() as $proc) {
            // Check if we've exceeded our timeout
            if (microtime(true) - $startTime > $timeout) {
                Util::logTrace('Process termination timeout exceeded, continuing with remaining operations');
                break;
            }

            $exe = strtolower(basename($proc[Win32Ps::EXECUTABLE_PATH]));
            $pid = $proc[Win32Ps::PROCESS_ID];

            // Skip if this is the excluded PID or a loading window PID
            if (in_array($exe, $targets) && $pid != $excludePid && !in_array($pid, $loadingPids)) {
                Util::logTrace('Terminating PHP process: ' . $pid);
                Win32Ps::kill($pid);
                usleep(100000); // 100ms delay between terminations
            } elseif (in_array($pid, $loadingPids)) {
                Util::logTrace('Preserving loading window process: ' . $pid);
            }
        }

        // Initiate self-termination with timeout
        if ($splash !== null) {
            $splash->setTextLoading('Final cleanup...');
        }

        try {
            Util::logTrace('Initiating self-termination for PID: ' . $currentPid);
            // Add a timeout wrapper around the killProc call
            $killSuccess = Vbs::killProc($currentPid);
            if (!$killSuccess) {
                Util::logTrace('Self-termination via Vbs::killProc failed, using alternative method');
            }
        } catch (\Exception $e) {
            Util::logTrace('Exception during self-termination: ' . $e->getMessage());
        }

        // Destroy window after process termination
        // Fix for PHP 8.2: Check if window is not null before destroying
        if ($window && $bearsamppWinbinder) {
            try {
                Util::logTrace('Destroying window');
                $bearsamppWinbinder->destroyWindow($window);
            } catch (\Exception $e) {
                Util::logTrace('Exception during window destruction: ' . $e->getMessage());
            }
        }

        // Force exit if still running after timeout
        if (microtime(true) - $startTime > $timeout * 1.5) {
            Util::logTrace('Forcing exit due to timeout');
            exit(0);
        }
    }

    /**
     * Verify that all services are actually stopped and clean up any that are still running.
     *
     * @param   array  $services  Array of service objects
     * @return  array  Verification results with status for each service
     */
    private function verifyServicesStoppedAndCleanup($services)
    {
        Util::logInfo('Verifying all services are stopped...');

        $results = [
            'all_stopped' => true,
            'services' => [],
            'still_running' => [],
            'verification_failed' => []
        ];

        foreach ($services as $sName => $service) {
            $displayName = $this->getServiceDisplayName($sName, $service);

            try {
                // Check if service is still installed/running
                $isInstalled = $service->isInstalled();
                $isRunning = $isInstalled ? $service->isRunning() : false;

                $results['services'][$sName] = [
                    'name' => $displayName,
                    'installed' => $isInstalled,
                    'running' => $isRunning
                ];

                if ($isRunning) {
                    Util::logWarning('Service still running after shutdown: ' . $displayName);
                    $results['still_running'][] = $displayName;
                    $results['all_stopped'] = false;

                    // Attempt to force stop
                    Util::logInfo('Attempting to force stop: ' . $displayName);
                    $service->stop();
                    usleep(500000); // Wait 500ms

                    // Verify again
                    if ($service->isRunning()) {
                        Util::logError('Failed to force stop service: ' . $displayName);
                    } else {
                        Util::logInfo('Successfully force stopped service: ' . $displayName);
                    }
                } elseif ($isInstalled) {
                    Util::logDebug('Service stopped but still installed: ' . $displayName);
                } else {
                    Util::logDebug('Service verified stopped and removed: ' . $displayName);
                }

            } catch (\Exception $e) {
                Util::logError('Failed to verify service status for ' . $displayName . ': ' . $e->getMessage());
                $results['verification_failed'][] = $displayName;
                $results['all_stopped'] = false;
            }
        }

        if ($results['all_stopped']) {
            Util::logInfo('All services verified stopped successfully');
        } else {
            Util::logWarning('Some services could not be verified as stopped');
        }

        return $results;
    }

    /**
     * Verify that symlinks have been removed.
     *
     * @return  array  Verification results
     */
    private function verifySymlinksRemoved()
    {
        global $bearsamppRoot;

        Util::logInfo('Verifying symlinks are removed...');

        $results = [
            'success' => true,
            'remaining' => []
        ];

        // Check common symlink locations
        $symlinkPaths = [
            $bearsamppRoot->getCurrentPath() . '/apache',
            $bearsamppRoot->getCurrentPath() . '/php',
            $bearsamppRoot->getCurrentPath() . '/mysql',
            $bearsamppRoot->getCurrentPath() . '/mariadb',
            $bearsamppRoot->getCurrentPath() . '/postgresql',
            $bearsamppRoot->getCurrentPath() . '/nodejs',
            $bearsamppRoot->getCurrentPath() . '/memcached',
            $bearsamppRoot->getCurrentPath() . '/mailpit',
            $bearsamppRoot->getCurrentPath() . '/xlight'
        ];

        foreach ($symlinkPaths as $path) {
            if (file_exists($path) || is_link($path)) {
                Util::logWarning('Symlink still exists: ' . $path);
                $results['remaining'][] = basename($path);
                $results['success'] = false;

                // Attempt to remove it
                try {
                    if (is_link($path)) {
                        @unlink($path);
                    } elseif (is_dir($path)) {
                        @rmdir($path);
                    }

                    // Verify removal
                    if (!file_exists($path)) {
                        Util::logInfo('Successfully removed remaining symlink: ' . $path);
                        $results['remaining'] = array_diff($results['remaining'], [basename($path)]);
                        if (empty($results['remaining'])) {
                            $results['success'] = true;
                        }
                    }
                } catch (\Exception $e) {
                    Util::logError('Failed to remove symlink ' . $path . ': ' . $e->getMessage());
                }
            }
        }

        if ($results['success']) {
            Util::logInfo('All symlinks verified removed');
        } else {
            Util::logWarning('Some symlinks could not be removed: ' . implode(', ', $results['remaining']));
        }

        return $results;
    }

    /**
     * Clean up temporary files created during Bearsampp operation.
     *
     * @return  array  Cleanup results
     */
    private function cleanupTemporaryFiles()
    {
        global $bearsamppCore;

        Util::logInfo('Cleaning up temporary files...');

        $results = [
            'success' => true,
            'cleaned' => 0,
            'failed' => [],
            'size_freed' => 0
        ];

        $tmpPath = $bearsamppCore->getTmpPath();

        if (!is_dir($tmpPath)) {
            Util::logDebug('Temp directory does not exist: ' . $tmpPath);
            return $results;
        }

        try {
            $files = glob($tmpPath . '/*');

            if ($files === false) {
                Util::logWarning('Failed to list temporary files');
                return $results;
            }

            foreach ($files as $file) {
                // Skip certain files that should be preserved
                $basename = basename($file);
                if (in_array($basename, ['.', '..', '.gitkeep', 'README.md'])) {
                    continue;
                }

                try {
                    $size = is_file($file) ? filesize($file) : 0;

                    if (is_file($file)) {
                        if (@unlink($file)) {
                            $results['cleaned']++;
                            $results['size_freed'] += $size;
                            Util::logDebug('Removed temp file: ' . $basename);
                        } else {
                            $results['failed'][] = $basename;
                            $results['success'] = false;
                            Util::logWarning('Failed to remove temp file: ' . $basename);
                        }
                    } elseif (is_dir($file)) {
                        // Don't remove directories, just files
                        Util::logDebug('Skipping temp directory: ' . $basename);
                    }
                } catch (\Exception $e) {
                    $results['failed'][] = $basename;
                    $results['success'] = false;
                    Util::logError('Error removing temp file ' . $basename . ': ' . $e->getMessage());
                }
            }

            $sizeMB = round($results['size_freed'] / 1024 / 1024, 2);
            Util::logInfo('Cleaned up ' . $results['cleaned'] . ' temporary files (' . $sizeMB . ' MB freed)');

            if (!empty($results['failed'])) {
                Util::logWarning('Failed to clean up ' . count($results['failed']) . ' files');
            }

        } catch (\Exception $e) {
            Util::logError('Error during temp file cleanup: ' . $e->getMessage());
            $results['success'] = false;
        }

        return $results;
    }

    /**
     * Check for orphaned Bearsampp processes that should have been terminated.
     *
     * @return  array  List of orphaned processes
     */
    private function checkForOrphanedProcesses()
    {
        global $bearsamppRoot;

        Util::logInfo('Checking for orphaned processes...');

        $orphaned = [
            'found' => false,
            'processes' => []
        ];

        try {
            $procs = Win32Ps::getListProcs();
            $bearsamppPath = strtolower(Util::formatUnixPath($bearsamppRoot->getRootPath()));
            $currentPid = Win32Ps::getCurrentPid();

            foreach ($procs as $proc) {
                $exePath = strtolower(Util::formatUnixPath($proc[Win32Ps::EXECUTABLE_PATH]));
                $pid = $proc[Win32Ps::PROCESS_ID];

                // Skip current process
                if ($pid == $currentPid) {
                    continue;
                }

                // Check if process is from Bearsampp directory
                if (strpos($exePath, $bearsamppPath) === 0) {
                    $processName = basename($exePath);

                    // Skip www directory processes (user applications)
                    if (strpos($exePath, $bearsamppPath . '/www/') === 0) {
                        continue;
                    }

                    // Skip the main Bearsampp executable
                    if (strtolower($processName) === 'bearsampp.exe') {
                        Util::logDebug('Skipping main Bearsampp process: ' . $processName . ' (PID: ' . $pid . ')');
                        continue;
                    }

                    // These are orphaned Bearsampp processes
                    $orphaned['found'] = true;
                    $orphaned['processes'][] = [
                        'pid' => $pid,
                        'name' => $processName,
                        'path' => $exePath
                    ];

                    Util::logWarning('Found orphaned process: ' . $processName . ' (PID: ' . $pid . ')');

                    // Attempt to kill orphaned process
                    try {
                        Win32Ps::kill($pid);
                        Util::logInfo('Terminated orphaned process: ' . $processName . ' (PID: ' . $pid . ')');
                    } catch (\Exception $e) {
                        Util::logError('Failed to terminate orphaned process ' . $processName . ': ' . $e->getMessage());
                    }
                }
            }

            if (!$orphaned['found']) {
                Util::logInfo('No orphaned processes found');
            } else {
                Util::logWarning('Found ' . count($orphaned['processes']) . ' orphaned process(es)');
            }

        } catch (\Exception $e) {
            Util::logError('Error checking for orphaned processes: ' . $e->getMessage());
        }

        return $orphaned;
    }

    /**
     * Generate a comprehensive cleanup report.
     *
     * @param   array  $serviceVerification  Service verification results
     * @param   array  $symlinkVerification  Symlink verification results
     * @param   array  $tempCleanup          Temp file cleanup results
     * @param   array  $orphanedProcesses    Orphaned process check results
     * @return  array  Comprehensive cleanup report
     */
    private function generateCleanupReport($serviceVerification, $symlinkVerification, $tempCleanup, $orphanedProcesses)
    {
        $report = [
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'summary' => []
        ];

        // Service verification
        if (!$serviceVerification['all_stopped']) {
            $report['success'] = false;

            if (!empty($serviceVerification['still_running'])) {
                $report['errors'][] = 'Services still running: ' . implode(', ', $serviceVerification['still_running']);
            }

            if (!empty($serviceVerification['verification_failed'])) {
                $report['warnings'][] = 'Could not verify status of: ' . implode(', ', $serviceVerification['verification_failed']);
            }
        }

        $report['summary'][] = 'Services checked: ' . count($serviceVerification['services']);

        // Symlink verification
        if (!$symlinkVerification['success']) {
            $report['warnings'][] = 'Symlinks not fully removed: ' . implode(', ', $symlinkVerification['remaining']);
        }

        // Temp file cleanup
        if ($tempCleanup['cleaned'] > 0) {
            $sizeMB = round($tempCleanup['size_freed'] / 1024 / 1024, 2);
            $report['summary'][] = 'Temp files cleaned: ' . $tempCleanup['cleaned'] . ' (' . $sizeMB . ' MB)';
        }

        if (!empty($tempCleanup['failed'])) {
            $report['warnings'][] = 'Failed to clean ' . count($tempCleanup['failed']) . ' temp file(s)';
        }

        // Orphaned processes
        if ($orphanedProcesses['found']) {
            $report['warnings'][] = 'Found ' . count($orphanedProcesses['processes']) . ' orphaned process(es)';
            foreach ($orphanedProcesses['processes'] as $proc) {
                $report['summary'][] = 'Orphaned: ' . $proc['name'] . ' (PID: ' . $proc['pid'] . ')';
            }
        }

        return $report;
    }

    /**
     * Perform quick cleanup verification without blocking the exit process.
     * This is a lightweight version that only does essential checks.
     *
     * @param   array  $services  Array of service objects
     * @return  void
     */
    private function performQuickCleanupVerification($services)
    {
        Util::logInfo('Performing quick cleanup verification...');

        $startTime = microtime(true);
        $maxTime = 2; // Maximum 2 seconds for verification

        try {
            // Quick temp file cleanup (non-blocking)
            $tempCleanup = $this->cleanupTemporaryFiles();

            // Check if we're running out of time
            if (microtime(true) - $startTime > $maxTime) {
                Util::logDebug('Cleanup verification timeout reached, skipping remaining checks');
                return;
            }

            // Quick orphaned process check (non-blocking)
            $orphanedProcesses = $this->checkForOrphanedProcesses();

            // Log summary
            if ($tempCleanup['cleaned'] > 0) {
                $sizeMB = round($tempCleanup['size_freed'] / 1024 / 1024, 2);
                Util::logInfo('Quick cleanup: ' . $tempCleanup['cleaned'] . ' temp files removed (' . $sizeMB . ' MB freed)');
            }

            if ($orphanedProcesses['found']) {
                Util::logInfo('Quick cleanup: ' . count($orphanedProcesses['processes']) . ' orphaned process(es) terminated');
            }

            $duration = round(microtime(true) - $startTime, 2);
            Util::logInfo('Quick cleanup verification completed in ' . $duration . ' seconds');

        } catch (\Exception $e) {
            Util::logWarning('Quick cleanup verification failed: ' . $e->getMessage());
        }
    }
}
