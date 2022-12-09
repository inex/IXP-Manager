<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

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
        'public' => env( 'IXP_API_JSONEXPORTSCHEMA_PUBLIC', true ),

        // or - we can set a static key here if we like:
        'access_key' => env( 'IXP_API_JSONEXPORTSCHEMA_ACCESS_KEY', false ),

        // some IXs want to exclude some information:
        'excludes' => [
            'switch'    => env( 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_SWITCH', false    ),
            'ixp'       => env( 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_IXP', false       ),
            'member'    => env( 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_MEMBER', false    ),
            'intinfo'   => env( 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_INTINFO', false   ),
        ],
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
        'ixp_www'         => env( 'IXP_WWW_PEERING_DB_IXP_URL',  "https://www.peeringdb.com/ix"  ),
    ],

    /*
    |--------------------------------------------------------------------------
    | IXP DB
    |--------------------------------------------------------------------------
    |
    */
    'IXPDB' => [
        'ixp_api'         => env( 'IXP_API_IXPDB_IXP_URL',  "https://api.ixpdb.net/v1/provider/list"  ),
        'ixp_www'         => env( 'IXP_WWW_IXPDB_IXP_URL',  "https://ixpdb.euro-ix.net/en/ixpdb/ixp" ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Whois servers
    |--------------------------------------------------------------------------
    |
    */

    'whois' => [
        // all responses cached for:
        'cache_ttl' => env( 'IXP_API_WHOIS_CACHE_TTL', 60 * 60 * 12 ),


        'asn' => [
            'host' => env( 'IXP_API_WHOIS_ASN_HOST',    'whois.peeringdb.com' ),
            'port' => env( 'IXP_API_WHOIS_ASN_PORT',    43 ),
        ],

        'asn2' => [
            'host' => env( 'IXP_API_WHOIS_ASN2_HOST',    'whois.cymru.com' ),
            'port' => env( 'IXP_API_WHOIS_ASN2_PORT',    43 ),
        ],

        'prefix' => [
            'host' => env( 'IXP_API_WHOIS_PREFIX_HOST', 'whois.bgpmon.net' ),
            'port' => env( 'IXP_API_WHOIS_PREFIX_PORT', 43 ),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ripe Atlas
    |--------------------------------------------------------------------------
    |
    */
    'atlas_measurement_key' => env( 'ATLAS_MEASUREMENT_KEY', '' ),
];
