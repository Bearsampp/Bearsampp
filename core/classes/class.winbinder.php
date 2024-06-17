<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class WinBinder
 *
 * This class provides an interface to the WinBinder library, allowing for the creation and management
 * of Windows GUI elements in PHP. It includes methods for creating windows, controls, handling events,
 * and executing system commands.
 */
class WinBinder
{
    // Constants for control IDs and objects
    const CTRL_ID = 0;
    const CTRL_OBJ = 1;

    // Constants for progress bar increment and new line
    const INCR_PROGRESS_BAR = '++';
    const NEW_LINE = '@nl@';

    // Constants for message box types
    const BOX_INFO = WBC_INFO;
    const BOX_OK = WBC_OK;
    const BOX_OKCANCEL = WBC_OKCANCEL;
    const BOX_QUESTION = WBC_QUESTION;
    const BOX_ERROR = WBC_STOP;
    const BOX_WARNING = WBC_WARNING;
    const BOX_YESNO = WBC_YESNO;
    const BOX_YESNOCANCEL = WBC_YESNOCANCEL;

    // Constants for cursor types
    const CURSOR_ARROW = 'arrow';
    const CURSOR_CROSS = 'cross';
    const CURSOR_FINGER = 'finger';
    const CURSOR_FORBIDDEN = 'forbidden';
    const CURSOR_HELP = 'help';
    const CURSOR_IBEAM = 'ibeam';
    const CURSOR_NONE = null;
    const CURSOR_SIZEALL = 'sizeall';
    const CURSOR_SIZENESW = 'sizenesw';
    const CURSOR_SIZENS = 'sizens';
    const CURSOR_SIZENWSE = 'sizenwse';
    const CURSOR_SIZEWE = 'sizewe';
    const CURSOR_UPARROW = 'uparrow';
    const CURSOR_WAIT = 'wait';
    const CURSOR_WAITARROW = 'waitarrow';

    // Constants for system information types
    const SYSINFO_SCREENAREA = 'screenarea';
    const SYSINFO_WORKAREA = 'workarea';

    private $defaultTitle;
    private $countCtrls;

    public $callback;
    public $gauge;

    /**
     * WinBinder constructor.
     *
     * Initializes the WinBinder class, sets the default window title, and resets control counters.
     */
    public function __construct()
    {
        global $bearsamppCore;
        Util::logInitClass( $this );

        $this->defaultTitle = APP_TITLE . ' ' . $bearsamppCore->getAppVersion();
        $this->reset();
    }

    /**
     * Writes a log message to the WinBinder log file.
     *
     * @param   string  $log  The log message to write.
     */
    private static function writeLog($log)
    {
        global $bearsamppRoot;
        Util::logDebug( $log, $bearsamppRoot->getWinbinderLogFilePath() );
    }

    /**
     * Resets the control counter and callback array.
     */
    public function reset()
    {
        $this->countCtrls = 1000;
        $this->callback   = array();
    }

    /**
     * Calls a WinBinder function with the specified parameters.
     *
     * @param   string  $function            The name of the WinBinder function to call.
     * @param   array   $params              The parameters to pass to the function.
     * @param   bool    $removeErrorHandler  Whether to remove the error handler during the call.
     *
     * @return mixed The result of the function call.
     */
    private function callWinBinder($function, $params = array(), $removeErrorHandler = false)
    {
        $result = false;
        if ( function_exists( $function ) ) {
            if ( $removeErrorHandler ) {
                $result = @call_user_func_array( $function, $params );
            }
            else {
                $result = call_user_func_array( $function, $params );
            }
        }

        return $result;
    }

