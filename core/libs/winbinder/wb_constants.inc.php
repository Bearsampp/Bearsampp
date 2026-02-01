<?php

// WinBinder Constant Definitions

if (!defined('WBC_OK')) define('WBC_OK', 1);
if (!defined('WBC_CANCEL')) define('WBC_CANCEL', 2);
if (!defined('WBC_YES')) define('WBC_YES', 6);
if (!defined('WBC_NO')) define('WBC_NO', 7);
if (!defined('WBC_ABORT')) define('WBC_ABORT', 3);
if (!defined('WBC_RETRY')) define('WBC_RETRY', 4);
if (!defined('WBC_IGNORE')) define('WBC_IGNORE', 5);

if (!defined('WBC_INFO')) define('WBC_INFO', 64);
if (!defined('WBC_STOP')) define('WBC_STOP', 16);
if (!defined('WBC_QUESTION')) define('WBC_QUESTION', 32);
if (!defined('WBC_WARNING')) define('WBC_WARNING', 48);

if (!defined('WBC_OKCANCEL')) define('WBC_OKCANCEL', 1);
if (!defined('WBC_YESNO')) define('WBC_YESNO', 4);
if (!defined('WBC_YESNOCANCEL')) define('WBC_YESNOCANCEL', 3);
if (!defined('WBC_RETRYCANCEL')) define('WBC_RETRYCANCEL', 5);
if (!defined('WBC_ABORTRETRYIGNORE')) define('WBC_ABORTRETRYIGNORE', 2);

if (!defined('WBC_CENTER')) define('WBC_CENTER', -777);
if (!defined('WBC_DEFAULTPOS')) define('WBC_DEFAULTPOS', -888);

if (!defined('WBC_TITLE')) define('WBC_TITLE', 0);
if (!defined('WBC_HEADER')) define('WBC_HEADER', 1);
if (!defined('WBC_FOOTER')) define('WBC_FOOTER', 2);

if (!defined('WBC_VISIBLE')) define('WBC_VISIBLE', 0x10000000);
if (!defined('WBC_INVISIBLE')) define('WBC_INVISIBLE', 0x00000000);
if (!defined('WBC_ENABLED')) define('WBC_ENABLED', 0x00000000);
if (!defined('WBC_DISABLED')) define('WBC_DISABLED', 0x08000000);
if (!defined('WBC_BORDER')) define('WBC_BORDER', 0x00800000);
if (!defined('WBC_GROUP')) define('WBC_GROUP', 0x00020000);
if (!defined('WBC_IMAGE')) define('WBC_IMAGE', 0x00000001);
if (!defined('WBC_ELLIPSIS')) define('WBC_ELLIPSIS', 0x00004000);
if (!defined('WBC_MULTILINE')) define('WBC_MULTILINE', 0x00000004);
if (!defined('WBC_READONLY')) define('WBC_READONLY', 0x00000800);
if (!defined('WBC_MASKED')) define('WBC_MASKED', 0x00000020);
if (!defined('WBC_NUMBER')) define('WBC_NUMBER', 0x00002000);
if (!defined('WBC_SORT')) define('WBC_SORT', 0x00000010);
if (!defined('WBC_LINES')) define('WBC_LINES', 0x00000002);
if (!defined('WBC_CHECKBOXES')) define('WBC_CHECKBOXES', 0x00000400);
if (!defined('WBC_SINGLE')) define('WBC_SINGLE', 0x00000004);

if (!defined('WBC_LEFT')) define('WBC_LEFT', 0x00000000);
if (!defined('WBC_RIGHT')) define('WBC_RIGHT', 0x00000002);
if (!defined('WBC_TOP')) define('WBC_TOP', 0x00000000);
if (!defined('WBC_BOTTOM')) define('WBC_BOTTOM', 0x00000008);

// Window Classes
if (!defined('AppWindow')) define('AppWindow', 1);
if (!defined('ModalDialog')) define('ModalDialog', 2);
if (!defined('ModelessDialog')) define('ModelessDialog', 3);
if (!defined('ResizableWindow')) define('ResizableWindow', 4);
if (!defined('ToolDialog')) define('ToolDialog', 5);
if (!defined('NakedWindow')) define('NakedWindow', 6);

// Control Classes
if (!defined('Label')) define('Label', 10);
if (!defined('PushButton')) define('PushButton', 11);
if (!defined('CheckBox')) define('CheckBox', 12);
if (!defined('RadioButton')) define('RadioButton', 13);
if (!defined('ComboBox')) define('ComboBox', 14);
if (!defined('ListBox')) define('ListBox', 15);
if (!defined('EditBox')) define('EditBox', 16);
if (!defined('MultiLineEditBox')) define('MultiLineEditBox', 17);
if (!defined('RTFEditBox')) define('RTFEditBox', 18);
if (!defined('ListView')) define('ListView', 19);
if (!defined('TreeView')) define('TreeView', 20);
if (!defined('TabControl')) define('TabControl', 21);
if (!defined('Gauge')) define('Gauge', 22);
if (!defined('Slider')) define('Slider', 23);
if (!defined('ScrollBar')) define('ScrollBar', 24);
if (!defined('Spinner')) define('Spinner', 25);
if (!defined('ProgressBar')) define('ProgressBar', 26);
if (!defined('HyperLink')) define('HyperLink', 27);
if (!defined('StatusBar')) define('StatusBar', 28);
if (!defined('ToolBar')) define('ToolBar', 29);
if (!defined('Menu')) define('Menu', 30);
if (!defined('Accel')) define('Accel', 31);
if (!defined('Calendar')) define('Calendar', 32);
if (!defined('Frame')) define('Frame', 33);

if (!defined('NOCOLOR')) define('NOCOLOR', -1);
if (!defined('GREEN')) define('GREEN', 0x00FF00);
