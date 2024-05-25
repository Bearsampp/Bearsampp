<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

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
