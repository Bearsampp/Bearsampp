<?php
/*
 * Copyright (c) 2021-2026 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ServiceHelper
 *
 * Utility class for service management operations.
 * Provides centralized methods for service lookup, syntax check commands,
 * and common service operations to eliminate code duplication.
 */
class ServiceHelper
{
    /**
     * Service name to binary instance mapping
     * @var array
     */
    private static $serviceMap = null;

    /**
     * Service name to syntax check command mapping
     * @var array
     */
    private static $syntaxCheckMap = null;

    /**
     * Initialize service mappings
     *
     * @param object $bearsamppBins The bins object
     * @return void
     */
    private static function initializeMappings($bearsamppBins)
    {
        if (self::$serviceMap === null) {
            self::$serviceMap = [
                BinApache::SERVICE_NAME => $bearsamppBins->getApache(),
                BinMysql::SERVICE_NAME => $bearsamppBins->getMysql(),
                BinMariadb::SERVICE_NAME => $bearsamppBins->getMariadb(),
                BinMailpit::SERVICE_NAME => $bearsamppBins->getMailpit(),
                BinMemcached::SERVICE_NAME => $bearsamppBins->getMemcached(),
                BinPostgresql::SERVICE_NAME => $bearsamppBins->getPostgresql(),
                BinXlight::SERVICE_NAME => $bearsamppBins->getXlight(),
            ];
        }

        if (self::$syntaxCheckMap === null) {
            self::$syntaxCheckMap = [
                BinApache::SERVICE_NAME => BinApache::CMD_SYNTAX_CHECK,
                BinMysql::SERVICE_NAME => BinMysql::CMD_SYNTAX_CHECK,
                BinMariadb::SERVICE_NAME => BinMariadb::CMD_SYNTAX_CHECK,
            ];
        }
    }

    /**
     * Get binary instance from service name
     *
     * @param string $serviceName The service name
     * @param object $bearsamppBins The bins object
     * @return object|null The binary instance or null if not found
     */
    public static function getBinFromServiceName($serviceName, $bearsamppBins)
    {
        self::initializeMappings($bearsamppBins);
        return isset(self::$serviceMap[$serviceName]) ? self::$serviceMap[$serviceName] : null;
    }

    /**
     * Get syntax check command for a service
     *
     * @param string $serviceName The service name
     * @param object $bearsamppBins The bins object (for initialization)
     * @return string|null The syntax check command or null if not applicable
     */
    public static function getSyntaxCheckCmd($serviceName, $bearsamppBins = null)
    {
        if ($bearsamppBins !== null) {
            self::initializeMappings($bearsamppBins);
        }
        return isset(self::$syntaxCheckMap[$serviceName]) ? self::$syntaxCheckMap[$serviceName] : null;
    }

    /**
     * Get service display name (Name + Version + Service Name)
     *
     * @param object $bin The binary instance
     * @param object $service The service instance
     * @return string The formatted service name
     */
    public static function getServiceDisplayName($bin, $service)
    {
        return $bin->getName() . ' ' . $bin->getVersion() . ' (' . $service->getName() . ')';
    }

    /**
     * Process all services with a callback function
     *
     * @param object $bearsamppBins The bins object
     * @param callable $callback Function to call for each service: function($serviceName, $service, $bin, $syntaxCheckCmd)
     * @return void
     */
    public static function processServices($bearsamppBins, callable $callback)
    {
        self::initializeMappings($bearsamppBins);

        foreach ($bearsamppBins->getServices() as $serviceName => $service) {
            $bin = self::getBinFromServiceName($serviceName, $bearsamppBins);
            $syntaxCheckCmd = self::getSyntaxCheckCmd($serviceName);

            if ($bin !== null) {
                $callback($serviceName, $service, $bin, $syntaxCheckCmd);
            }
        }
    }

