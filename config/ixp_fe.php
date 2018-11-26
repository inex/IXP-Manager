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
            'console-server-connection' => env( 'IXP_FE_FRONTEND_DISABLED_CONSOLE',         false ),
            'cust-kit'                  => env( 'IXP_FE_FRONTEND_DISABLED_CUSTKIT',         false ),
            'logo'                      => env( 'IXP_FE_FRONTEND_DISABLED_LOGO',            true  ),
            'lg'                        => env( 'IXP_FE_FRONTEND_DISABLED_LOOKING_GLASS',   true  ),
            'net-info'                  => env( 'IXP_FE_FRONTEND_DISABLED_NETINFO',         true ),
            'peering-manager'           => env( 'IXP_FE_FRONTEND_DISABLED_PEERING_MANAGER', false ),
            'peering-matrix'            => env( 'IXP_FE_FRONTEND_DISABLED_PEERING_MATRIX',  false ),
            'rs-prefixes'               => env( 'IXP_FE_FRONTEND_DISABLED_RS_PREFIXES',     false ),
        ],

        'beta' => [
            'core_bundles' => env( 'IXP_FE_BETA_CORE_BUNDLES', false ),
        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | Route Server Prefixes Access permissions
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
    ],


    /*
    |--------------------------------------------------------------------------
    | Customer Controller Options
    |--------------------------------------------------------------------------
    |
    |
    */
    'customer' => [
        'form'  => [
            'placeholders' => [
                // sample Irish number reserved for dramatic use
                'phone'    => env( 'IXP_FE_CUSTOMER_FORM_PLACEHOLDER_PHONE', '+353 20 910 1234' ),
            ]
        ],

        // Billing updates notifications
        //
        // Send email with updated billing details to the following address when billing details
        // are updated by an admin or a user.
        //
        'billing_updates_notify' => env( 'IXP_FE_CUSTOMER_BILLING_UPDATES_NOTIFY', false ),

        // customer notes - see: https://docs.ixpmanager.org/usage/notes/
        //
        // Admin users can opt to get notified when a customer note is added / edited / deleted.
        // For testing and demonstration purposes, this can be disabled and all updates
        // for all actions can be sent to a single address defined here:
        'notes' => [
            'only_send_to' => env( 'IXP_FE_CUSTOMER_NOTES_ONLYSENDTO', false ),
        ]
    ],



    /*
    |--------------------------------------------------------------------------
    | Customer Ability to Change Own MAC Addresses
    |--------------------------------------------------------------------------
    |
    */
    'layer2-addresses' => [

        'customer_can_edit'  => env( 'IXP_FE_LAYER2_ADDRESSES_CUST_CAN_EDIT', false ),

        'customer_params' => [
            'min_addresses' => env( 'IXP_FE_LAYER2_ADDRESSES_CUST_PARAMS_MIN_ADDRESSES', 1 ),
            'max_addresses' => env( 'IXP_FE_LAYER2_ADDRESSES_CUST_PARAMS_MAX_ADDRESSES', 2 ),
        ],

        'email_on_superuser_change'  => env( 'IXP_FE_LAYER2_ADDRESSES_EMAIL_ON_SUPERUSER_CHANGE', false ),
        'email_on_customer_change'   => env( 'IXP_FE_LAYER2_ADDRESSES_EMAIL_ON_CUSTOMER_CHANGE',  false ),
        'email_on_change_dest'       => env( 'IXP_FE_LAYER2_ADDRESSES_EMAIL_ON_CHANGE_DEST',      null  ),  // e.g. 'ops@ixp.example.net'

    ],


    /*
    |--------------------------------------------------------------------------
    | Admins Dashboard
    |--------------------------------------------------------------------------
    |
    */
    'admin_dashboard' => [

        'default_graph_period'       => env( 'IXP_FE_ADMIN_DASHBOARD_DEFAULT_GRAPH_PERIOD', 'week' ),

    ],


];
