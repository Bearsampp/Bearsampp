<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionStartAllServices
 * Handles starting all services with a single splash screen showing progress.
 */
class ActionStartAllServices
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
     * ActionStartAllServices constructor.
     * Initializes the starting process, displays the splash screen, and sets up the main loop.
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
            $bearsamppLang->getValue(Lang::MENU_START_SERVICES),
            self::GAUGE_PER_SERVICE * $enabledServicesCount + 1,
            $bearsamppLang->getValue(Lang::LOADING_START_SERVICES)
        );

        // Set handler for the splash screen window with 1000ms timeout like ActionStartup
        $bearsamppWinbinder->setHandler($this->splash->getWbWindow(), $this, 'processWindow', 1000);
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes the splash screen window events.
     * Starts all services sequentially with progress updates.
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

        // Start all services
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $bin = null;
            $syntaxCheckCmd = null;

            // Get the binary object and syntax check command if applicable
            if ($sName == BinApache::SERVICE_NAME) {
                $bin = $bearsamppBins->getApache();
                $syntaxCheckCmd = BinApache::CMD_SYNTAX_CHECK;
            } elseif ($sName == BinMysql::SERVICE_NAME) {
                $bin = $bearsamppBins->getMysql();
                $syntaxCheckCmd = BinMysql::CMD_SYNTAX_CHECK;
            } elseif ($sName == BinMariadb::SERVICE_NAME) {
                $bin = $bearsamppBins->getMariadb();
                $syntaxCheckCmd = BinMariadb::CMD_SYNTAX_CHECK;
            } elseif ($sName == BinMailpit::SERVICE_NAME) {
                $bin = $bearsamppBins->getMailpit();
            } elseif ($sName == BinMemcached::SERVICE_NAME) {
                $bin = $bearsamppBins->getMemcached();
            } elseif ($sName == BinPostgresql::SERVICE_NAME) {
                $bin = $bearsamppBins->getPostgresql();
            } elseif ($sName == BinXlight::SERVICE_NAME) {
                $bin = $bearsamppBins->getXlight();
            }

            if ($bin !== null) {
                $name = $bin->getName() . ' ' . $bin->getVersion();
                $name .= ' (' . $service->getName() . ')';

                $this->splash->incrProgressBar();
                $this->splash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::LOADING_START_SERVICE), $name));

                // Start the service
                Util::startService($bin, $syntaxCheckCmd, false);
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
