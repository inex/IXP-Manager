<?php

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 *
 */


// Not a traditional Laravel config file - this is an IXP Manager config file which
// determines what .env elements are configurable via the frontend and sets up the
// form for this.

return [

    /*
     * Configuration elements are arranged into panels
     */
    'panels' => [


        /*
         *
         *             'console-server-connection' => env( 'IXP_FE_FRONTEND_DISABLED_CONSOLE',           false ),
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
            'phpinfo'                   => env( 'IXP_FE_FRONTEND_DISABLED_PHPINFO',           true  ),
            'ripe-atlas'                => true, // not ready for use yet
            'rs-prefixes'               => env( 'IXP_FE_FRONTEND_DISABLED_RS_PREFIXES',       true  ),
            'rs-filters'                => env( 'IXP_FE_FRONTEND_DISABLED_RS_FILTERS',        true  ),
         */
        'frontend_controllers' => [
            'title'       => 'Features',
            'description' => "These are features or modules that can be enabled or disabled. Some are
                                    disabled by default as they may require extra configuration settings.",

            'fields' => [

                'console-server-connection' => [
                    'config_key' => 'ixp_fe.frontend.disabled.console-server-connection',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_CONSOLE',
                    'type'       => 'radio',
                    'invert'     => true,
                    'name'       => 'Console Server Connections',
                    'docs_url'   => 'https://docs.ixpmanager.org/features/console-servers/', // can be null
                    'help'       => 'An IXP would typically have out of band access (for emergencies, firmware upgrades, 
                                        etc) to critical infrastructure devices by means of a console server. This 
                                        module allows you to record console server port connections.',
                ],
                'cust-kit'                  => [
                    'config_key' => 'ixp_fe.frontend.disabled.cust-kit',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_CUSTKIT',
                    'type'       => 'radio',
                    'name'       => 'Customer Kit',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'docstore'                  => [
                    'config_key' => 'ixp_fe.frontend.disabled.docstore',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_DOCSTORE',
                    'type'       => 'radio',
                    'name'       => 'Document Store',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'docstore_customer'         => [
                    'config_key' => 'ixp_fe.frontend.disabled.docstore_customer',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_DOCSTORE_CUSTOMER',
                    'type'       => 'radio',
                    'name'       => 'Customer Document Store',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'filtered-prefixes'         => [
                    'config_key' => 'ixp_fe.frontend.disabled.filtered-prefixes',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_FILTERED_PREFIXES',
                    'type'       => 'radio',
                    'name'       => 'Filtered Prefixes',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'logs'                      => [
                    'config_key' => 'ixp_fe.frontend.disabled.logs',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_LOGS',
                    'type'       => 'radio',
                    'name'       => 'Logs',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'logo'                      => [
                    'config_key' => 'ixp_fe.frontend.disabled.logo',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_LOGO',
                    'type'       => 'radio',
                    'name'       => 'Logo',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'lg'                        => [
                    'config_key' => 'ixp_fe.frontend.disabled.lg',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_LOOKING_GLASS',
                    'type'       => 'radio',
                    'name'       => 'Looking Glass',
                    'docs_url'   => 'https://docs.ixpmanager.org/features/looking-glass/',
                    'help'       => 'IXP Manager supports full looking glass features when using the Bird BGP daemon and Bird\'s Eye (a simple secure micro service for querying Bird).',
                ],
                'net-info'                  => [
                    'config_key' => 'ixp_fe.frontend.disabled.net-info',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_NETINFO',
                    'type'       => 'radio',
                    'name'       => 'Net Information',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'peering-manager'           => [
                    'config_key' => 'ixp_fe.frontend.disabled.peering-manager',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_PEERING_MANAGER',
                    'type'       => 'radio',
                    'name'       => 'Peering Manager',
                    'docs_url'   => 'https://docs.ixpmanager.org/features/peering-manager/',
                    'help'       => 'The Peering Manager is a fantastic tool that allows your members to view and track their peerings with other IXP members.',
                ],
                'peering-matrix'            => [
                    'config_key' => 'ixp_fe.frontend.disabled.peering-matrix',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_PEERING_MATRIX',
                    'type'       => 'radio',
                    'name'       => 'Peering Matrix',
                    'docs_url'   => 'https://docs.ixpmanager.org/features/peering-matrix/',
                    'help'       => 'The peering matrix system builds up a list of who is peering with whom over your IXP.',
                ],
                'phpinfo'                   => [
                    'config_key' => 'ixp_fe.frontend.disabled.phpinfo',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_PHPINFO',
                    'type'       => 'radio',
                    'name'       => 'PHP Information',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'rs-prefixes'               => [
                    'config_key' => 'ixp_fe.frontend.disabled.rs-prefixes',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_RS_PREFIXES',
                    'type'       => 'radio',
                    'name'       => 'RS Prefixes',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'rs-filters'                => [
                    'config_key' => 'ixp_fe.frontend.disabled.rs-filters',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_RS_FILTERS',
                    'type'       => 'radio',
                    'name'       => 'RS Filters',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'as112'                     => [
                    'config_key' => 'ixp.as112.ui_active',
                    'dotenv_key' => 'IXP_AS112_UI_ACTIVE',
                    'type'       => 'radio',
                    'name'       => 'AS112 functionality',
                    'docs_url'   => 'https://github.com/inex/IXP-Manager/wiki/AS112',
                    'help'       => 'Specifies whether to display and enable control of AS112 functionality for customers',
                ],
            ],
        ],


        'identity' => [

            'title'       => 'IXP Identity',
            'description' => 'IXP Identity Information.',

            'fields' => [
                'legalname'        => [
                    'config_key' => 'identity.legalname',
                    'dotenv_key' => 'IDENTITY_LEGALNAME',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Legal Name',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'location_city'    => [
                    'config_key' => 'identity.location.city',
                    'dotenv_key' => 'IDENTITY_CITY',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Location: City',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'location_country' => [
                    'config_key' => 'identity.location.country',
                    'dotenv_key' => 'IDENTITY_COUNTRY',
                    'type'       => 'select',
                    'options'    => [ 'type' => 'countries' ], // special option list for countries
                    'rules'      => '',
                    'name'       => 'Location: Country',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'orgname'          => [
                    'config_key' => 'identity.orgname',
                    'dotenv_key' => 'IDENTITY_ORGNAME',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Organisation Name',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'name'             => [
                    'config_key' => 'identity.name',
                    'dotenv_key' => 'IDENTITY_NAME',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Name',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'email'            => [
                    'config_key' => 'identity.email',
                    'dotenv_key' => 'IDENTITY_EMAIL',
                    'type'       => 'text',
                    'rules'      => 'nullable|max:255|email',
                    'name'       => 'Email Address',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'testemail'        => [
                    'config_key' => 'identity.testemail',
                    'dotenv_key' => 'IDENTITY_TESTEMAIL',
                    'type'       => 'text',
                    'rules'      => 'nullable|max:255|email',
                    'name'       => 'Test Email Address',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'rsvpemail'        => [
                    'config_key' => 'identity.rsvpemail',
                    'dotenv_key' => 'IDENTITY_RSVPEMAIL',
                    'type'       => 'text',
                    'rules'      => 'nullable|max:255|email',
                    'name'       => 'RSVP Email Address',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'watermark'        => [
                    'config_key' => 'identity.watermark',
                    'dotenv_key' => 'IDENTITY_WATERMARK',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Watermark',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'support_email'    => [
                    'config_key' => 'identity.support_email',
                    'dotenv_key' => 'IDENTITY_SUPPORT_EMAIL',
                    'type'       => 'text',
                    'rules'      => 'nullable|max:255|email',
                    'name'       => 'Support Email Address',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'support_phone'    => [
                    'config_key' => 'identity.support_phone',
                    'dotenv_key' => 'IDENTITY_SUPPORT_PHONE',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Support Phone Number',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'support_hours'    => [
                    'config_key' => 'identity.support_hours',
                    'dotenv_key' => 'IDENTITY_SUPPORT_HOURS',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Support Hours',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'billing_email'    => [
                    'config_key' => 'identity.billing_email',
                    'dotenv_key' => 'IDENTITY_BILLING_EMAIL',
                    'type'       => 'text',
                    'rules'      => 'nullable|max:255|email',
                    'name'       => 'Billing Email Address',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'billing_phone'    => [
                    'config_key' => 'identity.billing_phone',
                    'dotenv_key' => 'IDENTITY_BILLING_PHONE',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Billing Phone Number',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'billing_hours'    => [
                    'config_key' => 'identity.billing_hours',
                    'dotenv_key' => 'IDENTITY_BILLING_HOURS',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Billing Hours',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'sitename'         => [
                    'config_key' => 'identity.sitename',
                    'dotenv_key' => 'IDENTITY_SITENAME',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Site Name',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'titlename'        => [
                    'config_key' => 'identity.titlename',
                    'dotenv_key' => 'IDENTITY_TITLENAME',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Site Title',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'corporate_url'    => [
                    'config_key' => 'identity.corporate_url',
                    'dotenv_key' => 'IDENTITY_CORPORATE_URL',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Corporate Url Address',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'url'              => [
                    'config_key' => 'identity.url',
                    'dotenv_key' => 'APP_URL',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Url Address',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'biglogo'          => [
                    'config_key' => 'identity.biglogo',
                    'dotenv_key' => 'IDENTITY_BIGLOGO',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Big Logo',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'vlans_default'    => [
                    'config_key' => 'identity.vlans.default',
                    'dotenv_key' => 'IDENTITY_DEFAULT_VLAN',
                    'type'       => 'select',
                    'options'    => [ 'type' => 'collection', 'list' => [ 'model' => 'Vlan', 'keys' => 'id', 'values' => 'name' ] ],
                    'name'       => 'Default Vlans',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'alerts_recipient_name' => [
                    'config_key' => 'mail.alerts_recipient.name',
                    'dotenv_key' => 'IDENTITY_ALERTS_NAME',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'Alert Recipient Name',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'alerts_recipient_address' => [
                    'config_key' => 'mail.alerts_recipient.address',
                    'dotenv_key' => 'IDENTITY_ALERTS_EMAIL',
                    'type'       => 'text',
                    'rules'      => 'nullable|max:255|email',
                    'name'       => 'Alert Recipient Email Address',
                    'docs_url'   => null,
                    'help'       => '',
                ],
            ],

        ],

        'auth' => [

            'title'       => 'Authentication',
            'description' => "Authentication related options.",

            'fields' => [

                'login_history' => [
                    'config_key' => 'ixp_fe.login_history.enabled',
                    'dotenv_key' => 'IXP_FE_LOGIN_HISTORY_ENABLED',
                    'type'       => 'radio',
                    'name'       => "Record Login History",
                    'help'       => 'Record the login history for users. Expunged after six months by default.',
                ],

                // do wee need here add the peeringdb api authentication and oauth data?

            ],

        ],

        'third_party' => [

            'title'       => '3rd Parties',
            'description' => "Configuration options for third party services.",

            'fields' => [

                'peeringdb_api_key' => [

                    'config_key' => 'ixp_api.peeringdb.api_key',
                    'dotenv_key' => 'IXP_API_PEERING_DB_API_KEY',
                    'type'       => 'text',
                    'name'       => "PeeringDB API Key",
                    'docs_url'   => 'https://docs.peeringdb.com/howto/api_keys/',
                    'help'       => "IXP Manager uses information from PeeringDB in a number of places. Setting an API
                                        key is highly recommended so additional information can be accessed and so that
                                        rate limited can be avoided.",
                ],

                'public'           => [
                    'config_key' => 'ixp_api.public',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_PUBLIC',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export is Public',
                    'docs_url'   => null,
                    'help'       => 'If false, an API key is required',
                ],
                'access_key'       => [
                    'config_key' => 'ixp_api.access_key',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_ACCESS_KEY',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Access Key',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'excludes_rfc5398' => [
                    'config_key' => 'ixp_api.excludes.rfc5398',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_RFC5398',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Excludes RFC5398',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'excludes_rfc6996' => [
                    'config_key' => 'ixp_api.excludes.rfc6996',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_RFC6996',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Excludes RFC6996',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'excludes_tags'    => [
                    'config_key' => 'ixp_api.excludes.tags',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_TAGS',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Excludes Tags',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'excludes_asnum'   => [
                    'config_key' => 'ixp_api.excludes.asnum',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_ASNUM',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Excludes AS Num',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'excludes_switch'  => [
                    'config_key' => 'ixp_api.excludes.switch',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_SWITCH',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Excludes Switches',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'excludes_ixp'     => [
                    'config_key' => 'ixp_api.excludes.ixp',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_IXP',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Excludes IXPs',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'excludes_member'  => [
                    'config_key' => 'ixp_api.excludes.member',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_MEMBER',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Excludes Members',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'excludes_intinfo' => [
                    'config_key' => 'ixp_api.excludes.intinfo',
                    'dotenv_key' => 'IXP_API_JSONEXPORTSCHEMA_EXCLUDE_INTINFO',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'JSON Export Excludes Int Info',
                    'docs_url'   => null,
                    'help'       => '',
                ],

            ],

        ],

        'admin_options' => [

            'title'       => 'Admin',
            'description' => "Various administrator related options.",

            'fields' => [

                'default_graph_period' => [

                    'config_key' => 'ixp_fe.admin.default_graph_period',
                    'dotenv_key' => 'IXP_FE_ADMIN_DASHBOARD_DEFAULT_GRAPH_PERIOD',
                    'type'       => 'select',
                    'options'    => [ 'type' => 'array', 'list' => IXP\Services\Grapher\Graph::PERIODS ],
                    'name'       => "Admin Dashbaord Graph Period",
                    'help'       => 'Default graph period on the admin dashboard.',
                ],


                'billing-updates-notification' => [

                    'config_key' => 'ixp_fe.frontend.billing-updates.notification',
                    'dotenv_key' => 'IXP_FE_BILLING_UPDATES',
                    'type'       => 'text',
                    'rules'      => 'nullable|max:255|email',
                    'name'       => 'Billing Updates Notification',
                    'docs_url'   => 'https://docs.ixpmanager.org/usage/customers/#notification-of-billing-details-changed',
                    'help'       => "If a member edits their billing details in their portal, the changes can be emailed to 
                                        this address. If left blank, then no emails will be sent.",
                ],

                // do we need more Grapher attributes here?
            ],
        ],

        'misc_options' => [

            'title'       => 'Miscellaneous',
            'description' => "These are various frontend options which you can tweak as appropriate.",

            'fields' => [

                'rs-filters-ttl' => [
                    // this via config() will give default value
                    'config_key' => 'ixp_fe.frontend.rs-filters.ttl',
                    'dotenv_key' => 'IXP_FE_RS_FILTERS_TIME_TO_LIVE',
                    'type'       => 'textarea',
                    'rules'      => 'nullable|max:1024',
                    'name'       => 'Route Server Update Period',
                    //'docs_url'   => '',
                    'help'       => "If you have enabled the route server community filtering via UI option, then your members will
                                        need to know how often you update their configurations. The text you enter here will be 
                                        displayed on the route server filters page.",
                ],
                'rpki_rtr1_host' => [
                    'config_key' => 'ixp.rpki.rtr1.host',
                    'dotenv_key' => 'IXP_RPKI_RTR1_HOST',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'RPKI RTR1 Host',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'rpki_rtr1_port' => [
                    'config_key' => 'ixp.rpki.rtr1.port',
                    'dotenv_key' => 'IXP_RPKI_RTR1_PORT',
                    'type'       => 'text',
                    'rules'      => 'between:1000,9999',
                    'name'       => 'RPKI RTR1 Port',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'rpki_rtr2_host' => [
                    'config_key' => 'ixp.rpki.rtr2.host',
                    'dotenv_key' => 'IXP_RPKI_RTR2_HOST',
                    'type'       => 'text',
                    'rules'      => '',
                    'name'       => 'RPKI RTR2 Host',
                    'docs_url'   => null,
                    'help'       => '',
                ],
                'rpki_rtr2_port' => [
                    'config_key' => 'ixp.rpki.rtr2.port',
                    'dotenv_key' => 'IXP_RPKI_RTR2_PORT',
                    'type'       => 'text',
                    'rules'      => 'between:1000,9999',
                    'name'       => 'RPKI RTR2 Port',
                    'docs_url'   => null,
                    'help'       => '',
                ],


            ],
        ],

    ],


];

