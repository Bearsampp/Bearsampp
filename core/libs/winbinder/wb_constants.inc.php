<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (https://github.com/crispy-computing-machine/Winbinder)

 WinBinder constants

*******************************************************************************/

// Define WinBinder constants if the extension provides them
// If the extension is loaded, these will be defined by the extension itself
// This file provides fallback definitions for when constants are not available

if (!defined('WBC_CENTER')) {
    // Window positioning
    define('WBC_CENTER', 0xFFFF);
    define('WBC_TOP', 0x0001);
    define('WBC_BOTTOM', 0x0002);
    define('WBC_LEFT', 0x0004);
    define('WBC_RIGHT', 0x0008);
    
    // Window styles
    define('WBC_NOTIFY', 0x0010);
    define('WBC_READONLY', 0x0020);
    define('WBC_DISABLED', 0x0040);
    define('WBC_ENABLED', 0x0080);
    define('WBC_INVISIBLE', 0x0100);
    define('WBC_VISIBLE', 0x0200);
    define('WBC_BORDER', 0x0400);
    define('WBC_LINES', 0x0800);
    define('WBC_CHECKBOXES', 0x1000);
    define('WBC_SORT', 0x2000);
    define('WBC_MULTISELECT', 0x4000);
    define('WBC_GROUP', 0x8000);
    define('WBC_MASKED', 0x10000);
    define('WBC_NUMBER', 0x20000);
    define('WBC_AUTOREPEAT', 0x40000);
    define('WBC_CUSTOMDRAW', 0x80000);
    define('WBC_MINIMIZE', 0x100000);
    define('WBC_MAXIMIZE', 0x200000);
    define('WBC_RESIZE', 0x400000);
    define('WBC_TASKBAR', 0x800000);
    define('WBC_REDRAW', 0x1000000);
    define('WBC_GETFOCUS', 0x2000000);
    define('WBC_KEYDOWN', 0x4000000);
    define('WBC_KEYUP', 0x8000000);
    define('WBC_MOUSEDOWN', 0x10000000);
    define('WBC_MOUSEUP', 0x20000000);
    define('WBC_MOUSEMOVE', 0x40000000);
    define('WBC_DBLCLICK', 0x80000000);
    
    // Window classes
    define('AppWindow', 'AppWindow');
    define('NakedWindow', 'NakedWindow');
    define('ToolDialog', 'ToolDialog');
    define('ModelessDialog', 'ModelessDialog');
    define('ModalDialog', 'ModalDialog');
    define('PopupWindow', 'PopupWindow');
    define('ResizableWindow', 'ResizableWindow');
    
    // Control classes
    define('Accel', 'Accel');
    define('Calendar', 'Calendar');
    define('CheckBox', 'CheckBox');
    define('ComboBox', 'ComboBox');
    define('EditBox', 'EditBox');
    define('Frame', 'Frame');
    define('Gauge', 'Gauge');
    define('HTMLControl', 'HTMLControl');
    define('HyperLink', 'HyperLink');
    define('ImageButton', 'ImageButton');
    define('InvisibleArea', 'InvisibleArea');
    define('Label', 'Label');
    define('ListBox', 'ListBox');
    define('ListView', 'ListView');
    define('Menu', 'Menu');
    define('PushButton', 'PushButton');
    define('RadioButton', 'RadioButton');
    define('RTFEditBox', 'RTFEditBox');
    define('ScrollBar', 'ScrollBar');
    define('Slider', 'Slider');
    define('Spinner', 'Spinner');
    define('StatusBar', 'StatusBar');
    define('TabControl', 'TabControl');
    define('ToolBar', 'ToolBar');
    define('TreeView', 'TreeView');
    
    // Message box types
    define('WBC_INFO', 0x40);
    define('WBC_OK', 0x00);
    define('WBC_OKCANCEL', 0x01);
    define('WBC_QUESTION', 0x20);
    define('WBC_STOP', 0x10);
    define('WBC_WARNING', 0x30);
    define('WBC_YESNO', 0x04);
    define('WBC_YESNOCANCEL', 0x03);
    
    // Message box return values
    define('IDOK', 1);
    define('IDCANCEL', 2);
    define('IDABORT', 3);
    define('IDRETRY', 4);
    define('IDIGNORE', 5);
    define('IDYES', 6);
    define('IDNO', 7);
    define('IDCLOSE', 8);
    define('IDHELP', 9);
    
    // Colors
    define('BLACK', 0x000000);
    define('BLUE', 0xFF0000);
    define('CYAN', 0xFFFF00);
    define('DARKBLUE', 0x800000);
    define('DARKCYAN', 0x808000);
    define('DARKGRAY', 0x808080);
    define('DARKGREEN', 0x008000);
    define('DARKMAGENTA', 0x800080);
    define('DARKRED', 0x000080);
    define('DARKYELLOW', 0x008080);
    define('GREEN', 0x00FF00);
    define('LIGHTGRAY', 0xC0C0C0);
    define('MAGENTA', 0xFF00FF);
    define('RED', 0x0000FF);
    define('WHITE', 0xFFFFFF);
    define('YELLOW', 0x00FFFF);
    
    // Font styles
    define('WBC_BOLD', 0x01);
    define('WBC_ITALIC', 0x02);
    define('WBC_UNDERLINE', 0x04);
    define('WBC_STRIKEOUT', 0x08);
    
    // System information
    define('WBC_SCREENAREA', 'screenarea');
    define('WBC_WORKAREA', 'workarea');
    
    // Timer
    define('WBC_TIMER', 0x01);
    
    // Keyboard
    define('WBC_ALT', 0x01);
    define('WBC_CONTROL', 0x02);
    define('WBC_SHIFT', 0x04);
    
    // Mouse buttons
    define('WBC_LBUTTON', 0x01);
    define('WBC_MBUTTON', 0x02);
    define('WBC_RBUTTON', 0x04);
    
    // ListView styles
    define('WBC_SINGLE', 0x01);
    define('WBC_MULTI', 0x02);
    define('WBC_EXTENDED', 0x04);
    
    // TreeView styles  
    define('WBC_HASLINES', 0x01);
    define('WBC_HASBUTTONS', 0x02);
    define('WBC_HASLINESATROOT', 0x04);
    define('WBC_CHECKBOXES', 0x08);
    
    // Tab control styles
    define('WBC_BOTTOM', 0x01);
    define('WBC_MULTILINE', 0x02);
    
    // Toolbar styles
    define('WBC_FLAT', 0x01);
    define('WBC_TOOLTIPS', 0x02);
    
    // Status bar styles
    define('WBC_SIZEGRIP', 0x01);
    
    // Edit box styles
    define('WBC_MULTILINE', 0x01);
    define('WBC_HSCROLL', 0x02);
    define('WBC_VSCROLL', 0x04);
    define('WBC_AUTOHSCROLL', 0x08);
    define('WBC_AUTOVSCROLL', 0x10);
    
    // Combo box styles
    define('WBC_DROPDOWN', 0x01);
    define('WBC_DROPDOWNLIST', 0x02);
    define('WBC_SIMPLE', 0x04);
    
    // List box styles
    define('WBC_NOINTEGRALHEIGHT', 0x01);
    define('WBC_DISABLENOSCROLL', 0x02);
    
    // Scroll bar styles
    define('WBC_HORIZONTAL', 0x01);
    define('WBC_VERTICAL', 0x02);
    
    // Slider styles
    define('WBC_AUTOTICKS', 0x01);
    define('WBC_NOTICKS', 0x02);
    define('WBC_TOOLTIPS', 0x04);
    
    // Spinner styles
    define('WBC_ARROWKEYS', 0x01);
    define('WBC_WRAP', 0x02);
    
    // Calendar styles
    define('WBC_NOTODAY', 0x01);
    define('WBC_NOTODAYCIRCLE', 0x02);
    define('WBC_WEEKNUMBERS', 0x04);
    
    // HTML control styles
    define('WBC_SILENT', 0x01);
    
    // Image button styles
    define('WBC_BITMAP', 0x01);
    define('WBC_ICON', 0x02);
    
    // Frame styles
    define('WBC_ETCHED', 0x01);
    define('WBC_RAISED', 0x02);
    define('WBC_SUNKEN', 0x04);
}

?>
