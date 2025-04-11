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
 * Class Nssm
 *
 * This class provides methods to manage Windows services using NSSM (Non-Sucking Service Manager).
 * It includes functionalities to create, delete, start, stop, and retrieve the status of services.
 * The class also logs operations and errors.
 */
class Nssm
{
    // Start params
    const SERVICE_AUTO_START = 'SERVICE_AUTO_START';
    const SERVICE_DELAYED_START = 'SERVICE_DELAYED_START';
    const SERVICE_DEMAND_START = 'SERVICE_DEMAND_START';
    const SERVICE_DISABLED = 'SERVICE_DISABLED';

    // Type params
    const SERVICE_WIN32_OWN_PROCESS = 'SERVICE_WIN32_OWN_PROCESS';
    const SERVICE_INTERACTIVE_PROCESS = 'SERVICE_INTERACTIVE_PROCESS';

    // Status
    const STATUS_CONTINUE_PENDING = 'SERVICE_CONTINUE_PENDING';
    const STATUS_PAUSE_PENDING = 'SERVICE_PAUSE_PENDING';
    const STATUS_PAUSED = 'SERVICE_PAUSED';
    const STATUS_RUNNING = 'SERVICE_RUNNING';
    const STATUS_START_PENDING = 'SERVICE_START_PENDING';
    const STATUS_STOP_PENDING = 'SERVICE_STOP_PENDING';
    const STATUS_STOPPED = 'SERVICE_STOPPED';
    const STATUS_NOT_EXIST = 'SERVICE_NOT_EXIST';
    const STATUS_NA = '-1';

    // Infos keys
    const INFO_APP_DIRECTORY = 'AppDirectory';
    const INFO_APPLICATION = 'Application';
    const INFO_APP_PARAMETERS = 'AppParameters';
    const INFO_APP_STDERR = 'AppStderr';
    const INFO_APP_STDOUT = 'AppStdout';
    const INFO_APP_ENVIRONMENT_EXTRA = 'AppEnvironmentExtra';

    const PENDING_TIMEOUT = 10;
    const SLEEP_TIME = 500000;

    private $name;
    private $displayName;
    private $binPath;
    private $params;
    private $start;
    private $stdout;
    private $stderr;
    private $environmentExtra;
    private $latestError;
    private $latestStatus;

    /**
     * Nssm constructor.
     * Initializes the Nssm class and logs the initialization.
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
     * @param   string  $log  The log message to write.
     */
    private function writeLog($log)
    {
        global $bearsamppRoot;
        Util::logDebug( $log, $bearsamppRoot->getNssmLogFilePath() );
    }

    /**
     * Writes an informational log entry.
     *
     * @param   string  $log  The log message to write.
     */
    private function writeLogInfo($log)
    {
        global $bearsamppRoot;
        Util::logInfo( $log, $bearsamppRoot->getNssmLogFilePath() );
    }

    /**
     * Writes an error log entry.
     *
     * @param   string  $log  The log message to write.
     */
    private function writeLogError($log)
    {
        global $bearsamppRoot;
        Util::logError( $log, $bearsamppRoot->getNssmLogFilePath() );
    }

    /**
     * Executes an NSSM command.
     *
     * @param   string  $args  The arguments for the NSSM command.
     *
     * @return array|false The result of the execution, or false on failure.
     */
    private function exec($args)
    {
        global $bearsamppCore;

        $command = '"' . $bearsamppCore->getNssmExe() . '" ' . $args;
        $this->writeLogInfo( 'Cmd: ' . $command );

        $result = Batch::exec( 'nssm', $command, 10 );
        if ( is_array( $result ) ) {
            $rebuildResult = array();
            foreach ( $result as $row ) {
                $row = trim( $row );
                if ( !empty( $row ) ) {
                    $rebuildResult[] = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $row );
                }
            }
            $result = $rebuildResult;
            if ( count( $result ) > 1 ) {
                $this->latestError = implode( ' ; ', $result );
            }

            return $result;
        }