    /**
     * Creates a new window.
     *
     * @param   mixed   $parent   The parent window or null for a top-level window.
     * @param   string  $wclass   The window class.
     * @param   string  $caption  The window caption.
     * @param   int     $xPos     The x-coordinate of the window.
     * @param   int     $yPos     The y-coordinate of the window.
     * @param   int     $width    The width of the window.
     * @param   int     $height   The height of the window.
     * @param   mixed   $style    The window style.
     * @param   mixed   $params   Additional parameters for the window.
     *
     * @return mixed The created window object.
     */
    public function createWindow($parent, $wclass, $caption, $xPos, $yPos, $width, $height, $style = null, $params = null)
    {
        global $bearsamppCore;

        $caption = empty( $caption ) ? $this->defaultTitle : $this->defaultTitle . ' - ' . $caption;
        $window  = $this->callWinBinder( 'wb_create_window', array($parent, $wclass, $caption, $xPos, $yPos, $width, $height, $style, $params) );

        // Set tiny window icon
        $this->setImage( $window, $bearsamppCore->getResourcesPath() . '/homepage/img/icons/app.ico' );

        return $window;
    }

    /**
     * Creates a new control.
     *
     * @param   mixed   $parent    The parent window or control.
     * @param   string  $ctlClass  The control class.
     * @param   string  $caption   The control caption.
     * @param   int     $xPos      The x-coordinate of the control.
     * @param   int     $yPos      The y-coordinate of the control.
     * @param   int     $width     The width of the control.
     * @param   int     $height    The height of the control.
     * @param   mixed   $style     The control style.
     * @param   mixed   $params    Additional parameters for the control.
     *
     * @return array An array containing the control ID and object.
     */
    public function createControl($parent, $ctlClass, $caption, $xPos, $yPos, $width, $height, $style = null, $params = null)
    {
        $this->countCtrls++;

        return array(
            self::CTRL_ID  => $this->countCtrls,
            self::CTRL_OBJ => $this->callWinBinder( 'wb_create_control', array(
                $parent, $ctlClass, $caption, $xPos, $yPos, $width, $height, $this->countCtrls, $style, $params
            ) ),
        );
    }

    /**
     * Creates a new application window.
     *
     * @param   string  $caption  The window caption.
     * @param   int     $width    The width of the window.
     * @param   int     $height   The height of the window.
     * @param   mixed   $style    The window style.
     * @param   mixed   $params   Additional parameters for the window.
     *
     * @return mixed The created window object.
     */
    public function createAppWindow($caption, $width, $height, $style = null, $params = null)
    {
        return $this->createWindow( null, AppWindow, $caption, WBC_CENTER, WBC_CENTER, $width, $height, $style, $params );
    }

    /**
     * Creates a new naked window.
     *
     * @param   string  $caption  The window caption.
     * @param   int     $width    The width of the window.
     * @param   int     $height   The height of the window.
     * @param   mixed   $style    The window style.
     * @param   mixed   $params   Additional parameters for the window.
     *
     * @return mixed The created window object.
     */
    public function createNakedWindow($caption, $width, $height, $style = null, $params = null)
    {
        $window = $this->createWindow( null, NakedWindow, $caption, WBC_CENTER, WBC_CENTER, $width, $height, $style, $params );
        $this->setArea( $window, $width, $height );

        return $window;
    }

    /**
     * Destroys a window.
     *
     * @param   mixed  $window  The window object to destroy.
     */
    public function destroyWindow($window)
    {
        $this->callWinBinder( 'wb_destroy_window', array($window), true );
        exit();
    }

    /**
     * Starts the main event loop.
     *
     * @return mixed The result of the main loop.
     */
    public function mainLoop()
    {
        return $this->callWinBinder( 'wb_main_loop' );
    }

    /**
     * Refreshes a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to refresh.
     *
     * @return mixed The result of the refresh operation.
     */
    public function refresh($wbobject)
    {
        return $this->callWinBinder( 'wb_refresh', array($wbobject, true) );
    }

    /**
     * Retrieves system information.
     *
     * @param   string  $info  The type of system information to retrieve.
     *
     * @return mixed The retrieved system information.
     */
    public function getSystemInfo($info)
    {
        return $this->callWinBinder( 'wb_get_system_info', array($info) );
    }

    /**
     * Draws an image on a WinBinder object.
     *
     * @param   mixed   $wbobject  The WinBinder object to draw on.
     * @param   string  $path      The path to the image file.
     * @param   int     $xPos      The x-coordinate of the image.
     * @param   int     $yPos      The y-coordinate of the image.
     * @param   int     $width     The width of the image.
     * @param   int     $height    The height of the image.
     *
     * @return mixed The result of the draw operation.
     */
    public function drawImage($wbobject, $path, $xPos = 0, $yPos = 0, $width = 0, $height = 0)
    {
        $image = $this->callWinBinder( 'wb_load_image', array($path) );

        return $this->callWinBinder( 'wb_draw_image', array($wbobject, $image, $xPos, $yPos, $width, $height) );
    }

