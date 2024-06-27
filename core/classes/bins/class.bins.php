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

    /**
     * Constructor for the Bins class.
     * Initializes the Bins object and logs the initialization.
     */
    public function __construct()
    {
        Util::logInitClass($this);
    }

    /**
     * Reloads the configuration for all bin modules.
     * Logs the reload action and calls the reload method on each bin module.
     */
    public function reload()
    {
        Util::logInfo('Reload bins');
        foreach ($this->getAll() as $bin) {
            $bin->reload();
        }
    }

    /**
     * Updates the configuration for all bin modules.
     * Logs the update action and calls the update method on each bin module.
     */
    public function update()
    {
        Util::logInfo('Update bins config');
        foreach ($this->getAll() as $bin) {
            $bin->update();
        }
    }

    /**
     * Retrieves all bin modules.
     *
     * @return array An array of all bin modules.
     */
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

    /**
     * Retrieves the Mailhog bin module.
     * If the Mailhog module is not initialized, it creates a new instance.
     *
     * @return BinMailhog The Mailhog bin module.
     */
    public function getMailhog()
    {
        if ($this->mailhog == null) {
            $this->mailhog = new BinMailhog('mailhog', self::TYPE);
        }
        return $this->mailhog;
    }

    /**
     * Retrieves the Mailpit bin module.
     * If the Mailpit module is not initialized, it creates a new instance.
     *
     * @return BinMailpit The Mailpit bin module.
     */
    public function getMailpit()
    {
        if ($this->mailpit == null) {
            $this->mailpit = new BinMailpit('mailpit', self::TYPE);
        }
        return $this->mailpit;
    }

    /**
     * Retrieves the Memcached bin module.
     * If the Memcached module is not initialized, it creates a new instance.
     *
     * @return BinMemcached The Memcached bin module.
     */
    public function getMemcached()
    {
        if ($this->memcached == null) {
            $this->memcached = new BinMemcached('memcached', self::TYPE);
        }
        return $this->memcached;
    }

    /**
     * Retrieves the Apache bin module.
     * If the Apache module is not initialized, it creates a new instance.
     *
     * @return BinApache The Apache bin module.
     */
    public function getApache()
    {
        if ($this->apache == null) {
            $this->apache = new BinApache('apache', self::TYPE);
        }
        return $this->apache;
    }

    /**
     * Retrieves the PHP bin module.
     * If the PHP module is not initialized, it creates a new instance.
     *
     * @return BinPhp The PHP bin module.
     */
    public function getPhp()
    {
        if ($this->php == null) {
            $this->php = new BinPhp('php', self::TYPE);
        }
        return $this->php;
    }

    /**
     * Retrieves the MySQL bin module.
     * If the MySQL module is not initialized, it creates a new instance.
     *
     * @return BinMysql The MySQL bin module.
     */
    public function getMysql()
    {
        if ($this->mysql == null) {
            $this->mysql = new BinMysql('mysql', self::TYPE);
        }
        return $this->mysql;
    }

    /**
     * Retrieves the MariaDB bin module.
     * If the MariaDB module is not initialized, it creates a new instance.
     *
     * @return BinMariadb The MariaDB bin module.
     */
    public function getMariadb()
    {
        if ($this->mariadb == null) {
            $this->mariadb = new BinMariadb('mariadb', self::TYPE);
        }
        return $this->mariadb;
    }

    /**
     * Retrieves the PostgreSQL bin module.
     * If the PostgreSQL module is not initialized, it creates a new instance.
     *
     * @return BinPostgresql The PostgreSQL bin module.
     */
    public function getPostgresql()
    {
        if ($this->postgresql == null) {
            $this->postgresql = new BinPostgresql('postgresql', self::TYPE);
        }
        return $this->postgresql;
    }

    /**
     * Retrieves the Node.js bin module.
     * If the Node.js module is not initialized, it creates a new instance.
     *
     * @return BinNodejs The Node.js bin module.
     */
    public function getNodejs()
    {
        if ($this->nodejs == null) {
            $this->nodejs = new BinNodejs('nodejs', self::TYPE);
        }
        return $this->nodejs;
    }

    /**
     * Retrieves the FileZilla bin module.
     * If the FileZilla module is not initialized, it creates a new instance.
     *
     * @return BinFilezilla The FileZilla bin module.
     */
    public function getFilezilla()
    {
        if ($this->filezilla == null) {
            $this->filezilla = new BinFilezilla('filezilla', self::TYPE);
        }
        return $this->filezilla;
    }

    /**
     * Retrieves the Xlight bin module.
     * If the Xlight module is not initialized, it creates a new instance.
     *
     * @return BinXlight The Xlight bin module.
     */
    public function getXlight()
    {
        if ($this->xlight == null) {
            $this->xlight = new BinXlight('xlight', self::TYPE);
        }
        return $this->xlight;
    }

    /**
     * Retrieves the log paths for all bin modules.
     *
     * @return array An array of log paths for all bin modules.
     */
    public function getLogsPath()
    {
        return array(
            $this->getFilezilla()->getLogsPath(),
        );
    }

    /**
     * Retrieves the services for all enabled bin modules.
     *
     * @return array An associative array of service names and their corresponding service objects.
     */
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
