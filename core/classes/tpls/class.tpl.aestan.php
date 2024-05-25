<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAestan
 *
 * This class provides utility functions to generate menu items and actions for a graphical user interface,
 * specifically tailored for the Bearsampp environment. It handles the creation of menu items linked to
 * various system operations like starting, stopping, and restarting services, as well as opening files and
 * links using specified applications. It also manages the display of glyphs and other UI elements.
 *
 * Constants:
 * - GLYPH_*: Constants representing glyph icons for various UI elements.
 * - SERVICE_*: Constants defining service-related actions.
 * - IMG_*: Constants for image resources used in the UI.
 *
 * Methods:
 * - getGlyphFlah($lang): Placeholder for a method to fetch glyph flags based on language settings.
 * - getItemSeparator(): Returns a string representation for a menu item separator.
 * - getItemConsoleZ($caption, $glyph, $id, $title, $initDir, $command): Generates a menu item for launching ConsoleZ with specific parameters.
 * - getItemLink($caption, $link, $local, $glyph): Creates a menu item that acts as a hyperlink.
 * - getItemNotepad($caption, $path): Generates a menu item to open a file in Notepad.
 * - getItemExe($caption, $exe, $glyph, $params): General method to create a menu item that executes a given executable.
 * - getItemExplore($caption, $path): Creates a menu item to explore a given file path.
 * - getActionService($service, $action, $item): Generates a string for a service action, optionally formatted as a menu item.
 * - getActionServiceStart($service), getItemActionServiceStart($service), getActionServiceStop($service), etc.: Methods to handle service actions like start, stop, and restart.
 * - getSectionMessages(): Returns configuration settings for UI messages.
 * - getSectionConfig(): Provides general configuration settings for the UI.
 * - getSectionMenuRightSettings(), getSectionMenuLeftSettings($caption): Methods to configure UI settings for menu alignment and appearance.
 */
class TplAestan
{
    const GLYPH_CONSOLEZ = 0;
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
    const GLYPH_FILEZILLA = 47;
    const GLYPH_FOLDER_DISABLED = 48;
    const GLYPH_FOLDER_ENABLED = 49;
    const GLYPH_PYTHON = 50;
    const GLYPH_PYTHON_CP = 51;
    const GLYPH_RUBY = 52;
    const GLYPH_YARN = 54;
    const GLYPH_PERL = 55;
    const GLYPH_GHOSTSCRIPT = 56;
    const GLYPH_NGROK = 57;

    const SERVICE_START = 'startresume';
    const SERVICE_STOP = 'stop';
    const SERVICE_RESTART = 'restart';
    const SERVICES_CLOSE = 'closeservices';

    const IMG_BAR_PICTURE = 'bar.dat';
    const IMG_GLYPH_SPRITES = 'sprites.dat';

    public static function getGlyphFlah($lang)
    {
    }

    /**
     * Returns a string representing a menu item separator.
     *
     * @return string The menu item type as a separator.
     */
    public static function getItemSeparator()
    {
        return 'Type: separator';
    }

    /**
     * Generates a menu item for launching ConsoleZ with specific parameters.
     *
     * @param string $caption The caption for the menu item.
     * @param int $glyph The glyph icon associated with the menu item.
     * @param string|null $id Optional. The ID for the ConsoleZ instance.
     * @param string|null $title Optional. The window title for the ConsoleZ instance.
     * @param string|null $initDir Optional. The initial directory for the ConsoleZ instance.
     * @param string|null $command Optional. The command to execute in ConsoleZ.
     * @return string The formatted string for an executable menu item.
     */
    public static function getItemConsoleZ($caption, $glyph, $id = null, $title = null, $initDir = null, $command = null)
    {
        global $bearsamppTools;

        $args = '';
        if ($id != null) {
            $args .= ' -t ""' . $id . '""';
        }
        if ($title != null) {
            $args .= ' -w ""' . $title . '""';
        }
        if ($initDir != null) {
            $args .= ' -d ""' . $initDir . '""';
        }
        if ($command != null) {
            $args .= ' -r ""' . $command . '""';
        }

        return self::getItemExe(
            $caption,
            $bearsamppTools->getConsoleZ()->getExe(),
            $glyph,
            $args
        );
    }

