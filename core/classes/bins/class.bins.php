<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class Bins
{
    const TYPE = 'bins';

    private $mailhog;
    private $mailpit;
    private $memcached;
    private $apache;
    private $php;
    private $mysql;
    private $mariadb;
    private $postgresql;
    private $nodejs;
    private $filezilla;
    private $xlight;

    public function __construct()
    {
        Util::logInitClass($this);
    }

    public function reload()
    {
        Util::logInfo('Reload bins');
        foreach ($this->getAll() as $bin) {
            $bin->reload();
        }
    }

    public function update()
    {
        Util::logInfo('Update bins config');
        foreach ($this->getAll() as $bin) {
            $bin->update();
        }
    }

    public function getAll()
    {
        return array(
            $this->getMailhog(),
            $this->getMailpit(),
            $this->getMemcached(),
            $this->getApache(),
            $this->getFilezilla(),
            $this->getMariadb(),
            $this->getPostgresql(),
            $this->getMysql(),
            $this->getPhp(),
            $this->getNodejs(),
            $this->getXlight(),
        );
    }

    public function getMailhog()
    {
        if ($this->mailhog == null) {
            $this->mailhog = new BinMailhog('mailhog', self::TYPE);
        }
        return $this->mailhog;
    }

    public function getMailpit()
    {
        if ($this->mailpit == null) {
            $this->mailpit = new BinMailpit('mailpit', self::TYPE);
        }
        return $this->mailpit;
    }

    public function getMemcached()
    {
        if ($this->memcached == null) {
            $this->memcached = new BinMemcached('memcached', self::TYPE);
        }
        return $this->memcached;
    }

    public function getApache()
    {
        if ($this->apache == null) {
            $this->apache = new BinApache('apache', self::TYPE);
        }
        return $this->apache;
    }

    public function getPhp()
    {
        if ($this->php == null) {
            $this->php = new BinPhp('php', self::TYPE);
        }
        return $this->php;
    }

    public function getMysql()
    {
        if ($this->mysql == null) {
            $this->mysql = new BinMysql('mysql', self::TYPE);
        }
        return $this->mysql;
    }

    public function getMariadb()
    {
        if ($this->mariadb == null) {
            $this->mariadb = new BinMariadb('mariadb', self::TYPE);
        }
        return $this->mariadb;
    }

    public function getPostgresql()
    {
        if ($this->postgresql == null) {
            $this->postgresql = new BinPostgresql('postgresql', self::TYPE);
        }
        return $this->postgresql;
    }

    public function getNodejs()
    {
        if ($this->nodejs == null) {
            $this->nodejs = new BinNodejs('nodejs', self::TYPE);
        }
        return $this->nodejs;
    }

    public function getFilezilla()
    {
        if ($this->filezilla == null) {
            $this->filezilla = new BinFilezilla('filezilla', self::TYPE);
        }
        return $this->filezilla;
    }

    public function getXlight()
    {
        if ($this->xlight == null) {
            $this->xlight = new BinXlight('xlight', self::TYPE);
        }
        return $this->xlight;
    }

    public function getLogsPath()
    {
        return array(
            $this->getFilezilla()->getLogsPath(),
        );
    }

    public function getServices()
    {
        $result = array();

        if ($this->getMailhog()->isEnable()) {
            $result[BinMailhog::SERVICE_NAME] = $this->getMailhog()->getService();
        }
        if ($this->getMailpit()->isEnable()) {
            $result[BinMailpit::SERVICE_NAME] = $this->getMailpit()->getService();
        }
        if ($this->getMemcached()->isEnable()) {
            $result[BinMemcached::SERVICE_NAME] = $this->getMemcached()->getService();
        }
        if ($this->getApache()->isEnable()) {
            $result[BinApache::SERVICE_NAME] = $this->getApache()->getService();
        }
        if ($this->getMysql()->isEnable()) {
            $result[BinMysql::SERVICE_NAME] = $this->getMysql()->getService();
        }
        if ($this->getMariadb()->isEnable()) {
            $result[BinMariadb::SERVICE_NAME] = $this->getMariadb()->getService();
        }
        if ($this->getPostgresql()->isEnable()) {
            $result[BinPostgresql::SERVICE_NAME] = $this->getPostgresql()->getService();
        }
        if ($this->getFilezilla()->isEnable()) {
            $result[BinFilezilla::SERVICE_NAME] = $this->getFilezilla()->getService();
        }
        if ($this->getXlight()->isEnable()) {
            $result[BinXlight::SERVICE_NAME] = $this->getXlight()->getService();
        }

        return $result;
    }
}
