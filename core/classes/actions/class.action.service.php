<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class ActionService
{
    const CREATE = 'create';
    const START = 'start';
    const STOP = 'stop';
    const RESTART = 'restart';

    const INSTALL = 'install';
    const REMOVE = 'remove';

    public function __construct($args)
    {
        global $bearsamppBins;
        Util::startLoading();

        // reload bins
        $bearsamppBins->reload();

        if (isset($args[0]) && !empty($args[0]) && isset($args[1]) && !empty($args[1])) {
            $sName = $args[0];
            $bin = null;
            $port = 0;
            $syntaxCheckCmd = null;

            if ($sName == BinMailhog::SERVICE_NAME) {
                $bin = $bearsamppBins->getMailhog();
                $port = $bin->getSmtpPort();
            } elseif ($sName == BinMemcached::SERVICE_NAME) {
                $bin = $bearsamppBins->getMemcached();
                $port = $bin->getPort();
            } elseif ($sName == BinApache::SERVICE_NAME) {
                $bin = $bearsamppBins->getApache();
                $port = $bin->getPort();
                $syntaxCheckCmd = BinApache::CMD_SYNTAX_CHECK;
            } elseif ($sName == BinMysql::SERVICE_NAME) {
                $bin = $bearsamppBins->getMysql();
                $port = $bin->getPort();
                $syntaxCheckCmd = BinMysql::CMD_SYNTAX_CHECK;
            } elseif ($sName == BinMariadb::SERVICE_NAME) {
                $bin = $bearsamppBins->getMariadb();
                $port = $bin->getPort();
                $syntaxCheckCmd = BinMariadb::CMD_SYNTAX_CHECK;
            } elseif ($sName == BinPostgresql::SERVICE_NAME) {
                $bin = $bearsamppBins->getPostgresql();
                $port = $bin->getPort();
            } elseif ($sName == BinFilezilla::SERVICE_NAME) {
                $bin = $bearsamppBins->getFilezilla();
                $port = $bin->getPort();
            }

            $name = $bin->getName();
            $service = $bin->getService();

            if (!empty($service) && $service instanceof Win32Service) {
                if ($args[1] == self::CREATE) {
                    $this->create($service);
                } elseif ($args[1] == self::START) {
                    $this->start($bin, $syntaxCheckCmd);
                } elseif ($args[1] == self::STOP) {
                    $this->stop($service);
                } elseif ($args[1] == self::RESTART) {
                    $this->restart($bin, $syntaxCheckCmd);
                } elseif ($args[1] == self::INSTALL) {
                    if (!empty($port)) {
                        $this->install($bin, $port, $syntaxCheckCmd);
                    }
                } elseif ($args[1] == self::REMOVE) {
                    $this->remove($service, $name);
                }
            }
        }

        Util::stopLoading();
    }

    private function create($service)
    {
        $service->create();
    }

    private function start($bin, $syntaxCheckCmd)
    {
        Util::startService($bin, $syntaxCheckCmd, true);
    }

    private function stop($service)
    {
        $service->stop();
    }

    private function restart($bin, $syntaxCheckCmd)
    {
        if ($bin->getService()->stop()) {
            $this->start($bin, $syntaxCheckCmd);
        }
    }

    private function install($bin, $port, $syntaxCheckCmd)
    {
        Util::installService($bin, $port, $syntaxCheckCmd, true);
    }

    private function remove($service, $name)
    {
        Util::removeService($service, $name);
    }
}