    /**
     * Draws text on a WinBinder object.
     *
     * @param   mixed     $parent   The parent WinBinder object.
     * @param   string    $caption  The text to draw.
     * @param   int       $xPos     The x-coordinate of the text.
     * @param   int       $yPos     The y-coordinate of the text.
     * @param   int|null  $width    The width of the text area.
     * @param   int|null  $height   The height of the text area.
     * @param   mixed     $font     The font to use for the text.
     *
     * @return mixed The result of the draw operation.
     */
    public function drawText($parent, $caption, $xPos, $yPos, $width = null, $height = null, $font = null)
    {
        $caption = str_replace( self::NEW_LINE, PHP_EOL, $caption );
        $width   = $width == null ? 120 : $width;
        $height  = $height == null ? 25 : $height;

        return $this->callWinBinder( 'wb_draw_text', array($parent, $caption, $xPos, $yPos, $width, $height, $font) );
    }

    /**
     * Draws a rectangle on a WinBinder object.
     *
     * @param   mixed  $parent  The parent WinBinder object.
     * @param   int    $xPos    The x-coordinate of the rectangle.
     * @param   int    $yPos    The y-coordinate of the rectangle.
     * @param   int    $width   The width of the rectangle.
     * @param   int    $height  The height of the rectangle.
     * @param   int    $color   The color of the rectangle.
     * @param   bool   $filled  Whether the rectangle should be filled.
     *
     * @return mixed The result of the draw operation.
     */
    public function drawRect($parent, $xPos, $yPos, $width, $height, $color = 15790320, $filled = true)
    {
        return $this->callWinBinder( 'wb_draw_rect', array($parent, $xPos, $yPos, $width, $height, $color, $filled) );
    }

    /**
     * Draws a line on a WinBinder object.
     *
     * @param   mixed  $wbobject   The WinBinder object to draw on.
     * @param   int    $xStartPos  The starting x-coordinate of the line.
     * @param   int    $yStartPos  The starting y-coordinate of the line.
     * @param   int    $xEndPos    The ending x-coordinate of the line.
     * @param   int    $yEndPos    The ending y-coordinate of the line.
     * @param   int    $color      The color of the line.
     * @param   int    $height     The height of the line.
     *
     * @return mixed The result of the draw operation.
     */
    public function drawLine($wbobject, $xStartPos, $yStartPos, $xEndPos, $yEndPos, $color, $height = 1)
    {
        return $this->callWinBinder( 'wb_draw_line', array($wbobject, $xStartPos, $yStartPos, $xEndPos, $yEndPos, $color, $height) );
    }

    /**
     * Creates a font for use in WinBinder controls.
     *
     * @param   string    $fontName  The name of the font.
     * @param   int|null  $size      The size of the font.
     * @param   int|null  $color     The color of the font.
     * @param   mixed     $style     The style of the font.
     *
     * @return mixed The created font object.
     */
    public function createFont($fontName, $size = null, $color = null, $style = null)
    {
        return $this->callWinBinder( 'wb_create_font', array($fontName, $size, $color, $style) );
    }

    /**
     * Waits for an event on a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to wait on.
     *
     * @return mixed The result of the wait operation.
     */
    public function wait($wbobject = null)
    {
        return $this->callWinBinder( 'wb_wait', array($wbobject), true );
    }

    /**
     * Creates a timer for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to create the timer for.
     * @param   int    $wait      The wait time in milliseconds.
     *
     * @return array An array containing the timer ID and object.
     */
    public function createTimer($wbobject, $wait = 1000)
    {
        $this->countCtrls++;

        return array(
            self::CTRL_ID  => $this->countCtrls,
            self::CTRL_OBJ => $this->callWinBinder( 'wb_create_timer', array($wbobject, $this->countCtrls, $wait) )
        );
    }

