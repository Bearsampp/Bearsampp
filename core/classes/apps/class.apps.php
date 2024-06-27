<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Apps
 *
 * This class manages various application modules within the Bearsampp application.
 * It provides methods to retrieve and update configurations for different tools such as Adminer, PhpMyAdmin, PhpPgAdmin, and Webgrind.
 */
class Apps
{
    /**
     * The type of the module.
     */
    const TYPE = 'apps';

    /**
     * @var AppPhpmyadmin|null Instance of the PhpMyAdmin module.
     */
    private $phpmyadmin;

    /**
     * @var AppWebgrind|null Instance of the Webgrind module.
     */
    private $webgrind;

    /**
     * @var AppAdminer|null Instance of the Adminer module.
     */
    private $adminer;

    /**
     * @var AppPhppgadmin|null Instance of the PhpPgAdmin module.
     */
    private $phppgadmin;

    /**
     * Constructor for the Apps class.
     */
    public function __construct()
    {
    }

    /**
     * Updates the configuration for all application modules.
     *
     * This method logs the update process and iterates through all the tools to update their configurations.
     */
    public function update()
    {
        Util::logInfo('Update apps config');
        foreach ($this->getAll() as $tool) {
            $tool->update();
        }
    }

    /**
     * Retrieves all application modules.
     *
     * @return array An array containing instances of all application modules.
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
     * Retrieves the Adminer module instance.
     *
     * If the instance is not already created, it initializes a new AppAdminer object.
     *
     * @return AppAdminer The instance of the Adminer module.
     */
    public function getAdminer()
    {
        if ($this->adminer == null) {
            $this->adminer = new AppAdminer('adminer', self::TYPE);
        }
        return $this->adminer;
    }

    /**
     * Retrieves the PhpMyAdmin module instance.
     *
     * If the instance is not already created, it initializes a new AppPhpmyadmin object.
     *
     * @return AppPhpmyadmin The instance of the PhpMyAdmin module.
     */
    public function getPhpmyadmin()
    {
        if ($this->phpmyadmin == null) {
            $this->phpmyadmin = new AppPhpmyadmin('phpmyadmin', self::TYPE);
        }
        return $this->phpmyadmin;
    }

    /**
     * Retrieves the PhpPgAdmin module instance.
     *
     * If the instance is not already created, it initializes a new AppPhppgadmin object.
     *
     * @return AppPhppgadmin The instance of the PhpPgAdmin module.
     */
    public function getPhppgadmin()
    {
        if ($this->phppgadmin == null) {
            $this->phppgadmin = new AppPhppgadmin('phppgadmin', self::TYPE);
        }
        return $this->phppgadmin;
    }

    /**
     * Retrieves the Webgrind module instance.
     *
     * If the instance is not already created, it initializes a new AppWebgrind object.
     *
     * @return AppWebgrind The instance of the Webgrind module.
     */
    public function getWebgrind()
    {
        if ($this->webgrind == null) {
            $this->webgrind = new AppWebgrind('webgrind', self::TYPE);
        }
        return $this->webgrind;
    }
}
