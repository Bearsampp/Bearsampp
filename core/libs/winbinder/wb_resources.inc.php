<?php

/*******************************************************************************
 *
 * WINBINDER - The native Windows binding for PHP for PHP
 *
 * Copyright Hypervisual - see LICENSE.TXT for details
 * Author: Rubem Pechansky (https://github.com/crispy-computing-machine/Winbinder)
 *
 * RC file parser: convert Windows resource file to WinBinder commands
 *******************************************************************************/

// TODO: These functions will be replaced by the visual layout editor
// so this file will not be necessary in the future

//-------------------------------------------------------------------- CONSTANTS

if (!defined("WB_KX_SCREEN")) {
    define("WB_KX_SCREEN", 1.498);
} // Determined through trial and error
if (!defined("WB_KY_SCREEN")) {
    define("WB_KY_SCREEN", 1.625);
} // Determined through trial and error

//------------------------------------------------- WINDOWS CONSTANTS FROM WIN.H

if (!defined("WS_VISIBLE")) {
    define("WS_VISIBLE", 0x10000000);
}
if (!defined("WS_DISABLED")) {
    define("WS_DISABLED", 0x08000000);
}
if (!defined("WS_GROUP")) {
    define("WS_GROUP", 0x00020000);
}
if (!defined("WS_EX_STATICEDGE")) {
    define("WS_EX_STATICEDGE", 0x00020000);
}
if (!defined('BM_SETCHECK')) {
    define('BM_SETCHECK', 241);
}
if (!defined('LVM_FIRST')) {
    define('LVM_FIRST', 0x1000);
}
if (!defined('LVM_DELETEALLITEMS')) {
    define('LVM_DELETEALLITEMS', (LVM_FIRST + 9));
}
if (!defined('LVM_GETITEMCOUNT')) {
    define('LVM_GETITEMCOUNT', (LVM_FIRST + 4));
}
if (!defined('LVM_GETITEMSTATE')) {
    define('LVM_GETITEMSTATE', (LVM_FIRST + 44));
}
if (!defined('LVM_GETSELECTEDCOUNT')) {
    define('LVM_GETSELECTEDCOUNT', (LVM_FIRST + 50));
}
if (!defined('LVIS_SELECTED')) {
    define('LVIS_SELECTED', 2);
}
if (!defined('TCM_GETCURSEL')) {
    define('TCM_GETCURSEL', 4875);
}
if (!defined('CB_FINDSTRINGEXACT')) {
    define('CB_FINDSTRINGEXACT', 344);
}
if (!defined('CB_SETCURSEL')) {
    define('CB_SETCURSEL', 334);
}
if (!defined('LB_FINDSTRINGEXACT')) {
    define('LB_FINDSTRINGEXACT', 418);
}
if (!defined('LB_SETCURSEL')) {
    define('LB_SETCURSEL', 390);
}
if (!defined('TCM_SETCURSEL')) {
    define('TCM_SETCURSEL', 4876);
}
if (!defined('WM_SETTEXT')) {
    define('WM_SETTEXT', 12);
}


// Button styles

if (!defined("BS_PUSHBUTTON")) {
    define("BS_PUSHBUTTON", 0x00);
}
if (!defined("BS_CHECKBOX")) {
    define("BS_CHECKBOX", 0x02);
}
if (!defined("BS_AUTOCHECKBOX")) {
    define("BS_AUTOCHECKBOX", 0x03);
}
if (!defined("BS_RADIOBUTTON")) {
    define("BS_RADIOBUTTON", 0x04);
}
if (!defined("BS_GROUPBOX")) {
    define("BS_GROUPBOX", 0x07);
}
if (!defined("BS_AUTORADIOBUTTON")) {
    define("BS_AUTORADIOBUTTON", 0x09);
}
if (!defined("BS_ICON")) {
    define("BS_ICON", 0x40);
}
if (!defined("BS_BITMAP")) {
    define("BS_BITMAP", 0x80);
}

// Edit control styles

if (!defined("ES_NUMBER")) {
    define("ES_NUMBER", 0x2000);
}
if (!defined("ES_PASSWORD")) {
    define("ES_PASSWORD", 0x20);
}
if (!defined("ES_READONLY")) {
    define("ES_READONLY", 0x0800);
}
if (!defined("ES_UPPERCASE")) {
    define("ES_UPPERCASE", 0x08);
}
if (!defined("ES_LEFT")) {
    define("ES_LEFT", 0x0);
}
if (!defined("ES_CENTER")) {
    define("ES_CENTER", 0x01);
}
if (!defined("ES_RIGHT")) {
    define("ES_RIGHT", 0x02);
}
if (!defined("ES_MULTILINE")) {
    define("ES_MULTILINE", 0x04);
}

