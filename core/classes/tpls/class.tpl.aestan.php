<?php
/*
 *
 *  * Copyright (c) 2021-2024 Bearsampp
 *  * License:  GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class TplAestan
 *
 * This class provides various methods to generate configuration strings for the Bearsampp application.
 * It includes methods to create items for PowerShell, links, Notepad, executables, and explorer actions.
 * Additionally, it handles service actions such as start, stop, and restart, and generates configuration
 * sections for messages, config, and menu settings.
 *
 * Constants:
 * - Glyph constants for various icons used in the application.
 * - Service actions for starting, stopping, and restarting services.
 * - Image files used in the application.
 *
 * Methods:
 * - getGlyphFlah($lang): Retrieves the glyph flag for a given language.
 * - getItemSeparator(): Returns a string representing a separator item.
 * - getItemPowerShell($caption, $glyph, $id, $title, $initDir, $command): Returns a string representing a PowerShell item.
 * - getItemLink($caption, $link, $local, $glyph): Returns a string representing a link item.
 * - getItemNotepad($caption, $path): Returns a string representing a Notepad item.
 * - getItemExe($caption, $exe, $glyph, $params): Returns a string representing an executable item.
 * - getItemExplore($caption, $path): Returns a string representing an explorer item.
 * - getActionService($service, $action, $item): Returns a string representing a service action.
 * - getActionServiceStart($service): Returns a string representing a service start action.
 * - getItemActionServiceStart($service): Returns a string representing a service start item.
 * - getActionServiceStop($service): Returns a string representing a service stop action.
 * - getItemActionServiceStop($service): Returns a string representing a service stop item.
 * - getActionServiceRestart($service): Returns a string representing a service restart action.
 * - getItemActionServiceRestart($service): Returns a string representing a service restart item.
 * - getActionServicesClose(): Returns a string representing a close services action.
 * - getItemActionServicesClose(): Returns a string representing a close services item.
 * - getSectionMessages(): Returns a string representing the messages section.
 * - getSectionConfig(): Returns a string representing the config section.
 * - getSectionMenuRightSettings(): Returns a string representing the right menu settings section.
 * - getSectionMenuLeftSettings($caption): Returns a string representing the left menu settings section.
 */
class TplAestan
{
    // Glyph constants
    const GLYPH_POWERSHELL = 0;
    const GLYPH_ADD = 1;
    const GLYPH_FOLDER_OPEN = 2;
    const GLYPH_FOLDER_CLOSE = 3;
    const GLYPH_BROWSER = 5;
    const GLYPH_FILE = 6;
    const GLYPH_SERVICE_REMOVE = 7;
    const GLYPH_SERVICE_INSTALL = 8;
    const GLYPH_START = 9;
    const GLYPH_PAUSE = 10;
    const GLYPH_STOP = 11;
    const GLYPH_RELOAD = 12;
    const GLYPH_CHECK = 13;
    const GLYPH_SERVICE_ALL_RUNNING = 16;
    const GLYPH_SERVICE_SOME_RUNNING = 17;
    const GLYPH_SERVICE_NONE_RUNNING = 18;
    const GLYPH_WARNING = 19;
    const GLYPH_EXIT = 20;
    const GLYPH_ABOUT = 21;
    const GLYPH_SERVICES_RESTART = 22;
    const GLYPH_SERVICES_STOP = 23;
    const GLYPH_SERVICES_START = 24;
    const GLYPH_LIGHT = 25;
    const GLYPH_GIT = 26;
    const GLYPH_NODEJS = 28;
    const GLYPH_NETWORK = 29;
    const GLYPH_WEB_PAGE = 30;
    const GLYPH_DEBUG = 31;
    const GLYPH_TRASHCAN = 32;
    const GLYPH_UPDATE = 33;
    const GLYPH_RESTART = 34;
    const GLYPH_SSL_CERTIFICATE = 35;
    const GLYPH_RED_LIGHT = 36;
    const GLYPH_COMPOSER = 37;
    const GLYPH_PEAR = 38;
    const GLYPH_HOSTSEDITOR = 39;
    const GLYPH_IMAGEMAGICK = 41;
    const GLYPH_NOTEPAD2 = 42;
    const GLYPH_PASSWORD = 45;
    const GLYPH_FOLDER_DISABLED = 48;
    const GLYPH_FOLDER_ENABLED = 49;
    const GLYPH_PYTHON = 50;
    const GLYPH_RUBY = 52;
    const GLYPH_PERL = 55;
    const GLYPH_GHOSTSCRIPT = 56;
    const GLYPH_NGROK = 57;
    const GLYPH_PWGEN = 58;
    const GLYPH_XLIGHT = 59;
    const GLYPH_REBUILD_INI = 60;
    const GLYPH_BRUNO = 61;