    /**
     * Destroys a timer for a WinBinder object.
     *
     * @param   mixed  $wbobject     The WinBinder object to destroy the timer for.
     * @param   mixed  $timerobject  The timer object to destroy.
     *
     * @return mixed The result of the destroy operation.
     */
    public function destroyTimer($wbobject, $timerobject)
    {
        return $this->callWinBinder( 'wb_destroy_timer', array($wbobject, $timerobject) );
    }

    /**
     * Executes a system command.
     *
     * @param   string       $cmd     The command to execute.
     * @param   string|null  $params  The parameters to pass to the command.
     * @param   bool         $silent  Whether to execute the command silently.
     *
     * @return mixed The result of the command execution.
     */
    public function exec($cmd, $params = null, $silent = false)
    {
        global $bearsamppCore;

        if ( $silent ) {
            $silent = '"' . $bearsamppCore->getScript( Core::SCRIPT_EXEC_SILENT ) . '" "' . $cmd . '"';
            $cmd    = 'wscript.exe';
            $params = !empty( $params ) ? $silent . ' "' . $params . '"' : $silent;
        }

        $this->writeLog( 'exec: ' . $cmd . ' ' . $params );

        return $this->callWinBinder( 'wb_exec', array($cmd, $params) );
    }

    /**
     * Finds a file using WinBinder.
     *
     * @param   string  $filename  The name of the file to find.
     *
     * @return mixed The result of the find operation.
     */
    public function findFile($filename)
    {
        $result = $this->callWinBinder( 'wb_find_file', array($filename) );
        $this->writeLog( 'findFile ' . $filename . ': ' . $result );

        return $result != $filename ? $result : false;
    }

    /**
     * Sets an event handler for a WinBinder object.
     *
     * @param   mixed  $wbobject        The WinBinder object to set the handler for.
     * @param   mixed  $classCallback   The class callback for the handler.
     * @param   mixed  $methodCallback  The method callback for the handler.
     * @param   mixed  $launchTimer     The timer to launch for the handler.
     *
     * @return mixed The result of the set handler operation.
     */
    public function setHandler($wbobject, $classCallback, $methodCallback, $launchTimer = null)
    {
        if ( $launchTimer != null ) {
            $launchTimer = $this->createTimer( $wbobject, $launchTimer );
        }

        $this->callback[$wbobject] = array($classCallback, $methodCallback, $launchTimer);

        return $this->callWinBinder( 'wb_set_handler', array($wbobject, '__winbinderEventHandler') );
    }

    /**
     * Sets an image for a WinBinder object.
     *
     * @param   mixed   $wbobject  The WinBinder object to set the image for.
     * @param   string  $path      The path to the image file.
     *
     * @return mixed The result of the set image operation.
     */
    public function setImage($wbobject, $path)
    {
        return $this->callWinBinder( 'wb_set_image', array($wbobject, $path) );
    }

    /**
     * Sets the maximum length for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the maximum length for.
     * @param   int    $length    The maximum length to set.
     *
     * @return mixed The result of the set maximum length operation.
     */
    public function setMaxLength($wbobject, $length)
    {
        return $this->callWinBinder( 'wb_send_message', array($wbobject, 0x00c5, $length, 0) );
    }

    /**
     * Sets the area of a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the area for.
     * @param   int    $width     The width of the area.
     * @param   int    $height    The height of the area.
     *
     * @return mixed The result of the set area operation.
     */
    public function setArea($wbobject, $width, $height)
    {
        return $this->callWinBinder( 'wb_set_area', array($wbobject, WBC_TITLE, 0, 0, $width, $height) );
    }

    /**
     * Retrieves the text from a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to get the text from.
     *
     * @return mixed The retrieved text.
     */
    public function getText($wbobject)
    {
        return $this->callWinBinder( 'wb_get_text', array($wbobject) );
    }

    /**
     * Sets the text for a WinBinder object.
     *
     * @param   mixed   $wbobject  The WinBinder object to set the text for.
     * @param   string  $content   The text content to set.
     *
     * @return mixed The result of the set text operation.
     */
    public function setText($wbobject, $content)
    {
        $content = str_replace( self::NEW_LINE, PHP_EOL, $content );

        return $this->callWinBinder( 'wb_set_text', array($wbobject, $content) );
    }

