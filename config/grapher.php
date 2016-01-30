<?php

return [

    /*
    | Providers - support providers.
    */
    'providers' => [
        'dummy' => IXP\Services\Grapher\Dummy::class,
        'mrtg'  => IXP\Services\Grapher\Mrtg::class,
        'sflow' => IXP\Services\Grapher\Sflow::class,
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

        'dummy' => [],

        'mrtg'  => [
            // see: http://oss.oetiker.ch/mrtg/doc/mrtg-rrd.en.html
            'dbtype'  => env( 'GRAPHER_BACKEND_MRTG_DBTYPE', 'log' ),  // options: log, rrd

            'workdir' => env( 'GRAPHER_BACKEND_MRTG_WORKDIR', '/tmp' ),
        ],

        'sflow' => [

        ]

    ],

];
