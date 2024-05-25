<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class ActionLoading
{
    /* This controls the progress bar window sizing */
    const WINDOW_WIDTH = 360;
    const WINDOW_HEIGHT = 90;
    const GAUGE = 20;

    private $wbWindow;
    private $wbProgressBar;

    public function __construct($args)
    {
        global $bearsamppCore, $bearsamppLang, $bearsamppWinbinder;

        $bearsamppWinbinder->reset();
        $bearsamppCore->addLoadingPid(Win32Ps::getCurrentPid());

        // Screen infos
        $screenArea = explode(' ', $bearsamppWinbinder->getSystemInfo(WinBinder::SYSINFO_WORKAREA));
        $screenWidth = intval($screenArea[2]);
        $screenHeight = intval($screenArea[3]);
        $xPos = $screenWidth - self::WINDOW_WIDTH;
        $yPos = $screenHeight - self::WINDOW_HEIGHT - 5;

        $this->wbWindow = $bearsamppWinbinder->createWindow(null, ToolDialog, null, $xPos, $yPos, self::WINDOW_WIDTH, self::WINDOW_HEIGHT, WBC_TOP, null);

        $bearsamppWinbinder->createLabel($this->wbWindow, $bearsamppLang->getValue(Lang::LOADING), 42, 2, 295, null, WBC_LEFT);
        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, self::GAUGE, 42, 20, 290, 15);

        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processLoading', 10);
        $bearsamppWinbinder->mainLoop();
    }

    public function incrProgressBar($nb = 1)
    {
        global $bearsamppCore, $bearsamppWinbinder;

        for ($i = 0; $i < $nb; $i++) {
            $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);
            $bearsamppWinbinder->drawImage($this->wbWindow, $bearsamppCore->getResourcesPath() . '/homepage/img/bearsampp.bmp', 4, 2, 32, 32);
        }

        $bearsamppWinbinder->wait();
        $bearsamppWinbinder->wait($this->wbWindow);
    }

    public function processLoading($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppRoot, $bearsamppWinbinder;

        switch ($id) {
            case IDCLOSE:
                Win32Ps::kill(Win32Ps::getCurrentPid());
                break;
        }

        while (true) {
            $bearsamppRoot->removeErrorHandling();
            $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
            usleep(100000);
            for ($i = 0; $i < self::GAUGE; $i++) {
                $this->incrProgressBar();
                usleep(100000);
            }
        }
    }
}
