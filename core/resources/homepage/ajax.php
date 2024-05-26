<?php

include_once __DIR__ . '/../../root.php';

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

$proc = Util::cleanPostVar('proc', 'text');  // Ensure 'proc' is cleaned and read correctly

if (in_array($proc, $procs)) {
    include 'ajax/ajax.' . $proc . '.php';  // This line should correctly include 'ajax.latestversion.php'
} else {
    // It's a good practice to handle the case where 'proc' is not valid
    echo json_encode(['error' => 'Invalid proc parameter']);
}
