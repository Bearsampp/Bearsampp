
<?php

/*******************************************************************************

WINBINDER - The native Windows binding for PHP for PHP

Copyright � Hypervisual - see LICENSE.TXT for details
Author: Rubem Pechansky (http://winbinder.org/contact.php)

RC file parser: convert Windows resource file to WinBinder commands

 *******************************************************************************/

// so this file will not be necessary in the future

//-------------------------------------------------------------------- CONSTANTS

// Define constants as class constants for better organization and performance
class WB_Resources {
    // Screen constants
    const KX_SCREEN = 1.498; // Determined through trial and error
    const KY_SCREEN = 1.625; // Determined through trial and error

    // Windows constants from WIN.H
    const WS_VISIBLE = 0x10000000;
    const WS_DISABLED = 0x08000000;
    const WS_GROUP = 0x00020000;
    const WS_EX_STATICEDGE = 0x00020000;

    // Button styles
    const BS_PUSHBUTTON = 0x00;
    const BS_CHECKBOX = 0x02;
    const BS_AUTOCHECKBOX = 0x03;
    const BS_RADIOBUTTON = 0x04;
    const BS_GROUPBOX = 0x07;
    const BS_AUTORADIOBUTTON = 0x09;
    const BS_ICON = 0x40;
    const BS_BITMAP = 0x80;

    // Edit control styles
    const ES_NUMBER = 0x2000;
    const ES_PASSWORD = 0x20;
    const ES_READONLY = 0x0800;
    const ES_UPPERCASE = 0x08;
    const ES_LEFT = 0x0;
    const ES_CENTER = 0x01;
    const ES_RIGHT = 0x02;
    const ES_MULTILINE = 0x04;

    // Static styles
    const SS_LEFT = 0x00;
    const SS_CENTER = 0x01;
    const SS_RIGHT = 0x02;
    const SS_ETCHEDHORZ = 0x10;
    const SS_ETCHEDVERT = 0x11;
    const SS_ETCHEDFRAME = 0x12;
    const SS_ICON = 0x03;
    const SS_BITMAP = 0x0E;
    const SS_LEFTNOWORDWRAP = 0x0C;
    const SS_WORDELLIPSIS = 0xC000;

    // Other styles
    const CBS_SORT = 0x100;
    const CBS_DROPDOWNLIST = 3;
    const LBS_SORT = 2;
    const LVS_NOSORTHEADER = 0x00008000;
    const LVS_GRIDLINES = 0x00800000;    // Actually WS_BORDER
    const LVS_CHECKBOXES = 0x00000800;   // Actually LVS_ALIGNLEFT
    const LVS_SINGLESEL = 0x00000004;
    const TBS_AUTOTICKS = 1;
}

// For backward compatibility, define the constants in the global namespace
// This ensures existing code that uses these constants will continue to work
define("WB_KX_SCREEN", WB_Resources::KX_SCREEN);
define("WB_KY_SCREEN", WB_Resources::KY_SCREEN);

define("WS_VISIBLE", WB_Resources::WS_VISIBLE);
define("WS_DISABLED", WB_Resources::WS_DISABLED);
define("WS_GROUP", WB_Resources::WS_GROUP);
define("WS_EX_STATICEDGE", WB_Resources::WS_EX_STATICEDGE);

define("BS_PUSHBUTTON", WB_Resources::BS_PUSHBUTTON);
define("BS_CHECKBOX", WB_Resources::BS_CHECKBOX);
define("BS_AUTOCHECKBOX", WB_Resources::BS_AUTOCHECKBOX);
define("BS_RADIOBUTTON", WB_Resources::BS_RADIOBUTTON);
define("BS_GROUPBOX", WB_Resources::BS_GROUPBOX);
define("BS_AUTORADIOBUTTON", WB_Resources::BS_AUTORADIOBUTTON);
define("BS_ICON", WB_Resources::BS_ICON);
define("BS_BITMAP", WB_Resources::BS_BITMAP);

define("ES_NUMBER", WB_Resources::ES_NUMBER);
define("ES_PASSWORD", WB_Resources::ES_PASSWORD);
define("ES_READONLY", WB_Resources::ES_READONLY);
define("ES_UPPERCASE", WB_Resources::ES_UPPERCASE);
define("ES_LEFT", WB_Resources::ES_LEFT);
define("ES_CENTER", WB_Resources::ES_CENTER);
define("ES_RIGHT", WB_Resources::ES_RIGHT);
define("ES_MULTILINE", WB_Resources::ES_MULTILINE);

