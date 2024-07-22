<?php
/*
 * Copyright (c) 2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

$urls = [
	'https://raw.githubusercontent.com/Bearsampp/module-adminer/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-apache/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-composer/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-consolez/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-filezilla/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-ghostscript/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-git/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-gitlist/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-mailhog/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-mariadb/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-memcached/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-mysql/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-ngrok/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-nodejs/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-perl/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-php/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-phpmemadmin/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-phpmyadmin/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-phppgadmin/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-postgresql/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-python/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-ruby/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-webgrind/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-xdc/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-xlight/main/releases.properties',
	'https://raw.githubusercontent.com/Bearsampp/module-yarn/main/releases.properties'
];

$combinedData = ['modules' => []];

foreach ( $urls as $url ) {
	$response = file_get_contents( $url );
	if ( $response !== false ) {
		$properties = explode( "\n", $response );
		$moduleName = explode( '/', $url )[4];
		$moduleData = [
			'name'     => $moduleName,
			'versions' => []
		];
		foreach ( $properties as $prop ) {
			if ( strpos( $prop, '=' ) !== false ) {
				list( $version, $versionUrl ) = explode( '=', $prop, 2 );
				$moduleData['versions'][] = [
					'version' => trim( $version ),
					'url'     => trim( $versionUrl )
				];
			}
		}
		$combinedData['modules'][] = $moduleData;
	}
}

file_put_contents( 'core/resources/quickpick-releases.json', json_encode( $combinedData, JSON_PRETTY_PRINT ) );
