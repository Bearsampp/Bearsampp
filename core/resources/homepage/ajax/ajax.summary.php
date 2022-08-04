<?php

$result = array(
    'binapache' => '',
    'binfilezilla' => '',
    'binmailhog' => '',
    'binmariadb' => '',
    'binmysql' => '',
    'binpostgresql' => '',
    'binmemcached' => '',
    'binsvn' => '',
    'binnodejs' => '',
    'binphp' => '',
);

$dlMoreTpl = '<a href="' . Util::getWebsiteUrl('module/%s', '#releases') . '" target="_blank" title="' . $bearsamppLang->getValue(Lang::DOWNLOAD_MORE) . '"><span style="float:right;margin-left:8px;"><i class="fa fa-download"></i></span></a>';

// Bin Apache
$apachePort = $bearsamppBins->getApache()->getPort();
$apacheSslPort = $bearsamppBins->getApache()->getSslPort();
$apacheLabel = 'label-default';

if ($bearsamppBins->getApache()->isEnable()) {
    $apacheLabel = 'label-danger';
    if ($bearsamppBins->getApache()->checkPort($apachePort)) {
        if ($bearsamppBins->getApache()->checkPort($apacheSslPort, true)) {
            $apacheLabel = 'label-success';
        } else {
            $apacheLabel = 'label-warning';
        }
    }
}



$result['binapache'] = sprintf($dlMoreTpl, 'apache');
$result['binapache'] .= '<span style="float:right;font-size:12px" class="label ' . $apacheLabel . '">' . $bearsamppBins->getApache()->getVersion() . '</span>';

// Bin Filezilla
$filezillaPort = $bearsamppBins->getFilezilla()->getPort();
$filezillaSslPort = $bearsamppBins->getFilezilla()->getSslPort();
$filezillaLabel = 'label-default';

if ($bearsamppBins->getFilezilla()->isEnable()) {
    $filezillaLabel = 'label-danger';
    if ($bearsamppBins->getFilezilla()->checkPort($filezillaPort)) {
        if ($bearsamppBins->getFilezilla()->checkPort($filezillaSslPort, true)) {
            $filezillaLabel = 'label-success';
        } else {
            $filezillaLabel = 'label-warning';
        }
    }
}

$result['binfilezilla'] = sprintf($dlMoreTpl, 'filezilla');
$result['binfilezilla'] .= '<span style="float:right;font-size:12px" class="label ' . $filezillaLabel . '">' . $bearsamppBins->getFilezilla()->getVersion() . '</span>';

// Bin MailHog
$mailhogPort = $bearsamppBins->getMailhog()->getSmtpPort();
$mailhogLabel = 'label-default';

if ($bearsamppBins->getMailhog()->isEnable()) {
    $mailhogLabel = 'label-danger';
    if ($bearsamppBins->getMailhog()->checkPort($mailhogPort)) {
        $mailhogLabel = 'label-success';
    }
}

$result['binmailhog'] = sprintf($dlMoreTpl, 'mailhog');
$result['binmailhog'] .= '<span style="float:right;font-size:12px" class="label ' . $mailhogLabel . '">' . $bearsamppBins->getMailhog()->getVersion() . '</span>';

// Bin MariaDB
$mariadbPort = $bearsamppBins->getMariadb()->getPort();
$mariadbLabel = 'label-default';

if ($bearsamppBins->getMariadb()->isEnable()) {
    $mariadbLabel = 'label-danger';
    if ($bearsamppBins->getMariadb()->checkPort($mariadbPort)) {
        $mariadbLabel = 'label-success';
    }
}

$result['binmariadb'] = sprintf($dlMoreTpl, 'mariadb');
$result['binmariadb'] .= '<span style="float:right;font-size:12px" class="label ' . $mariadbLabel . '">' . $bearsamppBins->getMariadb()->getVersion() . '</span>';

// Bin MySQL
$mysqlPort = $bearsamppBins->getMysql()->getPort();
$mysqlLabel = 'label-default';

if ($bearsamppBins->getMysql()->isEnable()) {
    $mysqlLabel = 'label-danger';
    if ($bearsamppBins->getMysql()->checkPort($mysqlPort)) {
        $mysqlLabel = 'label-success';
    }
}

$result['binmysql'] = sprintf($dlMoreTpl, 'mysql');
$result['binmysql'] .= '<span style="float:right;font-size:12px" class="label ' . $mysqlLabel . '">' . $bearsamppBins->getMysql()->getVersion() . '</span>';

// Bin PostgreSQL
$postgresqlPort = $bearsamppBins->getPostgresql()->getPort();
$postgresqlLabel = 'label-default';

if ($bearsamppBins->getPostgresql()->isEnable()) {
    $postgresqlLabel = 'label-danger';
    if ($bearsamppBins->getPostgresql()->checkPort($postgresqlPort)) {
        $postgresqlLabel = 'label-success';
    }
}

$result['binpostgresql'] = sprintf($dlMoreTpl, 'postgresql');
$result['binpostgresql'] .= '<span style="float:right;font-size:12px" class="label ' . $postgresqlLabel . '">' . $bearsamppBins->getPostgresql()->getVersion() . '</span>';

// Bin Memcached
$memcachedPort = $bearsamppBins->getMemcached()->getPort();
$memcachedLabel = 'label-default';

if ($bearsamppBins->getMemcached()->isEnable()) {
    $memcachedLabel = 'label-danger';
    if ($bearsamppBins->getMemcached()->checkPort($memcachedPort)) {
        $memcachedLabel = 'label-success';
    }
}

$result['binmemcached'] = sprintf($dlMoreTpl, 'memcached');
$result['binmemcached'] .= '<span style="float:right;font-size:12px" class="label ' . $memcachedLabel . '">' . $bearsamppBins->getMemcached()->getVersion() . '</span>';

// Bin SVN
$svnPort = $bearsamppBins->getSvn()->getPort();
$svnLabel = 'label-default';

if ($bearsamppBins->getSvn()->isEnable()) {
    $svnLabel = 'label-danger';
    if ($bearsamppBins->getSvn()->checkPort($svnPort)) {
        $svnLabel = 'label-success';
    }
}

$result['binsvn'] = sprintf($dlMoreTpl, 'svn');
$result['binsvn'] .= '<span style="float:right;font-size:12px" class="label ' . $svnLabel . '">' . $bearsamppBins->getSvn()->getVersion() . '</span>';

// Bin Node.js
$nodejsLabel = 'label-default';
if ($bearsamppBins->getNodejs()->isEnable()) {
    $nodejsLabel = 'label-primary';
}

$result['binnodejs'] = sprintf($dlMoreTpl, 'nodejs');
$result['binnodejs'] .= '<span style="float:right;font-size:12px" class="label ' . $nodejsLabel .'">' . $bearsamppBins->getNodejs()->getVersion() . '</span>';

// Bin PHP
$phpLabel = 'label-default';
if ($bearsamppBins->getPhp()->isEnable()) {
    $phpLabel = 'label-primary';
}

$result['binphp'] = sprintf($dlMoreTpl, 'php');
$result['binphp'] .= '<span style="float:right;font-size:12px" class="label ' . $phpLabel .'">' . $bearsamppBins->getPhp()->getVersion() . '</span>';

echo json_encode($result);
