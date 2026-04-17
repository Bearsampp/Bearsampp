<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionRestartAllServices
 * Handles restarting all services with a single splash screen showing progress.
 * Stops all services first, then starts them all.
 */
class ActionRestartAllServices
{
    /**
     * @var Splash The splash screen instance.
     */
    private $splash;

    /**
     * @var bool Flag to track if processing has been done
     */
    private $processed = false;

    /**
     * Gauge value for progress bar increments.
     */
    const GAUGE_PER_SERVICE = 2; // 1 for stop, 1 for start

    /**
     * ActionRestartAllServices constructor.
     * Initializes the restarting process, displays the splash screen, and sets up the main loop.
     *
     * @param   array  $args  Command line arguments.
     */
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        // Count enabled services for progress bar
        $enabledServicesCount = count($bearsamppBins->getServices());

        // Start splash screen (2 operations per service: stop + start)
        $this->splash = new Splash();
        $this->splash->init(
            $bearsamppLang->getValue(Lang::MENU_RESTART_SERVICES),
            self::GAUGE_PER_SERVICE * $enabledServicesCount + 1,
            $bearsamppLang->getValue(Lang::LOADING_RESTART_SERVICES)
        );

        // Set handler for the splash screen window with 1000ms timeout
        $bearsamppWinbinder->setHandler($this->splash->getWbWindow(), $this, 'processWindow', 1000);
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Get the optimal service shutdown order based on dependencies.
     * Services are ordered to stop dependent services first, then core services.
     * This is the reverse of the startup order.
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
     * Get the optimal service startup order based on dependencies.
     * Services are ordered to start core services first, then dependent services.
     * This is the reverse of the shutdown order.
     *
     * @return array Array of service names in startup order
     */
    private function getServiceStartupOrder()
    {
        // Define startup order: core services first, then dependent services
        // This ensures dependencies are available when needed
        return [
            // Tier 1: Web server (should start first as it's the foundation)
            BinApache::SERVICE_NAME,       // Apache web server (started first)

            // Tier 2: Database services (web server may depend on these)
            BinMysql::SERVICE_NAME,        // MySQL database
            BinMariadb::SERVICE_NAME,      // MariaDB database
            BinPostgresql::SERVICE_NAME,   // PostgreSQL database

            // Tier 3: Application services (no dependencies on other services)
            BinXlight::SERVICE_NAME,       // FTP server
            BinMemcached::SERVICE_NAME,    // Caching service
            BinMailpit::SERVICE_NAME,      // Mail testing tool
        ];
    }

    /**
     * Processes the splash screen window events.
     * Stops all services in shutdown order, then starts them in startup order with progress updates.
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

        // Only process once
        if ($this->processed) {
            return;
        }
        $this->processed = true;

        // Get all available services
        $allServices = $bearsamppBins->getServices();

        // Get optimal shutdown order
        $shutdownOrder = $this->getServiceShutdownOrder();

        // First, stop all services in optimal shutdown order
        foreach ($shutdownOrder as $serviceName) {
            // Check if this service exists and is enabled
            if (!isset($allServices[$serviceName])) {
                continue;
            }

            $service = $allServices[$serviceName];
            $bin = ServiceHelper::getBinFromServiceName($serviceName, $bearsamppBins);

            if ($bin !== null) {
                $name = ServiceHelper::getServiceDisplayName($bin, $service);

                $this->splash->incrProgressBar();
                $this->splash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::LOADING_STOP_SERVICE), $name));

                // Stop the service
                ServiceHelper::stopService($service);
            }
        }

        // Get optimal startup order
        $startupOrder = $this->getServiceStartupOrder();

        // Then, start all services in optimal startup order
        foreach ($startupOrder as $serviceName) {
            // Check if this service exists and is enabled
            if (!isset($allServices[$serviceName])) {
                continue;
            }

            $service = $allServices[$serviceName];
            $bin = ServiceHelper::getBinFromServiceName($serviceName, $bearsamppBins);
            $syntaxCheckCmd = ServiceHelper::getSyntaxCheckCmd($serviceName);

            if ($bin !== null) {
                $name = ServiceHelper::getServiceDisplayName($bin, $service);

                $this->splash->incrProgressBar();
                $this->splash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::LOADING_START_SERVICE), $name));

                // Start the service
                ServiceHelper::startService($bin, $syntaxCheckCmd, false);
            }
        }

        // Final update
        $this->splash->incrProgressBar();
        $this->splash->setTextLoading($bearsamppLang->getValue(Lang::LOADING_COMPLETE));

        // Close the splash screen and exit cleanly
        $bearsamppWinbinder->destroyWindow($window);
        $bearsamppWinbinder->reset();
        exit(0);
    }
}