define("SS_LEFT", WB_Resources::SS_LEFT);
define("SS_CENTER", WB_Resources::SS_CENTER);
define("SS_RIGHT", WB_Resources::SS_RIGHT);
define("SS_ETCHEDHORZ", WB_Resources::SS_ETCHEDHORZ);
define("SS_ETCHEDVERT", WB_Resources::SS_ETCHEDVERT);
define("SS_ETCHEDFRAME", WB_Resources::SS_ETCHEDFRAME);
define("SS_ICON", WB_Resources::SS_ICON);
define("SS_BITMAP", WB_Resources::SS_BITMAP);
define("SS_LEFTNOWORDWRAP", WB_Resources::SS_LEFTNOWORDWRAP);
define("SS_WORDELLIPSIS", WB_Resources::SS_WORDELLIPSIS);

define("CBS_SORT", WB_Resources::CBS_SORT);
define("CBS_DROPDOWNLIST", WB_Resources::CBS_DROPDOWNLIST);
define("LBS_SORT", WB_Resources::LBS_SORT);
define("LVS_NOSORTHEADER", WB_Resources::LVS_NOSORTHEADER);
define("LVS_GRIDLINES", WB_Resources::LVS_GRIDLINES);
define("LVS_CHECKBOXES", WB_Resources::LVS_CHECKBOXES);
define("LVS_SINGLESEL", WB_Resources::LVS_SINGLESEL);
define("TBS_AUTOTICKS", WB_Resources::TBS_AUTOTICKS);

//-------------------------------------------------------------------- FUNCTIONS

/*

  Returns the WinBinder code that results from the resource text $rc, usually
   read from a RC (Windows resource) file.

NOTE: Caption is not used, it's taken from the resource instead. The parameter is kept
      here just to be compatible with wb_create_window()
*/

function parse_rc($rc, $winvar='$mainwin', $parent=null, $type="AppWindow", $caption=null,
                  $x=WBC_CENTER, $y=WBC_CENTER, $width=WBC_CENTER, $height=WBC_CENTER, $style=0, $lparam=0,
                  $respath=PATH_RES)
{
    global $_winclass, $_usergeom, $path_res;

    // Read file

    $_usergeom = array($x, $y, $width, $height);
    $path_res = $respath;

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

    $tok = "\s*((?:[\"'][\S \t]*[\"'])|(?:[\S^,'\"]+))\s*";	// Normal or quoted token
    $rc = "// Create window\n\n" . preg_replace_callback("/^$tok\s+DIALOGEX$tok,$tok,$tok,$tok\s+CAPTION$tok\s+FONT$tok,$tok\s+STYLE$tok\s+EXSTYLE$tok/m", "_scale_dialog", $rc);

    // Create the controls

    $rc = preg_replace_callback("/^\s*CONTROL\s+$tok,$tok,$tok,$tok,$tok,$tok,$tok,$tok,$tok/m", "_scale_controls", $rc);

    // Create BEGIN / END comments

    $rc = preg_replace("/^\s*BEGIN/m", "\n// Insert controls\n", $rc);
    $rc = preg_replace("/^\s*END/m", "\n// End controls", $rc);

    // Replace variable names

    $rc = str_replace("%WINVAR%", $winvar, $rc);
    $rc = str_replace("%PARENT%", $parent? $parent : "NULL", $rc);
    $rc = str_replace("%STYLE%",  $style, $rc);
    $rc = str_replace("%LPARAM%", $lparam, $rc);

    return "$def\n$rc";
}

//----------------------------------------------------------- INTERNAL FUNCTIONS

