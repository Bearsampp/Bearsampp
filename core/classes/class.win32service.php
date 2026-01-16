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
 * Class Win32Service
 *
 * This class provides an interface to manage Windows services. It includes methods to create, delete, start, stop, and query the status of services.
 * It also handles logging and error reporting for service operations.
 */
class Win32Service
{
    // Win32Service Service Status Constants
    const WIN32_SERVICE_CONTINUE_PENDING = '5';
    const WIN32_SERVICE_PAUSE_PENDING = '6';
    const WIN32_SERVICE_PAUSED = '7';
    const WIN32_SERVICE_RUNNING = '4';
    const WIN32_SERVICE_START_PENDING = '2';
    const WIN32_SERVICE_STOP_PENDING = '3';
    const WIN32_SERVICE_STOPPED = '1';
    const WIN32_SERVICE_NA = '0';

    // Win32 Error Codes
    const WIN32_ERROR_ACCESS_DENIED = '5';
    const WIN32_ERROR_CIRCULAR_DEPENDENCY = '423';
    const WIN32_ERROR_DATABASE_DOES_NOT_EXIST = '429';
    const WIN32_ERROR_DEPENDENT_SERVICES_RUNNING = '41B';
    const WIN32_ERROR_DUPLICATE_SERVICE_NAME = '436';
    const WIN32_ERROR_FAILED_SERVICE_CONTROLLER_CONNECT = '427';
    const WIN32_ERROR_INSUFFICIENT_BUFFER = '7A';
    const WIN32_ERROR_INVALID_DATA = 'D';
    const WIN32_ERROR_INVALID_HANDLE = '6';
    const WIN32_ERROR_INVALID_LEVEL = '7C';
    const WIN32_ERROR_INVALID_NAME = '7B';
    const WIN32_ERROR_INVALID_PARAMETER = '57';
    const WIN32_ERROR_INVALID_SERVICE_ACCOUNT = '421';
    const WIN32_ERROR_INVALID_SERVICE_CONTROL = '41C';
    const WIN32_ERROR_PATH_NOT_FOUND = '3';
    const WIN32_ERROR_SERVICE_ALREADY_RUNNING = '420';
    const WIN32_ERROR_SERVICE_CANNOT_ACCEPT_CTRL = '425';
    const WIN32_ERROR_SERVICE_DATABASE_LOCKED = '41F';
    const WIN32_ERROR_SERVICE_DEPENDENCY_DELETED = '433';
    const WIN32_ERROR_SERVICE_DEPENDENCY_FAIL = '42C';
    const WIN32_ERROR_SERVICE_DISABLED = '422';
    const WIN32_ERROR_SERVICE_DOES_NOT_EXIST = '424';
    const WIN32_ERROR_SERVICE_EXISTS = '431';
    const WIN32_ERROR_SERVICE_LOGON_FAILED = '42D';
    const WIN32_ERROR_SERVICE_MARKED_FOR_DELETE = '430';
    const WIN32_ERROR_SERVICE_NO_THREAD = '41E';
    const WIN32_ERROR_SERVICE_NOT_ACTIVE = '426';
    const WIN32_ERROR_SERVICE_REQUEST_TIMEOUT = '41D';
    const WIN32_ERROR_SHUTDOWN_IN_PROGRESS = '45B';
    const WIN32_NO_ERROR = '0';

    const SERVER_ERROR_IGNORE = '0';
    const SERVER_ERROR_NORMAL = '1';

    const SERVICE_AUTO_START = '2';
    const SERVICE_DEMAND_START = '3';
    const SERVICE_DISABLED = '4';

    const PENDING_TIMEOUT = 20;
    const SLEEP_TIME = 500000;

    const VBS_NAME = 'Name';
    const VBS_DISPLAY_NAME = 'DisplayName';
    const VBS_DESCRIPTION = 'Description';
    const VBS_PATH_NAME = 'PathName';
    const VBS_STATE = 'State';

    private $name;
    private $displayName;
    private $binPath;
    private $params;
    private $startType;
    private $errorControl;
    private $nssm;

    private $latestStatus;
    private $latestError;

    // Track which functions have been logged to avoid duplicate log entries
    private static $loggedFunctions = array();

    /**
     * Constructor for the Win32Service class.
     *
     * @param   string  $name  The name of the service.
     */
    public function __construct($name)
    {
        Util::logInitClass( $this );
        $this->name = $name;
    }

    /**
     * Writes a log entry.
     *
     * @param   string  $log  The log message.
     */
    private function writeLog($log): void
    {
        global $bearsamppRoot;
        Util::logDebug( $log, $bearsamppRoot->getServicesLogFilePath() );
    }

