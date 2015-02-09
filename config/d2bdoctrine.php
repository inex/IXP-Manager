<?php

/*
|--------------------------------------------------------------------------
| Doctrine2Bridge :: Doctrine Configuration
|--------------------------------------------------------------------------
|
| See: https://github.com/opensolutions/doctrine2bridge-l5
|
| NB: Database configuration taken from Laravel5's own config/database.php
| settings. Implemented databases are: MySQL, Postgres and SQLite.
*/

return [

    // connection parameters are set in Laravael's own database config file.

    // Paths for models, proxies, repositories, etc.
    'paths' => array(
        'models'       => app()->databasePath(),                // entity namespace added by default
        'proxies'      => app()->databasePath() . '/Proxies',
        'repositories' => app()->databasePath(),                // repository namespace added by default
        'xml_schema'   => app()->databasePath() . '/xml'
    ),

    // set to true to have Doctrine2 generate proxies on the fly. Not recommended in a production system.
    'autogen_proxies'        => env('APP_DEBUG'),

    // Namespaces for entities, proxies and repositories.
    'namespaces' => array(
        'models'       => 'Entities',
        'proxies'      => 'Proxies',
        'repositories' => 'Repositories'
    ),

    // Doctrine2Bridge includes an implementation of Doctrine\DBAL\Logging\SQLLogger which
    // just calls the Laravel Log facade. If you wish to log your SQL queries (and execution
    // time), just set enabled in the following to true.
    'sqllogger' => array(
        'enabled' => env('APP_DEBUG'),
        'level'   => 'debug'   // one of debug, info, notice, warning, error, critical, alert
    ),

    // use Doctrine2bridge with Laravel's authentication menchanism
    // see: https://github.com/opensolutions/doctrine2bridge/wiki/Auth
    'auth' => array(
        'enabled' => false,
        'entity'  => '\Entities\User'   // the Doctrine2 entity representing the user
    )
];