    // Service actions
    const SERVICE_START = 'startresume';
    const SERVICE_STOP = 'stop';
    const SERVICE_RESTART = 'restart';
    const SERVICES_CLOSE = 'closeservices';

    // Image files
    const IMG_BAR_PICTURE = 'bar.dat';
    const IMG_GLYPH_SPRITES = 'sprites.dat';

    /**
     * Retrieves the glyph flag for a given language.
     *
     * @param string $lang The language code.
     * @return void
     */
    public static function getGlyphFlah($lang)
    {
    }

    /**
     * Returns a string representing a separator item.
     *
     * @return string The separator item string.
     */
    public static function getItemSeparator()
    {
        return 'Type: separator';
    }

    /**
     * Returns a string representing a PowerShell item.
     *
     * @param string $caption The caption for the item.
     * @param int $glyph The glyph index.
     * @param string|null $id The ID for the item (not used with PowerShell).
     * @param string|null $title The title for the tab.
     * @param string|null $initDir The initial directory for the item.
     * @param string|null $command The command to execute.
     * @return string The PowerShell item string.
     */
    public static function getItemPowerShell($caption, $glyph, $id = null, $title = null, $initDir = null, $command = null)
    {
        global $bearsamppTools;

        // PowerShell uses the launch batch file which handles the command execution
        // We use Windows Terminal command-line arguments:
        // --title or -w for window title
        // --startingDirectory or -d for starting directory
        $launchExe = $bearsamppTools->getPowerShell()->getLaunchExe();
        
        // Build the parameters using Windows Terminal switches
        $params = '';
        
        // Add title if provided (using -w for compatibility)
        if ($title != null) {
            $params .= '--title ""' . $title . '""';
        }
        
        // Add starting directory if provided (using -d for compatibility)
        if ($initDir != null) {
            if (!empty($params)) $params .= ' ';
            $params .= '--startingDirectory ""' . $initDir . '""';
        }
        
        // Add command to execute if provided
        if ($command != null) {
            if (!empty($params)) $params .= ' ';
            $params .= '--command ""' . $command . '""';
        }

        return self::getItemExe(
            $caption,
            $launchExe,
            $glyph,
            $params
        );
    }

    /**
     * Returns a string representing a link item.
     *
     * @param string $caption The caption for the item.
     * @param string $link The URL for the link.
     * @param bool $local Whether the link is local.
     * @param int $glyph The glyph index.
     * @return string The link item string.
     */
    public static function getItemLink($caption, $link, $local = false, $glyph = self::GLYPH_WEB_PAGE)
    {
        global $bearsamppRoot, $bearsamppConfig;

        if ($local) {
            $link = $bearsamppRoot->getLocalUrl($link);
        }

        return self::getItemExe(
            $caption,
            $bearsamppConfig->getBrowser(),
            $glyph,
            $link
        );
    }

    /**
     * Returns a string representing a Notepad item.
     *
     * @param string $caption The caption for the item.
     * @param string $path The path to the file.
     * @return string The Notepad item string.
     */
    public static function getItemNotepad($caption, $path)
    {
        global $bearsamppConfig;

        return self::getItemExe(
            $caption,
            $bearsamppConfig->getNotepad(),
            self::GLYPH_FILE,
            $path
        );
    }

    /**
     * Returns a string representing an executable item.
     *
     * @param string $caption The caption for the item.
     * @param string $exe The path to the executable.
     * @param int $glyph The glyph index.
     * @param string|null $params The parameters for the executable.
     * @return string The executable item string.
     */
    public static function getItemExe($caption, $exe, $glyph, $params = null)
    {
        return 'Type: item; ' .
            'Caption: "' . $caption . '"; ' .
            'Action: run; ' .
            'FileName: "' . $exe . '"; ' .
            (!empty($params) ? 'Parameters: "' . $params . '"; ' : '') .
            'Glyph: ' . $glyph;
    }