    /**
     * Creates a menu item that acts as a hyperlink, optionally localizing the URL.
     *
     * @param string $caption The caption for the menu item.
     * @param string $link The URL or local path for the hyperlink.
     * @param bool $local Optional. Whether to convert the link to a local URL.
     * @param int $glyph Optional. The glyph icon associated with the menu item. Defaults to web page icon.
     * @return string The formatted string for an executable menu item.
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
     * Generates a menu item to open a file in Notepad.
     *
     * @param string $caption The caption for the menu item.
     * @param string $path The file path to open in Notepad.
     * @return string The formatted string for an executable menu item.
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
     * Generates a formatted string for creating a menu item that executes an executable.
     * This method is used to construct a string that represents a menu item which, when activated,
     * will run a specified executable file with optional parameters.
     *
     * @param string $caption The text to display on the menu item.
     * @param string $exe The path to the executable file.
     * @param int $glyph The glyph icon associated with the menu item.
     * @param string|null $params Optional parameters to pass to the executable.
     * @return string The formatted string representing the executable menu item.
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
     * Generates a formatted string for creating a menu item that opens a specified file path in the system's file explorer.
     * This method is used to construct a string that represents a menu item which, when activated,
     * will open the specified file path using the system's default file explorer.
     *
     * @param string $caption The text to display on the menu item.
     * @param string $path The file path to open.
     * @return string The formatted string representing the file explorer menu item.
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
     * Constructs a string representing a service action, optionally formatted as a menu item.
     * This method can handle different types of service actions such as start, stop, and restart.
     * It can also format these actions as menu items with appropriate captions and glyphs if specified.
     *
     * @param string|null $service The name of the service to which the action applies. If null, the action applies globally.
     * @param string $action The specific action to perform on the service (e.g., 'start', 'stop', 'restart').
     * @param bool $item If true, formats the action as a menu item with additional UI elements like captions and glyphs.
     * @return string The formatted string representing the service action, suitable for UI display or configuration.
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
     * Returns a string for starting a service action without formatting it as a menu item.
     *
     * @param string $service The name of the service to start.
     * @return string The action string for starting the specified service.
     */
    public static function getActionServiceStart($service)
    {
        return self::getActionService($service, self::SERVICE_START, false);
    }

    /**
     * Returns a string for starting a service action formatted as a menu item.
     *
     * @param string $service The name of the service to start.
     * @return string The menu item string for starting the specified service.
     */
    public static function getItemActionServiceStart($service)
    {
        return self::getActionService($service, self::SERVICE_STOP, true);
    }

    /**
     * Returns a string for stopping a service action without formatting it as a menu item.
     *
     * @param string $service The name of the service to stop.
     * @return string The action string for stopping the specified service.
     */
    public static function getActionServiceStop($service)
    {
        return self::getActionService($service, self::SERVICE_STOP, false);
    }

    /**
     * Returns a string for stopping a service action formatted as a menu item.
     *
     * @param string $service The name of the service to stop.
     * @return string The menu item string for stopping the specified service.
     */
    public static function getItemActionServiceStop($service)
    {
        return self::getActionService($service, self::SERVICE_START, true);
    }

    /**
     * Returns a string for restarting a service action without formatting it as a menu item.
     *
     * @param string $service The name of the service to restart.
     * @return string The action string for restarting the specified service.
     */
    public static function getActionServiceRestart($service)
    {
        return self::getActionService($service, self::SERVICE_RESTART, false);
    }

    /**
     * Returns a string for restarting a service action formatted as a menu item.
     *
     * @param string $service The name of the service to restart.
     * @return string The menu item string for restarting the specified service.
     */
    public static function getItemActionServiceRestart($service)
    {
        return self::getActionService($service, self::SERVICE_RESTART, true);
    }
    /**
     * Returns the action string for closing services without formatting it as a menu item.
     *
     * @return string The action string for closing services.
     */
    public static function getActionServicesClose()
    {
        return self::getActionService(null, self::SERVICES_CLOSE, false);
    }

    /**
     * Returns the action string for closing services formatted as a menu item.
     *
     * @return string The menu item string for closing services.
     */
    public static function getItemActionServicesClose()
    {
        return self::getActionService(null, self::SERVICES_CLOSE, true);
    }

    /**
     * Generates the configuration section for messages in the application.
     * It fetches language-specific message hints for service running status.
     *
     * @global object $bearsamppLang Language handler object to fetch localized strings.
     * @return string Configuration string for message settings.
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
     * Generates the configuration section for the application.
     * It includes settings related to UI icons, service check intervals, and application metadata.
     *
     * @global object $bearsamppCore Core application handler object to fetch version information.
     * @return string Configuration string for application settings.
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
     * Generates the configuration section for the right menu settings.
     * It includes visibility and styling settings for the menu bar and separators.
     *
     * @return string Configuration string for right menu settings.
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
     * Generates the configuration section for the left menu settings.
     * It includes detailed settings for the appearance and behavior of the menu bar and separators.
     *
     * @param string $caption The caption text for the menu bar.
     * @return string Configuration string for left menu settings.
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
