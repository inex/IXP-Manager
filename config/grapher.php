<?php

return [

    /*
    | Providers - support providers.
    */
    'providers' => [
        'dummy'         => IXP\Services\Grapher\Backend\Dummy::class,
        'mrtg'          => IXP\Services\Grapher\Backend\Mrtg::class,
        'sflow'         => IXP\Services\Grapher\Backend\Sflow::class,
        'smokeping'     => IXP\Services\Grapher\Backend\Smokeping::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Helpdesk Backend
    |--------------------------------------------------------------------------
    |
    | Options are defined above in 'providers'
    |
    | Multiple backends are supported. Separate them in the the .env with '|'
    */

    'backend' => explode( '|', env( 'GRAPHER_BACKENDS', 'dummy' ) ),

    /*
    |--------------------------------------------------------------------------
    | Helpdesk Backends
    |--------------------------------------------------------------------------
    |
    */

    'backends' => [

        'dummy' => [
            // where to find the dummy MRTG log files (and png files)
            'logdir'  => env( 'GRAPHER_BACKEND_DUMMY_LOGDIR', base_path() . '/data/grapher/dummy' ),
        ],

        'mrtg'  => [
            // see: http://oss.oetiker.ch/mrtg/doc/mrtg-rrd.en.html
            'dbtype'  => env( 'GRAPHER_BACKEND_MRTG_DBTYPE', 'log' ),  // options: log, rrd

            // where to store log/rrd/png files as created above. This is from the perspective
            // of the mrtg daemon so should also be local
            'workdir' => env( 'GRAPHER_BACKEND_MRTG_WORKDIR', '/tmp' ),

            // where to find the WORKDIR above from IXP Manager's perspective. This can be a
            // local directory or a URL to remote web server
            'logdir'  => env( 'GRAPHER_BACKEND_MRTG_LOGDIR', '/tmp' ),

            'trunks'  => call_user_func( function() {
                if( file_exists( config_path() . '/grapher_trunks.php' ) ) {
                    return include( config_path() . '/grapher_trunks.php' );
                }
                return null;
            }),

            // tmp until we sort out trunks:
            'snmppasswd' => env( 'GRAPHER_BACKEND_MRTG_SNMPPASSWD', 'soopersecret' ),
        ],

        'sflow' => [
            // show sflow / p2p links on the frontend
            'enabled' => env( 'GRAPHER_BACKEND_SFLOW_ENABLED', false ),

            // for larger IXPs, it's quite intensive to display all the graphs
            'show_graphs_on_index_page' => env( 'GRAPHER_BACKEND_SFLOW_SHOW_ON_INDEX', false ),

            // where to find the MRTG rrd files
            'root'  => env( 'GRAPHER_BACKEND_SFLOW_ROOT', 'http://www.example.com/' ),
        ],

        'smokeping' => [
            // show sflow / p2p links on the frontend
            'enabled' => env( 'GRAPHER_BACKEND_SMOKEPING_ENABLED', false ),

            // where to find the smokeping files
            'url'  => env( 'GRAPHER_SMOKEPING_URL', 'http://www.example.com/' ),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Creating imahes / loading log files are expensive operations. Especially
    | when not on the local machine.
    |
    | As such, we cache these items by default in the local file store.
    |
    */

    'cache' => [
        // cache enabled?
        'enabled' => env( 'GRAPHER_CACHE_ENABLED', 'true' ),

        // cache store to use
        'store' => env( 'GRAPHER_CACHE_STORE', 'file' ),

        // cache lifetime - this is not editable as 5mins in an industry standard
        'lifetime' => 5,

        // namespace -> not a cache namespace but a key namespace.
        // All keys are prepended with 'grapher::'
    ],

    /*
    |--------------------------------------------------------------------------
    | Access permissions
    |--------------------------------------------------------------------------
    |
    | Most IXPs make aggregate graphs publically available. Namely IXP,
    | infrastructure, switch and trunks. As such, this is our default.
    |
    | You can alter these settings to change these defaults to a specific user
    | level.
    |
    | NB: if these are not defined, THEY DEFAULT TO PUBLIC ACCESS
    |
    */
    'access' => [
        'ixp'            => env( 'GRAPHER_ACCESS_IXP',            Entities\User::AUTH_PUBLIC ),
        'infrastructure' => env( 'GRAPHER_ACCESS_INFRASTRUCTURE', Entities\User::AUTH_PUBLIC ),
        'switch'         => env( 'GRAPHER_ACCESS_SWITCH',         Entities\User::AUTH_PUBLIC ),
        'trunk'          => env( 'GRAPHER_ACCESS_TRUNK',          Entities\User::AUTH_PUBLIC ),
        'smokeping'      => env( 'GRAPHER_ACCESS_SMOKEPING',      Entities\User::AUTH_PUBLIC ),
    ],


    /*
     |--------------------------------------------------------------------------
     |
     | CLI Tool Settings
     |
     | Relate to artisan grapher:xxx
     |
     | See: FIXME link to docs
     |
     */
    'cli' => [
        'traffic_differentials' => [
            'stddev_calc_length' => env( 'GRAPHER_CLI_TRAFFICDIFFERENTIALS_CALC_LEN', 60 ),
        ],

        'traffic_daily' => [

            // the traffic_daily table can get pretty full and most of the long term information
            // are in the MRTG / other stats files anyway. If you want to keep this data in the
            // database, set the following to false. If it's true, when the daily task runs
            // to populate this table, it will also delete any entries older than
            // the number of days.
            'delete_old' => env( 'GRAPHER_CLI_TRAFFICDAILY_DELETE_OLD', true ),

            // Remember that the traffic deltas script takes one row per week to build up
            // a standard deviation so this needs to be usefully large:
            'delete_old_days' => env( 'GRAPHER_CLI_TRAFFICDAILY_DELETE_OLD_DAYS', 140 ),
        ],
    ],


    /*
     | Smokeping - mostly temporary configuration until migrated in full / proper to Grapher
     |
     | FIXME: REMOVE!
     |
     | See: http://docs.ixpmanager.org/features/smokeping/
     */
    'smokeping' => [
        'url' => env( 'GRAPHER_SMOKEPING_URL', false ),

        'overrides' => call_user_func( function() {
            if( file_exists( config_path( 'grapher_smokeping_overrides.php' ) ) ) {
                return include( config_path( 'grapher_smokeping_overrides.php' ) );
            } else {
                return [];
            }
        }),
    ],

];
