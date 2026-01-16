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
 * Class ActionLoading
 *
 * This class handles the loading action, including the creation and management of a progress bar window.
 */
class ActionLoading
{
    /** @var int The width of the progress bar window. */
    const WINDOW_WIDTH = 360;

    /** @var int The height of the progress bar window. */
    const WINDOW_HEIGHT = 90;

    /** @var int The maximum value of the progress bar. */
    const GAUGE = 20;

    /** @var mixed The window object created by WinBinder. */
    private $wbWindow;

    /** @var mixed The progress bar object created by WinBinder. */
    private $wbProgressBar;

    /**
     * ActionLoading constructor.
     *
     * Initializes the loading action, creates the progress bar window, and starts the main loop.
     *
     * @param array $args The arguments passed to the constructor.
     */
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppWinbinder;

        $bearsamppWinbinder->reset();
        $bearsamppCore->addLoadingPid(Win32Ps::getCurrentPid());

        // Screen information
        $screenArea = explode(' ', $bearsamppWinbinder->getSystemInfo(WinBinder::SYSINFO_WORKAREA));
        $screenWidth = intval($screenArea[2]);
        $screenHeight = intval($screenArea[3]);
        $xPos = $screenWidth - self::WINDOW_WIDTH;
        $yPos = $screenHeight - self::WINDOW_HEIGHT - 5;

        // Create the window and progress bar
        $this->wbWindow = $bearsamppWinbinder->createWindow(null, ToolDialog, null, $xPos, $yPos, self::WINDOW_WIDTH, self::WINDOW_HEIGHT, WBC_TOP, null);
        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::LOADING), 42, 2, 295, null, WBC_LEFT);
        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, self::GAUGE, 42, 20, 290, 15);

        // Set the handler and start the main loop
        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processLoading', 10);
        $bearsamppWinbinder->mainLoop();
    }

    /**
     * Increments the progress bar by a specified number of steps.
     *
     * @param int $nb The number of steps to increment the progress bar by. Default is 1.
     */
    public function incrProgressBar($nb = 1)
    {
        global $bearsamppCore, $bearsamppWinbinder;

        for ($i = 0; $i < $nb; $i++) {
            $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);
            $bearsamppWinbinder->drawImage($this->wbWindow, $bearsamppCore->getImagesPath() . '/bearsampp.bmp', 4, 2, 32, 32);
        }

        $bearsamppWinbinder->wait();
        $bearsamppWinbinder->wait($this->wbWindow);
    }

    /**
     * Processes the loading action, including handling window events and updating the progress bar.
     *
     * @param mixed $window The window object.
     * @param int $id The ID of the event.
     * @param mixed $ctrl The control object.
     * @param mixed $param1 The first parameter of the event.
     * @param mixed $param2 The second parameter of the event.
     */
    public function processLoading($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppRoot, $bearsamppWinbinder;

        switch ($id) {
            case IDCLOSE:
                Win32Ps::kill(Win32Ps::getCurrentPid());
                break;
        }

        // Set a maximum number of iterations to prevent infinite loops
        $maxIterations = 10;
        $iterations = 0;

        // Set a timeout for the entire loading process
        $startTime = microtime(true); // Use microtime for more precise timing
        $maxLoadingTime = 15; // 15 seconds maximum

        while ($iterations < $maxIterations && (microtime(true) - $startTime) < $maxLoadingTime) {
            $bearsamppRoot->removeErrorHandling();
            $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);

            usleep(100000);

            for ($i = 0; $i < self::GAUGE && (microtime(true) - $startTime) < $maxLoadingTime; $i++) {
                $this->incrProgressBar();
                usleep(100000);
            }

            // Check if all services have started successfully
            $allServicesStarted = $this->checkAllServicesStarted();
            if ($allServicesStarted) {
                Util::logTrace('All services started successfully');
                break;
            }

            $iterations++;
            Util::logTrace('Loading iteration ' . $iterations . ' completed, checking services again');
        }

        if ($iterations >= $maxIterations) {
            Util::logTrace('Maximum iterations reached (' . $maxIterations . '), some services may not have started properly');
        }
        
        if ((microtime(true) - $startTime) >= $maxLoadingTime) {
            Util::logTrace('Loading timeout reached (' . $maxLoadingTime . ' seconds), some services may not have started properly');
        }
        
        // Add a small delay before killing the process to ensure UI updates are complete
        usleep(500000); // 500ms
        
        // Close the loading window
        Util::logTrace('Closing loading window');
        Win32Ps::kill(Win32Ps::getCurrentPid());
    }

    /**
     * Checks if all services have started successfully
     * 
     * @return bool True if all services are running, false otherwise
     */
    private function checkAllServicesStarted()
    {
        global $bearsamppBins, $bearsamppCore, $bearsamppRoot;
        
        Util::logTrace('Checking if all services have started successfully');
        
        $allStarted = true;
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            // Skip if service is not enabled
            if (!$service->isEnable()) {
                Util::logTrace('Service ' . $sName . ' is disabled, skipping check');
                continue;
            }
            
            // Add timeout for service status check
            $checkStartTime = microtime(true);
            $checkTimeout = 5; // 5 seconds timeout
            $serviceRunning = false;
            
            try {
                // Use a non-blocking check with timeout
                $tempFile = $bearsamppCore->getTmpPath() . '/service_check_' . uniqid() . '.tmp';
                
                // Start a background process to check the service
                $checkCmd = 'php -r "' .
                    'require \'' . $bearsamppRoot->getLibsPath() . '/classes/class.win32service.php\'; ' .
                    '$service = new Win32Service(\'' . $service->getName() . '\'); ' .
                    '$status = $service->status(); ' .
                    'file_put_contents(\'' . $tempFile . '\', $status == Win32Service::STATE_RUNNING ? \'1\' : \'0\'); ' .
                    'exit(0);" > nul 2>&1';
                
                // Execute the command in background
                pclose(popen('start /B ' . $checkCmd, 'r'));
                
                // Wait for the result with timeout
                $startWait = microtime(true);
                while (!file_exists($tempFile) && (microtime(true) - $startWait < $checkTimeout)) {
                    usleep(100000); // 100ms
                }
                
                // Check if we got a result
                if (file_exists($tempFile)) {
                    $result = file_get_contents($tempFile);
                    $serviceRunning = ($result === '1');
                    unlink($tempFile);
                    Util::logTrace('Service ' . $sName . ' status check: ' . ($serviceRunning ? 'running' : 'not running'));
                } else {
                    Util::logTrace('Service ' . $sName . ' status check timed out');
                    $serviceRunning = false;
                }
            } catch (\Exception $e) {
                Util::logTrace('Exception during service status check for ' . $sName . ': ' . $e->getMessage());
                $serviceRunning = false;
            }
            
            if (!$serviceRunning) {
                Util::logTrace('Service ' . $sName . ' is not running');
                $allStarted = false;
                break;
            }
        }
        
        Util::logTrace('All services started check result: ' . ($allStarted ? 'true' : 'false'));
        return $allStarted;
    }
}