    /**
     * Returns an array of VBS keys used for service information.
     *
     * @return array The array of VBS keys.
     */
    public static function getVbsKeys(): array
    {
        return array(
            self::VBS_NAME,
            self::VBS_DISPLAY_NAME,
            self::VBS_DESCRIPTION,
            self::VBS_PATH_NAME,
            self::VBS_STATE
        );
    }

    /**
     * Calls a Win32 service function.
     *
     * @param   string  $function    The function name.
     * @param   mixed   $param       The parameter to pass to the function.
     * @param   bool    $checkError  Whether to check for errors.
     *
     * @return mixed The result of the function call.
     */
    private function callWin32Service($function, $param, $checkError = false): mixed
    {
        $result = false;
        if ( function_exists( $function ) ) {
            if (!isset(self::$loggedFunctions[$function])) {
                Util::logTrace('Win32 function: ' . $function . ' exists');
                self::$loggedFunctions[$function] = true;
            }

            // Special handling for win32_query_service_status to prevent hanging
            if ($function === 'win32_query_service_status') {
                Util::logTrace("Using enhanced handling for win32_query_service_status");

                // Set a shorter timeout for this specific function
                $originalTimeout = ini_get('max_execution_time');
                set_time_limit(5); // 5 seconds timeout

                try {
                    // Ensure proper parameter handling for PHP 8.2.3 compatibility
                    $result = call_user_func($function, $param);

                    // Reset the timeout
                    set_time_limit($originalTimeout);

                    if ($checkError && $result !== null) {
                        // Convert to int before using dechex for PHP 8.2.3 compatibility
                        $resultInt = is_numeric($result) ? (int)$result : 0;
                        if (dechex($resultInt) != self::WIN32_NO_ERROR) {
                            $this->latestError = dechex($resultInt);
                        }
                    }
                } catch (\Win32ServiceException $e) {
                    // Reset the timeout
                    set_time_limit($originalTimeout);

                    Util::logTrace("Win32ServiceException caught: " . $e->getMessage());

                    // Handle "service does not exist" exception
                    if (strpos($e->getMessage(), 'service does not exist') !== false) {
                        Util::logTrace("Service does not exist exception handled for: " . $param);
                        // Return the appropriate error code for "service does not exist"
                        $result = hexdec(self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST);
                    } else {
                        // For other exceptions, log and return false
                        Util::logTrace("Unhandled Win32ServiceException: " . $e->getMessage());
                        $result = false;
                    }
                } catch (\Exception $e) {
                    // Reset the timeout
                    set_time_limit($originalTimeout);

                    // Catch any other exceptions to prevent application freeze
                    Util::logTrace("Exception caught in callWin32Service: " . $e->getMessage());
                    $result = false;
                } catch (\Throwable $e) {
                    // Reset the timeout
                    set_time_limit($originalTimeout);

                    // Catch any other throwable (PHP 7+) to prevent application freeze
                    Util::logTrace("Throwable caught in callWin32Service: " . $e->getMessage());
                    $result = false;
                }
            } else {
                // Standard handling for other functions
                try {
                    // Ensure proper parameter handling for PHP 8.2.3 compatibility
                    $result = call_user_func($function, $param);
                    if ($checkError && $result !== null) {
                        // Convert to int before using dechex for PHP 8.2.3 compatibility
                        $resultInt = is_numeric($result) ? (int)$result : 0;
                        if (dechex($resultInt) != self::WIN32_NO_ERROR) {
                            $this->latestError = dechex($resultInt);
                        }
                    }
                } catch (\Win32ServiceException $e) {
                    Util::logTrace("Win32ServiceException caught: " . $e->getMessage());

                    // Handle "service does not exist" exception
                    if (strpos($e->getMessage(), 'service does not exist') !== false) {
                        Util::logTrace("Service does not exist exception handled for: " . $param);
                        // Return the appropriate error code for "service does not exist"
                        $result = hexdec(self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST);
                    } else {
                        // For other exceptions, log and return false
                        Util::logTrace("Unhandled Win32ServiceException: " . $e->getMessage());
                        $result = false;
                    }
                } catch (\Exception $e) {
                    // Catch any other exceptions to prevent application freeze
                    Util::logTrace("Exception caught in callWin32Service: " . $e->getMessage());
                    $result = false;
                } catch (\Throwable $e) {
                    // Catch any other throwable (PHP 7+) to prevent application freeze
                    Util::logTrace("Throwable caught in callWin32Service: " . $e->getMessage());
                    $result = false;
                }
            }
        } else {
            if (!isset(self::$loggedFunctions[$function])) {
                Util::logTrace('Win32 function: ' . $function . ' missing');
                self::$loggedFunctions[$function] = true;
            }
        }
        return $result;
    }

