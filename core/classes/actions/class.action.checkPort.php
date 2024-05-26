<?php

class ActionCheckPort
{
    public function __construct($args)
    {
        global $bearsamppBins;

        if (isset($args[0]) && !empty($args[0]) && isset($args[1]) && !empty($args[1])) {
            $ssl = isset($args[2]) && !empty($args[2]);
            if ($args[0] == $bearsamppBins->getApache()->getName()) {
                $bearsamppBins->getApache()->checkPort($args[1], $ssl, true);
            } elseif ($args[0] == $bearsamppBins->getMysql()->getName()) {
                $bearsamppBins->getMysql()->checkPort($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getMariadb()->getName()) {
                $bearsamppBins->getMariadb()->checkPort($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getPostgresql()->getName()) {
                $bearsamppBins->getPostgresql()->checkPort($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getFilezilla()->getName()) {
                $bearsamppBins->getFilezilla()->checkPort($args[1], $ssl, true);
            } elseif ($args[0] == $bearsamppBins->getMailhog()->getName()) {
                $bearsamppBins->getMailhog()->checkPort($args[1], true);
            } elseif ($args[0] == $bearsamppBins->getMemcached()->getName()) {
                $bearsamppBins->getMemcached()->checkPort($args[1], true);
            }
        }
    }
}
