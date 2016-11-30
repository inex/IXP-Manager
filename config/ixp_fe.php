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
            'meeting'                   => env( 'IXP_FE_FRONTEND_DISABLED_MEETING',        true ),
            'net-info'                  => env( 'IXP_FE_FRONTEND_DISABLED_NETINFO',        true ),
            'rs-prefixes'               => env( 'IXP_FE_FRONTEND_DISABLED_RS_PREFIXES',    false ),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Should aggregate, trunk and switch graphs be public?
    |--------------------------------------------------------------------------
    |
    */

    'statistics' => [
        'public' => env( 'IXP_FE_STATISTICS_PUBLIC', true ),
    ],

];