    /**
     * Queries the status of the service.
     *
     * @param   bool  $timeout  Whether to use a timeout.
     *
     * @return string The status of the service.
     */
    public function status($timeout = true): string
    {
        usleep( self::SLEEP_TIME );

        $this->latestStatus = self::WIN32_SERVICE_NA;
        $maxtime            = time() + self::PENDING_TIMEOUT;

        Util::logTrace("Querying status for service: " . $this->getName() . " (timeout: " . ($timeout ? "enabled" : "disabled") . ")");
        if ($timeout) {
            Util::logTrace("Max timeout time set to: " . date('Y-m-d H:i:s', $maxtime));
        }

        // Add a safety counter to prevent infinite loops
        $loopCount = 0;
        $maxLoops = 5; // Maximum number of attempts
        $startTime = microtime(true);

        try {
            while ( ($this->latestStatus == self::WIN32_SERVICE_NA || $this->isPending( $this->latestStatus )) && $loopCount < $maxLoops ) {
                $loopCount++;
                Util::logTrace("Calling win32_query_service_status for service: " . $this->getName() . " (attempt " . $loopCount . " of " . $maxLoops . ")");

                // Add a timeout check before making the call
                if (microtime(true) - $startTime > 10) { // 10 seconds overall timeout
                    Util::logTrace("Overall timeout reached before making service status call");
                    break;
                }

                $this->latestStatus = $this->callWin32Service( 'win32_query_service_status', $this->getName() );

                if ( is_array( $this->latestStatus ) && isset( $this->latestStatus['CurrentState'] ) ) {
                    // Ensure proper type conversion for PHP 8.2.3 compatibility
                    $stateInt = is_numeric($this->latestStatus['CurrentState']) ? (int)$this->latestStatus['CurrentState'] : 0;
                    $this->latestStatus = dechex( $stateInt );
                    Util::logTrace("Service status returned as array, CurrentState: " . $this->latestStatus);
                }
                elseif ( $this->latestStatus !== null ) {
                    // Ensure proper type conversion for PHP 8.2.3 compatibility
                    $statusInt = is_numeric($this->latestStatus) ? (int)$this->latestStatus : 0;
                    $statusHex = dechex( $statusInt );
                    Util::logTrace("Service status returned as value: " . $statusHex);

                    if ( $statusHex == self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST ) {
                        $this->latestStatus = $statusHex;
                        Util::logTrace("Service does not exist, breaking loop");
                        break; // Exit the loop immediately if service doesn't exist
                    }
                } else {
                    Util::logTrace("Service status query returned null");
                    // If we get a null result, assume service does not exist to avoid hanging
                    if ($loopCount >= 2) { // Only do this after at least one retry
                        Util::logTrace("Multiple null results, assuming service does not exist");
                        $this->latestStatus = self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST;
                        break;
                    }
                }

                if ( $timeout && $maxtime < time() ) {
                    Util::logTrace("Timeout reached while querying service status");
                    break;
                }

                // Only sleep if we're going to loop again
                if ($loopCount < $maxLoops && ($this->latestStatus == self::WIN32_SERVICE_NA || $this->isPending($this->latestStatus))) {
                    Util::logTrace("Sleeping before next status check attempt");
                    usleep(self::SLEEP_TIME);
                }
            }
        } catch (\Exception $e) {
            Util::logTrace("Exception in status method: " . $e->getMessage());
            // If an exception occurs, assume service does not exist
            $this->latestStatus = self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST;
        } catch (\Throwable $e) {
            Util::logTrace("Throwable in status method: " . $e->getMessage());
            // If a throwable occurs, assume service does not exist
            $this->latestStatus = self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST;
        }

        if ($loopCount >= $maxLoops) {
            Util::logTrace("Maximum query attempts reached for service: " . $this->getName());
        }

        $elapsedTime = microtime(true) - $startTime;
        Util::logTrace("Status check completed in " . round($elapsedTime, 2) . " seconds after " . $loopCount . " attempts");

        if ( $this->latestStatus == self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST ) {
            $this->latestError  = $this->latestStatus;
            $this->latestStatus = self::WIN32_SERVICE_NA;
            Util::logTrace("Service does not exist, setting status to NA");
        }

        Util::logTrace("Final status for service " . $this->getName() . ": " . $this->latestStatus);
        return $this->latestStatus;
    }

