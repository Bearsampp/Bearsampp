<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class ActionEnable
{
    public function __construct($args)
    {
        global $bearsamppBins;

        if (isset($args[0]) && !empty($args[0]) && isset($args[1])) {
            Util::startLoading();
            if ($args[0] == $bearsamppBins->getApache()->getName()) {
                $bearsamppBins->getApache()->setEnable($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getPhp()->getName()) {
                $bearsamppBins->getPhp()->setEnable($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getMysql()->getName()) {
                $bearsamppBins->getMysql()->setEnable($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getMariadb()->getName()) {
                $bearsamppBins->getMariadb()->setEnable($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getNodejs()->getName()) {
                $bearsamppBins->getNodejs()->setEnable($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getPostgresql()->getName()) {
                $bearsamppBins->getPostgresql()->setEnable($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getFilezilla()->getName()) {
                $bearsamppBins->getFilezilla()->setEnable($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getMailhog()->getName()) {
                $bearsamppBins->getMailhog()->setEnable($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getMemcached()->getName()) {
                $bearsamppBins->getMemcached()->setEnable($args[1], true);
            }
        }
    }
}
