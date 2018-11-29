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
    | RIR details for updating RIR AS/AS-SET objects
    |--------------------------------------------------------------------------
    |
    */
    'rir' => [
        'password' => env( 'IXP_API_RIR_PASSWORD', 'xxxxx' ),
        'email'    => [
            'from' => env( 'IXP_API_RIR_EMAIL_FROM', null ),
            'to'   => env( 'IXP_API_RIR_EMAIL_TO',   null ),
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Nagios Configuration Generation
    |--------------------------------------------------------------------------
    |
    */
    'nagios' => [
        'birdseye_check'  => env( 'IXP_API_NAGIOS_BIRDSEYE_CHECK',
                                    "/usr/local/nagios-plugins-other/nagios-check-birdseye.php" ),
        'birdseye_bgp_session_check' => env( 'IXP_API_NAGIOS_BIRDSEYE_BGP_SESSION_CHECK',
                                    "/usr/local/nagios-plugins-other/nagios-check-birdseye-bgp-sessions.php" ),
        
        'infra_host'        => env( 'IXP_API_NAGIOS_INFRA_HOST',       "generic-host"    ),
        'customer_host'     => env( 'IXP_API_NAGIOS_CUSTOMER_HOST',    "generic-host"    ),
        'infra_service'     => env( 'IXP_API_NAGIOS_INFRA_SERVICE',    "generic-service" ),
        'customer_service'  => env( 'IXP_API_NAGIOS_CUSTOMER_SERVICE', "generic-service" ),
    ],

    /*
    |--------------------------------------------------------------------------
    | PEERING DB
    |--------------------------------------------------------------------------
    |
    */
    'peeringDB' => [
        'username'        => env( 'IXP_API_PEERING_DB_USERNAME', null ),
        'password'        => env( 'IXP_API_PEERING_DB_PASSWORD', null ),
        // you should not need to change this. The %s is either "$un:$pw@" or an empty string
        'url'             => env( 'IXP_API_PEERING_DB_URL',      "https://%speeringdb.com/api" ),

        'fac_api'         => env( 'IXP_API_PEERING_DB_FAC_URL',  "https://api.peeringdb.com/api/fac" ),
        'ixp_api'         => env( 'IXP_API_PEERING_DB_IXP_URL',  "https://api.peeringdb.com/api/ix"  ),
    ],

    /*
    |--------------------------------------------------------------------------
    | IXP DB
    |--------------------------------------------------------------------------
    |
    */
    'IXPDB' => [
        'ixp_api'         => env( 'IXP_API_IXPDB_IXP_URL',  "https://api.ixpdb.net/v1/provider/list"  ),
    ],



];
