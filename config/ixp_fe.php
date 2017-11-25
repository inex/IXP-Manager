<?php

// Front end config options for IXP Manager

return [

    /*
    | Skinning
    |
    */
    'skinning' => [
        // https://github.com/inex/IXP-Manager/wiki/Skinning :
        'smarty' => env( 'VIEW_SMARTY_SKIN', "" ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer Name Format
    |--------------------------------------------------------------------------
    |
    | How to display customer names on the front end. Used /most/ places we
    | reference a customer / member by name.
    |
    | The following substitutions are made:
    |   -  %n  - the customer name ($cust->getName())
    |   -  %a  - the customer abbreviated name ($cust->getAbbreviatedName())
    |   -  %s  - the customer shortname ($cust->getShortname())
    |   -  %i  - the customer's ASN ($cust->getAutsys()): "XXX"
    |   -  %j  - the customer's ASN ($cust->getAutsys()): "[ASXXX]"
    |   -  %k  - the customer's ASN ($cust->getAutsys()): "ASXXX - "
    |   -  %l  - the customer's ASN ($cust->getAutsys()): " - ASXXX"
    |
    | The %j,k,l options exist so that the extra characters can be excluded if the customer does not have an ASN (e.g. Associate member)
    |
    | Default (as of v4.1):
    | "%a [AS%j]"
    */
    'customer_name_format' => "%a %j",

    /*
    |--------------------------------------------------------------------------
    | Front end components (Zend Framework Controllers)
    |--------------------------------------------------------------------------
    |
    | Any ZF1 controller extending IXP_Controller_FrontEnd can be disabled by setting it to true below
    |
    | frontend.disabled.XXX = true
    |
    | e.g.
    |    frontend.disabled.cust-kit = true
    |    frontend.disabled.console-server-connection = true
    */

    'frontend' => [
        'disabled' => [
            'console-server-connection' => env( 'IXP_FE_FRONTEND_DISABLED_CONSOLE',        false ),
            'cust-kit'                  => env( 'IXP_FE_FRONTEND_DISABLED_CUSTKIT',        false ),
            'logo'                      => env( 'IXP_FE_FRONTEND_DISABLED_LOGO',           true  ),
            'lg'                        => env( 'IXP_FE_FRONTEND_DISABLED_LOOKING_GLASS',  true  ),
            'net-info'                  => env( 'IXP_FE_FRONTEND_DISABLED_NETINFO',        true ),
            'rs-prefixes'               => env( 'IXP_FE_FRONTEND_DISABLED_RS_PREFIXES',    false ),
        ],

        'beta' => [
            'core_bundles' => env( 'IXP_FE_BETA_CORE_BUNDLES', false ),
        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | Route Server PRefixes Access permissions
    |--------------------------------------------------------------------------
    |
    | Generally speaking, the filtering of route server prefixes is visible
    | on looking glasses / via comparison to route collector / etc.
    |
    | However, some IXs may wish to restrict access to the Route Server Prefix
    | Analysis Tool.
    |
    | The following options apply:
    |
    | 1. Entities\User::AUTH_PUBLIC     -> tool is publically available to all
    | 2. Entities\User::AUTH_CUSTUSER   -> tool is available to any logged in user
    | 3. Entities\User::AUTH_SUPERUSER  -> summary and any customer access is restricted to superadmins,
    |                                      logged in users may see their prefixes.
    */
    'rs-prefixes' => [
        'access'  => env( 'IXP_FE_RS_PREFIXES_ACCESS', Entities\User::AUTH_SUPERUSER ),
    ]
];
