<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Bins manages various service binaries like Mailhog, Memcached, Apache, etc.
 * It provides methods to reload and update configurations for these services.
 */
class Bins
{
    /**
     * Constant to define the type of bins.
     */
    const TYPE = 'bins';

    /**
     * @var BinMailhog|null Instance of Mailhog binary handler.
     */
    private $mailhog;

    /**
     * @var BinMemcached|null Instance of Memcached binary handler.
     */
    private $memcached;

    /**
     * @var BinApache|null Instance of Apache binary handler.
     */
    private $apache;

    /**
     * @var BinPhp|null Instance of PHP binary handler.
     */
    private $php;

    /**
     * @var BinMysql|null Instance of MySQL binary handler.
     */
    private $mysql;

    /**
     * @var BinMariadb|null Instance of MariaDB binary handler.
     */
    private $mariadb;

    /**
     * @var BinPostgresql|null Instance of PostgreSQL binary handler.
     */
    private $postgresql;

    /**
     * @var BinNodejs|null Instance of Node.js binary handler.
     */
    private $nodejs;

    /**
     * @var BinFilezilla|null Instance of Filezilla binary handler.
     */
    private $filezilla;

    /**
     * Constructor for Bins class.
     * Initializes the class and logs the initialization.
     */
    public function __construct()
    {
        Util::logInitClass($this);
    }

    /**
     * Reloads all service configurations.
     * Iterates through all service handlers and triggers their reload method.
     */
    public function reload()
    {
        Util::logInfo('Reload bins');
        foreach ($this->getAll() as $bin) {
            $bin->reload();
        }
    }

    /**
     * Updates all service configurations.
     * Iterates through all service handlers and triggers their update method.
     */
    public function update()
    {
        Util::logInfo('Update bins config');
        foreach ($this->getAll() as $bin) {
            $bin->update();
        }
    }

    /**
     * Retrieves all service handlers as an array.
     *
     * @return array An array of all service handlers.
     */
    public function getAll()
    {
        return array(
            $this->getMailhog(),
            $this->getMemcached(),
            $this->getApache(),
            $this->getFilezilla(),
            $this->getMariadb(),
            $this->getPostgresql(),
            $this->getMysql(),
            $this->getPhp(),
            $this->getNodejs(),
        );
    }
    /**
     * Retrieves the Mailhog binary handler instance.
     * If the instance does not exist, it creates a new instance of BinMailhog.
     *
     * @return BinMailhog The Mailhog binary handler instance.
     */
    public function getMailhog()
    {
        if ($this->mailhog == null) {
            $this->mailhog = new BinMailhog('mailhog', self::TYPE);
        }
        return $this->mailhog;
    }

    /**
     * Retrieves the Memcached binary handler instance.
     * If the instance does not exist, it creates a new instance of BinMemcached.
     *
     * @return BinMemcached The Memcached binary handler instance.
     */
    public function getMemcached()
    {
        if ($this->memcached == null) {
            $this->memcached = new BinMemcached('memcached', self::TYPE);
        }
        return $this->memcached;
    }

    /**
     * Retrieves the Apache binary handler instance.
     * If the instance does not exist, it creates a new instance of BinApache.
     *
     * @return BinApache The Apache binary handler instance.
     */
    public function getApache()
    {
        if ($this->apache == null) {
            $this->apache = new BinApache('apache', self::TYPE);
        }
        return $this->apache;
    }

    /**
     * Retrieves the PHP binary handler instance.
     * If the instance does not exist, it creates a new instance of BinPhp.
     *
     * @return BinPhp The PHP binary handler instance.
     */
    public function getPhp()
    {
        if ($this->php == null) {
            $this->php = new BinPhp('php', self::TYPE);
        }
        return $this->php;
    }

    /**
     * Retrieves the MySQL binary handler instance.
     * If the instance does not exist, it creates a new instance of BinMysql.
     *
     * @return BinMysql The MySQL binary handler instance.
     */
    public function getMysql()
    {
        if ($this->mysql == null) {
            $this->mysql = new BinMysql('mysql', self::TYPE);
        }
        return $this->mysql;
    }

    /**
     * Retrieves the MariaDB binary handler instance.
     * If the instance does not exist, it creates a new instance of BinMariadb.
     *
     * @return BinMariadb The MariaDB binary handler instance.
     */
    public function getMariadb()
    {
        if ($this->mariadb == null) {
            $this->mariadb = new BinMariadb('mariadb', self::TYPE);
        }
        return $this->mariadb;
    }

    /**
     * Retrieves the PostgreSQL binary handler instance.
     * If the instance does not exist, it creates a new instance of BinPostgresql.
     *
     * @return BinPostgresql The PostgreSQL binary handler instance.
     */
    public function getPostgresql()
    {
        if ($this->postgresql == null) {
            $this->postgresql = new BinPostgresql('postgresql', self::TYPE);
        }
        return $this->postgresql;
    }

    /**
     * Retrieves the Node.js binary handler instance.
     * If the instance does not exist, it creates a new instance of BinNodejs.
     *
     * @return BinNodejs The Node.js binary handler instance.
     */
    public function getNodejs()
    {
        if ($this->nodejs == null) {
            $this->nodejs = new BinNodejs('nodejs', self::TYPE);
        }
        return $this->nodejs;
    }

    /**
     * Retrieves the Filezilla binary handler instance.
     * If the instance does not exist, it creates a new instance of BinFilezilla.
     *
     * @return BinFilezilla The Filezilla binary handler instance.
     */
    public function getFilezilla()
    {
        if ($this->filezilla == null) {
            $this->filezilla = new BinFilezilla('filezilla', self::TYPE);
        }
        return $this->filezilla;
    }

    /**
     * Retrieves the logs path for Filezilla.
     *
     * @return array An array containing the logs path for Filezilla.
     */
    public function getLogsPath()
    {
        return array(
            $this->getFilezilla()->getLogsPath(),
        );
    }

    /**
     * Retrieves a list of all enabled services along with their respective service handlers.
     *
     * @return array An associative array where keys are service names and values are service instances.
     */
    public function getServices()
    {
        $result = array();

        if ($this->getMailhog()->isEnable()) {
            $result[BinMailhog::SERVICE_NAME] = $this->getMailhog()->getService();
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

        return $result;
    }
}
