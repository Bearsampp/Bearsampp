<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionSwitchOnline
 * Handles the switching of the application between online and offline modes.
 */
class ActionSwitchOnline
{
    /**
     * ActionSwitchOnline constructor.
     * Initializes the online/offline switch based on the provided arguments.
     *
     * @param array $args Arguments to determine the online/offline state.
     */
    public function __construct($args)
    {
        global $bearsamppConfig;

        if (isset($args[0]) && $args[0] == Config::ENABLED || $args[0] == Config::DISABLED) {
            Util::startLoading();
            $putOnline = $args[0] == Config::ENABLED;

            $this->switchApache($putOnline);
            $this->switchAlias($putOnline);
            $this->switchVhosts($putOnline);
            $bearsamppConfig->replace(Config::CFG_ONLINE, $args[0]);
        }
    }

    /**
     * Switches the Apache configuration based on the online/offline state.
     *
     * @param bool $putOnline True to put online, false to put offline.
     */
    private function switchApache($putOnline)
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->refreshConf($putOnline);
    }

    /**
     * Switches the Apache aliases based on the online/offline state.
     *
     * @param bool $putOnline True to put online, false to put offline.
     */
    private function switchAlias($putOnline)
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->refreshAlias($putOnline);
    }

    /**
     * Switches the Apache virtual hosts based on the online/offline state.
     *
     * @param bool $putOnline True to put online, false to put offline.
     */
    private function switchVhosts($putOnline)
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->refreshVhosts($putOnline);
    }
}