    /**
     * Creates the service.
     *
     * @return bool True if the service was created successfully, false otherwise.
     */
    public function create(): bool
    {
        global $bearsamppBins;

        Util::logTrace("Starting Win32Service::create for service: " . $this->getName());

        if ( $this->getName() == BinPostgresql::SERVICE_NAME ) {
            Util::logTrace("PostgreSQL service detected - using specialized installation");
            $bearsamppBins->getPostgresql()->rebuildConf();
            Util::logTrace("PostgreSQL configuration rebuilt");

            $bearsamppBins->getPostgresql()->initData();
            Util::logTrace("PostgreSQL data initialized");

            $result = Batch::installPostgresqlService();
            Util::logTrace("PostgreSQL service installation " . ($result ? "succeeded" : "failed"));
            return $result;
        }

        if ( $this->getNssm() instanceof Nssm ) {
            Util::logTrace("Using NSSM for service installation");

            $nssmEnvPath = Util::getAppBinsRegKey( false );
            Util::logTrace("NSSM environment path (bins): " . $nssmEnvPath);

            $nssmEnvPath .= Util::getNssmEnvPaths();
            Util::logTrace("NSSM environment path (with additional paths): " . $nssmEnvPath);

            $nssmEnvPath .= '%SystemRoot%/system32;';
            $nssmEnvPath .= '%SystemRoot%;';
            $nssmEnvPath .= '%SystemRoot%/system32/Wbem;';
            $nssmEnvPath .= '%SystemRoot%/system32/WindowsPowerShell/v1.0';
            Util::logTrace("NSSM final environment PATH: " . $nssmEnvPath);

            $this->getNssm()->setEnvironmentExtra( 'PATH=' . $nssmEnvPath );
            Util::logTrace("NSSM service parameters:");
            Util::logTrace("-> Name: " . $this->getNssm()->getName());
            Util::logTrace("-> DisplayName: " . $this->getNssm()->getDisplayName());
            Util::logTrace("-> BinPath: " . $this->getNssm()->getBinPath());
            Util::logTrace("-> Params: " . $this->getNssm()->getParams());
            Util::logTrace("-> Start: " . $this->getNssm()->getStart());
            Util::logTrace("-> Stdout: " . $this->getNssm()->getStdout());
            Util::logTrace("-> Stderr: " . $this->getNssm()->getStderr());

            $result = $this->getNssm()->create();
            Util::logTrace("NSSM service creation " . ($result ? "succeeded" : "failed"));
            if (!$result) {
                Util::logTrace("NSSM error: " . $this->getNssm()->getLatestError());
            }
            return $result;
        }

        Util::logTrace("Using win32_create_service for service installation");
        $serviceParams = array(
            'service'       => $this->getName(),
            'display'       => $this->getDisplayName(),
            'description'   => $this->getDisplayName(),
            'path'          => $this->getBinPath(),
            'params'        => $this->getParams(),
            'start_type'    => $this->getStartType() != null ? $this->getStartType() : self::SERVICE_DEMAND_START,
            'error_control' => $this->getErrorControl() != null ? $this->getErrorControl() : self::SERVER_ERROR_NORMAL,
        );

        Util::logTrace("win32_create_service parameters:");
        foreach ($serviceParams as $key => $value) {
            Util::logTrace("-> $key: $value");
        }

        $result = $this->callWin32Service( 'win32_create_service', $serviceParams, true );
        // Ensure proper type conversion for PHP 8.2.3 compatibility
        $resultInt = is_numeric($result) ? (int)$result : 0;
        $create = $result !== null ? dechex( $resultInt ) : '0';
        Util::logTrace("win32_create_service result code: " . $create);

        $this->writeLog( 'Create service: ' . $create . ' (status: ' . $this->status() . ')' );
        $this->writeLog( '-> service: ' . $this->getName() );
        $this->writeLog( '-> display: ' . $this->getDisplayName() );
        $this->writeLog( '-> description: ' . $this->getDisplayName() );
        $this->writeLog( '-> path: ' . $this->getBinPath() );
        $this->writeLog( '-> params: ' . $this->getParams() );
        $this->writeLog( '-> start_type: ' . ($this->getStartType() != null ? $this->getStartType() : self::SERVICE_DEMAND_START) );
        $this->writeLog( '-> service: ' . ($this->getErrorControl() != null ? $this->getErrorControl() : self::SERVER_ERROR_NORMAL) );

        if ( $create != self::WIN32_NO_ERROR ) {
            Util::logTrace("Service creation failed with error code: " . $create);
            return false;
        }
        elseif ( !$this->isInstalled() ) {
            Util::logTrace("Service created but not found as installed");
            $this->latestError = self::WIN32_NO_ERROR;
            return false;
        }

        Util::logTrace("Service created successfully: " . $this->getName());
        return true;
    }