function _scale_dialog($c)
{
    global $_winclass, $_usergeom, $_tabN;

    if($_winclass == "TabControl") {

        $_tabN++;
        $code = "wbtemp_create_item(%PARENT%, ". $c[6] . ");\n";

    } else {

        $_addx = 8;					//width + 2xborder
        $_addy = 4 + 42 + 17 + 4;	//border + caption + border

        switch(is_string($_winclass) ? strtolower($_winclass) : $_winclass) {

            case "appwindow":
                $_winclass = AppWindow;
                $_addx = 8;					//width + 2xborder
                $_addy = 3 + 18 + 22 + 18 + 3;	//border + caption + menu + statusbar + border
                break;
            case "resizablewindow":
                $_winclass = ResizableWindow;
                $_addx = 8;					//width + 2xborder
                $_addy = 4 + 42 + 17 + 4;	//border + caption + menu + statusbar + border
                break;
            case "modaldialog":
                $_winclass = ModalDialog;
                $_addx = 8;					//width + 2xborder
                $_addy = 4 + 42 + 17 + 4;	//border + caption + border
                break;
            case "modelessdialog":
                $_winclass = ModelessDialog;
                break;
            case "tooldialog":
                $_winclass = ToolDialog;
                break;
        }

        if(!(($_usergeom[0] == WBC_CENTER && $_usergeom[1] == WBC_CENTER &&
            $_usergeom[2] == WBC_CENTER && $_usergeom[3] == WBC_CENTER))) {

            $code = "%WINVAR% = wb_create_window(" .
                "%PARENT%, " .								// parent
                "$_winclass, " .							// class
                $c[6] . ", " .								// caption
                $_usergeom[0] . ", " .						// left
                $_usergeom[1] . ", " .						// top
                $_usergeom[2] . ", " .						// width
                $_usergeom[3] . ", " .						// height
                "%STYLE%, " . 								// style
                "%LPARAM%);\n";								// lparam

        } else {

            if(is_array($_usergeom)) {
                if(count($_usergeom) == 2) {		// Width, height only
                    $_usergeom[2] = $_usergeom[0];
                    $_usergeom[3] = $_usergeom[1];
                    $_usergeom[0] = WBC_CENTER;
                    $_usergeom[1] = WBC_CENTER;
                }
            } elseif(is_null($_usergeom)) {
                $_usergeom[0] = WBC_DEFAULTPOS;
                $_usergeom[1] = WBC_DEFAULTPOS;
                $_usergeom[2] = WBC_DEFAULTPOS;
                $_usergeom[3] = WBC_DEFAULTPOS;
            }

            $code = "%WINVAR% = wb_create_window(" .
                "%PARENT%, " .								// parent
                "$_winclass, " .							// class
                $c[6] . ", " .								// caption
                "WBC_CENTER, " .							// left
                "WBC_CENTER, " .							// top
//			(int)($c[4] * WB_KX_SCREEN + $_addx) . ", " .
//			(int)($c[5] * WB_KY_SCREEN + $_addy) . ", " .
                (int)($c[4] * WB_Resources::KX_SCREEN) . ", " .
                (int)($c[5] * WB_Resources::KY_SCREEN) . ", " .
                "%STYLE%, " .								// style
                "%LPARAM%);\n";								// lparam
        }

        $_tabN = 0;

    }
    return $code;
}