// Static styles

if (!defined("SS_LEFT")) {
    define("SS_LEFT", 0x00);
}
if (!defined("SS_CENTER")) {
    define("SS_CENTER", 0x01);
}
if (!defined("SS_RIGHT")) {
    define("SS_RIGHT", 0x02);
}
if (!defined("SS_ETCHEDHORZ")) {
    define("SS_ETCHEDHORZ", 0x10);
}
if (!defined("SS_ETCHEDVERT")) {
    define("SS_ETCHEDVERT", 0x11);
}
if (!defined("SS_ETCHEDFRAME")) {
    define("SS_ETCHEDFRAME", 0x12);
}
if (!defined("SS_ICON")) {
    define("SS_ICON", 0x03);
}
if (!defined("SS_BITMAP")) {
    define("SS_BITMAP", 0x0E);
}
if (!defined("SS_LEFTNOWORDWRAP")) {
    define("SS_LEFTNOWORDWRAP", 0x0C);
}
if (!defined("SS_WORDELLIPSIS")) {
    define("SS_WORDELLIPSIS", 0xC000);
}

// Other styles

if (!defined("CBS_SORT")) {
    define("CBS_SORT", 0x100);
}
if (!defined("CBS_DROPDOWNLIST")) {
    define("CBS_DROPDOWNLIST", 3);
}

if (!defined("LBS_SORT")) {
    define("LBS_SORT", 2);
}
if (!defined("LVS_NOSORTHEADER")) {
    define("LVS_NOSORTHEADER", 0x00008000);
}
if (!defined("LVS_GRIDLINES")) {
    define("LVS_GRIDLINES", 0x00800000);
}    // Actually WS_BORDER
if (!defined("LVS_CHECKBOXES")) {
    define("LVS_CHECKBOXES", 0x00000800);
}    // Actually LVS_ALIGNLEFT
if (!defined("LVS_SINGLESEL")) {
    define("LVS_SINGLESEL", 0x00000004);
}
if (!defined("TBS_AUTOTICKS")) {
    define("TBS_AUTOTICKS", 1);
}

//-------------------------------------------------------------------- FUNCTIONS

/*

  Returns the WinBinder code that results from the resource text $rc, usually
   read from a RC (Windows resource) file.

TODO: Extend support to several RC generators (currently supports WinAsm Studio only)
NOTE: Caption is not used, it's taken from the resource instead. The parameter is kept
      here just to be compatible with wb_create_window()
*/