    /**
     * Deletes the service.
     *
     * @return bool True if the service was deleted successfully, false otherwise.
     */
    public function delete(): bool
    {
        Util::logTrace("Starting Win32Service::delete for service: " . $this->getName());
        Util::logTrace("Checking if service is installed: " . $this->getName());

        if ( !$this->isInstalled() ) {
            Util::logTrace("Service is not installed, skipping deletion: " . $this->getName());
            return true;
        }

        Util::logTrace("Stopping service before deletion: " . $this->getName());
        $this->stop();

        if ( $this->getName() == BinPostgresql::SERVICE_NAME ) {
            Util::logTrace("PostgreSQL service detected - using specialized uninstallation");
            $result = Batch::uninstallPostgresqlService();
            Util::logTrace("PostgreSQL service uninstallation " . ($result ? "succeeded" : "failed"));
            return $result;
        }

        Util::logTrace("Calling win32_delete_service for service: " . $this->getName());
        $result = $this->callWin32Service( 'win32_delete_service', $this->getName(), true );
        // Ensure proper type conversion for PHP 8.2.3 compatibility
        $resultInt = is_numeric($result) ? (int)$result : 0;
        $delete = $result !== null ? dechex( $resultInt ) : '0';
        Util::logTrace("Delete service result code: " . $delete);
        $this->writeLog( 'Delete service ' . $this->getName() . ': ' . $delete . ' (status: ' . $this->status() . ')' );

        if ( $delete != self::WIN32_NO_ERROR && $delete != self::WIN32_ERROR_SERVICE_DOES_NOT_EXIST ) {
            return false;
        }
        elseif ( $this->isInstalled() ) {
            $this->latestError = self::WIN32_NO_ERROR;

            return false;
        }

        return true;
    }

    /**
     * Resets the service by deleting and recreating it.
     *
     * @return bool True if the service was reset successfully, false otherwise.
     */
    public function reset(): bool
    {
        if ( $this->delete() ) {
            usleep( self::SLEEP_TIME );

            return $this->create();
        }

        return false;
    }

    /**
     * Starts the service.
     *
     * @return bool True if the service was started successfully, false otherwise.
     */
    public function start(): bool
    {
        global $bearsamppBins;

        Util::logInfo('Attempting to start service: ' . $this->getName());

        if ( $this->getName() == BinMysql::SERVICE_NAME ) {
            $bearsamppBins->getMysql()->initData();
        }
        elseif ( $this->getName() == BinMariadb::SERVICE_NAME ) {
            $bearsamppBins->getMariadb()->initData();
        }
        elseif ( $this->getName() == BinMailpit::SERVICE_NAME ) {
            $bearsamppBins->getMailpit()->rebuildConf();
        }
        elseif ( $this->getName() == BinMemcached::SERVICE_NAME ) {
            $bearsamppBins->getMemcached()->rebuildConf();
        }
        elseif ( $this->getName() == BinPostgresql::SERVICE_NAME ) {
            $bearsamppBins->getPostgresql()->rebuildConf();
            $bearsamppBins->getPostgresql()->initData();
        }
        elseif ( $this->getName() == BinXlight::SERVICE_NAME ) {
            $bearsamppBins->getXlight()->rebuildConf();
        }


        $result = $this->callWin32Service( 'win32_start_service', $this->getName(), true );
        // Ensure proper type conversion for PHP 8.2.3 compatibility
        $resultInt = is_numeric($result) ? (int)$result : 0;
        $start = $result !== null ? dechex( $resultInt ) : '0';
        Util::logDebug( 'Start service ' . $this->getName() . ': ' . $start . ' (status: ' . $this->status() . ')' );

        if ( $start != self::WIN32_NO_ERROR && $start != self::WIN32_ERROR_SERVICE_ALREADY_RUNNING ) {

            // Write error to log
            Util::logError('Failed to start service: ' . $this->getName() . ' with error code: ' . $start);

            if ( $this->getName() == BinApache::SERVICE_NAME ) {
                $cmdOutput = $bearsamppBins->getApache()->getCmdLineOutput( BinApache::CMD_SYNTAX_CHECK );
                if ( !$cmdOutput['syntaxOk'] ) {
                    file_put_contents(
                        $bearsamppBins->getApache()->getErrorLog(),
                        '[' . date( 'Y-m-d H:i:s', time() ) . '] [error] ' . $cmdOutput['content'] . PHP_EOL,
                        FILE_APPEND
                    );
                }
            }
            elseif ( $this->getName() == BinMysql::SERVICE_NAME ) {
                $cmdOutput = $bearsamppBins->getMysql()->getCmdLineOutput( BinMysql::CMD_SYNTAX_CHECK );
                if ( !$cmdOutput['syntaxOk'] ) {
                    file_put_contents(
                        $bearsamppBins->getMysql()->getErrorLog(),
                        '[' . date( 'Y-m-d H:i:s', time() ) . '] [error] ' . $cmdOutput['content'] . PHP_EOL,
                        FILE_APPEND
                    );
                }
            }
            elseif ( $this->getName() == BinMariadb::SERVICE_NAME ) {
                $cmdOutput = $bearsamppBins->getMariadb()->getCmdLineOutput( BinMariadb::CMD_SYNTAX_CHECK );
                if ( !$cmdOutput['syntaxOk'] ) {
                    file_put_contents(
                        $bearsamppBins->getMariadb()->getErrorLog(),
                        '[' . date( 'Y-m-d H:i:s', time() ) . '] [error] ' . $cmdOutput['content'] . PHP_EOL,
                        FILE_APPEND
                    );
                }
            }

            return false;
        }
        elseif ( !$this->isRunning() ) {
            $this->latestError = self::WIN32_NO_ERROR;
            Util::logError('Service ' . $this->getName() . ' is not running after start attempt.');
            $this->latestError = null;
            return false;
        }

        Util::logInfo('Service ' . $this->getName() . ' started successfully.');
        return true;
    }