    /**
     * Returns a string representing an explorer item.
     *
     * @param string $caption The caption for the item.
     * @param string $path The path to explore.
     * @return string The explorer item string.
     */
    public static function getItemExplore($caption, $path)
    {
        return 'Type: item; ' .
            'Caption: "' . $caption . '"; ' .
            'Action: shellexecute; ' .
            'FileName: "' . $path . '"; ' .
            'Glyph: ' . self::GLYPH_FOLDER_OPEN;
    }

    /**
     * Returns a string representing a service action.
     *
     * @param string|null $service The service name.
     * @param string $action The action to perform.
     * @param bool $item Whether to return as an item.
     * @return string The service action string.
     */
    private static function getActionService($service, $action, $item = false)
    {
        global $bearsamppLang;
        $result = 'Action: ' . $action;

        if ($service != null) {
            $result = 'Action: service; ' .
                'Service: ' . $service . '; ' .
                'ServiceAction: ' . $action;
        }

        if ($item) {
            $result = 'Type: item; ' . $result;
            if ($action == self::SERVICE_START) {
                $result .= '; Caption: "' . $bearsamppLang->getValue(Lang::MENU_START_SERVICE) . '"' .
                    '; Glyph: ' . self::GLYPH_START;
            } elseif ($action == self::SERVICE_STOP) {
                $result .= '; Caption: "' . $bearsamppLang->getValue(Lang::MENU_STOP_SERVICE) . '"' .
                    '; Glyph: ' . self::GLYPH_STOP;
            } elseif ($action == self::SERVICE_RESTART) {
                $result .= '; Caption: "' . $bearsamppLang->getValue(Lang::MENU_RESTART_SERVICE) . '"' .
                    '; Glyph: ' . self::GLYPH_RELOAD;
            }
        } elseif ($action != self::SERVICES_CLOSE) {
            $result .= '; Flags: ignoreerrors waituntilterminated';
        }

        return $result;
    }

    /**
     * Returns a string representing a service start action.
     *
     * @param string $service The service name.
     * @return string The service start action string.
     */
    public static function getActionServiceStart($service)
    {
        return self::getActionService($service, self::SERVICE_START, false);
    }

    /**
     * Returns a string representing a service start item.
     *
     * @param string $service The service name.
     * @return string The service start item string.
     */
    public static function getItemActionServiceStart($service)
    {
        return self::getActionService($service, self::SERVICE_STOP, true);
    }

    /**
     * Returns a string representing a service stop action.
     *
     * @param string $service The service name.
     * @return string The service stop action string.
     */
    public static function getActionServiceStop($service)
    {
        return self::getActionService($service, self::SERVICE_STOP, false);
    }

    /**
     * Returns a string representing a service stop item.
     *
     * @param string $service The service name.
     * @return string The service stop item string.
     */
    public static function getItemActionServiceStop($service)
    {
        return self::getActionService($service, self::SERVICE_START, true);
    }

    /**
     * Returns a string representing a service restart action.
     *
     * @param string $service The service name.
     * @return string The service restart action string.
     */
    public static function getActionServiceRestart($service)
    {
        return self::getActionService($service, self::SERVICE_RESTART, false);
    }

    /**
     * Returns a string representing a service restart item.
     *
     * @param string $service The service name.
     * @return string The service restart item string.
     */
    public static function getItemActionServiceRestart($service)
    {
        return self::getActionService($service, self::SERVICE_RESTART, true);
    }

    /**
     * Returns a string representing a close services action.
     *
     * @return string The close services action string.
     */
    public static function getActionServicesClose()
    {
        return self::getActionService(null, self::SERVICES_CLOSE, false);
    }

    /**
     * Returns a string representing a close services item.
     *
     * @return string The close services item string.
     */
    public static function getItemActionServicesClose()
    {
        return self::getActionService(null, self::SERVICES_CLOSE, true);
    }

    /**
     * Returns a string representing the messages section.
     *
     * @return string The messages section string.
     */
    public static function getSectionMessages()
    {
        global $bearsamppLang;

        return '[Messages]' . PHP_EOL .
            'AllRunningHint=' . $bearsamppLang->getValue(Lang::ALL_RUNNING_HINT) . PHP_EOL .
            'SomeRunningHint=' . $bearsamppLang->getValue(Lang::SOME_RUNNING_HINT) . PHP_EOL .
            'NoneRunningHint=' . $bearsamppLang->getValue(Lang::NONE_RUNNING_HINT) . PHP_EOL;
    }

