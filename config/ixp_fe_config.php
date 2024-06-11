<?php

// Not a traditional Laravel config file - determines what is configurable
// on the IXP Manager frontend

// Laslzo - what else might you think about:


// everything in identity.php
//   vlans.default -> should be a dropdown of VLANs so we'll need a new select options in the array below:
//           'optionsdb' => [ 'model' => 'Vlan', 'keys' => 'id', 'values' => 'name' ]

// ixp_api.json_export_schema
// ixp.as112 as part of modules
// ixp.rpki





return [

    'panels' => [

        'frontend_controllers' => [
            'tabname'        => 'Modules',
            'tabdescription' => "These are features that can be enabled or disabled. Some are
                                    disabled by default as they may require extra configuration settings.",

            'options' => [

                'console-server-connection' => [

                    // this via config() will give default value
                    'config_key' => 'ixp_fe.frontend.disabled.console-server-connection',
                    'dotenv_key' => 'IXP_FE_FRONTEND_DISABLED_CONSOLE',
                    'type'       => 'radio',
                    'name'       => 'Console Server Connections',
                    'docs_url'   => 'https://docs.ixpmanager.org/features/console-servers/', // can be null
                    'help'       => "An IXP would typically have out of band access (for emergencies, firmware upgrades, 
                                        etc) to critical infrastructure devices by means of a console server. This 
                                        module allows you to record console server port connections.",

                ],

                //                'cust-kit'                  => env( 'IXP_FE_FRONTEND_DISABLED_CUSTKIT',           false ),
                //                'docstore'                  => env( 'IXP_FE_FRONTEND_DISABLED_DOCSTORE',          false ),
                //                'docstore_customer'         => env( 'IXP_FE_FRONTEND_DISABLED_DOCSTORE_CUSTOMER', false ),
                //                'filtered-prefixes'         => env( 'IXP_FE_FRONTEND_DISABLED_FILTERED_PREFIXES', true  ),
                //                'logs'                      => env( 'IXP_FE_FRONTEND_DISABLED_LOGS',              false ),
                //                'logo'                      => env( 'IXP_FE_FRONTEND_DISABLED_LOGO',              true  ),
                //                'lg'                        => env( 'IXP_FE_FRONTEND_DISABLED_LOOKING_GLASS',     true  ),
                //                'net-info'                  => env( 'IXP_FE_FRONTEND_DISABLED_NETINFO',           true ),
                //                'peering-manager'           => env( 'IXP_FE_FRONTEND_DISABLED_PEERING_MANAGER',   false ),
                //                'peering-matrix'            => env( 'IXP_FE_FRONTEND_DISABLED_PEERING_MATRIX',    false ),
                //                'phpinfo'                   => env( 'IXP_FE_FRONTEND_DISABLED_PHPINFO',           true  ),
                //                'ripe-atlas'                => true, // not ready for use yet
                //                'rs-prefixes'               => env( 'IXP_FE_FRONTEND_DISABLED_RS_PREFIXES',       true  ),
                //                'rs-filters'                => env( 'IXP_FE_FRONTEND_DISABLED_RS_FILTERS',        true  ),


            ]
        ],


        'auth' => [

            'tabname'        => 'Authentication',
            // 'tabdescription' => "Authentication related options.",

            'options' => [

                'login_history' => [

                    'config_key' => 'ixp_fe.login_history.enabled',
                    'dotenv_key' => 'IXP_FE_LOGIN_HISTORY_ENABLED',
                    'type'       => 'radio',
                    'name'       => "Record Login History",
                    'help'       => 'Record the login history for users. Expunged after six months by default.',
                ],

            ],

        ],

        'third_party' => [

            'tabname'        => '3rd Parties',
             'tabdescription' => "Configuration options for third party services.",

            'options' => [

                'peeringdb_api_key' => [

                    'config_key' => 'ixp_api.peeringdb.api_key',
                    'dotenv_key' => 'IXP_API_PEERING_DB_API_KEY',
                    'type'       => 'test',
                    'name'       => "PeeringDB API Key",
                    'docs_url'   => 'https://docs.peeringdb.com/howto/api_keys/',
                    'help'       => "IXP Manager uses information from PeeringDB in a number of places. Setting an API
                                        key is highly recommended so additional information can be accessed and so that
                                        rate limited can be avoided.",
                ],

            ],

        ],



        'admin_options' => [

            'tabname'        => 'Admin',
            'tabdescription' => "Various administrator related options.",

            'options' => [

                'default_graph_period' => [

                    'config_key' => 'ixp_fe.admin.default_graph_period',
                    'dotenv_key' => 'IXP_FE_ADMIN_DASHBOARD_DEFAULT_GRAPH_PERIOD',
                    'type'       => 'select',
                    'options'    => IXP\Services\Grapher\Graph::PERIODS,
                    'name'       => "Admin Dashbaord Graph Period",
                    'help'       => 'Default graph period on the admin dashboard.',
                ],



                'billing-updates-notification' => [

                    'config_key' => 'ixp_fe.frontend.billing-updates.notification',
                    'dotenv_key' => 'IXP_FE_BILLING_UPDATES',
                    'type'       => 'email',
                    'rules'      => 'nullable|max:255|email',
                    'name'       => 'Billing Updates Notification',
                    'docs_url'   => 'https://docs.ixpmanager.org/usage/customers/#notification-of-billing-details-changed',
                    'help'       => "If a member edits their billing details in their portal, the changes can be emailed to 
                                        this address. If left blank, then no emails will be sent.",
                ],

            ],
        ],


        'misc_options' => [

            'tabname'        => 'Miscellaneous',
            'tabdescription' => "These are various frontend options which you can tweak as appropriate.",

            'options' => [

                'rs-filters-ttl' => [

                    // this via config() will give default value
                    'config_key' => 'ixp_fe.frontend.rs-filters.ttl',
                    'dotenv_key' => 'IXP_FE_RS_FILTERS_TIME_TO_LIVE',
                    'type'       => 'textarea',
                    'rules'      => 'nullable|max:1024',
                    'name'       => 'Route Server Update Period',
                    // 'docs_url'   => ''
                    'help'       => "If you have enabled the route server community filtering via UI option, then your members will
                                        need to know how often you update their configurations. The text you enter here will be 
                                        displayed on the route server filters page.",
                ],



            ]

        ]



    ]


];