    /**
     * Stops the service.
     *
     * @return bool True if the service was stopped successfully, false otherwise.
     */
    public function stop(): bool
    {
        Util::logTrace("Starting Win32Service::stop for service: " . $this->getName());

        Util::logTrace("Calling win32_stop_service for service: " . $this->getName());
        $result = $this->callWin32Service( 'win32_stop_service', $this->getName(), true );

        // Ensure proper type conversion for PHP 8.2.3 compatibility
        $resultInt = is_numeric($result) ? (int)$result : 0;
        $stop = $result !== null ? dechex( $resultInt ) : '0';
        Util::logTrace("Stop service result code: " . $stop);

        Util::logTrace("Checking current status after stop attempt");
        $currentStatus = $this->status();
        Util::logTrace("Current status: " . $currentStatus);

        $this->writeLog( 'Stop service ' . $this->getName() . ': ' . $stop . ' (status: ' . $currentStatus . ')' );

        if ( $stop != self::WIN32_NO_ERROR ) {
            return false;
        }
        elseif ( !$this->isStopped() ) {
            $this->latestError = self::WIN32_NO_ERROR;

            return false;
        }

        return true;
    }

    /**
     * Restarts the service by stopping and then starting it.
     *
     * @return bool True if the service was restarted successfully, false otherwise.
     */
    public function restart(): bool
    {
        if ( $this->stop() ) {
            return $this->start();
        }

        return false;
    }

    /**
     * Retrieves information about the service.
     *
     * @return array|false The service information, or false on failure.
     */
    public function infos()
    {
        Util::logTrace("Starting Win32Service::infos for service: " . $this->getName());

        try {
            // Set a timeout for the entire operation
            $startTime = microtime(true);
            $timeout = 10; // 10 seconds timeout for the entire operation

            if ($this->getNssm() instanceof Nssm) {
                Util::logTrace("Using NSSM to get service info");
                $result = $this->getNssm()->infos();
                Util::logTrace("NSSM info retrieval completed in " . round(microtime(true) - $startTime, 2) . " seconds");
                return $result;
            }

            Util::logTrace("Using VBS to get service info");

            // Use set_time_limit to prevent PHP script timeout
            $originalTimeout = ini_get('max_execution_time');
            set_time_limit(15); // 15 seconds timeout

            // Create a separate process to get service info with a timeout
            $result = Vbs::getServiceInfos($this->getName());

            // Reset the timeout
            set_time_limit($originalTimeout);

            // Check if we've exceeded our timeout
            if (microtime(true) - $startTime > $timeout) {
                Util::logTrace("Timeout exceeded in infos() method, returning false");
                return false;
            }

            Util::logTrace("VBS info retrieval completed in " . round(microtime(true) - $startTime, 2) . " seconds");
            return $result;
        } catch (\Exception $e) {
            Util::logTrace("Exception in infos() method: " . $e->getMessage() . ", returning false");
            return false;
        } catch (\Throwable $e) {
            Util::logTrace("Throwable in infos() method: " . $e->getMessage() . ", returning false");
            return false;
        }
    }

    /**
     * Checks if the service is installed.
     *
     * @return bool True if the service is installed, false otherwise.
     */
    public function isInstalled(): bool
    {
        Util::logTrace("Checking if service is installed: " . $this->getName());

        try {
            // Set a timeout for the entire operation
            $startTime = microtime(true);
            $timeout = 15; // 15 seconds timeout for the entire operation

            // Call status() with a try-catch to ensure we don't get stuck
            $status = $this->status();

            // Check if we've exceeded our timeout
            if (microtime(true) - $startTime > $timeout) {
                Util::logTrace("Timeout exceeded in isInstalled() method, assuming service is not installed");
                $this->writeLog('isInstalled ' . $this->getName() . ': NO (timeout exceeded)');
                return false;
            }

            $isInstalled = $status != self::WIN32_SERVICE_NA;

            Util::logTrace("Service " . $this->getName() . " installation status: " . ($isInstalled ? "YES" : "NO") . " (status code: " . $status . ")");
            $this->writeLog('isInstalled ' . $this->getName() . ': ' . ($isInstalled ? 'YES' : 'NO') . ' (status: ' . $status . ')');

            return $isInstalled;
        } catch (\Exception $e) {
            Util::logTrace("Exception in isInstalled() method: " . $e->getMessage() . ", assuming service is not installed");
            $this->writeLog('isInstalled ' . $this->getName() . ': NO (exception: ' . $e->getMessage() . ')');
            return false;
        } catch (\Throwable $e) {
            Util::logTrace("Throwable in isInstalled() method: " . $e->getMessage() . ", assuming service is not installed");
            $this->writeLog('isInstalled ' . $this->getName() . ': NO (throwable: ' . $e->getMessage() . ')');
            return false;
        }
    }

