<?php
/*
 *
 *  * Copyright (c) 2021-2024 Bearsampp
 *  * License:  GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class ActionSwitchVersion
 * Handles the switching of versions for various services and binaries in the Bearsampp application.
 */
class ActionSwitchVersion
{
    private $bearsamppSplash;
    private $version;
    private $bin;
    private $currentVersion;
    private $service;
    private $changePort;
    private $boxTitle;
    private $pathsToScan = [];

    const GAUGE_SERVICES = 1;
    const GAUGE_OTHERS = 7;
    
    // Configuration sections
    const CONFIG_SECTION_APACHE = 'apache';
    const CONFIG_SECTION_PHP = 'php';
    const CONFIG_SECTION_MYSQL = 'mysql';
    const CONFIG_SECTION_MARIADB = 'mariadb';
    const CONFIG_SECTION_POSTGRESQL = 'postgresql';
    const CONFIG_SECTION_NODEJS = 'nodejs';
    const CONFIG_SECTION_MEMCACHED = 'memcached';
    const CONFIG_SECTION_MAILPIT = 'mailpit';
    const CONFIG_SECTION_XLIGHT = 'xlight';
    
    // Configuration keys
    const CONFIG_KEY_VERSION = 'version';

    /**
     * ActionSwitchVersion constructor.
     * Initializes the class with the provided arguments and sets up the splash screen.
     *
     * @param   array  $args  Command line arguments for switching versions.
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0]) && isset($args[1]) && !empty($args[1])) {
            $this->pathsToScan = array();
            $this->version     = $args[1];

            if ($args[0] == $bearsamppBins->getApache()->getName()) {
                $this->bin            = $bearsamppBins->getApache();
                $this->currentVersion = $bearsamppBins->getApache()->getVersion();
                $this->service        = $bearsamppBins->getApache()->getService();
                $this->changePort     = true;
                $folderList           = Util::getFolderList($bearsamppBins->getApache()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getApache()->getRootPath() . '/' . $folder,
                        'includes'  => array('.ini', '.conf'),
                        'recursive' => true
                    );
                }
            } elseif ($args[0] == $bearsamppBins->getPhp()->getName()) {
                $this->bin            = $bearsamppBins->getPhp();
                $this->currentVersion = $bearsamppBins->getPhp()->getVersion();
                $this->service        = $bearsamppBins->getApache()->getService();
                $this->changePort     = false;
                $folderList           = Util::getFolderList($bearsamppBins->getPhp()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getPhp()->getRootPath() . '/' . $folder,
                        'includes'  => array('.php', '.bat', '.ini', '.reg', '.inc'),
                        'recursive' => true
                    );
                }
            } elseif ($args[0] == $bearsamppBins->getMysql()->getName()) {
                $this->bin            = $bearsamppBins->getMysql();
                $this->currentVersion = $bearsamppBins->getMysql()->getVersion();
                $this->service        = $bearsamppBins->getMysql()->getService();
                $this->changePort     = true;
                $folderList           = Util::getFolderList($bearsamppBins->getMysql()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getMysql()->getRootPath() . '/' . $folder,
                        'includes'  => array('my.ini'),
                        'recursive' => false
                    );
                }
            } elseif ($args[0] == $bearsamppBins->getMariadb()->getName()) {
                $this->bin            = $bearsamppBins->getMariadb();
                $this->currentVersion = $bearsamppBins->getMariadb()->getVersion();
                $this->service        = $bearsamppBins->getMariadb()->getService();
                $this->changePort     = true;
                $folderList           = Util::getFolderList($bearsamppBins->getMariadb()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getMariadb()->getRootPath() . '/' . $folder,
                        'includes'  => array('my.ini'),
                        'recursive' => false
                    );
                }
            } elseif ($args[0] == $bearsamppBins->getPostgresql()->getName()) {
                $this->bin            = $bearsamppBins->getPostgresql();
                $this->currentVersion = $bearsamppBins->getPostgresql()->getVersion();
                $this->service        = $bearsamppBins->getPostgresql()->getService();
                $this->changePort     = true;
                $folderList           = Util::getFolderList($bearsamppBins->getPostgresql()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getPostgresql()->getRootPath() . '/' . $folder,
                        'includes'  => array('.ber', '.conf', '.bat'),
                        'recursive' => true
                    );
                }
            } elseif ($args[0] == $bearsamppBins->getNodejs()->getName()) {
                $this->bin            = $bearsamppBins->getNodejs();
                $this->currentVersion = $bearsamppBins->getNodejs()->getVersion();
                $this->service        = null;
                $this->changePort     = false;
                $folderList           = Util::getFolderList($bearsamppBins->getNodejs()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getNodejs()->getRootPath() . '/' . $folder . '/etc',
                        'includes'  => array('npmrc'),
                        'recursive' => true
                    );
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getNodejs()->getRootPath() . '/' . $folder . '/node_modules/npm',
                        'includes'  => array('npmrc'),
                        'recursive' => false
                    );
                }
            }  elseif ($args[0] == $bearsamppBins->getMemcached()->getName()) {
                $this->bin            = $bearsamppBins->getMemcached();
                $this->currentVersion = $bearsamppBins->getMemcached()->getVersion();
                $this->service        = $bearsamppBins->getMemcached()->getService();
                $this->changePort     = true;
            } elseif ($args[0] == $bearsamppBins->getMailpit()->getName()) {
                $this->bin            = $bearsamppBins->getMailpit();
                $this->currentVersion = $bearsamppBins->getMailpit()->getVersion();
                $this->service        = $bearsamppBins->getMailpit()->getService();
                $this->changePort     = false;
                $folderList           = Util::getFolderList($bearsamppBins->getMailpit()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getMailpit()->getRootPath() . '/' . $folder,
                        'includes'  => array('.conf'),
                        'recursive' => true
                    );
                }
            } elseif ($args[0] == $bearsamppBins->getXlight()->getName()) {
                $this->bin            = $bearsamppBins->getXlight();
                $this->currentVersion = $bearsamppBins->getXlight()->getVersion();
                $this->service        = $bearsamppBins->getXlight()->getService();
                $this->changePort     = true;
                $folderList           = Util::getFolderList($bearsamppBins->getXlight()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getXlight()->getRootPath() . '/' . $folder,
                        'includes'  => array('.conf, ftpd.hosts, ftpd.option, ftpd.password, ftpd.rules, ftpd.users, .ini'),
                        'recursive' => true
                    );
                }
            }

            $this->boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->bin->getName(), $this->version);

            // Start splash screen
            $this->bearsamppSplash = new Splash();
            $this->bearsamppSplash->init(
                $this->boxTitle,
                self::GAUGE_SERVICES * count($bearsamppBins->getServices()) + self::GAUGE_OTHERS,
                $this->boxTitle
            );

            $bearsamppWinbinder->setHandler($this->bearsamppSplash->getWbWindow(), $this, 'processWindow', 1000);
            $bearsamppWinbinder->mainLoop();
            $bearsamppWinbinder->reset();
        }
    }

    /**
     * Processes the window events for the splash screen.
     *
     * @param   mixed  $window  The window handle.
     * @param   int    $id      The event ID.
     * @param   mixed  $ctrl    The control handle.
     * @param   mixed  $param1  The first parameter.
     * @param   mixed  $param2  The second parameter.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if ($this->version == $this->currentVersion) {
            $bearsamppWinbinder->messageBoxWarning(sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_SAME_ERROR), $this->bin->getName(), $this->version), $this->boxTitle);
            $bearsamppWinbinder->destroyWindow($window);
        }

        // scan folder
        $this->bearsamppSplash->incrProgressBar();
        if (!empty($this->pathsToScan)) {
            Util::changePath(Util::getFilesToScan($this->pathsToScan));
        }

        // switch
        $this->bearsamppSplash->incrProgressBar();
        if ($this->bin->switchVersion($this->version, true) === false) {
            $this->bearsamppSplash->incrProgressBar(self::GAUGE_SERVICES * count($bearsamppBins->getServices()) + self::GAUGE_OTHERS);
            $bearsamppWinbinder->destroyWindow($window);
        }

        // stop service
        if ($this->service != null) {
            $binName = $this->bin->getName() == $bearsamppLang->getValue(Lang::PHP) ? $bearsamppLang->getValue(Lang::APACHE) : $this->bin->getName();
            $this->bearsamppSplash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::STOP_SERVICE_TITLE), $binName));
            $this->bearsamppSplash->incrProgressBar();
            $this->service->stop();
        } else {
            $this->bearsamppSplash->incrProgressBar();
        }

        // reload config
        $this->bearsamppSplash->setTextLoading($bearsamppLang->getValue(Lang::SWITCH_VERSION_RELOAD_CONFIG));
        $this->bearsamppSplash->incrProgressBar();
        Root::loadConfig();

        // reload bins
        $this->bearsamppSplash->setTextLoading($bearsamppLang->getValue(Lang::SWITCH_VERSION_RELOAD_BINS));
        $this->bearsamppSplash->incrProgressBar();
        $bearsamppBins->reload();

        // change port
        if ($this->changePort) {
            $this->bin->reload();
            $this->bin->changePort($this->bin->getPort());
        }

        // start service
        if ($this->service != null) {
            $binName = $this->bin->getName() == $bearsamppLang->getValue(Lang::PHP) ? $bearsamppLang->getValue(Lang::APACHE) : $this->bin->getName();
            $this->bearsamppSplash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::START_SERVICE_TITLE), $binName));
            $this->bearsamppSplash->incrProgressBar();
            $this->service->start();
        } else {
            $this->bearsamppSplash->incrProgressBar();
        }

        $this->bearsamppSplash->incrProgressBar(self::GAUGE_SERVICES * count($bearsamppBins->getServices()) + 1);

        // Update configuration file with the new version
        Util::logTrace('Updating ini & menu...');
        $this->updateConfigVersion();

        Util::logTrace('Creating modal...');
        $bearsamppWinbinder->messageBoxInfo(
            sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_OK), $this->bin->getName(), $this->version),
            $this->boxTitle
        );

        Util::logTrace('Destroying modal window...');
        $bearsamppWinbinder->destroyWindow($window);

        // Store current registry value for comparison
        $currentRegValue = Util::getAppBinsRegKey(false);
        $regEntry = Registry::APP_BINS_REG_ENTRY;

        Util::logTrace(sprintf(
            'Starting registry adjustment for key: %s | Current value: %s',
            $regEntry,
            $currentRegValue
        ));

        $this->bearsamppSplash->setTextLoading(sprintf(
            $bearsamppLang->getValue(Lang::SWITCH_VERSION_REGISTRY),
            $regEntry
        ));

        $this->bearsamppSplash->incrProgressBar(2);

        // Perform the registry update
        $newRegValue = Util::setAppBinsRegKey($currentRegValue);
        Util::logTrace(sprintf(
            'Registry update completed | Key: %s | New value: %s | Previous value: %s',
            $regEntry,
            $newRegValue,
            $currentRegValue
        ));

        Util::logTrace(sprintf(
            'Resetting services: %s',
            $bearsamppLang->getValue(Lang::SWITCH_VERSION_RESET_SERVICES)
        ));
        
        $this->bearsamppSplash->setTextLoading($bearsamppLang->getValue(Lang::SWITCH_VERSION_RESET_SERVICES));
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            Util::logTrace(sprintf('Deleting service: %s', $sName));
            $this->bearsamppSplash->incrProgressBar();
            $service->delete();
            Util::logTrace(sprintf('Service deleted: %s', $sName));
        }
        Util::logTrace('All services reset completed');

        $bearsamppWinbinder->messageBoxInfo(
            sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_OK_RESTART), $this->bin->getName(), $this->version, APP_TITLE),
            $this->boxTitle
        );

        Util::logTrace('Running seExec line 316..');
        $bearsamppCore->setExec(ActionExec::RESTART);

        Util::logTrace('Destroying final window...');
        $bearsamppWinbinder->destroyWindow($window);
    }
    
    /**
     * Updates the configuration file with the new version of the binary
     * This ensures version persistence across restarts
     */
    private function updateConfigVersion(): void
    {
        $bearsamppConfig = new Config();
        $configSection = '';
        $version = $this->version; // Ensure version is available in scope
        
        // Determine the correct configuration section based on binary type
        if ($this->bin->getName() == $GLOBALS['bearsamppBins']->getApache()->getName()) {
            $configSection = self::CONFIG_SECTION_APACHE;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        } elseif ($this->bin->getName() == $GLOBALS['bearsamppBins']->getPhp()->getName()) {
            $configSection = self::CONFIG_SECTION_PHP;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        } elseif ($this->bin->getName() == $GLOBALS['bearsamppBins']->getMysql()->getName()) {
            $configSection = self::CONFIG_SECTION_MYSQL;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        } elseif ($this->bin->getName() == $GLOBALS['bearsamppBins']->getMariadb()->getName()) {
            $configSection = self::CONFIG_SECTION_MARIADB;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        } elseif ($this->bin->getName() == $GLOBALS['bearsamppBins']->getPostgresql()->getName()) {
            $configSection = self::CONFIG_SECTION_POSTGRESQL;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        } elseif ($this->bin->getName() == $GLOBALS['bearsamppBins']->getNodejs()->getName()) {
            $configSection = self::CONFIG_SECTION_NODEJS;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        } elseif ($this->bin->getName() == $GLOBALS['bearsamppBins']->getMemcached()->getName()) {
            $configSection = self::CONFIG_SECTION_MEMCACHED;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        } elseif ($this->bin->getName() == $GLOBALS['bearsamppBins']->getMailpit()->getName()) {
            $configSection = self::CONFIG_SECTION_MAILPIT;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        } elseif ($this->bin->getName() == $GLOBALS['bearsamppBins']->getXlight()->getName()) {
            $configSection = self::CONFIG_SECTION_XLIGHT;
            Util::logTrace(sprintf('Switch %s version to %s', $configSection, $version));
        }
        
        // Update the configuration if a valid section was found
        if (!empty($configSection)) {
            Util::logTrace('Updating .ini file...');
            $bearsamppConfig->replace($configSection, self::CONFIG_KEY_VERSION, $version);

            // Update tray menu display if TrayMenu class is available
            Util::logTrace('Updating TrayMenu...');
            if (class_exists('TrayMenu')) {
                $trayMenu = TrayMenu::getInstance();
                if (method_exists($trayMenu, 'updateSectionVersion')) {
                    $trayMenu->updateSectionVersion(
                        strtoupper($configSection), 
                        $version
                    );
                }
            }
        }
        Util::logTrace('Returning to parent call');
    }
}
