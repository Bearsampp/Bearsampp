<?php

$result = array(
    'binapache' => '',
    'binfilezilla' => '',
    'binmailhog' => '',
    'binmariadb' => '',
    'binmongodb' => '',
    'binmysql' => '',
    'binpostgresql' => '',
    'binmemcached' => '',
    'binsvn' => '',
    'binnodejs' => '',
    'binphp' => '',
);

$dlMoreTpl = '<a href="' . Util::getWebsiteUrl('modules/%s', '#releases') . '" target="_blank" title="' . $neardLang->getValue(Lang::DOWNLOAD_MORE) . '"><span style="float:right;margin-left:8px;"><i class="fa fa-download"></i></span></a>';

// Bin Apache
$apachePort = $neardBins->getApache()->getPort();
$apacheSslPort = $neardBins->getApache()->getSslPort();
$apacheLabel = 'label-default';

if ($neardBins->getApache()->isEnable()) {
    $apacheLabel = 'label-danger';
    if ($neardBins->getApache()->checkPort($apachePort)) {
        if ($neardBins->getApache()->checkPort($apacheSslPort, true)) {
            $apacheLabel = 'label-success';
        } else {
            $apacheLabel = 'label-warning';
        }
    }
}



$result['binapache'] = sprintf($dlMoreTpl, 'apache');
$result['binapache'] .= '<span style="float:right;font-size:12px" class="label ' . $apacheLabel . '">' . $neardBins->getApache()->getVersion() . '</span>';

// Bin Filezilla
$filezillaPort = $neardBins->getFilezilla()->getPort();
$filezillaSslPort = $neardBins->getFilezilla()->getSslPort();
$filezillaLabel = 'label-default';

if ($neardBins->getFilezilla()->isEnable()) {
    $filezillaLabel = 'label-danger';
    if ($neardBins->getFilezilla()->checkPort($filezillaPort)) {
        if ($neardBins->getFilezilla()->checkPort($filezillaSslPort, true)) {
            $filezillaLabel = 'label-success';
        } else {
            $filezillaLabel = 'label-warning';
        }
    }
}

$result['binfilezilla'] = sprintf($dlMoreTpl, 'filezilla');
$result['binfilezilla'] .= '<span style="float:right;font-size:12px" class="label ' . $filezillaLabel . '">' . $neardBins->getFilezilla()->getVersion() . '</span>';

// Bin MailHog
$mailhogPort = $neardBins->getMailhog()->getSmtpPort();
$mailhogLabel = 'label-default';

if ($neardBins->getMailhog()->isEnable()) {
    $mailhogLabel = 'label-danger';
    if ($neardBins->getMailhog()->checkPort($mailhogPort)) {
        $mailhogLabel = 'label-success';
    }
}

$result['binmailhog'] = sprintf($dlMoreTpl, 'mailhog');
$result['binmailhog'] .= '<span style="float:right;font-size:12px" class="label ' . $mailhogLabel . '">' . $neardBins->getMailhog()->getVersion() . '</span>';

// Bin MariaDB
$mariadbPort = $neardBins->getMariadb()->getPort();
$mariadbLabel = 'label-default';

if ($neardBins->getMariadb()->isEnable()) {
    $mariadbLabel = 'label-danger';
    if ($neardBins->getMariadb()->checkPort($mariadbPort)) {
        $mariadbLabel = 'label-success';
    }
}

$result['binmariadb'] = sprintf($dlMoreTpl, 'mariadb');
$result['binmariadb'] .= '<span style="float:right;font-size:12px" class="label ' . $mariadbLabel . '">' . $neardBins->getMariadb()->getVersion() . '</span>';

// Bin MongoDB
$mongodbPort = $neardBins->getMongodb()->getPort();
$mongodbLabel = 'label-default';

if ($neardBins->getMongodb()->isEnable()) {
    $mongodbLabel = 'label-danger';
    if ($neardBins->getMongodb()->checkPort($mongodbPort)) {
        $mongodbLabel = 'label-success';
    }
}

$result['binmongodb'] = sprintf($dlMoreTpl, 'mongodb');
$result['binmongodb'] .= '<span style="float:right;font-size:12px" class="label ' . $mongodbLabel . '">' . $neardBins->getMongodb()->getVersion() . '</span>';

// Bin MySQL
$mysqlPort = $neardBins->getMysql()->getPort();
$mysqlLabel = 'label-default';

if ($neardBins->getMysql()->isEnable()) {
    $mysqlLabel = 'label-danger';
    if ($neardBins->getMysql()->checkPort($mysqlPort)) {
        $mysqlLabel = 'label-success';
    }
}

$result['binmysql'] = sprintf($dlMoreTpl, 'mysql');
$result['binmysql'] .= '<span style="float:right;font-size:12px" class="label ' . $mysqlLabel . '">' . $neardBins->getMysql()->getVersion() . '</span>';

// Bin PostgreSQL
$postgresqlPort = $neardBins->getPostgresql()->getPort();
$postgresqlLabel = 'label-default';

if ($neardBins->getPostgresql()->isEnable()) {
    $postgresqlLabel = 'label-danger';
    if ($neardBins->getPostgresql()->checkPort($postgresqlPort)) {
        $postgresqlLabel = 'label-success';
    }
}

$result['binpostgresql'] = sprintf($dlMoreTpl, 'postgresql');
$result['binpostgresql'] .= '<span style="float:right;font-size:12px" class="label ' . $postgresqlLabel . '">' . $neardBins->getPostgresql()->getVersion() . '</span>';

// Bin Memcached
$memcachedPort = $neardBins->getMemcached()->getPort();
$memcachedLabel = 'label-default';

if ($neardBins->getMemcached()->isEnable()) {
    $memcachedLabel = 'label-danger';
    if ($neardBins->getMemcached()->checkPort($memcachedPort)) {
        $memcachedLabel = 'label-success';
    }
}

$result['binmemcached'] = sprintf($dlMoreTpl, 'memcached');
$result['binmemcached'] .= '<span style="float:right;font-size:12px" class="label ' . $memcachedLabel . '">' . $neardBins->getMemcached()->getVersion() . '</span>';

// Bin SVN
$svnPort = $neardBins->getSvn()->getPort();
$svnLabel = 'label-default';

if ($neardBins->getSvn()->isEnable()) {
    $svnLabel = 'label-danger';
    if ($neardBins->getSvn()->checkPort($svnPort)) {
        $svnLabel = 'label-success';
    }
}

$result['binsvn'] = sprintf($dlMoreTpl, 'svn');
$result['binsvn'] .= '<span style="float:right;font-size:12px" class="label ' . $svnLabel . '">' . $neardBins->getSvn()->getVersion() . '</span>';

// Bin Node.js
$nodejsLabel = 'label-default';
if ($neardBins->getNodejs()->isEnable()) {
    $nodejsLabel = 'label-primary';
}

$result['binnodejs'] = sprintf($dlMoreTpl, 'nodejs');
$result['binnodejs'] .= '<span style="float:right;font-size:12px" class="label ' . $nodejsLabel .'">' . $neardBins->getNodejs()->getVersion() . '</span>';

// Bin PHP
$phpLabel = 'label-default';
if ($neardBins->getPhp()->isEnable()) {
    $phpLabel = 'label-primary';
}

$result['binphp'] = sprintf($dlMoreTpl, 'php');
$result['binphp'] .= '<span style="float:right;font-size:12px" class="label ' . $phpLabel .'">' . $neardBins->getPhp()->getVersion() . '</span>';

echo json_encode($result);
