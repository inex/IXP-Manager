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

            'workdir' => env( 'GRAPHER_BACKEND_MRTG_WORKDIR', '/tmp' ),

            // where to find the MRTG log files (and png files)
            'logdir'  => env( 'GRAPHER_BACKEND_MRTG_LOGDIR', '/tmp' ),
        ],

        'sflow' => [
            // where to find the MRTG rrd files
            'api'  => env( 'GRAPHER_BACKEND_SFLOW_API', 'http://www.example.com/' ),
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
