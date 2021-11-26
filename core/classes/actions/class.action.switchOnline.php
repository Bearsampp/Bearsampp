<?php

class ActionSwitchOnline
{
    public function __construct($args)
    {
        global $bearsamppConfig;

        if (isset($args[0]) && $args[0] == Config::ENABLED || $args[0] == Config::DISABLED) {
            Util::startLoading();
            $putOnline = $args[0] == Config::ENABLED;

            $this->switchApache($putOnline);
            $this->switchAlias($putOnline);
            $this->switchVhosts($putOnline);
            $this->switchFilezilla($putOnline);
            $bearsamppConfig->replace(Config::CFG_ONLINE, $args[0]);
        }
    }

    private function switchApache($putOnline)
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->refreshConf($putOnline);
    }

    private function switchAlias($putOnline)
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->refreshAlias($putOnline);
    }

    private function switchVhosts($putOnline)
    {
        global $bearsamppBins;
        $bearsamppBins->getApache()->refreshVhosts($putOnline);
    }

    private function switchFilezilla($putOnline)
    {
        global $bearsamppBins;

        if ($putOnline) {
            $bearsamppBins->getFilezilla()->setConf(array(
                BinFilezilla::CFG_IP_FILTER_ALLOWED => '*',
                BinFilezilla::CFG_IP_FILTER_DISALLOWED => '',
            ));
        } else {
            $bearsamppBins->getFilezilla()->setConf(array(
                BinFilezilla::CFG_IP_FILTER_ALLOWED => '127.0.0.1 ::1',
                BinFilezilla::CFG_IP_FILTER_DISALLOWED => '*',
            ));
        }
    }
}
