<?php

/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

if(!extension_loaded('winbinder'))
	if(!dl('php_winbinder.dll'))
		trigger_error("WinBinder extension could not be loaded.\n", E_USER_ERROR);

$_mainpath = pathinfo(__FILE__);
$_mainpath = $_mainpath["dirname"] . "/";

// WinBinder PHP functions

include $_mainpath . "wb_windows.inc.php";
include $_mainpath . "wb_generic.inc.php";
include $_mainpath . "wb_resources.inc.php";

//------------------------------------------------------------------ END OF FILE

?>
