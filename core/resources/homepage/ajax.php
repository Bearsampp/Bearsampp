<?php

include_once __DIR__ . '\..\..\bootstrap.php';

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
    'postgresql',
    'svn'
);

$proc = Util::cleanPostVar('proc');

if (in_array($proc, $procs)) {
    include 'ajax/ajax.' . $proc . '.php';
}
