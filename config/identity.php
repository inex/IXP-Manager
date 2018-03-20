<?php

return [

    /*
     * IXP Identity Information
     *
     *
     */


    // ****************************************************************************************
    // IXP Manager v3 legacy below. Needs to be configured!
    // ****************************************************************************************

    'legalname'   => env( 'IDENTITY_LEGALNAME', '*** CONFIG IDENTITY IN .env ***' ),

    'location'    => [
            'city'       => env( 'IDENTITY_CITY', '*** CONFIG IDENTITY IN .env ***' ),
            'country'    => env( 'IDENTITY_COUNTRY', '*** CONFIG IDENTITY IN .env ***' ),
        ],

    'ixfid'       => env( 'IDENTITY_IXFID', 0 ),
    'orgname'     => env( 'IDENTITY_ORGNAME', '*** CONFIG IDENTITY IN .env ***' ),
    'name'        => env( 'IDENTITY_NAME', '*** CONFIG IDENTITY IN .env ***' ),
    'email'       => env( 'IDENTITY_EMAIL', '*** CONFIG IDENTITY IN .env ***' ),
    'testemail'   => env( 'IDENTITY_TESTEMAIL', '*** CONFIG IDENTITY IN .env ***' ),
    'rsvpemail'   => env( 'IDENTITY_RSVPEMAIL', '*** CONFIG IDENTITY IN .env ***' ),
    
    'watermark'   => env( 'IDENTITY_WATERMARK', '*** CONFIG IDENTITY IN .env ***' ),

    'support_email'       => env( 'IDENTITY_SUPPORT_EMAIL', '*** CONFIG IDENTITY IN .env ***' ),
    'support_phone'       => env( 'IDENTITY_SUPPORT_PHONE', '*** CONFIG IDENTITY IN .env ***' ),
    'support_hours'       => env( 'IDENTITY_SUPPORT_HOURS', '*** CONFIG IDENTITY IN .env ***' ),

    'billing_email'       => env( 'IDENTITY_BILLING_EMAIL', '*** CONFIG IDENTITY IN .env ***' ),
    'billing_phone'       => env( 'IDENTITY_BILLING_PHONE', '*** CONFIG IDENTITY IN .env ***' ),
    'billing_hours'       => env( 'IDENTITY_BILLING_HOURS', '*** CONFIG IDENTITY IN .env ***' ),

    'sitename'      => env( 'IDENTITY_SITENAME', '*** CONFIG IDENTITY IN .env ***' ),
    'corporate_url' => env( 'IDENTITY_CORPORATE_URL', '*** CONFIG IDENTITY IN .env ***' ),
    'url'           => env( 'APP_URL', '*** CONFIG APP_URL IN .env ***' ),
    'logo'          => env( 'IDENTITY_LOGO', '*** CONFIG IDENTITY IN .env ***' ),
    'biglogo'       => env( 'IDENTITY_BIGLOGO', '*** CONFIG IDENTITY IN .env ***' ),

    'biglogoconf' => [
            'offset'             => env( 'IDENTITY_BIGLOGO_OFFSET', '*** CONFIG IDENTITY IN .env ***' ),
        ],

    'vlans'       => [
            'default' => env( 'IDENTITY_DEFAULT_VLAN', 1 ),
    ],

];
