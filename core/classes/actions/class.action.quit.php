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

        // Start splash screen
        $this->splash = new Splash();
        $this->splash->init(
            $bearsamppLang->getValue( Lang::QUIT ),
            self::GAUGE_PROCESSES * count( $bearsamppBins->getServices() ) + self::GAUGE_OTHERS,
            sprintf( $bearsamppLang->getValue( Lang::EXIT_LEAVING_TEXT ), APP_TITLE . ' ' . $bearsamppCore->getAppVersion() )
        );

        // Set handler for the splash screen window
        $bearsamppWinbinder->setHandler( $this->splash->getWbWindow(), $this, 'processWindow', 2000 );
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }


    /**
     * Processes the splash screen window events.
     * Stops all services, deletes symlinks, and kills remaining processes.
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

        // Stop all services
        foreach ( $bearsamppBins->getServices() as $sName => $service ) {
            $name = $bearsamppBins->getApache()->getName() . ' ' . $bearsamppBins->getApache()->getVersion();
            if ( $sName == BinMysql::SERVICE_NAME ) {
                $name = $bearsamppBins->getMysql()->getName() . ' ' . $bearsamppBins->getMysql()->getVersion();
            }
            elseif ( $sName == BinMailpit::SERVICE_NAME ) {
                $name = $bearsamppBins->getMailpit()->getName() . ' ' . $bearsamppBins->getMailpit()->getVersion();
            }
            elseif ( $sName == BinMariadb::SERVICE_NAME ) {
                $name = $bearsamppBins->getMariadb()->getName() . ' ' . $bearsamppBins->getMariadb()->getVersion();
            }
            elseif ( $sName == BinPostgresql::SERVICE_NAME ) {
                $name = $bearsamppBins->getPostgresql()->getName() . ' ' . $bearsamppBins->getPostgresql()->getVersion();
            }
            elseif ( $sName == BinMemcached::SERVICE_NAME ) {
                $name = $bearsamppBins->getMemcached()->getName() . ' ' . $bearsamppBins->getMemcached()->getVersion();
            }
            elseif ($sName == BinXlight::SERVICE_NAME) {
                $name = $bearsamppBins->getXlight()->getName() . ' ' . $bearsamppBins->getXlight()->getVersion();
            }
            $name .= ' (' . $service->getName() . ')';

            $this->splash->incrProgressBar();
            $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::EXIT_REMOVE_SERVICE_TEXT ), $name ) );
            $service->delete();
        }

        // Purge "current" symlinks
        Symlinks::deleteCurrentSymlinks();

        // Stop other processes
        $this->splash->incrProgressBar();
        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::EXIT_STOP_OTHER_PROCESS_TEXT ) );
        Win32Ps::killBins( true );

        // Terminate any remaining processes
        // Final termination sequence
        $this->splash->setTextLoading('Completing shutdown...');
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $currentPid = Win32Ps::getCurrentPid();

            // 1. Terminate PHP processes first
            self::terminatePhpProcesses($currentPid, $window, $this->splash);

            // 4. Force exit if still running
            exit(0);
        }

        // Non-Windows fallback
        $bearsamppWinbinder->destroyWindow($window);
        exit(0);
    }

    /**
     * Terminates PHP processes.
     *
     * @param   int     $excludePid  Process ID to exclude
     * @param   mixed   $window      Window handle or null
     * @param   mixed   $splash      Splash screen or null
     * @return  void
     */
    public static function terminatePhpProcesses($excludePid, $window = null, $splash = null)
    {
        global $bearsamppWinbinder;

        $currentPid = Win32Ps::getCurrentPid();

        $targets = ['php-win.exe', 'php.exe'];
        foreach (Win32Ps::getListProcs() as $proc) {
            $exe = strtolower(basename($proc[Win32Ps::EXECUTABLE_PATH]));
            $pid = $proc[Win32Ps::PROCESS_ID];

            if (in_array($exe, $targets) && $pid != $excludePid) {
                Win32Ps::kill($pid);
                usleep(100000); // 100ms delay between terminations
            }
        }

        // 2. Initiate self-termination
        if ($splash !== null) {
            $splash->setTextLoading('Final cleanup...');
        }
        Vbs::killProc($currentPid);

        // 3. Destroy window after process termination
        // Fix for PHP 8.2: Check if window is not null before destroying
        if ($window && $bearsamppWinbinder) {
            $bearsamppWinbinder->destroyWindow($window);
        }
    }
}
