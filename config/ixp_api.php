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




];
