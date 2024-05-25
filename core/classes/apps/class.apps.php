<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Manages the applications within the Bearsampp environment.
 */
class Apps
{
    /**
     * Constant to define the type of applications.
     */
    const TYPE = 'apps';

    /**
     * @var AppPhpmyadmin|null Holds the instance of the PhpMyAdmin application.
     */
    private $phpmyadmin;

    /**
     * @var AppWebgrind|null Holds the instance of the Webgrind application.
     */
    private $webgrind;

    /**
     * @var AppAdminer|null Holds the instance of the Adminer application.
     */
    private $adminer;

    /**
     * @var AppPhppgadmin|null Holds the instance of the PhpPgAdmin application.
     */
    private $phppgadmin;

    /**
     * Constructor for the Apps class.
     */
    public function __construct()
    {
    }

    /**
     * Updates the configuration for all managed applications.
     */
    public function update()
    {
        Util::logInfo('Update apps config');
        foreach ($this->getAll() as $tool) {
            $tool->update();
        }
    }

    /**
     * Retrieves all application instances as an array.
     *
     * @return array An array of application instances.
     */
    public function getAll()
    {
        return array(
            $this->getAdminer(),
            $this->getPhpmyadmin(),
            $this->getPhppgadmin(),
            $this->getWebgrind()
        );
    }

    /**
     * Gets the Adminer application instance, creating it if it does not exist.
     *
     * @return AppAdminer The Adminer application instance.
     */
    public function getAdminer()
    {
        if ($this->adminer == null) {
            $this->adminer = new AppAdminer('adminer', self::TYPE);
        }
        return $this->adminer;
    }

    /**
     * Gets the PhpMyAdmin application instance, creating it if it does not exist.
     *
     * @return AppPhpmyadmin The PhpMyAdmin application instance.
     */
    public function getPhpmyadmin()
    {
        if ($this->phpmyadmin == null) {
            $this->phpmyadmin = new AppPhpmyadmin('phpmyadmin', self::TYPE);
        }
        return $this->phpmyadmin;
    }

    /**
     * Gets the PhpPgAdmin application instance, creating it if it does not exist.
     *
     * @return AppPhppgadmin The PhpPgAdmin application instance.
     */
    public function getPhppgadmin()
    {
        if ($this->phppgadmin == null) {
            $this->phppgadmin = new AppPhppgadmin('phppgadmin', self::TYPE);
        }
        return $this->phppgadmin;
    }

    /**
     * Gets the Webgrind application instance, creating it if it does not exist.
     *
     * @return AppWebgrind The Webgrind application instance.
     */
    public function getWebgrind()
    {
        if ($this->webgrind == null) {
            $this->webgrind = new AppWebgrind('webgrind', self::TYPE);
        }
        return $this->webgrind;
    }
}
