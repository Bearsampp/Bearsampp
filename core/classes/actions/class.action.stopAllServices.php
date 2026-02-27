<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionStopAllServices
 * Handles stopping all services with a single splash screen showing progress.
 */
class ActionStopAllServices
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
    const GAUGE_PER_SERVICE = 1;

    /**
     * ActionStopAllServices constructor.
     * Initializes the stopping process, displays the splash screen, and sets up the main loop.
     *
     * @param   array  $args  Command line arguments.
     */
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        // Count enabled services for progress bar
        $enabledServicesCount = count($bearsamppBins->getServices());

        // Start splash screen
        $this->splash = new Splash();
        $this->splash->init(
            $bearsamppLang->getValue(Lang::MENU_STOP_SERVICES),
            self::GAUGE_PER_SERVICE * $enabledServicesCount + 1,
            $bearsamppLang->getValue(Lang::LOADING_STOP_SERVICES)
        );

        // Set handler for the splash screen window with 1000ms timeout
        $bearsamppWinbinder->setHandler($this->splash->getWbWindow(), $this, 'processWindow', 1000);
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes the splash screen window events.
     * Stops all services sequentially with progress updates.
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

        // Stop all services using ServiceHelper
        ServiceHelper::processServices($bearsamppBins, function($serviceName, $service, $bin, $syntaxCheckCmd) use ($bearsamppLang) {
            $name = ServiceHelper::getServiceDisplayName($bin, $service);

            $this->splash->incrProgressBar();
            $this->splash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::LOADING_STOP_SERVICE), $name));

            // Stop the service
            ServiceHelper::stopService($service);
        });

        // Final update
        $this->splash->incrProgressBar();
        $this->splash->setTextLoading($bearsamppLang->getValue(Lang::LOADING_COMPLETE));

        // Close the splash screen and exit cleanly
        $bearsamppWinbinder->destroyWindow($window);
        $bearsamppWinbinder->reset();
        exit(0);
    }
}
