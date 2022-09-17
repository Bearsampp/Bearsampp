<?php

$result = array(
    'binapache' => '',
    'binfilezilla' => '',
    'binmailhog' => '',
    'binmariadb' => '',
    'binmysql' => '',
    'binpostgresql' => '',
    'binmemcached' => '',
    'binnodejs' => '',
    'binphp' => '',
);

$dlMoreTpl = '<a href="' . Util::getWebsiteUrl('module/%s', '#releases') . '" target="_blank" title="' . $bearsamppLang->getValue(Lang::DOWNLOAD_MORE) . '"><span style="float:right;margin-left:.5rem;"><i class="fa fa-download"></i></span></a>';

// Bin Apache
$apachePort = $bearsamppBins->getApache()->getPort();
$apacheSslPort = $bearsamppBins->getApache()->getSslPort();
$apacheLabel = 'bg-secondary';

if ($bearsamppBins->getApache()->isEnable()) {
    $apacheLabel = 'bg-danger';
    if ($bearsamppBins->getApache()->checkPort($apachePort)) {
        if ($bearsamppBins->getApache()->checkPort($apacheSslPort, true)) {
            $apacheLabel = 'bg-success';
        } else {
            $apacheLabel = 'bg-warning';
        }
    }
}



$result['binapache'] = sprintf($dlMoreTpl, 'apache');
$result['binapache'] .= '<span style="float:right;font-size: 1em" class="badge ' . $apacheLabel . '">' . $bearsamppBins->getApache()->getVersion() . '</span>';

// Bin Filezilla
$filezillaPort = $bearsamppBins->getFilezilla()->getPort();
$filezillaSslPort = $bearsamppBins->getFilezilla()->getSslPort();
$filezillaLabel = 'bg-secondary';

if ($bearsamppBins->getFilezilla()->isEnable()) {
    $filezillaLabel = 'bg-danger';
    if ($bearsamppBins->getFilezilla()->checkPort($filezillaPort)) {
        if ($bearsamppBins->getFilezilla()->checkPort($filezillaSslPort, true)) {
            $filezillaLabel = 'bg-success';
        } else {
            $filezillaLabel = 'bg-warning';
        }
    }
}

$result['binfilezilla'] = sprintf($dlMoreTpl, 'filezilla');
$result['binfilezilla'] .= '<span style="float:right;font-size: 1em" class="badge ' . $filezillaLabel . '">' . $bearsamppBins->getFilezilla()->getVersion() . '</span>';

// Bin MailHog
$mailhogPort = $bearsamppBins->getMailhog()->getSmtpPort();
$mailhogLabel = 'bg-secondary';

if ($bearsamppBins->getMailhog()->isEnable()) {
    $mailhogLabel = 'bg-danger';
    if ($bearsamppBins->getMailhog()->checkPort($mailhogPort)) {
        $mailhogLabel = 'bg-success';
    }
}

$result['binmailhog'] = sprintf($dlMoreTpl, 'mailhog');
$result['binmailhog'] .= '<span style="float:right;font-size: 1em" class="badge ' . $mailhogLabel . '">' . $bearsamppBins->getMailhog()->getVersion() . '</span>';

// Bin MariaDB
$mariadbPort = $bearsamppBins->getMariadb()->getPort();
$mariadbLabel = 'bg-secondary';

if ($bearsamppBins->getMariadb()->isEnable()) {
    $mariadbLabel = 'bg-danger';
    if ($bearsamppBins->getMariadb()->checkPort($mariadbPort)) {
        $mariadbLabel = 'bg-success';
    }
}

$result['binmariadb'] = sprintf($dlMoreTpl, 'mariadb');
$result['binmariadb'] .= '<span style="float:right;font-size: 1em" class="badge ' . $mariadbLabel . '">' . $bearsamppBins->getMariadb()->getVersion() . '</span>';

// Bin MySQL
$mysqlPort = $bearsamppBins->getMysql()->getPort();
$mysqlLabel = 'bg-secondary';

if ($bearsamppBins->getMysql()->isEnable()) {
    $mysqlLabel = 'bg-danger';
    if ($bearsamppBins->getMysql()->checkPort($mysqlPort)) {
        $mysqlLabel = 'bg-success';
    }
}

$result['binmysql'] = sprintf($dlMoreTpl, 'mysql');
$result['binmysql'] .= '<span style="float:right;font-size: 1em" class="badge ' . $mysqlLabel . '">' . $bearsamppBins->getMysql()->getVersion() . '</span>';

// Bin PostgreSQL
$postgresqlPort = $bearsamppBins->getPostgresql()->getPort();
$postgresqlLabel = 'bg-secondary';

if ($bearsamppBins->getPostgresql()->isEnable()) {
    $postgresqlLabel = 'bg-danger';
    if ($bearsamppBins->getPostgresql()->checkPort($postgresqlPort)) {
        $postgresqlLabel = 'bg-success';
    }
}

$result['binpostgresql'] = sprintf($dlMoreTpl, 'postgresql');
$result['binpostgresql'] .= '<span style="float:right;font-size: 1em" class="badge ' . $postgresqlLabel . '">' . $bearsamppBins->getPostgresql()->getVersion() . '</span>';

// Bin Memcached
$memcachedPort = $bearsamppBins->getMemcached()->getPort();
$memcachedLabel = 'bg-secondary';

if ($bearsamppBins->getMemcached()->isEnable()) {
    $memcachedLabel = 'bg-danger';
    if ($bearsamppBins->getMemcached()->checkPort($memcachedPort)) {
        $memcachedLabel = 'bg-success';
    }
}

$result['binmemcached'] = sprintf($dlMoreTpl, 'memcached');
$result['binmemcached'] .= '<span style="float:right;font-size: 1em" class="badge ' . $memcachedLabel . '">' . $bearsamppBins->getMemcached()->getVersion() . '</span>';


// Bin Node.js
$nodejsLabel = 'bg-secondary';
if ($bearsamppBins->getNodejs()->isEnable()) {
    $nodejsLabel = 'bg-primary';
}

$result['binnodejs'] = sprintf($dlMoreTpl, 'nodejs');
$result['binnodejs'] .= '<span style="float:right;font-size: 1em" class="badge ' . $nodejsLabel .'">' . $bearsamppBins->getNodejs()->getVersion() . '</span>';

// Bin PHP
$phpLabel = 'bg-secondary';
if ($bearsamppBins->getPhp()->isEnable()) {
    $phpLabel = 'bg-primary';
}

$result['binphp'] = sprintf($dlMoreTpl, 'php');
$result['binphp'] .= '<span style="float:right;font-size: 1em" class="badge ' . $phpLabel .'">' . $bearsamppBins->getPhp()->getVersion() . '</span>';

echo json_encode($result);