function _scale_controls($c)
{
    global $_tabN, $path_res;

    $winclass = $c[3];
    $winstyle = hexdec($c[4]);
    $winexstyle = hexdec($c[9]);

    if(_bit_test($winstyle, WB_Resources::WS_VISIBLE))
        $style = "WBC_VISIBLE";
    else
        $style = "WBC_INVISIBLE";

    if(_bit_test($winstyle, WB_Resources::WS_DISABLED))
        $style .= " | WBC_DISABLED";
    else
        $style .= " | WBC_ENABLED";

    if(_bit_test($winexstyle, WB_Resources::WS_EX_STATICEDGE))
        $style .= " | WBC_BORDER";

    // Set attributes according to control class

    switch(strtolower($winclass)) {

        case '"button"':

            switch($winstyle & 0x0F) {
                case WB_Resources::BS_AUTORADIOBUTTON:
                case WB_Resources::BS_RADIOBUTTON:
                    $class = "RadioButton";
                    if(_bit_test($winstyle, WB_Resources::WS_GROUP))
                        $style .= " | WBC_GROUP";
                    break;
                case WB_Resources::BS_AUTOCHECKBOX:
                case WB_Resources::BS_CHECKBOX:
                    $class = "CheckBox";
                    break;
                case WB_Resources::BS_GROUPBOX:
                    $class = "Frame";
                    break;
                case WB_Resources::BS_PUSHBUTTON:
                default:
                    $class = "PushButton";
                    break;
            }
            break;

        case '"static"':

            switch($winstyle & 0x1F) {
                case WB_Resources::SS_ICON:
                case WB_Resources::SS_BITMAP:
                    $style .= " | WBC_IMAGE | WBC_CENTER";
                    $class = "Frame";
                    break;
                case WB_Resources::SS_ETCHEDHORZ:
                case WB_Resources::SS_ETCHEDVERT:
                case WB_Resources::SS_ETCHEDFRAME:
                    $class = "Frame";
                    break;
                case WB_Resources::SS_CENTER:
                    if(_bit_test($winstyle, WB_Resources::SS_WORDELLIPSIS))
                        $style .= " | WBC_ELLIPSIS";
                    $style .= " | WBC_CENTER";
                    $class = "Label";
                    break;
                case WB_Resources::SS_RIGHT:
                    if(_bit_test($winstyle, WB_Resources::SS_WORDELLIPSIS))
                        $style .= " | WBC_ELLIPSIS";
                    $style .= " | WBC_RIGHT";
                    $class = "Label";
                    break;
                case WB_Resources::SS_LEFT:
                default:
                    if(!_bit_test($winstyle, WB_Resources::SS_LEFTNOWORDWRAP))
                        $style .= " | WBC_MULTILINE";
                    if(_bit_test($winstyle, WB_Resources::SS_WORDELLIPSIS))
                        $style .= " | WBC_ELLIPSIS";
                    $class = "Label";
                    break;
            }
            break;

        case '"edit"':
            $class = "EditBox";
            if(_bit_test($winstyle, WB_Resources::ES_MULTILINE)) {
                $style .= " | WBC_MULTILINE";
            } else {
                switch($winstyle & 0x03) {
                    case WB_Resources::ES_CENTER:
                        $style .= " | WBC_CENTER";
                        break;
                    case WB_Resources::ES_RIGHT:
                        $style .= " | WBC_RIGHT";
                        break;
                    case WB_Resources::ES_LEFT:
                    default:
                        break;
                }
            }
            if(_bit_test($winstyle, WB_Resources::ES_READONLY))
                $style .= " | WBC_READONLY";
            if(_bit_test($winstyle, WB_Resources::ES_PASSWORD))
                $style .= " | WBC_MASKED";
            if(_bit_test($winstyle, WB_Resources::ES_NUMBER))
                $style .= " | WBC_NUMBER";
            break;

        case '"richedit20a"':
            if(_bit_test($winstyle, WB_Resources::ES_READONLY))
                $style .= " | WBC_READONLY";
            $class = "RTFEditBox";
            switch($winstyle & 0x03) {
                case WB_Resources::ES_CENTER:
                    $style .= " | WBC_CENTER";
                    break;
                case WB_Resources::ES_RIGHT:
                    $style .= " | WBC_RIGHT";
                    break;
                case WB_Resources::ES_LEFT:
                default:
                    break;
            }
            break;

        case '"combobox"':
            $class = "ComboBox";
            if(_bit_test($winstyle, WB_Resources::CBS_SORT))
                $style .= " | WBC_SORT";
            if(_bit_test($winstyle, WB_Resources::CBS_DROPDOWNLIST))
                $style .= " | WBC_READONLY";
            break;

        case '"listbox"':
            $class = "ListBox";
            if(_bit_test($winstyle, WB_Resources::LBS_SORT))
                $style .= " | WBC_SORT";
            break;

        case '"scrollbar"':
            $class = "ScrollBar";
            break;

        case '"syslistview32"':
            $class = "ListView";
            if(!_bit_test($winstyle, WB_Resources::LVS_NOSORTHEADER))
                $style .= " | WBC_SORT";
            if(_bit_test($winstyle, WB_Resources::LVS_GRIDLINES))
                $style .= " | WBC_LINES";
            if(_bit_test($winstyle, WB_Resources::LVS_CHECKBOXES))
                $style .= " | WBC_CHECKBOXES";
            if(!_bit_test($winstyle, WB_Resources::LVS_SINGLESEL))
                $style .= " | WBC_SINGLE";
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
            if(_bit_test($winstyle, WB_Resources::TBS_AUTOTICKS))
                $style .= " | WBC_LINES";
            break;

        case '"msctls_updown32"':
            $class = "Spinner";
            if(_bit_test($winstyle, WB_Resources::WS_GROUP))
                $style .= " | WBC_GROUP";
            break;
    }

    // Convert Windows style to WinBinder style

    $str = "wb_create_control(" .
        "%WINVAR%, " .								// Parent
        $class . ", " . 							// Class
        $c[1] . ", " .								// Caption
        (int)($c[5] * WB_Resources::KX_SCREEN) . ", " .		// Left
        (int)($c[6] * WB_Resources::KY_SCREEN) . ", " .		// Top
        (int)($c[7] * WB_Resources::KX_SCREEN) . ", " .		// Width
        (int)($c[8] * WB_Resources::KY_SCREEN) . ", " .		// Height
        $c[2] . ", " . 								// ID
        $style . ", " .			 					// Style
        "0" .										// Param
        ($_tabN ? ", " . ($_tabN - 1) . ");\n" : ");\n");	// Tab #

    // Add some attributes to controls where needed

    switch($class) {

        case "Frame":

            if(strstr($style, "WBC_IMAGE")) {
                if(($winstyle & (WB_Resources::SS_BITMAP | WB_Resources::SS_ICON)) && ($c[1] !== '""')) {
                    $image = $path_res . _trim_quotes($c[1]);
                    if(preg_match("/\.(bmp|ico)$/", $image))
                        $str = "\$_tmp_ctrl_ = " . $str . "wb_set_image(\$_tmp_ctrl_, '$image', GREEN);" . " unset(\$_tmp_ctrl_);\n";
                }
            }
            break;

        case "PushButton":

            if(($winstyle & (WB_Resources::BS_BITMAP | WB_Resources::BS_ICON)) && ($c[1] !== '""')) {
                $image = $path_res . _trim_quotes($c[1]);
                if($image)
                    if(preg_match("/\.(bmp|ico)$/", $image))
                        $str = "\$_tmp_ctrl_ = " . $str . "wb_set_image(\$_tmp_ctrl_, '$image', GREEN);" . " unset(\$_tmp_ctrl_);\n";
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

//-------------------------------------------------------------------------- END

?>
