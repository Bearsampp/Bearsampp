<?php

class ActionQuit
{
    private $splash;

    const GAUGE_PROCESSES = 1;
    const GAUGE_OTHERS = 1;

    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        // Start splash screen
        $this->splash = new Splash();
        $this->splash->init(
            $bearsamppLang->getValue(Lang::QUIT),
            self::GAUGE_PROCESSES * count($bearsamppBins->getServices()) + self::GAUGE_OTHERS,
            sprintf($bearsamppLang->getValue(Lang::EXIT_LEAVING_TEXT), APP_TITLE . ' ' . $bearsamppCore->getAppVersion())
        );

        $bearsamppWinbinder->setHandler($this->splash->getWbWindow(), $this, 'processWindow', 2000);
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppBins, $bearsamppLang, $bearsamppWinbinder;

        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $name = $bearsamppBins->getApache()->getName() . ' ' . $bearsamppBins->getApache()->getVersion();
            if ($sName == BinMysql::SERVICE_NAME) {
                $name = $bearsamppBins->getMysql()->getName() . ' ' . $bearsamppBins->getMysql()->getVersion();
            } elseif ($sName == BinMailhog::SERVICE_NAME) {
                $name = $bearsamppBins->getMailhog()->getName() . ' ' . $bearsamppBins->getMailhog()->getVersion();
            } elseif ($sName == BinMariadb::SERVICE_NAME) {
                $name = $bearsamppBins->getMariadb()->getName() . ' ' . $bearsamppBins->getMariadb()->getVersion();
            } elseif ($sName == BinPostgresql::SERVICE_NAME) {
                $name = $bearsamppBins->getPostgresql()->getName() . ' ' . $bearsamppBins->getPostgresql()->getVersion();
            } elseif ($sName == BinMailhog::SERVICE_NAME) {
                $name = $bearsamppBins->getPostgresql()->getName() . ' ' . $bearsamppBins->getPostgresql()->getVersion();
            } elseif ($sName == BinMemcached::SERVICE_NAME) {
                $name = $bearsamppBins->getMemcached()->getName() . ' ' . $bearsamppBins->getMemcached()->getVersion();
            } elseif ($sName == BinFilezilla::SERVICE_NAME) {
                $name = $bearsamppBins->getFilezilla()->getName() . ' ' . $bearsamppBins->getFilezilla()->getVersion();
            }
            $name .= ' (' . $service->getName() . ')';

            $this->splash->incrProgressBar();
            $this->splash->setTextLoading(sprintf($bearsamppLang->getValue(Lang::EXIT_REMOVE_SERVICE_TEXT), $name));
            $service->delete();
        }

        $this->splash->incrProgressBar();
        $this->splash->setTextLoading($bearsamppLang->getValue(Lang::EXIT_STOP_OTHER_PROCESS_TEXT));
        Win32Ps::killBins(true);

        $bearsamppWinbinder->destroyWindow($window);
    }
}