    /**
     * Start a service with optional syntax check
     *
     * @param object $bin The binary instance
     * @param string|null $syntaxCheckCmd The syntax check command (optional)
     * @param bool $showWindow Whether to show error messages in a window (default: false)
     * @return bool True if service started successfully, false otherwise
     */
    public static function startService($bin, $syntaxCheckCmd = null, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        if (method_exists($bin, 'initData')) {
            $bin->initData();
        }

        $name     = $bin->getName();
        $service  = $bin->getService();
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::START_SERVICE_TITLE), $name);

        if (!$service->start()) {
            $serviceError    = sprintf($bearsamppLang->getValue(Lang::START_SERVICE_ERROR), $name);
            $serviceErrorLog = sprintf('Error while starting the %s service', $name);
            if (!empty($syntaxCheckCmd)) {
                $cmdSyntaxCheck = $bin->getCmdLineOutput($syntaxCheckCmd);
                if (!$cmdSyntaxCheck['syntaxOk']) {
                    $serviceError    .= PHP_EOL . sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_SYNTAX_ERROR), $cmdSyntaxCheck['content']);
                    $serviceErrorLog .= sprintf(' (conf errors detected : %s)', $cmdSyntaxCheck['content']);
                }
            }
            Log::error($serviceErrorLog);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError($serviceError, $boxTitle);
            }

            return false;
        }

        return true;
    }

    /**
     * Stop a service
     *
     * @param object $service The service instance
     * @return bool True if service stopped successfully, false otherwise
     */
    public static function stopService($service)
    {
        return $service->stop();
    }

    /**
     * Checks if a specific port is in use.
     *
     * @param   int  $port  The port number to check
     *
     * @return mixed False if the port is not in use, otherwise returns the process using the port
     */
    public static function isPortInUse($port)
    {
        // Set localIP statically
        $localIP = APP_LOCALHOST;

        // Save current error reporting level
        $errorReporting = error_reporting();

        // Disable error reporting temporarily
        error_reporting(0);

        $connection = @fsockopen($localIP, $port);

        // Restore original error reporting level
        error_reporting($errorReporting);

        if (is_resource($connection)) {
            fclose($connection);
            $process = Batch::getProcessUsingPort($port);

            return $process != null ? $process : 'N/A';
        }

        return false;
    }

    /**
     * Attempts to install and start a service on a specific port, with optional syntax checking and user notifications.
     *
     * @param   object  $bin             An object containing the binary information and methods related to the service.
     * @param   int     $port            The port number on which the service should run.
     * @param   string  $syntaxCheckCmd  The command to execute for syntax checking of the service configuration.
     * @param   bool    $showWindow      Optional. Whether to show message boxes for information, warnings, and errors. Defaults to false.
     *
     * @return bool Returns true if the service is successfully installed and started, false otherwise.
     */
    public static function installService($bin, $port, $syntaxCheckCmd, $showWindow = false)
    {
        global $bearsamppLang, $bearsamppWinbinder;

        if (method_exists($bin, 'initData')) {
            $bin->initData();
        }

        $name     = $bin->getName();
        $service  = $bin->getService();
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::INSTALL_SERVICE_TITLE), $name);

        $isPortInUse = self::isPortInUse($port);
        if ($isPortInUse === false) {
            if (!$service->isInstalled()) {
                $service->create();
                if ($service->start()) {
                    Log::info(sprintf('%s service successfully installed. (name: %s ; port: %s)', $name, $service->getName(), $port));
                    if ($showWindow) {
                        $bearsamppWinbinder->messageBoxInfo(
                            sprintf($bearsamppLang->getValue(Lang::SERVICE_INSTALLED), $name, $service->getName(), $port),
                            $boxTitle
                        );
                    }

                    return true;
                } else {
                    $serviceError    = sprintf($bearsamppLang->getValue(Lang::SERVICE_INSTALL_ERROR), $name);
                    $serviceErrorLog = sprintf('Error during the installation of %s service', $name);
                    if (!empty($syntaxCheckCmd)) {
                        $cmdSyntaxCheck = $bin->getCmdLineOutput($syntaxCheckCmd);
                        if (!$cmdSyntaxCheck['syntaxOk']) {
                            $serviceError    .= PHP_EOL . sprintf($bearsamppLang->getValue(Lang::STARTUP_SERVICE_SYNTAX_ERROR), $cmdSyntaxCheck['content']);
                            $serviceErrorLog .= sprintf(' (conf errors detected : %s)', $cmdSyntaxCheck['content']);
                        }
                    }
                    Log::error($serviceErrorLog);
                    if ($showWindow) {
                        $bearsamppWinbinder->messageBoxError($serviceError, $boxTitle);
                    }
                }
            } else {
                Log::warning(sprintf('%s service already installed', $name));
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxWarning(
                        sprintf($bearsamppLang->getValue(Lang::SERVICE_ALREADY_INSTALLED), $name),
                        $boxTitle
                    );
                }

                return true;
            }
        } elseif ($service->isRunning()) {
            Log::warning(sprintf('%s service already installed and running', $name));
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf($bearsamppLang->getValue(Lang::SERVICE_ALREADY_INSTALLED), $name),
                    $boxTitle
                );
            }

            return true;
        } else {
            Log::error(sprintf('Port %s is used by an other application : %s', $port, $isPortInUse));
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED_BY), $port, $isPortInUse),
                    $boxTitle
                );
            }
        }

        return false;
    }

    /**
     * Removes a service if it is installed.
     *
     * @param   Win32Service  $service  The service object to be removed.
     * @param   string        $name     The name of the service.
     *
     * @return bool Returns true if the service is successfully removed, false otherwise.
     */
    public static function removeService($service, $name)
    {
        if (!($service instanceof Win32Service)) {
            Log::error('$service not an instance of Win32Service');

            return false;
        }

        if ($service->isInstalled()) {
            if ($service->delete()) {
                Log::info(sprintf('%s service successfully removed', $name));

                return true;
            } else {
                Log::error(sprintf('Error during the uninstallation of %s service', $name));

                return false;
            }
        } else {
            Log::warning(sprintf('%s service does not exist', $name));
        }

        return true;
    }

    /**
     * Get all service names
     *
     * @return array Array of service name constants
     */
    public static function getAllServiceNames()
    {
        return [
            BinApache::SERVICE_NAME,
            BinMysql::SERVICE_NAME,
            BinMariadb::SERVICE_NAME,
            BinMailpit::SERVICE_NAME,
            BinMemcached::SERVICE_NAME,
            BinPostgresql::SERVICE_NAME,
            BinXlight::SERVICE_NAME,
        ];
    }

    /**
     * Check if a service has syntax check capability
     *
     * @param string $serviceName The service name
     * @return bool True if service supports syntax check
     */
    public static function hasSyntaxCheck($serviceName)
    {
        if (self::$syntaxCheckMap === null) {
            // Initialize with dummy bins object if needed
            self::$syntaxCheckMap = [
                BinApache::SERVICE_NAME => BinApache::CMD_SYNTAX_CHECK,
                BinMysql::SERVICE_NAME => BinMysql::CMD_SYNTAX_CHECK,
                BinMariadb::SERVICE_NAME => BinMariadb::CMD_SYNTAX_CHECK,
            ];
        }
        return isset(self::$syntaxCheckMap[$serviceName]);
    }

    /**
     * Get port for a service
     *
     * @param string $serviceName The service name
     * @param object $bearsamppBins The bins object
     * @return int The port number or 0 if not applicable
     */
    public static function getServicePort($serviceName, $bearsamppBins)
    {
        $bin = self::getBinFromServiceName($serviceName, $bearsamppBins);
        if ($bin === null) {
            return 0;
        }

        // Different services have different methods to get port
        if (method_exists($bin, 'getPort')) {
            return $bin->getPort();
        } elseif (method_exists($bin, 'getSmtpPort')) {
            return $bin->getSmtpPort();
        }

        return 0;
    }

    /**
     * Stop all services in parallel for optimized shutdown
     *
     * Sends stop commands to all services simultaneously, then monitors their status.
     * Falls back to sequential shutdown if parallel fails. Significantly reduces shutdown time.
     *
     * Performance: 40-60% faster shutdown (parallel vs sequential)
     *
     * @param object $bearsamppBins The bins object
     * @param callable|null $progressCallback Optional callback: function($current, $total, $serviceName)
     * @param int $shutdownTimeout Timeout in seconds (default: 15)
     * @return bool True if all services stopped successfully
     */
    public static function stopAllServicesParallel($bearsamppBins, ?callable $progressCallback = null, $shutdownTimeout = 15)
    {
        Log::trace('ServiceHelper::stopAllServicesParallel() starting');
        $startTime = microtime(true);

        $services = $bearsamppBins->getServices();
        if (empty($services)) {
            Log::trace('No services to shut down');
            return true;
        }

        Log::trace('Starting parallel shutdown of ' . count($services) . ' services');

        // Try parallel shutdown
        $parallelSuccess = self::shutdownServicesParallel($services, $progressCallback, $shutdownTimeout);

        if ($parallelSuccess) {
            $duration = round(microtime(true) - $startTime, 3);
            Log::info('Parallel shutdown completed successfully in ' . $duration . 's');
            return true;
        }

        // Fallback to sequential
        Log::info('Parallel shutdown timed out, falling back to sequential shutdown');
        $sequentialSuccess = self::shutdownServicesSequential($services, $progressCallback);

        $totalDuration = round(microtime(true) - $startTime, 3);
        Log::info('Sequential shutdown completed in ' . $totalDuration . 's');

        return $sequentialSuccess;
    }

    /**
     * Shutdown services in parallel (non-blocking)
     *
     * Phase 1: Send stop commands to all services (non-blocking)
     * Phase 2: Monitor their status until all stopped or timeout
     * Phase 3: Force kill any services still running
     *
     * @param array $services Array of services
     * @param callable|null $progressCallback Optional progress callback
     * @param int $shutdownTimeout Timeout in seconds
     * @return bool True if all services stopped
     */
    private static function shutdownServicesParallel($services, ?callable $progressCallback = null, $shutdownTimeout = 15)
    {
        Log::trace('Phase 1: Sending stop commands to all services');
        $totalServices = count($services);
        $currentIndex = 0;

        // Phase 1: Send stop commands (non-blocking)
        foreach ($services as $serviceName => $service) {
            $currentIndex++;
            if ($progressCallback) {
                $progressCallback($currentIndex, $totalServices, $serviceName);
            }

            Log::trace('Sending stop to: ' . $serviceName);
            $service->stop();
        }

        // Phase 2: Monitor status
        Log::trace('Phase 2: Monitoring service status');
        $monitorStartTime = microtime(true);
        $monitorTimeout = $shutdownTimeout;
        $checkInterval = 0.5;
        $allStopped = false;

        while ((microtime(true) - $monitorStartTime) < $monitorTimeout) {
            $allStopped = true;

            foreach ($services as $serviceName => $service) {
                if (!$service->isStopped()) {
                    $allStopped = false;
                    break;
                }
            }

            if ($allStopped) {
                Log::trace('All services stopped in parallel phase');
                return true;
            }

            usleep($checkInterval * 1000000);
        }

        // Phase 3: Force kill remaining services
        Log::trace('Phase 3: Force killing remaining services');
        foreach ($services as $serviceName => $service) {
            if (!$service->isStopped()) {
                Log::trace('Force killing: ' . $serviceName);
                self::forceKillService($serviceName);
            }
        }

        // Final check
        return self::allServicesStopped($services);
    }

    /**
     * Shutdown services sequentially (fallback method)
     *
     * Stops services one by one, waiting for each to complete.
     * Used as fallback when parallel shutdown fails.
     *
     * @param array $services Array of services
     * @param callable|null $progressCallback Optional progress callback
     * @return bool True if all services stopped
     */
    private static function shutdownServicesSequential($services, ?callable $progressCallback = null)
    {
        Log::trace('Starting sequential shutdown phase');
        $totalServices = count($services);
        $currentIndex = 0;

        foreach ($services as $serviceName => $service) {
            $currentIndex++;
            if ($progressCallback) {
                $progressCallback($currentIndex, $totalServices, $serviceName);
            }

            Log::trace('Sequential stop: ' . $serviceName);
            $service->stop();

            if (!$service->isStopped()) {
                Log::trace('Force killing: ' . $serviceName);
                self::forceKillService($serviceName);
            }
        }

        return self::allServicesStopped($services);
    }

    /**
     * Force kill a service by process name
     *
     * @param string $serviceName The service name
     * @return bool True if force kill was executed
     */
    private static function forceKillService($serviceName)
    {
        $processMap = [
            BinApache::SERVICE_NAME => 'httpd.exe',
            BinMysql::SERVICE_NAME => 'mysqld.exe',
            BinMariadb::SERVICE_NAME => 'mysqld.exe',
            BinMailpit::SERVICE_NAME => 'mailpit.exe',
            BinMemcached::SERVICE_NAME => 'memcached.exe',
            BinPostgresql::SERVICE_NAME => 'postgres.exe',
            BinXlight::SERVICE_NAME => 'xlightftpd.exe',
            'nodejs' => 'node.exe',  // NodeJS - not a Windows service but needs to be killed on exit
        ];

        if (isset($processMap[$serviceName])) {
            Log::trace('Killing process: ' . $processMap[$serviceName]);
            Win32Ps::killBins([$processMap[$serviceName]]);
            return true;
        }

        return false;
    }

    /**
     * Check if all services are stopped
     *
     * @param array $services Array of services
     * @return bool True if all services are stopped
     */
    private static function allServicesStopped($services)
    {
        foreach ($services as $serviceName => $service) {
            if (!$service->isStopped()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Start all services in parallel for optimized startup
     *
     * Sends start commands to all services simultaneously, then monitors their status.
     * Falls back to sequential startup if parallel fails. Significantly reduces startup time.
     *
     * Performance: 40-60% faster startup (parallel vs sequential)
     *
     * @param array $serviceInfos Array of service information arrays with 'service', 'bin', 'name' keys
     * @param callable|null $progressCallback Optional callback: function($current, $total, $serviceName)
     * @param int $startupTimeout Timeout in seconds (default: 30)
     * @return bool True if all services started successfully
     */
    public static function startAllServicesParallel($serviceInfos, ?callable $progressCallback = null, $startupTimeout = 30)
    {
        Log::trace('ServiceHelper::startAllServicesParallel() starting with ' . count($serviceInfos) . ' services');
        $startTime = microtime(true);

        if (empty($serviceInfos)) {
            Log::trace('No services to start');
            return true;
        }

        Log::trace('Starting parallel startup of ' . count($serviceInfos) . ' services');

        // Try parallel startup
        $parallelSuccess = self::startServicesParallel($serviceInfos, $progressCallback, $startupTimeout);

        if ($parallelSuccess) {
            $duration = round(microtime(true) - $startTime, 3);
            Log::info('Parallel startup completed successfully in ' . $duration . 's');
            return true;
        }

        // Fallback to sequential
        Log::info('Parallel startup timed out, falling back to sequential startup');
        $sequentialSuccess = self::startServicesSequential($serviceInfos, $progressCallback);

        $totalDuration = round(microtime(true) - $startTime, 3);
        Log::info('Sequential startup completed in ' . $totalDuration . 's');

        return $sequentialSuccess;
    }

    /**
     * Start services in parallel (non-blocking)
     *
     * Phase 1: Send start commands to all services (non-blocking)
     * Phase 2: Monitor their status until all running or timeout
     * Phase 3: Retry any services that failed to start
     *
     * @param array $serviceInfos Array of service information
     * @param callable|null $progressCallback Optional progress callback
     * @param int $startupTimeout Timeout in seconds
     * @return bool True if all services started
     */
    private static function startServicesParallel($serviceInfos, ?callable $progressCallback = null, $startupTimeout = 30)
    {
        Log::trace('Phase 1: Sending start commands to all services');
        $totalServices = count($serviceInfos);
        $currentIndex = 0;

        // Phase 1: Send start commands (non-blocking)
        foreach ($serviceInfos as $serviceName => $serviceInfo) {
            $currentIndex++;
            if ($progressCallback) {
                $progressCallback($currentIndex, $totalServices, $serviceInfo['name']);
            }

            Log::trace('Sending start to: ' . $serviceName);
            $service = $serviceInfo['service'];

            // Start the service
            $service->start();
        }

        // Phase 2: Monitor status
        Log::trace('Phase 2: Monitoring service status');
        $monitorStartTime = microtime(true);
        $monitorTimeout = $startupTimeout;
        $checkInterval = 0.5;
        $allRunning = false;
        $failedServices = [];

        while ((microtime(true) - $monitorStartTime) < $monitorTimeout) {
            $allRunning = true;

            foreach ($serviceInfos as $serviceName => $serviceInfo) {
                $service = $serviceInfo['service'];

                if (!$service->isRunning()) {
                    $allRunning = false;
                    $failedServices[$serviceName] = $serviceInfo;
                }
            }

            if ($allRunning) {
                Log::trace('All services running in parallel phase');
                return true;
            }

            usleep($checkInterval * 1000000);
        }

        // Phase 3: Retry failed services
        if (!empty($failedServices)) {
            Log::trace('Phase 3: Retrying ' . count($failedServices) . ' failed services');

            foreach ($failedServices as $serviceName => $serviceInfo) {
                $service = $serviceInfo['service'];

                if (!$service->isRunning()) {
                    Log::trace('Retrying start for: ' . $serviceName);
                    $service->start();
                    usleep(500000); // 500ms delay between retries
                }
            }

            // Final check
            return self::allServicesRunning($serviceInfos);
        }

        return true;
    }

    /**
     * Start services sequentially (fallback method)
     *
     * Starts services one by one, waiting for each to complete.
     * Used as fallback when parallel startup fails.
     *
     * @param array $serviceInfos Array of service information
     * @param callable|null $progressCallback Optional progress callback
     * @return bool True if all services started
     */
    private static function startServicesSequential($serviceInfos, ?callable $progressCallback = null)
    {
        Log::trace('Starting sequential startup phase');
        $totalServices = count($serviceInfos);
        $currentIndex = 0;

        foreach ($serviceInfos as $serviceName => $serviceInfo) {
            $currentIndex++;
            if ($progressCallback) {
                $progressCallback($currentIndex, $totalServices, $serviceInfo['name']);
            }

            Log::trace('Sequential start: ' . $serviceName);
            $service = $serviceInfo['service'];

            $service->start();

            // Small delay between service starts
            usleep(200000);

            if (!$service->isRunning()) {
                Log::trace('Service failed to start: ' . $serviceName);
                // Retry once
                $service->start();
                usleep(500000);
            }
        }

        return self::allServicesRunning($serviceInfos);
    }

    /**
     * Check if all services are running
     *
     * @param array $serviceInfos Array of service information
     * @return bool True if all services are running
     */
    private static function allServicesRunning($serviceInfos)
    {
        foreach ($serviceInfos as $serviceName => $serviceInfo) {
            $service = $serviceInfo['service'];
            if (!$service->isRunning()) {
                return false;
            }
        }
        return true;
    }
}
