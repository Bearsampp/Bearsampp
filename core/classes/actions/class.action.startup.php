<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionStartup
 * Handles the startup process of the Bearsampp application, including initializing services,
 * cleaning temporary files, refreshing configurations, and more.
 */
class ActionStartup
{
    private $splash;
    private $restart;
    private $startTime;
    private $error;

    private $rootPath;
    private $filesToScan;

    const GAUGE_SERVICES = 5;
    const GAUGE_OTHERS = 19;

    /**
     * ActionStartup constructor.
     * Initializes the startup process, including the splash screen and various configurations.
     *
     * @param   array  $args  Command line arguments.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;
        $this->writeLog( 'Starting ' . APP_TITLE );

        // Init
        $this->splash    = new Splash();
        $this->restart   = false;
        $this->startTime = Util::getMicrotime();
        $this->error     = '';

        $this->rootPath    = $bearsamppRoot->getRootPath();
        $this->filesToScan = array();

        $gauge = self::GAUGE_SERVICES * count( $bearsamppBins->getServices() );
        $gauge += self::GAUGE_OTHERS + 1;

        // Start splash screen
        $this->splash->init(
            $bearsamppLang->getValue( Lang::STARTUP ),
            $gauge,
            sprintf( $bearsamppLang->getValue( Lang::STARTUP_STARTING_TEXT ), APP_TITLE . ' ' . $bearsamppCore->getAppVersion() )
        );

        $bearsamppWinbinder->setHandler( $this->splash->getWbWindow(), $this, 'processWindow', 1000 );
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes the main window events during startup.
     *
     * @param   mixed  $window  The window handle.
     * @param   int    $id      The event ID.
     * @param   mixed  $ctrl    The control that triggered the event.
     * @param   mixed  $param1  Additional parameter 1.
     * @param   mixed  $param2  Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppApps, $bearsamppWinbinder;

        // Rotation logs
        $this->rotationLogs();

        // Clean
        $this->cleanTmpFolders();
        $this->cleanOldBehaviors();

        // List procs
        if ( $bearsamppRoot->getProcs() !== false ) {
            $this->writeLog( 'List procs:' );
            $listProcs = array();
            foreach ( $bearsamppRoot->getProcs() as $proc ) {
                $unixExePath = Util::formatUnixPath( $proc[Win32Ps::EXECUTABLE_PATH] );
                $listProcs[] = '-> ' . basename( $unixExePath ) . ' (PID ' . $proc[Win32Ps::PROCESS_ID] . ') in ' . $unixExePath;
            }
            sort( $listProcs );
            foreach ( $listProcs as $proc ) {
                $this->writeLog( $proc );
            }
        }

        // List modules
        $this->writeLog( 'List bins modules:' );
        foreach ( $bearsamppBins->getAll() as $module ) {
            if ( !$module->isEnable() ) {
                $this->writeLog( '-> ' . $module->getName() . ': ' . $bearsamppLang->getValue( Lang::DISABLED ) );
            }
            else {
                $this->writeLog( '-> ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')' );
            }
        }
        $this->writeLog( 'List tools modules:' );
        foreach ( $bearsamppTools->getAll() as $module ) {
            if ( !$module->isEnable() ) {
                $this->writeLog( '-> ' . $module->getName() . ': ' . $bearsamppLang->getValue( Lang::DISABLED ) );
            }
            else {
                $this->writeLog( '-> ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')' );
            }
        }
        $this->writeLog( 'List apps modules:' );
        foreach ( $bearsamppApps->getAll() as $module ) {
            if ( !$module->isEnable() ) {
                $this->writeLog( '-> ' . $module->getName() . ': ' . $bearsamppLang->getValue( Lang::DISABLED ) );
            }
            else {
                $this->writeLog( '-> ' . $module->getName() . ': ' . $module->getVersion() . ' (' . $module->getRelease() . ')' );
            }
        }

        // Kill old instances
        $this->killOldInstances();

        // Prepare app
        $this->refreshHostname();
        $this->checkLaunchStartup();
        $this->checkBrowser();
        $this->sysInfos();
        $this->refreshAliases();
        $this->refreshVhosts();

        // Check app path
        $this->checkPath();
        $this->scanFolders();
        $this->changePath();
        $this->savePath();

        // Check BEARSAMPP_PATH, BEARSAMPP_BINS and System Path reg keys
        $this->checkPathRegKey();
        $this->checkBinsRegKey();
        $this->checkSystemPathRegKey();

        // Update config
        $this->updateConfig();

        // Create SSL certificates
        $this->createSslCrts();

        // Install
        $this->installServices();

        // Actions if everything OK
        if ( !$this->restart && empty( $this->error ) ) {
            $this->refreshGitRepos();
            $this->writeLog( 'Started in ' . round( Util::getMicrotime() - $this->startTime, 3 ) . 's' );
        }
        else {
            $this->splash->incrProgressBar( 2 );
        }

        if ( $this->restart ) {
            $this->writeLog( APP_TITLE . ' has to be restarted' );
            $this->splash->setTextLoading(
                sprintf(
                    $bearsamppLang->getValue( Lang::STARTUP_PREPARE_RESTART_TEXT ),
                    APP_TITLE . ' ' . $bearsamppCore->getAppVersion()
                )
            );
            foreach ( $bearsamppBins->getServices() as $sName => $service ) {
                $service->delete();
            }
            $bearsamppCore->setExec( ActionExec::RESTART );
        }

        if ( !empty( $this->error ) ) {
            $this->writeLog( 'Error: ' . $this->error );
            $bearsamppWinbinder->messageBoxError( $this->error, $bearsamppLang->getValue( Lang::STARTUP_ERROR_TITLE ) );
        }

        Util::startLoading();
        $bearsamppWinbinder->destroyWindow( $window );
    }

    /**
     * Rotates the logs by archiving old logs and purging old archives.
     */
    private function rotationLogs()
    {
        global $bearsamppRoot, $bearsamppCore, $bearsamppConfig, $bearsamppLang, $bearsamppBins;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_ROTATION_LOGS_TEXT ) );
        $this->splash->incrProgressBar();

        $archivesPath = $bearsamppRoot->getLogsPath() . '/archives';
        if ( !is_dir( $archivesPath ) ) {
            mkdir( $archivesPath, 0777, true );

            return;
        }

        $date               = date( 'Y-m-d-His', time() );
        $archiveLogsPath    = $archivesPath . '/' . $date;
        $archiveScriptsPath = $archiveLogsPath . '/scripts';

        // Create archive folders
        mkdir( $archiveLogsPath, 0777, true );
        mkdir( $archiveScriptsPath, 0777, true );

        // Count archives
        $archives = array();
        $handle   = @opendir( $archivesPath );
        if ( !$handle ) {
            return;
        }
        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file == '.' || $file == '..' ) {
                continue;
            }
            $archives[] = $archivesPath . '/' . $file;
        }
        closedir( $handle );
        sort( $archives );

        // Remove old archives
        if ( count( $archives ) > $bearsamppConfig->getMaxLogsArchives() ) {
            $total = count( $archives ) - $bearsamppConfig->getMaxLogsArchives();
            for ( $i = 0; $i < $total; $i++ ) {
                Util::deleteFolder( $archives[$i] );
            }
        }

        // Logs
        $srcPath = $bearsamppRoot->getLogsPath();
        $handle  = @opendir( $srcPath );
        if ( !$handle ) {
            return;
        }
        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file == '.' || $file == '..' || is_dir( $srcPath . '/' . $file ) ) {
                continue;
            }
            copy( $srcPath . '/' . $file, $archiveLogsPath . '/' . $file );
        }
        closedir( $handle );

        // Scripts
        $srcPath = $bearsamppCore->getTmpPath();
        $handle  = @opendir( $srcPath );
        if ( !$handle ) {
            return;
        }
        while ( false !== ($file = readdir( $handle )) ) {
            if ( $file == '.' || $file == '..' || is_dir( $srcPath . '/' . $file ) ) {
                continue;
            }
            copy( $srcPath . '/' . $file, $archiveScriptsPath . '/' . $file );
        }
        closedir( $handle );

        // Purge logs
        Util::clearFolder( $bearsamppRoot->getLogsPath(), array('archives', '.gitignore') );
    }

    /**
     * Cleans temporary folders by removing unnecessary files.
     */
    private function cleanTmpFolders()
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppCore;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_CLEAN_TMP_TEXT ) );
        $this->splash->incrProgressBar();

        $this->writeLog( 'Clear tmp folders' );
        Util::clearFolder( $bearsamppRoot->getTmpPath(), array('cachegrind', 'composer', 'openssl', 'mailpit', 'xlight', 'npm-cache', 'pip', '.gitignore') );
        Util::clearFolder( $bearsamppCore->getTmpPath(), array('.gitignore') );
    }

    /**
     * Cleans old behaviors by removing outdated registry entries.
     */
    private function cleanOldBehaviors()
    {
        global $bearsamppLang, $bearsamppRegistry;

        $this->writeLog( 'Clean old behaviors' );

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_CLEAN_OLD_BEHAVIORS_TEXT ) );
        $this->splash->incrProgressBar();

        // App >= 1.0.13
        $bearsamppRegistry->deleteValue(
            Registry::HKEY_LOCAL_MACHINE,
            'SOFTWARE\Microsoft\Windows\CurrentVersion\Run',
            APP_TITLE
        );
    }

    /**
     * Kills old instances of Bearsampp processes.
     */
    private function killOldInstances()
    {
        global $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_KILL_OLD_PROCS_TEXT ) );
        $this->splash->incrProgressBar();

        // Stop services
        /*foreach ($bearsamppBins->getServices() as $sName => $service) {
            $serviceInfos = $service->infos();
            if ($serviceInfos === false) {
                continue;
            }
            $service->stop();
        }*/

        // Stop third party procs
        $procsKilled = Win32Ps::killBins();
        if ( !empty( $procsKilled ) ) {
            $this->writeLog( 'Procs killed:' );
            $procsKilledSort = array();
            foreach ( $procsKilled as $proc ) {
                $unixExePath       = Util::formatUnixPath( $proc[Win32Ps::EXECUTABLE_PATH] );
                $procsKilledSort[] = '-> ' . basename( $unixExePath ) . ' (PID ' . $proc[Win32Ps::PROCESS_ID] . ') in ' . $unixExePath;
            }
            sort( $procsKilledSort );
            foreach ( $procsKilledSort as $proc ) {
                $this->writeLog( $proc );
            }
        }
    }

    /**
     * Refreshes the hostname in the configuration.
     */
    private function refreshHostname()
    {
        global $bearsamppConfig, $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_REFRESH_HOSTNAME_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Refresh hostname' );

        $bearsamppConfig->replace( Config::CFG_HOSTNAME, gethostname() );
    }

    /**
     * Checks and sets the launch startup configuration.
     */
    private function checkLaunchStartup()
    {
        global $bearsamppConfig;

        $this->writeLog( 'Check launch startup' );

        if ( $bearsamppConfig->isLaunchStartup() ) {
            Util::enableLaunchStartup();
        }
        else {
            Util::disableLaunchStartup();
        }
    }

    /**
     * Checks and sets the default browser configuration.
     */
    private function checkBrowser()
    {
        global $bearsamppConfig, $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_CHECK_BROWSER_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Check browser' );

        $currentBrowser = $bearsamppConfig->getBrowser();
        if ( empty( $currentBrowser ) || !file_exists( $currentBrowser ) ) {
            $bearsamppConfig->replace( Config::CFG_BROWSER, Vbs::getDefaultBrowser() );
        }
    }

    /**
     * Logs system information.
     */
    private function sysInfos()
    {
        global $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_SYS_INFOS ) );
        $this->splash->incrProgressBar();

        $os = Batch::getOsInfo();
        $this->writeLog( sprintf( 'OS: %s', $os ) );
    }

    /**
     * Refreshes the aliases in the Apache configuration.
     */
    private function refreshAliases()
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppBins;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_REFRESH_ALIAS_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Refresh aliases' );

        $bearsamppBins->getApache()->refreshAlias( $bearsamppConfig->isOnline() );
    }

    /**
     * Refreshes the virtual hosts in the Apache configuration.
     */
    private function refreshVhosts()
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppBins;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_REFRESH_VHOSTS_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Refresh vhosts' );

        $bearsamppBins->getApache()->refreshVhosts( $bearsamppConfig->isOnline() );
    }

    /**
     * Checks the application path and logs the last path content.
     */
    private function checkPath()
    {
        global $bearsamppCore, $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_CHECK_PATH_TEXT ) );
        $this->splash->incrProgressBar();

        $this->writeLog( 'Last path: ' . $bearsamppCore->getLastPathContent() );
    }

    /**
     * Scans folders and logs the number of files to scan.
     */
    private function scanFolders()
    {
        global $bearsamppLang;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_SCAN_FOLDERS_TEXT ) );
        $this->splash->incrProgressBar();

        $this->filesToScan = Util::getFilesToScan();
        $this->writeLog( 'Files to scan: ' . count( $this->filesToScan ) );
    }

    /**
     * Changes the application path and logs the number of files and occurrences changed.
     */
    private function changePath()
    {
        global $bearsamppLang;

        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_CHANGE_PATH_TEXT ), $this->rootPath ) );
        $this->splash->incrProgressBar();

        $result = Util::changePath( $this->filesToScan, $this->rootPath );
        $this->writeLog( 'Nb files changed: ' . $result['countChangedFiles'] );
        $this->writeLog( 'Nb occurences changed: ' . $result['countChangedOcc'] );
    }

    /**
     * Saves the current application path.
     */
    private function savePath()
    {
        global $bearsamppCore;

        file_put_contents( $bearsamppCore->getLastPath(), $this->rootPath );
        $this->writeLog( 'Save current path: ' . $this->rootPath );
    }

    /**
     * Checks and updates the application path registry key.
     */
    private function checkPathRegKey()
    {
        global $bearsamppRoot, $bearsamppLang, $bearsamppRegistry;

        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_TEXT ), Registry::APP_PATH_REG_ENTRY ) );
        $this->splash->incrProgressBar();

        $currentAppPathRegKey = Util::getAppPathRegKey();
        $genAppPathRegKey     = Util::formatWindowsPath( $bearsamppRoot->getRootPath() );
        $this->writeLog( 'Current app path reg key: ' . $currentAppPathRegKey );
        $this->writeLog( 'Gen app path reg key: ' . $genAppPathRegKey );
        if ( $currentAppPathRegKey != $genAppPathRegKey ) {
            if ( !Util::setAppPathRegKey( $genAppPathRegKey ) ) {
                if ( !empty( $this->error ) ) {
                    $this->error .= PHP_EOL . PHP_EOL;
                }
                $this->error .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_ERROR_TEXT ), Registry::APP_PATH_REG_ENTRY );
                $this->error .= PHP_EOL . $bearsamppRegistry->getLatestError();
            }
            else {
                $this->writeLog( 'Need restart: checkPathRegKey' );
                $this->restart = true;
            }
        }
    }

    /**
     * Checks and updates the application bins registry key.
     * If the current registry key does not match the generated key, it updates the registry key.
     * Logs the current and generated registry keys.
     * Sets an error message if the registry key update fails.
     * Sets a restart flag if the registry key is updated.
     */
    private function checkBinsRegKey()
    {
        global $bearsamppLang, $bearsamppRegistry;

        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_TEXT ), Registry::APP_BINS_REG_ENTRY ) );
        $this->splash->incrProgressBar();

        $currentAppBinsRegKey = Util::getAppBinsRegKey();
        $genAppBinsRegKey     = Util::getAppBinsRegKey( false );
        $this->writeLog( 'Current app bins reg key: ' . $currentAppBinsRegKey );
        $this->writeLog( 'Gen app bins reg key: ' . $genAppBinsRegKey );
        if ( $currentAppBinsRegKey != $genAppBinsRegKey ) {
            if ( !Util::setAppBinsRegKey( $genAppBinsRegKey ) ) {
                if ( !empty( $this->error ) ) {
                    $this->error .= PHP_EOL . PHP_EOL;
                }
                $this->error .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_ERROR_TEXT ), Registry::APP_BINS_REG_ENTRY );
                $this->error .= PHP_EOL . $bearsamppRegistry->getLatestError();
            }
            else {
                $this->writeLog( 'Need restart: checkBinsRegKey' );
                $this->restart = true;
            }
        }
    }

    /**
     * Checks and updates the system PATH registry key.
     * Ensures the application bins registry entry is at the beginning of the system PATH.
     * Logs the current and new system PATH.
     * Sets an error message if the system PATH update fails.
     * Sets a restart flag if the system PATH is updated.
     */
    private function checkSystemPathRegKey()
    {
        global $bearsamppLang, $bearsamppRegistry;

        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_TEXT ), Registry::SYSPATH_REG_ENTRY ) );
        $this->splash->incrProgressBar();

        $currentSysPathRegKey = Util::getSysPathRegKey();
        $this->writeLog( 'Current system PATH: ' . $currentSysPathRegKey );

        $newSysPathRegKey = str_replace( '%' . Registry::APP_BINS_REG_ENTRY . '%;', '', $currentSysPathRegKey );
        $newSysPathRegKey = str_replace( '%' . Registry::APP_BINS_REG_ENTRY . '%', '', $newSysPathRegKey );
        $newSysPathRegKey = '%' . Registry::APP_BINS_REG_ENTRY . '%;' . $newSysPathRegKey;
        $this->writeLog( 'New system PATH: ' . $newSysPathRegKey );

        if ( $currentSysPathRegKey != $newSysPathRegKey ) {
            if ( !Util::setSysPathRegKey( $newSysPathRegKey ) ) {
                if ( !empty( $this->error ) ) {
                    $this->error .= PHP_EOL . PHP_EOL;
                }
                $this->error .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_REGISTRY_ERROR_TEXT ), Registry::SYSPATH_REG_ENTRY );
                $this->error .= PHP_EOL . $bearsamppRegistry->getLatestError();
            }
            else {
                $this->writeLog( 'Need restart: checkSystemPathRegKey' );
                $this->restart = true;
            }
        }
        else {
            $this->writeLog( 'Refresh system PATH: ' . $currentSysPathRegKey );
            Util::setSysPathRegKey( str_replace( '%' . Registry::APP_BINS_REG_ENTRY . '%', '', $currentSysPathRegKey ) );
            Util::setSysPathRegKey( $currentSysPathRegKey );
        }
    }

    /**
     * Updates the configuration for bins, tools, and apps.
     * Logs the update process.
     */
    private function updateConfig()
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppApps;

        $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_UPDATE_CONFIG_TEXT ) );
        $this->splash->incrProgressBar();
        $this->writeLog( 'Update config' );

        $bearsamppBins->update();
        $bearsamppTools->update();
        $bearsamppApps->update();
    }

    /**
     * Creates SSL certificates if they do not already exist.
     * Logs the creation process.
     */
    private function createSslCrts()
    {
        global $bearsamppLang, $bearsamppOpenSsl;

        $this->splash->incrProgressBar();
        if ( !$bearsamppOpenSsl->existsCrt( 'localhost' ) ) {
            $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_GEN_SSL_CRT_TEXT ), 'localhost' ) );
            $bearsamppOpenSsl->createCrt( 'localhost' );
        }
    }

    /**
     * Installs and starts services for the application.
     * Checks if services are already installed and updates them if necessary.
     * Logs the installation process and any errors encountered.
     */
    private function installServices()
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppRoot;

        if ( !$this->restart ) {

            foreach ( $bearsamppBins->getServices() as $sName => $service ) {
                $serviceError            = '';
                $serviceRestart          = false;
                $serviceAlreadyInstalled = false;
                $serviceToRemove         = false;
                $startServiceTime        = Util::getMicrotime();

                $syntaxCheckCmd = null;
                $bin            = null;
                $port           = 0;
                if ( $sName == BinMailpit::SERVICE_NAME ) {
                    $bin  = $bearsamppBins->getMailpit();
                    $port = $bearsamppBins->getMailpit()->getSmtpPort();
                }
                elseif ( $sName == BinMemcached::SERVICE_NAME ) {
                    $bin  = $bearsamppBins->getMemcached();
                    $port = $bearsamppBins->getMemcached()->getPort();
                }
                elseif ( $sName == BinApache::SERVICE_NAME ) {
                    $bin            = $bearsamppBins->getApache();
                    $port           = $bearsamppBins->getApache()->getPort();
                    $syntaxCheckCmd = BinApache::CMD_SYNTAX_CHECK;
                }
                elseif ( $sName == BinMysql::SERVICE_NAME ) {
                    $bin            = $bearsamppBins->getMysql();
                    $port           = $bearsamppBins->getMysql()->getPort();
                    $syntaxCheckCmd = BinMysql::CMD_SYNTAX_CHECK;
                }
                elseif ( $sName == BinMariadb::SERVICE_NAME ) {
                    $bin            = $bearsamppBins->getMariadb();
                    $port           = $bearsamppBins->getMariadb()->getPort();
                    $syntaxCheckCmd = BinMariadb::CMD_SYNTAX_CHECK;
                }
                elseif ( $sName == BinPostgresql::SERVICE_NAME ) {
                    $bin  = $bearsamppBins->getPostgresql();
                    $port = $bearsamppBins->getPostgresql()->getPort();
                }
                elseif ( $sName == BinXlight::SERVICE_NAME ) {
                    $bin  = $bearsamppBins->getXlight();
                    $port = $bearsamppBins->getXlight()->getPort();
                }

                $name = $bin->getName() . ' ' . $bin->getVersion() . ' (' . $service->getName() . ')';

                $this->splash->incrProgressBar();
                $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_CHECK_SERVICE_TEXT ), $name ) );
                $serviceInfos = $service->infos();
                if ( $serviceInfos !== false ) {
                    $serviceAlreadyInstalled = true;
                    $this->writeLog( $name . ' service already installed' );
                    foreach ( $serviceInfos as $key => $value ) {
                        $this->writeLog( '-> ' . $key . ': ' . $value );
                    }
                    $serviceGenPathName = trim( str_replace( '"', '', $service->getBinPath() . ($service->getParams() ? ' ' . $service->getParams() : '') ) );
                    $serviceVbsPathName = trim( str_replace( '"', '', $serviceInfos[Win32Service::VBS_PATH_NAME] ) );
                    if ( $serviceGenPathName != $serviceVbsPathName ) {
                        $serviceToRemove = true;
                        $this->writeLog( $name . ' service has to be removed' );
                        $this->writeLog( '-> serviceGenPathName: ' . $serviceGenPathName );
                        $this->writeLog( '-> serviceVbsPathName: ' . $serviceVbsPathName );
                    }
                }

                $this->splash->incrProgressBar();
                if ( $serviceToRemove && !$service->delete() ) {
                    $serviceRestart = true;
                }

                if ( !$serviceRestart ) {
                    $isPortInUse = Util::isPortInUse( $port );
                    if ( $isPortInUse === false ) {
                        $this->splash->incrProgressBar();
                        if ( !$serviceAlreadyInstalled && !$serviceToRemove ) {
                            $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_INSTALL_SERVICE_TEXT ), $name ) );
                            if ( !$service->create() ) {
                                $serviceError .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_SERVICE_CREATE_ERROR ), $service->getError() );
                            }
                        }

                        $this->splash->incrProgressBar();
                        $this->splash->setTextLoading( sprintf( $bearsamppLang->getValue( Lang::STARTUP_START_SERVICE_TEXT ), $name ) );
                        if ( !$service->start() ) {
                            if ( !empty( $serviceError ) ) {
                                $serviceError .= PHP_EOL;
                            }
                            $serviceError .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_SERVICE_START_ERROR ), $service->getError() );
                            if ( !empty( $syntaxCheckCmd ) ) {
                                $cmdSyntaxCheck = $bin->getCmdLineOutput( $syntaxCheckCmd );
                                if ( !$cmdSyntaxCheck['syntaxOk'] ) {
                                    $serviceError .= PHP_EOL . sprintf( $bearsamppLang->getValue( Lang::STARTUP_SERVICE_SYNTAX_ERROR ), $cmdSyntaxCheck['content'] );
                                }
                            }
                        }
                        $this->splash->incrProgressBar();
                    }
                    else {
                        if ( !empty( $serviceError ) ) {
                            $serviceError .= PHP_EOL;
                        }
                        $serviceError .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_SERVICE_PORT_ERROR ), $port, $isPortInUse );
                        $this->splash->incrProgressBar( 3 );
                    }
                }
                else {
                    $this->writeLog( 'Need restart: installService ' . $bin->getName() );
                    $this->restart = true;
                    $this->splash->incrProgressBar( 3 );
                }

                if ( !empty( $serviceError ) ) {
                    if ( !empty( $this->error ) ) {
                        $this->error .= PHP_EOL . PHP_EOL;
                    }
                    $this->error .= sprintf( $bearsamppLang->getValue( Lang::STARTUP_SERVICE_ERROR ), $name ) . PHP_EOL . $serviceError;
                }
                else {
                    $this->writeLog( $name . ' service installed in ' . round( Util::getMicrotime() - $startServiceTime, 3 ) . 's' );
                }
            }
        }
        else {
            $this->splash->incrProgressBar( self::GAUGE_SERVICES * count( $bearsamppBins->getServices() ) );
        }
    }

    /**
     * Refreshes Git repositories if the scan on startup is enabled.
     * Logs the number of repositories found.
     */
    private function refreshGitRepos()
    {
        global $bearsamppLang, $bearsamppTools;

        $this->splash->incrProgressBar();
        if ( $bearsamppTools->getGit()->isScanStartup() ) {
            $this->splash->setTextLoading( $bearsamppLang->getValue( Lang::STARTUP_REFRESH_GIT_REPOS_TEXT ) );

            $repos = $bearsamppTools->getGit()->findRepos( false );
            $this->writeLog( 'Update GIT repos: ' . count( $repos ) . ' found' );
        }
    }

    /**
     * Writes a log message to the startup log file.
     *
     * @param   string  $log  The log message to write.
     */
    private function writeLog($log)
    {
        global $bearsamppRoot;
        Util::logDebug( $log, $bearsamppRoot->getStartupLogFilePath() );
    }
}
