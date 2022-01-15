<?php

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
    */
    'customer_name_format' => env( 'IXP_FE_CUSTOMER_NAME_FORMAT', "%a (AS%i)" ),

    /*
     * Customer or Member?
     */
    'lang' => [
        'customer' => [
            'one'    => env( 'IXP_FE_FRONTEND_CUSTOMER_ONE',   'member' ),
            'many'   => env( 'IXP_FE_FRONTEND_CUSTOMER_MANY',  'members' ),
            'owner'  => env( 'IXP_FE_FRONTEND_CUSTOMER_OWNER',  "member's" ),
            'owners' => env( 'IXP_FE_FRONTEND_CUSTOMER_OWNERS', "members'" ),
        ],
    ],

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
            'console-server-connection' => env( 'IXP_FE_FRONTEND_DISABLED_CONSOLE',           false ),
            'cust-kit'                  => env( 'IXP_FE_FRONTEND_DISABLED_CUSTKIT',           false ),
            'docstore'                  => env( 'IXP_FE_FRONTEND_DISABLED_DOCSTORE',          false ),
            'docstore_customer'         => env( 'IXP_FE_FRONTEND_DISABLED_DOCSTORE_CUSTOMER', false ),
            'filtered-prefixes'         => env( 'IXP_FE_FRONTEND_DISABLED_FILTERED_PREFIXES', true  ),
            'logs'                      => env( 'IXP_FE_FRONTEND_DISABLED_LOGS',              false ),
            'logo'                      => env( 'IXP_FE_FRONTEND_DISABLED_LOGO',              true  ),
            'lg'                        => env( 'IXP_FE_FRONTEND_DISABLED_LOOKING_GLASS',     true  ),
            'net-info'                  => env( 'IXP_FE_FRONTEND_DISABLED_NETINFO',           true ),
            'peering-manager'           => env( 'IXP_FE_FRONTEND_DISABLED_PEERING_MANAGER',   false ),
            'peering-matrix'            => env( 'IXP_FE_FRONTEND_DISABLED_PEERING_MATRIX',    false ),
            'ripe-atlas'                => true, // not ready for use yet
            'rs-prefixes'               => env( 'IXP_FE_FRONTEND_DISABLED_RS_PREFIXES',       true  ),
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
    | 1. \IXP\Models\User::AUTH_PUBLIC     -> tool is publicly available to all
    | 2. \IXP\Models\User::AUTH_CUSTUSER   -> tool is available to any logged in user
    | 3. \IXP\Models\User::AUTH_SUPERUSER  -> summary and any customer access is restricted to superadmins,
    |                                      logged in users may see their prefixes.
    */
    'rs-prefixes' => [
        'access'  => env( 'IXP_FE_RS_PREFIXES_ACCESS', \IXP\Models\User::AUTH_SUPERUSER ),
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
        ],


        // public member list and details are shown by default:
        'details_public' => env( 'IXP_FE_CUSTOMER_DETAILS_PUBLIC', true ),
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

    /*
   |--------------------------------------------------------------------------
   | Login History
   |--------------------------------------------------------------------------
   |
   */
    'login_history' => [
        'enabled'       => env( 'IXP_FE_LOGIN_HISTORY_ENABLED', true ),
    ],

    /*
   |--------------------------------------------------------------------------
   | API Keys
   |--------------------------------------------------------------------------
   |
   */
    'api_keys' => [

        // when an API key is created it is only shown once in the UI and there after it is hidden.
        // set IXP_FE_API_KEYS_SHOW=true in .env to show the keys
        'show_keys'       => env( 'IXP_FE_API_KEYS_SHOW', false ),

        // maximum API keys per user
        'max_keys'        => env( 'IXP_FE_API_KEYS_MAX', 10 ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Vlan Interface
    |--------------------------------------------------------------------------
    |
    */
    'vlaninterfaces' => [
        'hostname_required'  => env( 'IXP_FE_VLANINTERFACES_HOSTNAME_REQUIRED', true ),
    ],

    /*
    |--------------------------------------------------------------------------
    | IX-F Sources
    |--------------------------------------------------------------------------
    |
    */
    'ixfsources' => [
        'INEX LAN1' => [ 'url' => 'https://www.inex.ie/ixp/api/v4/member-export/ixf/1.0', 'ixid' => 1 ],
        'INEX LAN2' => [ 'url' => 'https://www.inex.ie/ixp/api/v4/member-export/ixf/1.0', 'ixid' => 2 ],
        'INEX LAN3' => [ 'url' => 'https://www.inex.ie/ixp/api/v4/member-export/ixf/1.0', 'ixid' => 3 ],

        'LONAP' => [ 'url' => 'https://portal.lonap.net/api/v4/member-export/ixf/1.0', 'ixid' => 1 ],

        'LINX LON1' => [ 'url' => 'https://portal.linx.net/members.json',       'ixid' => 0 ],
        'LINX LON2' => [ 'url' => 'https://portal.linx.net/members.json',       'ixid' => 1 ],
        'LINX Manchester' => [ 'url' => 'https://portal.linx.net/members.json', 'ixid' => 2 ],
        'LINX Scotland' => [ 'url' => 'https://portal.linx.net/members.json',   'ixid' => 3 ],
        'LINX NoVA' => [ 'url' => 'https://portal.linx.net/members.json',       'ixid' => 4 ],
        'LINX Wales' => [ 'url' => 'https://portal.linx.net/members.json',      'ixid' => 5 ],
        'LINX JED-IX' => [ 'url' => 'https://portal.linx.net/members.json',     'ixid' => 6 ],
        'LINX ManxIX' => [ 'url' => 'https://portal.linx.net/members.json',     'ixid' => 7 ],

    ],



];