    /**
     * Retrieves the value from a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to get the value from.
     *
     * @return mixed The retrieved value.
     */
    public function getValue($wbobject)
    {
        return $this->callWinBinder( 'wb_get_value', array($wbobject) );
    }

    /**
     * Sets the value for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the value for.
     * @param   mixed  $content   The value to set.
     *
     * @return mixed The result of the set value operation.
     */
    public function setValue($wbobject, $content)
    {
        return $this->callWinBinder( 'wb_set_value', array($wbobject, $content) );
    }

    /**
     * Retrieves the focus from a WinBinder object.
     *
     * @return mixed The WinBinder object that has the focus.
     */
    public function getFocus()
    {
        return $this->callWinBinder( 'wb_get_focus' );
    }

    /**
     * Sets the focus to a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the focus to.
     *
     * @return mixed The result of the set focus operation.
     */
    public function setFocus($wbobject)
    {
        return $this->callWinBinder( 'wb_set_focus', array($wbobject) );
    }

    /**
     * Sets the cursor type for a WinBinder object.
     *
     * @param   mixed   $wbobject  The WinBinder object to set the cursor for.
     * @param   string  $type      The cursor type to set.
     *
     * @return mixed The result of the set cursor operation.
     */
    public function setCursor($wbobject, $type = self::CURSOR_ARROW)
    {
        return $this->callWinBinder( 'wb_set_cursor', array($wbobject, $type) );
    }

    /**
     * Checks if a WinBinder object is enabled.
     *
     * @param   mixed  $wbobject  The WinBinder object to check.
     *
     * @return mixed True if the object is enabled, false otherwise.
     */
    public function isEnabled($wbobject)
    {
        return $this->callWinBinder( 'wb_get_enabled', array($wbobject) );
    }

    /**
     * Sets the enabled state for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the enabled state for.
     * @param   bool   $enabled   True to enable the object, false to disable it.
     *
     * @return mixed The result of the set enabled state operation.
     */
    public function setEnabled($wbobject, $enabled = true)
    {
        return $this->callWinBinder( 'wb_set_enabled', array($wbobject, $enabled) );
    }

    /**
     * Disables a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to disable.
     *
     * @return mixed The result of the disable operation.
     */
    public function setDisabled($wbobject)
    {
        return $this->setEnabled( $wbobject, false );
    }

    /**
     * Sets the style for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the style for.
     * @param   mixed  $style     The style to set.
     *
     * @return mixed The result of the set style operation.
     */
    public function setStyle($wbobject, $style)
    {
        return $this->callWinBinder( 'wb_set_style', array($wbobject, $style) );
    }

    /**
     * Sets the range for a WinBinder object.
     *
     * @param   mixed  $wbobject  The WinBinder object to set the range for.
     * @param   int    $min       The minimum value of the range.
     * @param   int    $max       The maximum value of the range.
     *
     * @return mixed The result of the set range operation.
     */
    public function setRange($wbobject, $min, $max)
    {
        return $this->callWinBinder( 'wb_set_range', array($wbobject, $min, $max) );
    }

    /**
     * Opens a system dialog to select a path.
     *
     * @param   mixed        $parent  The parent window for the dialog.
     * @param   string       $title   The title of the dialog.
     * @param   string|null  $path    The initial path for the dialog.
     *
     * @return mixed The selected path.
     */
    public function sysDlgPath($parent, $title, $path = null)
    {
        return $this->callWinBinder( 'wb_sys_dlg_path', array($parent, $title, $path) );
    }

    /**
     * Opens a system dialog to open a file.
     *
     * @param   mixed        $parent  The parent window for the dialog.
     * @param   string       $title   The title of the dialog.
     * @param   string|null  $filter  The file filter for the dialog.
     * @param   string|null  $path    The initial path for the dialog.
     *
     * @return mixed The selected file path.
     */
    public function sysDlgOpen($parent, $title, $filter = null, $path = null)
    {
        return $this->callWinBinder( 'wb_sys_dlg_open', array($parent, $title, $filter, $path) );
    }

