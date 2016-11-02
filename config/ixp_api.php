<?php

// API config options for IXP Manager

return [

    /*
    |--------------------------------------------------------------------------
    | JSON Export Schema
    |--------------------------------------------------------------------------
    |
    */
    'json_export_schema' => [

        // if false, an API key is required
        'public' => env( 'IXP_API_JSONEXPORTSCHEMA_PUBLIC', false ),
    ],

    /*
    |--------------------------------------------------------------------------
    | RIR Password for updated RIR AS/AS-SET objects
    |--------------------------------------------------------------------------
    |
    */
    'rir' => [
        'password' => env( 'IXP_API_RIR_PASSWORD', 'xxxxx' ),
    ],




];
