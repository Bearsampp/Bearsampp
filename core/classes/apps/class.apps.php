<?php

class Apps
{
    const TYPE = 'apps';

    private $phpmyadmin;
    private $webgrind;
    private $adminer;
    private $phppgadmin;

    public function __construct()
    {
    }

    public function update()
    {
        Util::logInfo('Update apps config');
        foreach ($this->getAll() as $tool) {
            $tool->update();
        }
    }

    public function getAll()
    {
        return array(
            $this->getAdminer(),
            $this->getPhpmyadmin(),
            $this->getPhppgadmin(),
            $this->getWebgrind()
        );
    }

    public function getAdminer()
    {
        if ($this->adminer == null) {
            $this->adminer = new AppAdminer('adminer', self::TYPE);
        }
        return $this->adminer;
    }

    public function getPhpmyadmin()
    {
        if ($this->phpmyadmin == null) {
            $this->phpmyadmin = new AppPhpmyadmin('phpmyadmin', self::TYPE);
        }
        return $this->phpmyadmin;
    }

    public function getPhppgadmin()
    {
        if ($this->phppgadmin == null) {
            $this->phppgadmin = new AppPhppgadmin('phppgadmin', self::TYPE);
        }
        return $this->phppgadmin;
    }

    public function getWebgrind()
    {
        if ($this->webgrind == null) {
            $this->webgrind = new AppWebgrind('webgrind', self::TYPE);
        }
        return $this->webgrind;
    }

}
