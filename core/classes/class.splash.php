<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class Splash
 *
 * This class represents a splash screen with a progress bar for the Bearsampp application.
 * It provides methods to initialize the splash screen, set loading text, and increment the progress bar.
 */
class Splash
{
    /* Set progress bar "loading" modal size. */
    const WINDOW_WIDTH = 460;
    const WINDOW_HEIGHT = 90;

    private $wbWindow;
    private $wbImage;
    private $wbTextLoading;
    private $wbProgressBar;

    private $currentImg;

    /**
     * Splash constructor.
     *
     * Initializes the Splash class and sets the current image to null.
     */
    public function __construct()
    {
        Util::logInitClass($this);
        $this->currentImg = null;
    }

    /**
     * Initializes the splash screen.
     *
     * @param string $title The title of the splash screen window.
     * @param int $gauge The maximum value for the progress bar.
     * @param string $text The initial loading text to display.
     */
    public function init($title, $gauge, $text)
    {
        global $bearsamppCore, $bearsamppWinbinder;

        $bearsamppWinbinder->reset();

        $screenArea = explode(' ', $bearsamppWinbinder->getSystemInfo(WinBinder::SYSINFO_WORKAREA));
        $screenWidth = intval($screenArea[2]);
        $screenHeight = intval($screenArea[3]);
        $xPos = $screenWidth - self::WINDOW_WIDTH;
        $yPos = $screenHeight - self::WINDOW_HEIGHT - 5;

        $this->wbWindow = $bearsamppWinbinder->createWindow(null, ToolDialog, $title, $xPos, $yPos, self::WINDOW_WIDTH, self::WINDOW_HEIGHT, WBC_TOP | WBC_READONLY, null);
        $this->wbImage = $bearsamppWinbinder->drawImage($this->wbWindow, $bearsamppCore->getResourcesPath() . '/homepage/img/bearsampp.bmp');
        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar($this->wbWindow, $gauge + 1, 42, 24, 390, 15);

        $this->setTextLoading($text);
        $this->incrProgressBar();
    }

    /**
     * Sets the loading text on the splash screen.
     *
     * @param string $caption The loading text to display.
     */
    public function setTextLoading($caption)
    {
        global $bearsamppWinbinder;

        $bearsamppWinbinder->drawRect($this->wbWindow, 42, 0, self::WINDOW_WIDTH - 42, self::WINDOW_HEIGHT);
        $this->wbTextLoading = $bearsamppWinbinder->drawText($this->wbWindow, $caption, 42, 0, self::WINDOW_WIDTH - 44, 25);
    }

    /**
     * Increments the progress bar by a specified number of steps.
     *
     * @param int $nb The number of steps to increment the progress bar by. Default is 1.
     */
    public function incrProgressBar($nb = 1)
    {
        global $bearsamppCore, $bearsamppWinbinder;

        for ($i = 0; $i < $nb; $i++) {
            $bearsamppWinbinder->drawImage($this->wbWindow, $bearsamppCore->getResourcesPath() . '/homepage/img/bearsampp.bmp', 4, 4, 32, 32);
            $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);
        }

        $bearsamppWinbinder->wait();
        $bearsamppWinbinder->wait($this->wbWindow);
    }

    /**
     * Retrieves the window object of the splash screen.
     *
     * @return mixed The window object of the splash screen.
     */
    public function getWbWindow()
    {
        return $this->wbWindow;
    }
}
