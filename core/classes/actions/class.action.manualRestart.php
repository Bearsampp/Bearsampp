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
 * Class ActionManualRestart
 *
 * This class handles the manual restart of services in the Bearsampp application.
 * It stops all running services, kills all related processes, and sets the application
 * to restart.
 */
class ActionManualRestart
{
    /**
     * ActionManualRestart constructor.
     *
     * @param array $args Arguments passed to the constructor.
     *
     * This constructor initializes the manual restart process by performing the following steps:
     * 1. Starts the loading process.
     * 2. Deletes all services managed by Bearsampp.
     * 3. Kills all related processes.
     * 4. Sets the application to restart.
     * 5. Stops the loading process.
     */
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppBins;

        Log::trace("Starting ActionManualRestart constructor");

        // Start the loading process
        Log::trace("Starting loading process");
        Util::startLoading();

        // Delete all services managed by Bearsampp
        Log::trace("Deleting all services managed by Bearsampp");
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            Log::trace("Deleting service: " . $sName);
            $service->delete();
        }

        // Kill all related processes
        Log::trace("Killing all related processes");
        Win32Ps::killBins(true);

        // Set the application to restart
        Log::trace("Setting application to restart");
        $bearsamppCore->setExec(ActionExec::RESTART);

        // Stop the loading process
        Log::trace("Stopping loading process");
        Util::stopLoading();
    }
}