        return false;
    }

    /**
     * Retrieves the status of the service.
     *
     * @param   bool  $timeout  Whether to apply a timeout for the status check.
     *
     * @return string The status of the service.
     */
    public function status($timeout = true)
    {
        usleep( self::SLEEP_TIME );

        $this->latestStatus = self::STATUS_NA;
        $maxtime            = time() + self::PENDING_TIMEOUT;

        while ( $this->latestStatus == self::STATUS_NA || $this->isPending( $this->latestStatus ) ) {
            $exec = $this->exec( 'status ' . $this->getName() );
            if ( $exec !== false ) {
                if ( count( $exec ) > 1 ) {
                    $this->latestStatus = self::STATUS_NOT_EXIST;
                }
                else {
                    $this->latestStatus = $exec[0];
                }
            }
            if ( $timeout && $maxtime < time() ) {
                break;
            }
        }

        if ( $this->latestStatus == self::STATUS_NOT_EXIST ) {
            $this->latestError  = 'Error 3: The specified service does not exist as an installed service.';
            $this->latestStatus = self::STATUS_NA;
        }

        return $this->latestStatus;
    }

    /**
     * Creates a new service.
     *
     * @return bool True if the service was created successfully, false otherwise.
     */
    public function create()
    {
        $this->writeLog( 'Create service' );
        $this->writeLog( '-> service: ' . $this->getName() );
        $this->writeLog( '-> display: ' . $this->getDisplayName() );
        $this->writeLog( '-> description: ' . $this->getDisplayName() );
        $this->writeLog( '-> path: ' . $this->getBinPath() );
        $this->writeLog( '-> params: ' . $this->getParams() );
        $this->writeLog( '-> stdout: ' . $this->getStdout() );
        $this->writeLog( '-> stderr: ' . $this->getStderr() );
        $this->writeLog( '-> environment extra: ' . $this->getEnvironmentExtra() );
        $this->writeLog( '-> start_type: ' . ($this->getStart() != null ? $this->getStart() : self::SERVICE_DEMAND_START) );

        // Install bin
        $exec = $this->exec( 'install ' . $this->getName() . ' "' . $this->getBinPath() . '"' );
        if ( $exec === false ) {
            return false;
        }

        // Params
        $exec = $this->exec( 'set ' . $this->getName() . ' AppParameters "' . $this->getParams() . '"' );
        if ( $exec === false ) {
            return false;
        }

        // DisplayName
        $exec = $this->exec( 'set ' . $this->getName() . ' DisplayName "' . $this->getDisplayName() . '"' );
        if ( $exec === false ) {
            return false;
        }

        // Description
        $exec = $this->exec( 'set ' . $this->getName() . ' Description "' . $this->getDisplayName() . '"' );
        if ( $exec === false ) {
            return false;
        }

        // No AppNoConsole to fix nssm problems with Windows 10 Creators update.
        $exec = $this->exec( 'set ' . $this->getName() . ' AppNoConsole "1"' );
        if ( $exec === false ) {
            return false;
        }

        // Start
        $exec = $this->exec( 'set ' . $this->getName() . ' Start "' . ($this->getStart() != null ? $this->getStart() : self::SERVICE_DEMAND_START) . '"' );
        if ( $exec === false ) {
            return false;
        }

        // Stdout
        $exec = $this->exec( 'set ' . $this->getName() . ' AppStdout "' . $this->getStdout() . '"' );
        if ( $exec === false ) {
            return false;
        }

        // Stderr
        $exec = $this->exec( 'set ' . $this->getName() . ' AppStderr "' . $this->getStderr() . '"' );
        if ( $exec === false ) {
            return false;
        }

        // Environment Extra
        $exec = $this->exec( 'set ' . $this->getName() . ' AppEnvironmentExtra ' . $this->getEnvironmentExtra() );
        if ( $exec === false ) {
            return false;
        }

        if ( !$this->isInstalled() ) {
            $this->latestError = null;

            return false;
        }

        return true;
    }

    /**
     * Deletes the service.
     *
     * @return bool True if the service was deleted successfully, false otherwise.
     */
    public function delete()
    {
        $this->stop();

        $this->writeLog( 'Delete service ' . $this->getName() );
        $exec = $this->exec( 'remove ' . $this->getName() . ' confirm' );
        if ( $exec === false ) {
            return false;
        }

        if ( $this->isInstalled() ) {
            $this->latestError = null;

            return false;
        }

        return true;
    }

    /**
     * Starts the service.
     *
     * @return bool True if the service was started successfully, false otherwise.
     */
    public function start()
    {
        $this->writeLog( 'Start service ' . $this->getName() );

        $exec = $this->exec( 'start ' . $this->getName() );
        if ( $exec === false ) {
            return false;
        }

        if ( !$this->isRunning() ) {
            $this->latestError = null;

            return false;
        }

        return true;
    }

    /**
     * Stops the service.
     *
     * @return bool True if the service was stopped successfully, false otherwise.
     */
    public function stop()
    {
        $this->writeLog( 'Stop service ' . $this->getName() );

        $exec = $this->exec( 'stop ' . $this->getName() );
        if ( $exec === false ) {
            return false;
        }

        if ( !$this->isStopped() ) {
            $this->latestError = null;

            return false;
        }

        return true;
    }

    /**
     * Restarts the service.
     *
     * @return bool True if the service was restarted successfully, false otherwise.
     */
    public function restart()
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
        global $bearsamppRegistry;

        $infos = Vbs::getServiceInfos( $this->getName() );
        if ( $infos === false ) {
            return false;
        }

        $infosNssm = array();
        $infosKeys = array(
            self::INFO_APPLICATION,
            self::INFO_APP_PARAMETERS,
        );

        foreach ( $infosKeys as $infoKey ) {
            $value  = null;
            $exists = $bearsamppRegistry->exists(
                Registry::HKEY_LOCAL_MACHINE,
                'SYSTEM\CurrentControlSet\Services\\' . $this->getName() . '\Parameters',
                $infoKey
            );
            if ( $exists ) {
                $value = $bearsamppRegistry->getValue(
                    Registry::HKEY_LOCAL_MACHINE,
                    'SYSTEM\CurrentControlSet\Services\\' . $this->getName() . '\Parameters',
                    $infoKey
                );
            }
            $infosNssm[$infoKey] = $value;
        }

        if ( !isset( $infosNssm[self::INFO_APPLICATION] ) ) {
            return $infos;
        }

        $infos[Win32Service::VBS_PATH_NAME] = $infosNssm[Nssm::INFO_APPLICATION] . ' ' . $infosNssm[Nssm::INFO_APP_PARAMETERS];

        return $infos;
    }

    /**
     * Checks if the service is installed.
     *
     * @return bool True if the service is installed, false otherwise.
     */
    public function isInstalled()
    {
        $status = $this->status();
        $this->writeLog( 'isInstalled ' . $this->getName() . ': ' . ($status != self::STATUS_NA ? 'YES' : 'NO') . ' (status: ' . $status . ')' );

        return $status != self::STATUS_NA;
    }

    /**
     * Checks if the service is running.
     *
     * @return bool True if the service is running, false otherwise.
     */
    public function isRunning()
    {
        $status = $this->status();
        $this->writeLog( 'isRunning ' . $this->getName() . ': ' . ($status == self::STATUS_RUNNING ? 'YES' : 'NO') . ' (status: ' . $status . ')' );

        return $status == self::STATUS_RUNNING;
    }

    /**
     * Checks if the service is stopped.
     *
     * @return bool True if the service is stopped, false otherwise.
     */
    public function isStopped()
    {
        $status = $this->status();
        $this->writeLog( 'isStopped ' . $this->getName() . ': ' . ($status == self::STATUS_STOPPED ? 'YES' : 'NO') . ' (status: ' . $status . ')' );

        return $status == self::STATUS_STOPPED;
    }

    /**
     * Checks if the service is paused.
     *
     * @return bool True if the service is paused, false otherwise.
     */
    public function isPaused()
    {
        $status = $this->status();
        $this->writeLog( 'isPaused ' . $this->getName() . ': ' . ($status == self::STATUS_PAUSED ? 'YES' : 'NO') . ' (status: ' . $status . ')' );

        return $status == self::STATUS_PAUSED;
    }

    /**
     * Checks if the service status is pending.
     *
     * @param   string  $status  The status to check.
     *
     * @return bool True if the status is pending, false otherwise.
     */
    public function isPending($status)
    {
        return $status == self::STATUS_START_PENDING || $status == self::STATUS_STOP_PENDING
            || $status == self::STATUS_CONTINUE_PENDING || $status == self::STATUS_PAUSE_PENDING;
    }

    /**
     * Retrieves the description of the service status.
     *
     * @param   string  $status  The status to describe.
     *
     * @return string|null The description of the status, or null if not recognized.
     */
    private function getServiceStatusDesc($status)
    {
        switch ( $status ) {
            case self::STATUS_CONTINUE_PENDING:
                return 'The service continue is pending.';

            case self::STATUS_PAUSE_PENDING:
                return 'The service pause is pending.';

            case self::STATUS_PAUSED:
                return 'The service is paused.';

            case self::STATUS_RUNNING:
                return 'The service is running.';

            case self::STATUS_START_PENDING:
                return 'The service is starting.';

            case self::STATUS_STOP_PENDING:
                return 'The service is stopping.';

            case self::STATUS_STOPPED:
                return 'The service is not running.';

            case self::STATUS_NA:
                return 'Cannot retrieve service status.';

            default:
                return null;
        }
    }

    /**
     * Gets the name of the service.
     *
     * @return string The name of the service.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the service.
     *
     * @param   string  $name  The name to set.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the display name of the service.
     *
     * @return string The display name of the service.
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Sets the display name of the service.
     *
     * @param   string  $displayName  The display name to set.
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Gets the binary path of the service.
     *
     * @return string The binary path of the service.
     */
    public function getBinPath()
    {
        return $this->binPath;
    }

    /**
     * Sets the binary path of the service.
     *
     * @param   string  $binPath  The binary path to set.
     */
    public function setBinPath($binPath)
    {
        $this->binPath = str_replace( '"', '', Util::formatWindowsPath( $binPath ) );
    }

    /**
     * Gets the parameters of the service.
     *
     * @return string The parameters of the service.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Sets the parameters of the service.
     *
     * @param   string  $params  The parameters to set.
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Gets the start type of the service.
     *
     * @return string The start type of the service.
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Sets the start type of the service.
     *
     * @param   string  $start  The start type to set.
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Gets the stdout path of the service.
     *
     * @return string The stdout path of the service.
     */
    public function getStdout()
    {
        return $this->stdout;
    }

    /**
     * Sets the stdout path of the service.
     *
     * @param   string  $stdout  The stdout path to set.
     */
    public function setStdout($stdout)
    {
        $this->stdout = $stdout;
    }

    /**
     * Gets the stderr path of the service.
     *
     * @return string The stderr path of the service.
     */
    public function getStderr()
    {
        return $this->stderr;
    }

    /**
     * Sets the stderr path of the service.
     *
     * @param   string  $stderr  The stderr path to set.
     */
    public function setStderr($stderr)
    {
        $this->stderr = $stderr;
    }

    /**
     * Gets the additional environment variables for the service.
     *
     * @return string The additional environment variables.
     */
    public function getEnvironmentExtra()
    {
        return $this->environmentExtra;
    }

    /**
     * Sets the additional environment variables for the service.
     *
     * @param   string  $environmentExtra  The additional environment variables to set.
     */
    public function setEnvironmentExtra($environmentExtra)
    {
        $this->environmentExtra = Util::formatWindowsPath( $environmentExtra );
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
     * Gets the latest error message related to the service.
     *
     * @return string The latest error message.
     */
    public function getLatestError()
    {
        return $this->latestError;
    }

    /**
     * Retrieves the error message or status description of the service.
     *
     * @return string|null The error message or status description, or null if no error or status is available.
     */
    public function getError()
    {
        global $bearsamppLang;

        if ( !empty( $this->latestError ) ) {
            return $bearsamppLang->getValue( Lang::ERROR ) . ' ' . $this->latestError;
        }
        elseif ( $this->latestStatus != self::STATUS_NA ) {
            return $bearsamppLang->getValue( Lang::STATUS ) . ' ' . $this->latestStatus . ' : ' . $this->getWin32ServiceStatusDesc( $this->latestStatus );
        }

        return null;
    }
}
