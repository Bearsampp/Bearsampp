<?php
/**
 * PEAR, the PHP Extension and Application Repository
 *
 * Command line interface
 *
 * PHP versions 4 and 5
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Tomas V.V.Cox <cox@idecnet.com>
 * @copyright  1997-2009 The Authors
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://pear.php.net/package/PEAR
 */

/**
 * @nodep Gtk
 */
//the space is needed for windows include paths with trailing backslash
// http://pear.php.net/bugs/bug.php?id=19482
require_once dirname(__DIR__, 4) . '/classes/class.path.php';
Path::init(dirname(dirname(__DIR__, 4)), dirname(__DIR__, 4));
$pearDir = Path::getPearPath() . '/pear';
ini_set('include_path', $pearDir . PATH_SEPARATOR . get_include_path());
$raw = false;
define('PEAR_RUNTYPE', 'pecl');
require_once 'pearcmd.php';
/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * mode: php
 * End:
 */
// vim600:syn=php

?>
