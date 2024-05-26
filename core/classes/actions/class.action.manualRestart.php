<?php

class ActionManualRestart
{
    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppBins;

        Util::startLoading();

        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $service->delete();
        }

        Win32Ps::killBins(true);

        $bearsamppCore->setExec(ActionExec::RESTART);
        Util::stopLoading();
    }
}
