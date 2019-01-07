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

return [

    /*
    | IXP Configuration
    */

    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
    ;;
    ;; Enables multi-IXP mode.
    ;;
    ;; See: https://github.com/inex/IXP-Manager/wiki/Multi-IXP-Functionality
    ;; */
    'multiixp' => [
        'enabled' => env( 'IXP_MULTIIXP_ENABLED', false ),
    ],

    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
    ;;
    ;; Enables resller mode
    ;;
    ;; See: https://github.com/inex/IXP-Manager/wiki/Reseller-Functionality
    ;; */
    'reseller' => [
        'enabled' => env( 'IXP_RESELLER_ENABLED', false ),

        // If reseller mode enabled and this is set to true then super admin or customer itself
        // can not add/change a resold customer's billing details.
        'no_billing' => env( 'IXP_RESELLER_RESOLD_BILLING', false ),
    ],


    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
    ;; Specifies whether to display and enable control of AS112 functionality for customers
    ;;
    ;; See https://github.com/inex/IXP-Manager/wiki/AS112
    ;; */
    'as112' => [
        'ui_active' => env( 'IXP_AS112_UI_ACTIVE', false ),
    ],


    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
       ;; Peering Manager
       ;;
       ;; See: https://github.com/inex/IXP-Manager/wiki/Peering-Manager
       ;;
       ;; The Peering Manager allows your members to send peering requests to other members
       ;; that contain all the necessary peering details.
       ;;
       ;; For testing / experimentation you can enabled test mode below and, when enabled, all
       ;; peering requests will be sent to the testemail.
       ;;
       ;; Normally, the peering manager adds a note to the peer's notes and sets a request last
       ;; sent date when a peering request is sent. In test mode, this will not happen.
       ;; If you want this to happen in test mode, set testnote and testdate to true below.
       ;;
       ;; */
    'peering_manager' => [
        'testmode'  => env( 'PEERING_MANAGER_TESTMODE', false ),
        'testemail' => env( 'PEERING_MANAGER_TESTEMAIL', "user@example.com" ),
        'testnote'  => env( 'PEERING_MANAGER_TESTNOTE', false ),
        'testdate'  => env( 'PEERING_MANAGER_TESTDATE', false ),
    ],


    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
       ;; Minimum user auth level for the peering matrix
       ;;
       ;; See https://docs.ixpmanager.org/usage/users/#types-of-users
       ;;
       ;; */
    'peering-matrix' => [
        'min-auth' => env( 'PEERING_MATRIX_MIN_AUTH', \Entities\User::AUTH_PUBLIC ),
    ],



    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
       ;; Route Server and IRRDB Filtering
       ;;
       ;;
       ;; */

    'irrdb' => [
        'bgpq3' => [
            'path' => env( 'IXP_IRRDB_BGPQ3_PATH', false ),
        ],

        // ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        // Minimum subnet sizes
        //
        // Used by the route server/collector/as112 templates and
        // the bgpq3 command line.
        // Templates:
        //   - resources/views/api/v4/router/as112/bird/header.foil.php
        //   - resources/views/api/v4/router/collector/bird/header.foil.php
        //   - resources/views/api/v4/router/server/bird/header.foil.php
        //
        'min_v4_subnet_size' => env( 'IXP_IRRDB_MIN_V4_SUBNET_SIZE', 24 ),
        'min_v6_subnet_size' => env( 'IXP_IRRDB_MIN_V6_SUBNET_SIZE', 48 ),

    ],


];
