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

    const GAUGE_SERVICES = 1;
    const GAUGE_OTHERS = 7;

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
            } elseif ($args[0] == $bearsamppBins->getFilezilla()->getName()) {
                $this->bin            = $bearsamppBins->getFilezilla();
                $this->currentVersion = $bearsamppBins->getFilezilla()->getVersion();
                $this->service        = $bearsamppBins->getFilezilla()->getService();
                $this->changePort     = true;
                $folderList           = Util::getFolderList($bearsamppBins->getFilezilla()->getRootPath());
                foreach ($folderList as $folder) {
                    $this->pathsToScan[] = array(
                        'path'      => $bearsamppBins->getFilezilla()->getRootPath() . '/' . $folder,
                        'includes'  => array('.xml'),
                        'recursive' => true
                    );
                }
            } elseif ($args[0] == $bearsamppBins->getMemcached()->getName()) {
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
        $bearsamppWinbinder->messageBoxInfo(
            sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_OK), $this->bin->getName(), $this->version),
            $this->boxTitle
        );
        $bearsamppWinbinder->destroyWindow($window);

        $this->bearsamppSplash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_REGISTRY), Registry::APP_BINS_REG_ENTRY));
        $this->bearsamppSplash->incrProgressBar(2);
        Util::setAppBinsRegKey(Util::getAppBinsRegKey(false));

        $this->bearsamppSplash->setTextLoading($bearsamppLang->getValue(Lang::SWITCH_VERSION_RESET_SERVICES));
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $this->bearsamppSplash->incrProgressBar();
            $service->delete();
        }

        $bearsamppWinbinder->messageBoxInfo(
            sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_OK_RESTART), $this->bin->getName(), $this->version, APP_TITLE),
            $this->boxTitle
        );

        $bearsamppCore->setExec(ActionExec::RESTART);

        $bearsamppWinbinder->destroyWindow($window);
    }
}