    /**
     * Checks if the service is running.
     *
     * @return bool True if the service is running, false otherwise.
     */
    public function isRunning(): bool
    {
        Util::logTrace("Checking if service is running: " . $this->getName());

        $status = $this->status();
        $isRunning = $status == self::WIN32_SERVICE_RUNNING;

        Util::logTrace("Service " . $this->getName() . " running status: " . ($isRunning ? "YES" : "NO") . " (status code: " . $status . ")");
        $this->writeLog( 'isRunning ' . $this->getName() . ': ' . ($isRunning ? 'YES' : 'NO') . ' (status: ' . $status . ')' );

        return $isRunning;
    }

    /**
     * Checks if the service is stopped.
     *
     * @return bool True if the service is stopped, false otherwise.
     */
    public function isStopped(): bool
    {
        Util::logTrace("Checking if service is stopped: " . $this->getName());

        $status = $this->status();
        $isStopped = $status == self::WIN32_SERVICE_STOPPED;

        Util::logTrace("Service " . $this->getName() . " stopped status: " . ($isStopped ? "YES" : "NO") . " (status code: " . $status . ")");
        $this->writeLog( 'isStopped ' . $this->getName() . ': ' . ($isStopped ? 'YES' : 'NO') . ' (status: ' . $status . ')' );

        return $isStopped;
    }

    /**
     * Checks if the service is paused.
     *
     * @return bool True if the service is paused, false otherwise.
     */
    public function isPaused(): bool
    {
        Util::logTrace("Checking if service is paused: " . $this->getName());

        $status = $this->status();
        $isPaused = $status == self::WIN32_SERVICE_PAUSED;

        Util::logTrace("Service " . $this->getName() . " paused status: " . ($isPaused ? "YES" : "NO") . " (status code: " . $status . ")");
        $this->writeLog( 'isPaused ' . $this->getName() . ': ' . ($isPaused ? 'YES' : 'NO') . ' (status: ' . $status . ')' );

        return $isPaused;
    }

    /**
     * Checks if the service is in a pending state.
     *
     * @param   string  $status  The status to check.
     *
     * @return bool True if the service is in a pending state, false otherwise.
     */
    public function isPending($status): bool
    {
        $isPending = $status == self::WIN32_SERVICE_START_PENDING || $status == self::WIN32_SERVICE_STOP_PENDING
            || $status == self::WIN32_SERVICE_CONTINUE_PENDING || $status == self::WIN32_SERVICE_PAUSE_PENDING;

        Util::logTrace("Checking if status is pending: " . $status . " - Result: " . ($isPending ? "YES" : "NO"));

        if ($isPending) {
            if ($status == self::WIN32_SERVICE_START_PENDING) {
                Util::logTrace("Service is in START_PENDING state");
            } else if ($status == self::WIN32_SERVICE_STOP_PENDING) {
                Util::logTrace("Service is in STOP_PENDING state");
            } else if ($status == self::WIN32_SERVICE_CONTINUE_PENDING) {
                Util::logTrace("Service is in CONTINUE_PENDING state");
            } else if ($status == self::WIN32_SERVICE_PAUSE_PENDING) {
                Util::logTrace("Service is in PAUSE_PENDING state");
            }
        }

        return $isPending;
    }

    /**
     * Returns a description of the Win32 service status.
     *
     * @param   string  $status  The status code.
     *
     * @return string|null The status description.
     */
    private function getWin32ServiceStatusDesc($status): ?string
    {
        switch ( $status ) {
            case self::WIN32_SERVICE_CONTINUE_PENDING:
                return 'The service continue is pending.';

            case self::WIN32_SERVICE_PAUSE_PENDING:
                return 'The service pause is pending.';

            case self::WIN32_SERVICE_PAUSED:
                return 'The service is paused.';

            case self::WIN32_SERVICE_RUNNING:
                return 'The service is running.';

            case self::WIN32_SERVICE_START_PENDING:
                return 'The service is starting.';

            case self::WIN32_SERVICE_STOP_PENDING:
                return 'The service is stopping.';

            case self::WIN32_SERVICE_STOPPED:
                return 'The service is not running.';

            case self::WIN32_SERVICE_NA:
                return 'Cannot retrieve service status.';

            default:
                return null;
        }
    }