    /**
     * Returns a string representing the config section.
     *
     * @return string The config section string.
     */
    public static function getSectionConfig()
    {
        global $bearsamppCore;
        return '[Config]' . PHP_EOL .
            'ImageList=' . self::IMG_GLYPH_SPRITES . PHP_EOL .
            'ServiceCheckInterval=1' . PHP_EOL .
            'TrayIconAllRunning=' . self::GLYPH_SERVICE_ALL_RUNNING . PHP_EOL .
            'TrayIconSomeRunning=' . self::GLYPH_SERVICE_SOME_RUNNING . PHP_EOL .
            'TrayIconNoneRunning=' . self::GLYPH_SERVICE_NONE_RUNNING . PHP_EOL .
            'ID={' . strtolower(APP_TITLE) . '}' . PHP_EOL .
            'AboutHeader=' . APP_TITLE . PHP_EOL .
            'AboutVersion=Version ' . $bearsamppCore->getAppVersion() . PHP_EOL;
    }

    /**
     * Returns a string representing the right menu settings section.
     *
     * @return string The right menu settings section string.
     */
    public static function getSectionMenuRightSettings()
    {
        return '[Menu.Right.Settings]' . PHP_EOL .
            'BarVisible=no' . PHP_EOL .
            'SeparatorsAlignment=center' . PHP_EOL .
            'SeparatorsFade=yes' . PHP_EOL .
            'SeparatorsFadeColor=clBtnShadow' . PHP_EOL .
            'SeparatorsFlatLines=yes' . PHP_EOL .
            'SeparatorsGradientEnd=clSilver' . PHP_EOL .
            'SeparatorsGradientStart=clGray' . PHP_EOL .
            'SeparatorsGradientStyle=horizontal' . PHP_EOL .
            'SeparatorsSeparatorStyle=shortline' . PHP_EOL;
    }

    /**
     * Returns a string representing the left menu settings section.
     *
     * @param string $caption The caption for the left menu.
     * @return string The left menu settings section string.
     */
    public static function getSectionMenuLeftSettings($caption)
    {
        return '[Menu.Left.Settings]' . PHP_EOL .
            'AutoLineReduction=no' . PHP_EOL .
            'BarVisible=yes' . PHP_EOL .
            'BarCaptionAlignment=bottom' . PHP_EOL .
            'BarCaptionCaption=' . $caption . PHP_EOL .
            'BarCaptionDepth=1' . PHP_EOL .
            'BarCaptionDirection=downtoup' . PHP_EOL .
            'BarCaptionFont=Tahoma,14,clWhite' . PHP_EOL .
            'BarCaptionHighlightColor=clNone' . PHP_EOL .
            'BarCaptionOffsetY=0' . PHP_EOL .
            'BarCaptionShadowColor=clNone' . PHP_EOL .
            'BarPictureHorzAlignment=center' . PHP_EOL .
            'BarPictureOffsetX=0' . PHP_EOL .
            'BarPictureOffsetY=0' . PHP_EOL .
            'BarPicturePicture=' . self::IMG_BAR_PICTURE . PHP_EOL .
            'BarPictureTransparent=yes' . PHP_EOL .
            'BarPictureVertAlignment=bottom' . PHP_EOL .
            'BarBorder=clNone' . PHP_EOL .
            'BarGradientEnd=$00c07840' . PHP_EOL .
            'BarGradientStart=$00c07840' . PHP_EOL .
            'BarGradientStyle=horizontal' . PHP_EOL .
            'BarSide=left' . PHP_EOL .
            'BarSpace=0' . PHP_EOL .
            'BarWidth=32' . PHP_EOL .
            'SeparatorsAlignment=center' . PHP_EOL .
            'SeparatorsFade=yes' . PHP_EOL .
            'SeparatorsFadeColor=clBtnShadow' . PHP_EOL .
            'SeparatorsFlatLines=yes' . PHP_EOL .
            'SeparatorsFont=Arial,8,clWhite,bold' . PHP_EOL .
            'SeparatorsGradientEnd=$00FFAA55' . PHP_EOL .
            'SeparatorsGradientStart=$00550000' . PHP_EOL .
            'SeparatorsGradientStyle=horizontal' . PHP_EOL .
            'SeparatorsSeparatorStyle=caption' . PHP_EOL;
    }
}