    /**
     * Creates a label control.
     *
     * @param   mixed     $parent   The parent window or control.
     * @param   string    $caption  The caption for the label.
     * @param   int       $xPos     The x-coordinate of the label.
     * @param   int       $yPos     The y-coordinate of the label.
     * @param   int|null  $width    The width of the label.
     * @param   int|null  $height   The height of the label.
     * @param   mixed     $style    The style for the label.
     * @param   mixed     $params   Additional parameters for the label.
     *
     * @return array An array containing the control ID and object.
     */
    public function createLabel($parent, $caption, $xPos, $yPos, $width = null, $height = null, $style = null, $params = null)
    {
        $caption = str_replace( self::NEW_LINE, PHP_EOL, $caption );
        $width   = $width == null ? 120 : $width;
        $height  = $height == null ? 25 : $height;

        return $this->createControl( $parent, Label, $caption, $xPos, $yPos, $width, $height, $style, $params );
    }

    /**
     * Creates an input text control.
     *
     * @param   mixed     $parent     The parent window or control.
     * @param   string    $value      The initial value for the input text.
     * @param   int       $xPos       The x-coordinate of the input text.
     * @param   int       $yPos       The y-coordinate of the input text.
     * @param   int|null  $width      The width of the input text.
     * @param   int|null  $height     The height of the input text.
     * @param   int|null  $maxLength  The maximum length of the input text.
     * @param   mixed     $style      The style for the input text.
     * @param   mixed     $params     Additional parameters for the input text.
     *
     * @return array An array containing the control ID and object.
     */
    public function createInputText($parent, $value, $xPos, $yPos, $width = null, $height = null, $maxLength = null, $style = null, $params = null)
    {
        $value     = str_replace( self::NEW_LINE, PHP_EOL, $value );
        $width     = $width == null ? 120 : $width;
        $height    = $height == null ? 25 : $height;
        $inputText = $this->createControl( $parent, EditBox, (string) $value, $xPos, $yPos, $width, $height, $style, $params );
        if ( is_numeric( $maxLength ) && $maxLength > 0 ) {
            $this->setMaxLength( $inputText[self::CTRL_OBJ], $maxLength );
        }

        return $inputText;
    }

    /**
     * Creates an edit box control.
     *
     * @param   mixed     $parent  The parent window or control.
     * @param   string    $value   The initial value for the edit box.
     * @param   int       $xPos    The x-coordinate of the edit box.
     * @param   int       $yPos    The y-coordinate of the edit box.
     * @param   int|null  $width   The width of the edit box.
     * @param   int|null  $height  The height of the edit box.
     * @param   mixed     $style   The style for the edit box.
     * @param   mixed     $params  Additional parameters for the edit box.
     *
     * @return array An array containing the control ID and object.
     */
    public function createEditBox($parent, $value, $xPos, $yPos, $width = null, $height = null, $style = null, $params = null)
    {
        $value   = str_replace( self::NEW_LINE, PHP_EOL, $value );
        $width   = $width == null ? 540 : $width;
        $height  = $height == null ? 340 : $height;
        $editBox = $this->createControl( $parent, RTFEditBox, (string) $value, $xPos, $yPos, $width, $height, $style, $params );

        return $editBox;
    }

    /**
     * Creates a hyperlink control.
     *
     * @param   mixed     $parent   The parent window or control.
     * @param   string    $caption  The caption for the hyperlink.
     * @param   int       $xPos     The x-coordinate of the hyperlink.
     * @param   int       $yPos     The y-coordinate of the hyperlink.
     * @param   int|null  $width    The width of the hyperlink.
     * @param   int|null  $height   The height of the hyperlink.
     * @param   mixed     $style    The style for the hyperlink.
     * @param   mixed     $params   Additional parameters for the hyperlink.
     *
     * @return array An array containing the control ID and object.
     */
    public function createHyperLink($parent, $caption, $xPos, $yPos, $width = null, $height = null, $style = null, $params = null)
    {
        $caption   = str_replace( self::NEW_LINE, PHP_EOL, $caption );
        $width     = $width == null ? 120 : $width;
        $height    = $height == null ? 15 : $height;
        $hyperLink = $this->createControl( $parent, HyperLink, (string) $caption, $xPos, $yPos, $width, $height, $style, $params );
        $this->setCursor( $hyperLink[self::CTRL_OBJ], self::CURSOR_FINGER );

        return $hyperLink;
    }

