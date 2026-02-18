<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (https://github.com/crispy-computing-machine/Winbinder)

 Main inclusion file for WinBinder

*******************************************************************************/

if(!extension_loaded('winbinder'))
	if(!dl('php_winbinder.dll'))
		trigger_error("WinBinder extension could not be loaded.\n", E_USER_ERROR);

$_mainpath = pathinfo(__FILE__);
$_mainpath = $_mainpath["dirname"];

// WinBinder PHP functions

require_once $_mainpath . '/wb_constants.inc.php';
require_once $_mainpath . '/wb_resources.inc.php';
require_once $_mainpath . '/wb_windows.inc.php';
require_once $_mainpath . '/wb_generic.inc.php';
