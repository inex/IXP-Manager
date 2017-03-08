<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */
    'table'     => 'migrations',
    /*
    |--------------------------------------------------------------------------
    | Migration Directory
    |--------------------------------------------------------------------------
    |
    | This directory is where all migrations will be stored
    |
    */
    'directory' => database_path('migrations'),
    /*
    |--------------------------------------------------------------------------
    | Migration Namespace
    |--------------------------------------------------------------------------
    |
    | This namespace will be used on all migrations
    |
    */
    'namespace' => 'Database\\Migrations',
    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | Tables which are filtered by Regular Expression. You optionally
    | exclude or limit to certain tables. The default will
    | filter all tables.
    |
    */
    'schema'    => [
        'filter' => '/^(?).*$/'
    ]
];
