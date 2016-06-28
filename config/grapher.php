<?php

return [

    /*
    | Providers - support providers.
    */
    'providers' => [
        'dummy' => IXP\Services\Grapher\Backend\Dummy::class,
        'mrtg'  => IXP\Services\Grapher\Backend\Mrtg::class,
        'sflow' => IXP\Services\Grapher\Backend\Sflow::class,
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
            // where to find the MRTG rrd files
            'root'  => env( 'GRAPHER_BACKEND_SFLOW_ROOT', 'http://www.example.com/' ),
        ]

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


];