function parse_rc(
    $rc,
    $winvar = '$mainwin',
    $parent = 0,
    $type = "AppWindow",
    $caption = "",
    $x = WBC_CENTER,
    $y = WBC_CENTER,
    $width = WBC_CENTER,
    $height = WBC_CENTER,
    $style = 0,
    $lparam = 0,
    $respath = PATH_RES
) {
    global $_winclass, $_usergeom, $path_res;

    // Read file

    $_usergeom = array($x, $y, $width, $height);
    $path_res  = $respath;

    // Remove comments and useless spaces

    $rc = preg_replace("/^\s*;.*$/m", "", $rc);
    $rc = preg_replace("/^\s*(.*)$/m", "\\1", $rc);

    // Maintain #defines and discard the rest (fixed to work with newer versions of PHP -- thanks Hans)

//	$def = preg_replace("/(?!^\s*#define)(.*)$/m", "\\2", $rc);
    $def = preg_replace('/^((?!#define).)*$/m', "\\2", $rc);

    // Remove blank lines

    $def = preg_replace("/\n+/m", "\n", $def);

    // Change string C #defines to PHP format

    $def = preg_replace("/#define\s+(\w+)\s+\"(.*)\"/", "if(!defined(\"\\1\")) define(\"\\1\", \"\\2\");", $def);

    // Change character C #defines to PHP format

    $def = preg_replace("/#define\s+(\w+)\s+'(.+)'/", "if(!defined(\"\\1\")) define(\"\\1\", \"\\2\");", $def);

    // Change numeric C #defines to PHP format

    $def = preg_replace("/#define\s+(\w+)\s+(\S+)/", "if(!defined(\"\\1\")) define(\"\\1\", \\2);", $def);
    $def = "// Control identifiers\n\n" . preg_replace("/(\r\n|\r|\n)+/sm", "\n", $def);

    // Return to original string and eliminates the #defines

    $rc = preg_replace("/^\s*#define(.*)$/m", "", $rc);

    // Create the window

    $_winclass = $type;

    $tok = "\s*((?:[\"'][\S \t]*[\"'])|(?:[\S^,'\"]+))\s*";    // Normal or quoted token
    $rc  = "// Create window\n\n" . preg_replace_callback("/^$tok\s+DIALOGEX$tok,$tok,$tok,$tok\s+CAPTION$tok\s+FONT$tok,$tok\s+STYLE$tok\s+EXSTYLE$tok/m", "_scale_dialog", $rc);

    // Create the controls

    $rc = preg_replace_callback("/^\s*CONTROL\s+$tok,$tok,$tok,$tok,$tok,$tok,$tok,$tok,$tok/m", "_scale_controls", $rc);

    // Create BEGIN / END comments

    $rc = preg_replace("/^\s*BEGIN/m", "\n// Insert controls\n", $rc);
    $rc = preg_replace("/^\s*END/m", "\n// End controls", $rc);

    // Replace variable names

    $rc = str_replace("%WINVAR%", $winvar, $rc);
    $rc = str_replace("%PARENT%", $parent ? $parent : "0", $rc);
    $rc = str_replace("%STYLE%", $style, $rc);
    $rc = str_replace("%LPARAM%", $lparam, $rc);

    return "$def\n$rc";
}

//----------------------------------------------------------- INTERNAL FUNCTIONS

function _scale_dialog($c)
{
    global $_winclass, $_usergeom, $_tabN;

    if ($_winclass == "TabControl") {
        $_tabN++;
        $code = "wb_create_item(%PARENT%, " . $c[6] . ");\n";
    } else {
        $_addx = 8;                    //width + 2xborder
        $_addy = 4 + 42 + 17 + 4;    //border + caption + border

        switch (is_string($_winclass) ? strtolower($_winclass) : $_winclass) {
            case "appwindow":
                $_winclass = AppWindow;
                $_addx     = 8;                    //width + 2xborder
                $_addy     = 3 + 18 + 22 + 18 + 3;    //border + caption + menu + statusbar + border
                break;
            case "resizablewindow":
                $_winclass = ResizableWindow;
                $_addx     = 8;                    //width + 2xborder
                $_addy     = 4 + 42 + 17 + 4;    //border + caption + menu + statusbar + border
                break;
            case "modaldialog":
                $_winclass = ModalDialog;
                $_addx     = 8;                    //width + 2xborder
                $_addy     = 4 + 42 + 17 + 4;    //border + caption + border
                break;
            case "modelessdialog":
                $_winclass = ModelessDialog;
                break;
            case "tooldialog":
                $_winclass = ToolDialog;
                break;
        }

        if (!(($_usergeom[0] == WBC_CENTER && $_usergeom[1] == WBC_CENTER &&
            $_usergeom[2] == WBC_CENTER && $_usergeom[3] == WBC_CENTER))) {
            $code = "%WINVAR% = wb_create_window(" .
                "%PARENT%, " .                                // parent
                "$_winclass, " .                            // class
                $c[6] . ", " .                                // caption
                $_usergeom[0] . ", " .                        // left
                $_usergeom[1] . ", " .                        // top
                $_usergeom[2] . ", " .                        // width
                $_usergeom[3] . ", " .                        // height
                "%STYLE%, " .                                // style
                "%LPARAM%);\n";                                // lparam

        } else {
            if (is_array($_usergeom)) {
                if (count($_usergeom) == 2) {        // Width, height only
                    $_usergeom[2] = $_usergeom[0];
                    $_usergeom[3] = $_usergeom[1];
                    $_usergeom[0] = WBC_CENTER;
                    $_usergeom[1] = WBC_CENTER;
                }
            } elseif (is_null($_usergeom)) {
                $_usergeom[0] = WBC_DEFAULTPOS;
                $_usergeom[1] = WBC_DEFAULTPOS;
                $_usergeom[2] = WBC_DEFAULTPOS;
                $_usergeom[3] = WBC_DEFAULTPOS;
            }

            $code = "%WINVAR% = wb_create_window(" .
                "%PARENT%, " .                                // parent
                "$_winclass, " .                            // class
                $c[6] . ", " .                                // caption
                "WBC_CENTER, " .                            // left
                "WBC_CENTER, " .                            // top
//			(int)($c[4] * WB_KX_SCREEN + $_addx) . ", " .
//			(int)($c[5] * WB_KY_SCREEN + $_addy) . ", " .
                (int)($c[4] * WB_KX_SCREEN) . ", " .
                (int)($c[5] * WB_KY_SCREEN) . ", " .
                "%STYLE%, " .                                // style
                "%LPARAM%);\n";                                // lparam
        }

        $_tabN = 0;
    }

    return $code;
}

