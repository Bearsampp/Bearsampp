<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * phpMyAdmin sample configuration, you can use it as base for
 * manual configuration. For easier setup you can use setup/
 *
 * All directives are explained in documentation in the doc/ folder
 * or at <https://docs.phpmyadmin.net/>.
 *
 * @package PhpMyAdmin
 */

/*
 * This is needed for cookie based authentication to encrypt password in
 * cookie. Needs to be 32 chars long.
 */
$cfg['blowfish_secret'] = 'MJkN7gbnKdyEjaR0sY}C=aX/VUCZYH3T'; /* YOU MUST FILL IN THIS FOR COOKIE AUTH! */

/*
 * Servers configuration
 */
$i = 0;
$mysqlPort = 3306;
$mysqlRootUser = 'root';
$mysqlRootPwd = '';
$mariadbPort = 3307;
$mariadbRootUser = 'root';
$mariadbRootPwd = '';

/**
 * MySQL server
 */
$i++;

$cfg['Servers'][$i]['verbose'] = 'MySQL port ' . $mysqlPort;
$cfg['Servers'][$i]['port'] = $mysqlPort;
$cfg['Servers'][$i]['user'] = $mysqlRootUser;
$cfg['Servers'][$i]['password'] = $mysqlRootPwd;
$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['extension'] = 'mysqli';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/**
 * MariaDB server
 */
$i++;

$cfg['Servers'][$i]['verbose'] = 'MariaDB port ' . $mariadbPort;
$cfg['Servers'][$i]['port'] = $mariadbPort;
$cfg['Servers'][$i]['user'] = $mariadbRootUser;
$cfg['Servers'][$i]['password'] = $mariadbRootPwd;
$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['extension'] = 'mysqli';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/*
 * End of servers configuration
 */

/*
 * Directories for saving/loading files from server
 */
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';

/**
 * Defines whether a user should be displayed a "show all (records)"
 * button in browse mode or not.
 * default = false
 */
//$cfg['ShowAll'] = true;

/**
 * Number of rows displayed when browsing a result set. If the result
 * set contains more rows, "Previous" and "Next".
 * default = 30
 */
//$cfg['MaxRows'] = 50;

/**
 * disallow editing of binary fields
 * valid values are:
 *   false    allow editing
 *   'blob'   allow editing except for BLOB fields
 *   'noblob' disallow editing except for BLOB fields
 *   'all'    disallow editing
 * default = blob
 */
//$cfg['ProtectBinary'] = 'false';

/**
 * Default language to use, if not browser-defined or user-defined
 * (you find all languages in the locale folder)
 * uncomment the desired line:
 * default = 'en'
 */
//$cfg['DefaultLang'] = 'en';
//$cfg['DefaultLang'] = 'de';

/**
 * default display direction (horizontal|vertical|horizontalflipped)
 */
//$cfg['DefaultDisplay'] = 'vertical';


/**
 * How many columns should be used for table display of a database?
 * (a value larger than 1 results in some information being hidden)
 * default = 1
 */
//$cfg['PropertiesNumColumns'] = 2;

/**
 * Set to true if you want DB-based query history.If false, this utilizes
 * JS-routines to display query history (lost by window close)
 *
 * This requires configuration storage enabled, see above.
 * default = false
 */
//$cfg['QueryHistoryDB'] = true;

/**
 * When using DB-based query history, how many entries should be kept?
 *
 * default = 25
 */
//$cfg['QueryHistoryMax'] = 100;

/*
 * You can find more configuration options in the documentation
 * in the doc/ folder or at <https://docs.phpmyadmin.net/>.
 */
$cfg['ServerDefault'] = 1;
$cfg['ProtectBinary'] = 'false';

?>