    /**
     * Creates a radio button control.
     *
     * @param   mixed     $parent      The parent window or control.
     * @param   string    $caption     The caption for the radio button.
     * @param   bool      $checked     Whether the radio button is checked.
     * @param   int       $xPos        The x-coordinate of the radio button.
     * @param   int       $yPos        The y-coordinate of the radio button.
     * @param   int|null  $width       The width of the radio button.
     * @param   int|null  $height      The height of the radio button.
     * @param   bool      $startGroup  Whether this radio button starts a new group.
     *
     * @return array An array containing the control ID and object.
     */
    public function createRadioButton($parent, $caption, $checked, $xPos, $yPos, $width = null, $height = null, $startGroup = false)
    {
        $caption = str_replace( self::NEW_LINE, PHP_EOL, $caption );
        $width   = $width == null ? 120 : $width;
        $height  = $height == null ? 25 : $height;

        return $this->createControl( $parent, RadioButton, (string) $caption, $xPos, $yPos, $width, $height, $startGroup ? WBC_GROUP : null, $checked ? 1 : 0 );
    }

    /**
     * Creates a button control.
     *
     * @param   mixed     $parent   The parent window or control.
     * @param   string    $caption  The caption for the button.
     * @param   int       $xPos     The x-coordinate of the button.
     * @param   int       $yPos     The y-coordinate of the button.
     * @param   int|null  $width    The width of the button.
     * @param   int|null  $height   The height of the button.
     * @param   mixed     $style    The style for the button.
     * @param   mixed     $params   Additional parameters for the button.
     *
     * @return array An array containing the control ID and object.
     */
    public function createButton($parent, $caption, $xPos, $yPos, $width = null, $height = null, $style = null, $params = null)
    {
        $width  = $width == null ? 80 : $width;
        $height = $height == null ? 25 : $height;

        return $this->createControl( $parent, PushButton, $caption, $xPos, $yPos, $width, $height, $style, $params );
    }

    /**
     * Creates a progress bar control.
     *
     * @param   mixed     $parent  The parent window or control.
     * @param   int       $max     The maximum value for the progress bar.
     * @param   int       $xPos    The x-coordinate of the progress bar.
     * @param   int       $yPos    The y-coordinate of the progress bar.
     * @param   int|null  $width   The width of the progress bar.
     * @param   int|null  $height  The height of the progress bar.
     * @param   mixed     $style   The style for the progress bar.
     * @param   mixed     $params  Additional parameters for the progress bar.
     *
     * @return array An array containing the control ID and object.
     */
    public function createProgressBar($parent, $max, $xPos, $yPos, $width = null, $height = null, $style = null, $params = null)
    {
        global $bearsamppLang;

        $width       = $width == null ? 200 : $width;
        $height      = $height == null ? 15 : $height;
        $progressBar = $this->createControl( $parent, Gauge, $bearsamppLang->getValue( Lang::LOADING ), $xPos, $yPos, $width, $height, $style, $params );

        $this->setRange( $progressBar[self::CTRL_OBJ], 0, $max );
        $this->gauge[$progressBar[self::CTRL_OBJ]] = 0;

        return $progressBar;
    }

    /**
     * Increments the value of a progress bar.
     *
     * @param   array  $progressBar  The progress bar control.
     */
    public function incrProgressBar($progressBar)
    {
        $this->setProgressBarValue( $progressBar, self::INCR_PROGRESS_BAR );
    }

    /**
     * Resets the value of a progress bar to zero.
     *
     * @param   array  $progressBar  The progress bar control.
     */
    public function resetProgressBar($progressBar)
    {
        $this->setProgressBarValue( $progressBar, 0 );
    }

