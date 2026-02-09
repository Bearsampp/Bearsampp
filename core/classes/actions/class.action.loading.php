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

    /** @var mixed The label control for displaying status text. */
    private $wbLabel;

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

        $currentPid = Win32Ps::getCurrentPid();
        Util::logTrace('ActionLoading constructor started - PID: ' . $currentPid);

        $bearsamppWinbinder->reset();
        Util::logTrace('WinBinder reset complete');
        
        $bearsamppCore->addLoadingPid($currentPid);
        Util::logTrace('Loading PID added to tracking file: ' . $currentPid);

        // Screen information
        Util::logTrace('Getting screen information');
        $screenArea = explode(' ', $bearsamppWinbinder->getSystemInfo(WinBinder::SYSINFO_WORKAREA));
        $screenWidth = intval($screenArea[2]);
        $screenHeight = intval($screenArea[3]);
        $xPos = $screenWidth - self::WINDOW_WIDTH;
        $yPos = $screenHeight - self::WINDOW_HEIGHT - 5;
        Util::logTrace('Screen dimensions: ' . $screenWidth . 'x' . $screenHeight . ', Window position: (' . $xPos . ',' . $yPos . ')');

        // Create the window and progress bar
        Util::logTrace('Creating loading window...');
        $this->wbWindow = $bearsamppWinbinder->createWindow(null, ToolDialog, null, $xPos, $yPos, self::WINDOW_WIDTH, self::WINDOW_HEIGHT, WBC_TOP, null);
        
        // Check if window was created successfully
        if ($this->wbWindow === false || $this->wbWindow === null) {
            Util::logError('CRITICAL: Failed to create loading window - window handle is: ' . var_export($this->wbWindow, true));
            Util::logError('WinBinder extension loaded: ' . (extension_loaded('winbinder') ? 'YES' : 'NO'));
            Util::logError('wb_create_window function exists: ' . (function_exists('wb_create_window') ? 'YES' : 'NO'));
            return;
        }
        
        Util::logTrace('Loading window created successfully - handle: ' . $this->wbWindow);
        
        // CRITICAL: wb_set_visible() must be called AFTER window creation in PHP 8.4
        // The WS_VISIBLE flag during creation doesn't work
        Util::logTrace('Making window visible with wb_set_visible()');
        wb_set_visible($this->wbWindow, true);
        Util::logTrace('Window set to visible');
        
        Util::logTrace('Creating label control...');
        $this->wbLabel = $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::LOADING), 42, 2, 295, null, WBC_LEFT);
        Util::logTrace('Label created: ' . var_export($this->wbLabel, true));
        
        Util::logTrace('Creating progress bar...');
        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, self::GAUGE, 42, 20, 290, 15);
        Util::logTrace('Progress bar created: ' . var_export($this->wbProgressBar, true));

        // Set the handler and start the main loop
        Util::logTrace('Setting window handler...');
        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processLoading', 10);
        Util::logTrace('Handler set, starting main loop...');
        $bearsamppWinbinder->mainLoop();
        Util::logTrace('Main loop exited');
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
                
                // Check for status file updates to show current service being processed
                $this->updateLabelFromStatusFile();
                
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
        
        // Close the loading window
        Util::logTrace('Closing loading window');
        Win32Ps::kill(Win32Ps::getCurrentPid());
    }

    /**
     * Updates the loading text on the window
     * 
     * @param string $text The text to display
     */
    private function updateLoadingText($text)
    {
        global $bearsamppWinbinder;
        
        if ($this->wbLabel) {
            wb_set_text($this->wbLabel, $text);
            wb_refresh($this->wbWindow);
        }
    }

    /**
     * Updates the label text from status file if it exists
     * This allows external processes to update the loading screen text dynamically
     */
    private function updateLabelFromStatusFile()
    {
        global $bearsamppCore, $bearsamppWinbinder;
        
        $statusFile = $bearsamppCore->getTmpPath() . '/loading_status.txt';
        
        if (file_exists($statusFile)) {
            $content = @file_get_contents($statusFile);
            if ($content !== false && !empty($content)) {
                $status = @json_decode($content, true);
                if ($status && isset($status['text']) && !empty($status['text'])) {
                    // Update the label with new text
                    $bearsamppWinbinder->setText($this->wbLabel[WinBinder::CTRL_OBJ], $status['text']);
                    $bearsamppWinbinder->refresh($this->wbWindow);
                }
            }
        }
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
            
            // Update the loading text to show which service we're checking
            $serviceName = $service->getName();
            $this->updateLoadingText('Checking ' . $serviceName . '...');
            
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