function _scale_controls($c)
{
    global $_tabN, $path_res;

    $winclass   = $c[3];
    $winstyle   = hexdec($c[4]);
    $winexstyle = hexdec($c[9]);

    if (_bit_test($winstyle, WS_VISIBLE)) {
        $style = "WBC_VISIBLE";
    } else {
        $style = "WBC_INVISIBLE";
    }

    if (_bit_test($winstyle, WS_DISABLED)) {
        $style .= " | WBC_DISABLED";
    } else {
        $style .= " | WBC_ENABLED";
    }

    if (_bit_test($winexstyle, WS_EX_STATICEDGE)) {
        $style .= " | WBC_BORDER";
    }

    // Set attributes according to control class

    switch (strtolower($winclass)) {
        case '"button"':

            switch ($winstyle & 0x0F) {
                case BS_AUTORADIOBUTTON:
                case BS_RADIOBUTTON:
                    $class = "RadioButton";
                    if (_bit_test($winstyle, WS_GROUP)) {
                        $style .= " | WBC_GROUP";
                    }
                    break;
                case BS_AUTOCHECKBOX:
                case BS_CHECKBOX:
                    $class = "CheckBox";
                    break;
                case BS_GROUPBOX:
                    $class = "Frame";
                    break;
                case BS_PUSHBUTTON:
                default:
                    $class = "PushButton";
                    break;
            }
            break;

        case '"static"':

            switch ($winstyle & 0x1F) {
                case SS_ICON:
                case SS_BITMAP:
                    $style .= " | WBC_IMAGE | WBC_CENTER";
                    $class = "Frame";
                    break;
                case SS_ETCHEDHORZ:
                case SS_ETCHEDVERT:
                case SS_ETCHEDFRAME:
                    $class = "Frame";
                    break;
                case SS_CENTER:
                    if (_bit_test($winstyle, SS_WORDELLIPSIS)) {
                        $style .= " | WBC_ELLIPSIS";
                    }
                    $style .= " | WBC_CENTER";
                    $class = "Label";
                    break;
                case SS_RIGHT:
                    if (_bit_test($winstyle, SS_WORDELLIPSIS)) {
                        $style .= " | WBC_ELLIPSIS";
                    }
                    $style .= " | WBC_RIGHT";
                    $class = "Label";
                    break;
                case SS_LEFT:
                default:
                    if (!_bit_test($winstyle, SS_LEFTNOWORDWRAP)) {
                        $style .= " | WBC_MULTILINE";
                    }
                    if (_bit_test($winstyle, SS_WORDELLIPSIS)) {
                        $style .= " | WBC_ELLIPSIS";
                    }
                    $class = "Label";
                    break;
            }
            break;

        case '"edit"':
            $class = "EditBox";
            if (_bit_test($winstyle, ES_MULTILINE)) {
                $style .= " | WBC_MULTILINE";
            } else {
                switch ($winstyle & 0x03) {
                    case ES_CENTER:
                        $style .= " | WBC_CENTER";
                        break;
                    case ES_RIGHT:
                        $style .= " | WBC_RIGHT";
                        break;
                    case ES_LEFT:
                    default:
                        break;
                }
            }
            if (_bit_test($winstyle, ES_READONLY)) {
                $style .= " | WBC_READONLY";
            }
            if (_bit_test($winstyle, ES_PASSWORD)) {
                $style .= " | WBC_MASKED";
            }
            if (_bit_test($winstyle, ES_NUMBER)) {
                $style .= " | WBC_NUMBER";
            }
            break;

        case '"richedit20a"':
            if (_bit_test($winstyle, ES_READONLY)) {
                $style .= " | WBC_READONLY";
            }
            $class = "RTFEditBox";
            switch ($winstyle & 0x03) {
                case ES_CENTER:
                    $style .= " | WBC_CENTER";
                    break;
                case ES_RIGHT:
                    $style .= " | WBC_RIGHT";
                    break;
                case ES_LEFT:
                default:
                    break;
            }
            break;

        case '"combobox"':
            $class = "ComboBox";
            if (_bit_test($winstyle, CBS_SORT)) {
                $style .= " | WBC_SORT";
            }
            if (_bit_test($winstyle, CBS_DROPDOWNLIST)) {
                $style .= " | WBC_READONLY";
            }
            break;

        case '"listbox"':
            $class = "ListBox";
            if (_bit_test($winstyle, LBS_SORT)) {
                $style .= " | WBC_SORT";
            }
            break;

        case '"scrollbar"':
            $class = "ScrollBar";
            break;

        case '"syslistview32"':
            $class = "ListView";
            if (!_bit_test($winstyle, LVS_NOSORTHEADER)) {
                $style .= " | WBC_SORT";
            }
            if (_bit_test($winstyle, LVS_GRIDLINES)) {
                $style .= " | WBC_LINES";
            }
            if (_bit_test($winstyle, LVS_CHECKBOXES)) {
                $style .= " | WBC_CHECKBOXES";
            }
            if (!_bit_test($winstyle, LVS_SINGLESEL)) {
                $style .= " | WBC_SINGLE";
            }
            break;

        case '"systabcontrol32"':
            $class = "TabControl";
            break;

        case '"systreeview32"':
            $class = "TreeView";
            break;

        case '"toolbarwindow32"':
            $class = "ToolBar";
            break;

        case '"msctls_progress32"':
            $class = "Gauge";
            break;

        case '"msctls_statusbar32"':
            $class = "StatusBar";
            break;

        case '"sysmonthcal32"':
            $class = "Calendar";
            break;

        case '"msctls_trackbar32"':
            $class = "Slider";
            if (_bit_test($winstyle, TBS_AUTOTICKS)) {
                $style .= " | WBC_LINES";
            }
            break;

        case '"msctls_updown32"':
            $class = "Spinner";
            if (_bit_test($winstyle, WS_GROUP)) {
                $style .= " | WBC_GROUP";
            }
            break;
    }

    // Convert Windows style to WinBinder style

    $str = "wb_create_control(" .
        "%WINVAR%, " .                                // Parent
        $class . ", " .                            // Class
        $c[1] . ", " .                                // Caption
        (int)($c[5] * WB_KX_SCREEN) . ", " .        // Left
        (int)($c[6] * WB_KY_SCREEN) . ", " .        // Top
        (int)($c[7] * WB_KX_SCREEN) . ", " .        // Width
        (int)($c[8] * WB_KY_SCREEN) . ", " .        // Height
        $c[2] . ", " .                                // ID
        $style . ", " .                                // Style
        "0" .                                        // Param
        ($_tabN ? ", " . ($_tabN - 1) . ");\n" : ");\n");    // Tab #

    // Add some attributes to controls where needed

    switch ($class) {
        case "Frame":

            if (strstr($style, "WBC_IMAGE")) {
                if (($winstyle & (SS_BITMAP | SS_ICON)) && ($c[1] !== '""')) {
                    $image = $path_res . _trim_quotes($c[1]);
                    if (preg_match("/\.(bmp|ico)$/", $image)) {
                        $str = "\$_tmp_ctrl_ = " . $str . "wb_set_image(\$_tmp_ctrl_, '$image', GREEN);" . " unset(\$_tmp_ctrl_);\n";
                    }
                }
            }
            break;

        case "PushButton":

            if (($winstyle & (BS_BITMAP | BS_ICON)) && ($c[1] !== '""')) {
                $image = $path_res . _trim_quotes($c[1]);
                if ($image) {
                    if (preg_match("/\.(bmp|ico)$/", $image)) {
                        $str = "\$_tmp_ctrl_ = " . $str . "wb_set_image(\$_tmp_ctrl_, '$image', GREEN);" . " unset(\$_tmp_ctrl_);\n";
                    }
                }
            }
            break;
    }

    return $str;
}

function _trim_quotes($str)
{
    return str_replace('"', '', $str);
}

function _bit_test($v, $t)
{
    return (($v & $t) == $t);
}