    /**
     * Sets the value of a progress bar.
     *
     * @param   array  $progressBar  The progress bar control.
     * @param   mixed  $value        The value to set.
     */
    public function setProgressBarValue($progressBar, $value)
    {
        if ( $progressBar != null && isset( $progressBar[self::CTRL_OBJ] ) && isset( $this->gauge[$progressBar[self::CTRL_OBJ]] ) ) {
            if ( strval( $value ) == self::INCR_PROGRESS_BAR ) {
                $value = $this->gauge[$progressBar[self::CTRL_OBJ]] + 1;
            }
            if ( is_numeric( $value ) ) {
                $this->gauge[$progressBar[self::CTRL_OBJ]] = $value;
                $this->setValue( $progressBar[self::CTRL_OBJ], $value );
            }
        }
    }

    /**
     * Sets the maximum value of a progress bar.
     *
     * @param   array  $progressBar  The progress bar control.
     * @param   int    $max          The maximum value to set.
     */
    public function setProgressBarMax($progressBar, $max)
    {
        $this->setRange( $progressBar[self::CTRL_OBJ], 0, $max );
    }

    /**
     * Displays a message box.
     *
     * @param   string       $message  The message to display.
     * @param   int          $type     The type of message box.
     * @param   string|null  $title    The title of the message box.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBox($message, $type, $title = null)
    {
        global $bearsamppCore;

        $message    = str_replace( self::NEW_LINE, PHP_EOL, $message );
        $messageBox = $this->callWinBinder( 'wb_message_box', array(
            null, strlen( $message ) < 64 ? str_pad( $message, 64 ) : $message, // Pad message to display entire title
            $title == null ? $this->defaultTitle : $this->defaultTitle . ' - ' . $title, $type
        ) );

        // TODO why does this create an error?
        // Set tiny window icon
        $this->setImage( $messageBox, $bearsamppCore->getResourcesPath() . '/homepage/img/icons/app.ico' );

        return $messageBox;
    }

    /**
     * Displays an informational message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxInfo($message, $title = null)
    {
        return $this->messageBox( $message, self::BOX_INFO, $title );
    }

    /**
     * Displays an OK message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxOk($message, $title = null)
    {
        return $this->messageBox( $message, self::BOX_OK, $title );
    }

    /**
     * Displays an OK/Cancel message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxOkCancel($message, $title = null)
    {
        return $this->messageBox( $message, self::BOX_OKCANCEL, $title );
    }

    /**
     * Displays a question message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxQuestion($message, $title = null)
    {
        return $this->messageBox( $message, self::BOX_QUESTION, $title );
    }

    /**
     * Displays an error message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxError($message, $title = null)
    {
        return $this->messageBox( $message, self::BOX_ERROR, $title );
    }

    /**
     * Displays a warning message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxWarning($message, $title = null)
    {
        return $this->messageBox( $message, self::BOX_WARNING, $title );
    }

    /**
     * Displays a Yes/No message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxYesNo($message, $title = null)
    {
        return $this->messageBox( $message, self::BOX_YESNO, $title );
    }

    /**
     * Displays a Yes/No/Cancel message box.
     *
     * @param   string       $message  The message to display.
     * @param   string|null  $title    The title of the message box. If null, the default title will be used.
     *
     * @return mixed The result of the message box operation.
     */
    public function messageBoxYesNoCancel($message, $title = null)
    {
        return $this->messageBox( $message, self::BOX_YESNOCANCEL, $title );
    }

}

/**
 * Event handler for WinBinder events.
 *
 * This function is called by WinBinder when an event occurs. It retrieves the callback
 * associated with the window and executes it. If a timer is associated with the callback,
 * the timer is destroyed before executing the callback.
 *
 * @param   mixed  $window  The window object where the event occurred.
 * @param   int    $id      The ID of the event.
 * @param   mixed  $ctrl    The control that triggered the event.
 * @param   mixed  $param1  The first parameter of the event.
 * @param   mixed  $param2  The second parameter of the event.
 */
function __winbinderEventHandler($window, $id, $ctrl, $param1, $param2)
{
    global $bearsamppWinbinder;

    if ( $bearsamppWinbinder->callback[$window][2] != null ) {
        $bearsamppWinbinder->destroyTimer( $window, $bearsamppWinbinder->callback[$window][2][0] );
    }

    call_user_func_array(
        array($bearsamppWinbinder->callback[$window][0], $bearsamppWinbinder->callback[$window][1]),
        array($window, $id, $ctrl, $param1, $param2)
    );
}
