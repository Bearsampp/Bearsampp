<?php

include './config.php';

function adminer_object() {
    global $mysqlPort, $mysqlRootUser, $mysqlRootPwd,
    $mariadbPort, $mariadbRootUser, $mariadbRootPwd,
    $postgresqlPort, $postgresqlRootUser, $postgresqlRootPwd,
    $mongodbPort;

    include_once './plugins/plugin.php';

    foreach (glob('plugins/*.php') as $filename) {
        include_once './' . $filename;
    }
    
    $plugins = array(
        new AdminerLoginServersEnhanced(
            array(
                new AdminerLoginServerEnhanced('127.0.0.1:' . $mysqlPort, 'MySQL port ' . $mysqlPort, 'server'),
                new AdminerLoginServerEnhanced('127.0.0.1:' . $mariadbPort, 'MariaDB port ' . $mariadbPort, 'server'),
                new AdminerLoginServerEnhanced('127.0.0.1:' . $postgresqlPort, 'PostgreSQL port ' . $postgresqlPort, 'pgsql'),
                new AdminerLoginServerEnhanced('127.0.0.1:' . $mongodbPort, 'MongoDB port ' . $mongodbPort, 'mongo')
            )
        ),
    );
    
    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */
    
    return new AdminerPlugin($plugins);
}

include './adminer.php';