    /**
     * Returns a description of the Win32 error code.
     *
     * @param   string  $code  The error code.
     *
     * @return string|null The description of the error code, or null if the code is not recognized.
     */
    private function getWin32ErrorCodeDesc($code): ?string
    {
        switch ( $code ) {
            case self::WIN32_ERROR_ACCESS_DENIED:
                return 'The handle to the SCM database does not have the appropriate access rights.';
            // ... other cases ...
            default:
                return null;
        }
    }

    /**
     * Gets the name of the service.
     *
     * @return string The name of the service.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the service.
     *
     * @param   string  $name  The name to set.
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the display name of the service.
     *
     * @return string The display name of the service.
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Sets the display name of the service.
     *
     * @param   string  $displayName  The display name to set.
     */
    public function setDisplayName($displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * Gets the binary path of the service.
     *
     * @return string The binary path of the service.
     */
    public function getBinPath(): string
    {
        return $this->binPath;
    }

    /**
     * Sets the binary path of the service.
     *
     * @param   string  $binPath  The binary path to set.
     */
    public function setBinPath($binPath): void
    {
        $this->binPath = str_replace( '"', '', Util::formatWindowsPath( $binPath ) );
    }

    /**
     * Gets the parameters for the service.
     *
     * @return string The parameters for the service.
     */
    public function getParams(): string
    {
        return $this->params;
    }

    /**
     * Sets the parameters for the service.
     *
     * @param   string  $params  The parameters to set.
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     * Gets the start type of the service.
     *
     * @return string The start type of the service.
     */
    public function getStartType(): string
    {
        return $this->startType;
    }

    /**
     * Sets the start type of the service.
     *
     * @param   string  $startType  The start type to set.
     */
    public function setStartType($startType): void
    {
        $this->startType = $startType;
    }

    /**
     * Gets the error control setting of the service.
     *
     * @return string The error control setting of the service.
     */
    public function getErrorControl(): string
    {
        return $this->errorControl;
    }

    /**
     * Sets the error control setting of the service.
     *
     * @param   string  $errorControl  The error control setting to set.
     */
    public function setErrorControl($errorControl): void
    {
        $this->errorControl = $errorControl;
    }

    /**
     * Gets the NSSM instance associated with the service.
     *
     * @return Nssm The NSSM instance.
     */
    public function getNssm()
    {
        return $this->nssm;
    }

    /**
     * Sets the NSSM instance associated with the service.
     *
     * @param   Nssm  $nssm  The NSSM instance to set.
     */
    public function setNssm($nssm)
    {
        if ( $nssm instanceof Nssm ) {
            $this->setDisplayName( $nssm->getDisplayName() );
            $this->setBinPath( $nssm->getBinPath() );
            $this->setParams( $nssm->getParams() );
            $this->setStartType( $nssm->getStart() );
            $this->nssm = $nssm;
        }
    }

    /**
     * Gets the latest status of the service.
     *
     * @return string The latest status of the service.
     */
    public function getLatestStatus()
    {
        return $this->latestStatus;
    }

    /**
     * Gets the latest error encountered by the service.
     *
     * @return string The latest error encountered by the service.
     */
    public function getLatestError()
    {
        return $this->latestError;
    }

    /**
     * Gets a detailed error message for the latest error encountered by the service.
     *
     * @return string|null The detailed error message, or null if no error.
     */
    public function getError()
    {
        global $bearsamppLang;
        if ( $this->latestError != self::WIN32_NO_ERROR ) {
            // Ensure proper type conversion for PHP 8.2.3 compatibility
            $errorInt = is_numeric($this->latestError) ? hexdec( $this->latestError ) : 0;
            return $bearsamppLang->getValue( Lang::ERROR ) . ' ' .
                $this->latestError . ' (' . $errorInt . ' : ' . $this->getWin32ErrorCodeDesc( $this->latestError ) . ')';
        }
        elseif ( $this->latestStatus != self::WIN32_SERVICE_NA ) {
            // Ensure proper type conversion for PHP 8.2.3 compatibility
            $statusInt = is_numeric($this->latestStatus) ? hexdec( $this->latestStatus ) : 0;
            return $bearsamppLang->getValue( Lang::STATUS ) . ' ' .
                $this->latestStatus . ' (' . $statusInt . ' : ' . $this->getWin32ServiceStatusDesc( $this->latestStatus ) . ')';
        }

        return null;
    }
}
