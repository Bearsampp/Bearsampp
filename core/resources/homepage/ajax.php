<?php

include_once __DIR__ . '\..\..\root.php';

$procs = array(
    'summary',
    'latestversion',
    'apache',
    'filezilla',
    'mailhog',
    'memcached',
    'mariadb',
    'mysql',
    'nodejs',
    'php',
    'postgresql'
);

$proc = Util::cleanPostVar('proc');

if (in_array($proc, $procs)) {
    include 'ajax/ajax.' . $proc . '.php';
}
