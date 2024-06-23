<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Generates a JSON output containing information about various bins such as Apache, Filezilla, MailHog, MariaDB, MySQL, PostgreSQL, Memcached, Node.js, and PHP.
 * The output includes download links, version numbers, and status labels for each bin.
 */

// Declare global variables
global $downloadTitle, $bearsamppBins;

// Initialize result array
$result = array(
    'binapache'     => '',
    'binfilezilla'  => '',
    'binmailhog'    => '',
    'binmariadb'    => '',
    'binmysql'      => '',
    'binpostgresql' => '',
    'binmemcached'  => '',
    'binnodejs'     => '',
    'binphp'        => '',
    'binxlight'     => '',
);

// Template for download link
$dlMoreTpl = '<a href="' . Util::getWebsiteUrl( 'module/%s', '#releases' ) . '" target="_blank" title="' . $downloadTitle . '"><span class="float-end" style="margin-left:.5rem;"><i class="fa-solid fa-cloud-arrow-down"></i></span></a>';

try {
    /**
     * Apache Bin Information
     * Retrieves the port and SSL port for Apache, checks if Apache is enabled and the ports are open.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $apachePort    = $bearsamppBins->getApache()->getPort();
    $apacheSslPort = $bearsamppBins->getApache()->getSslPort();
    $apacheLabel   = 'bg-secondary';
    if ( $bearsamppBins->getApache()->isEnable() ) {
        $apacheLabel = 'bg-danger';
        if ( $bearsamppBins->getApache()->checkPort( $apachePort ) ) {
            if ( $bearsamppBins->getApache()->checkPort( $apacheSslPort, true ) ) {
                $apacheLabel = 'bg-success';
            }
            else {
                $apacheLabel = 'bg-warning';
            }
        }
    }
    $result['binapache'] = sprintf( $dlMoreTpl, 'apache' );
    $result['binapache'] .= '<span class = " float-end badge ' . $apacheLabel . '">' . $bearsamppBins->getApache()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binapache'] = 'An error occurred getting the summary of Apache. ' . $e->getMessage();
}

try {
    /**
     * Filezilla Bin Information
     * Retrieves the port and SSL port for Filezilla, checks if Filezilla is enabled and the ports are open.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $filezillaPort    = $bearsamppBins->getFilezilla()->getPort();
    $filezillaSslPort = $bearsamppBins->getFilezilla()->getSslPort();
    $filezillaLabel   = 'bg-secondary';
    if ( $bearsamppBins->getFilezilla()->isEnable() ) {
        $filezillaLabel = 'bg-danger';
        if ( $bearsamppBins->getFilezilla()->checkPort( $filezillaPort ) ) {
            if ( $bearsamppBins->getFilezilla()->checkPort( $filezillaSslPort, true ) ) {
                $filezillaLabel = 'bg-success';
            }
            else {
                $filezillaLabel = 'bg-warning';
            }
        }
    }
    $result['binfilezilla'] = sprintf( $dlMoreTpl, 'filezilla' );
    $result['binfilezilla'] .= '<span class = " float-end badge ' . $filezillaLabel . '">' . $bearsamppBins->getFilezilla()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binfilezilla'] = 'An error occurred getting the summary of Filezilla. ' . $e->getMessage();
}

try {
    /**
     * MailHog Bin Information
     * Retrieves the SMTP port for MailHog, checks if MailHog is enabled and the port is open.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $mailhogPort  = $bearsamppBins->getMailhog()->getSmtpPort();
    $mailhogLabel = 'bg-secondary';
    if ( $bearsamppBins->getMailhog()->isEnable() ) {
        $mailhogLabel = 'bg-danger';
        if ( $bearsamppBins->getMailhog()->checkPort( $mailhogPort ) ) {
            $mailhogLabel = 'bg-success';
        }
    }
    $result['binmailhog'] = sprintf( $dlMoreTpl, 'mailhog' );
    $result['binmailhog'] .= '<span class = " float-end badge ' . $mailhogLabel . '">' . $bearsamppBins->getMailhog()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binmailhog'] = 'An error occurred getting the summary of Mailhog. ' . $e->getMessage();
}

try {
    /**
     * Xlight Bin Information
     * Retrieves the port for Xlight, checks if Xlight is enabled and the port is open.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $xlightPort  = $bearsamppBins->getXlight()->getPort();
    $xlightLabel = 'bg-secondary';
    if ( $bearsamppBins->getXlight()->isEnable() ) {
        $xlightLabel = 'bg-danger';
        if ( $bearsamppBins->getXlight()->checkPort( $xlightPort ) ) {
            $xlightLabel = 'bg-success';
        }
    }
    $result['binxlight'] = sprintf( $dlMoreTpl, 'xlight' );
    $result['binxlight'] .= '<span class = " float-end badge ' . $xlightLabel . '">' . $bearsamppBins->getXlight()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binxlight'] = 'An error occurred getting the summary of Xlight. ' . $e->getMessage();
}

try {
    /**
     * MariaDB Bin Information
     * Retrieves the port for MariaDB, checks if MariaDB is enabled and the port is open.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $mariadbPort  = $bearsamppBins->getMariadb()->getPort();
    $mariadbLabel = 'bg-secondary';
    if ( $bearsamppBins->getMariadb()->isEnable() ) {
        $mariadbLabel = 'bg-danger';
        if ( $bearsamppBins->getMariadb()->checkPort( $mariadbPort ) ) {
            $mariadbLabel = 'bg-success';
        }
    }
    $result['binmariadb'] = sprintf( $dlMoreTpl, 'mariadb' );
    $result['binmariadb'] .= '<span class = " float-end badge ' . $mariadbLabel . '">' . $bearsamppBins->getMariadb()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binmariadb'] = 'An error occurred getting the summary of MariaDB. ' . $e->getMessage();
}

try {
    /**
     * MySQL Bin Information
     * Retrieves the port for MySQL, checks if MySQL is enabled and the port is open.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $mysqlPort  = $bearsamppBins->getMysql()->getPort();
    $mysqlLabel = 'bg-secondary';
    if ( $bearsamppBins->getMysql()->isEnable() ) {
        $mysqlLabel = 'bg-danger';
        if ( $bearsamppBins->getMysql()->checkPort( $mysqlPort ) ) {
            $mysqlLabel = 'bg-success';
        }
    }
    $result['binmysql'] = sprintf( $dlMoreTpl, 'mysql' );
    $result['binmysql'] .= '<span class = " float-end badge ' . $mysqlLabel . '">' . $bearsamppBins->getMysql()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binmysql'] = 'An error occurred getting the summary of MySql. ' . $e->getMessage();
}

try {
    /**
     * PostgreSQL Bin Information
     * Retrieves the port for PostgreSQL, checks if PostgreSQL is enabled and the port is open.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $postgresqlPort  = $bearsamppBins->getPostgresql()->getPort();
    $postgresqlLabel = 'bg-secondary';
    if ( $bearsamppBins->getPostgresql()->isEnable() ) {
        $postgresqlLabel = 'bg-danger';
        if ( $bearsamppBins->getPostgresql()->checkPort( $postgresqlPort ) ) {
            $postgresqlLabel = 'bg-success';
        }
    }
    $result['binpostgresql'] = sprintf( $dlMoreTpl, 'postgresql' );
    $result['binpostgresql'] .= '<span class = " float-end badge ' . $postgresqlLabel . '">' . $bearsamppBins->getPostgresql()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binpostgresql'] = 'An error occurred getting the summary of Postgresql. ' . $e->getMessage();
}

try {
    /**
     * Memcached Bin Information
     * Retrieves the port for Memcached, checks if Memcached is enabled and the port is open.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $memcachedPort  = $bearsamppBins->getMemcached()->getPort();
    $memcachedLabel = 'bg-secondary';
    if ( $bearsamppBins->getMemcached()->isEnable() ) {
        $memcachedLabel = 'bg-danger';
        if ( $bearsamppBins->getMemcached()->checkPort( $memcachedPort ) ) {
            $memcachedLabel = 'bg-success';
        }
    }
    $result['binmemcached'] = sprintf( $dlMoreTpl, 'memcached' );
    $result['binmemcached'] .= '<span class = " float-end badge ' . $memcachedLabel . '">' . $bearsamppBins->getMemcached()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binmemcached'] = 'An error occurred getting the summary of Memchached. ' . $e->getMessage();
}

try {
    /**
     * Node.js Bin Information
     * Checks if Node.js is enabled.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $nodejsLabel = 'bg-secondary';
    if ( $bearsamppBins->getNodejs()->isEnable() ) {
        $nodejsLabel = 'bg-success';
    }
    $result['binnodejs'] = sprintf( $dlMoreTpl, 'nodejs' );
    $result['binnodejs'] .= '<span class = " float-end badge ' . $nodejsLabel . '">' . $bearsamppBins->getNodejs()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binnodejs'] = 'An error occurred getting the summary of NodeJS. ' . $e->getMessage();
}

try {
    /**
     * PHP Bin Information
     * Checks if PHP is enabled.
     * Sets the appropriate label based on the status and appends the version and download link to the result.
     */
    $phpLabel = 'bg-secondary';
    if ( $bearsamppBins->getPhp()->isEnable() ) {
        $phpLabel = 'bg-success';
    }
    $result['binphp'] = sprintf( $dlMoreTpl, 'php' );
    $result['binphp'] .= '<span class = " float-end badge ' . $phpLabel . '">' . $bearsamppBins->getPhp()->getVersion() . '</span>';
}
catch ( Exception $e ) {
    $result['binphp'] = 'An error occurred getting the summary of PHP. ' . $e->getMessage();
}

// Output the result as JSON
echo json_encode( $result );
