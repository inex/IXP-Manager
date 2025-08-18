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
     * IXP Identity Information
     *
     *
     */


    'legalname'   => env( 'IDENTITY_LEGALNAME', 'IXP' ),

    'location'    => [
            'city'       => env( 'IDENTITY_CITY', 'Cork' ),
            'country'    => env( 'IDENTITY_COUNTRY', 'IE' ),
        ],

    'orgname'     => env( 'IDENTITY_ORGNAME', 'IXP' ),
    'name'        => env( 'IDENTITY_NAME', 'IXP' ),
    'email'       => env( 'IDENTITY_EMAIL', 'ixp@example.com' ),
    'testemail'   => env( 'IDENTITY_TESTEMAIL', 'ixp@example.com' ),
    'rsvpemail'   => env( 'IDENTITY_RSVPEMAIL', 'ixp@example.com' ),
    
    'watermark'   => env( 'IDENTITY_WATERMARK', 'IXP Manager' ),

    'support_email'       => env( 'IDENTITY_SUPPORT_EMAIL', 'ixp@example.com' ),
    'support_phone'       => env( 'IDENTITY_SUPPORT_PHONE', '+353 20 912 2000' ),
    'support_hours'       => env( 'IDENTITY_SUPPORT_HOURS', \IXP\Models\Customer::NOC_HOURS_24x7 ),

    'billing_email'       => env( 'IDENTITY_BILLING_EMAIL', 'ixp@example.com' ),
    'billing_phone'       => env( 'IDENTITY_BILLING_PHONE', '+353 20 912 2000' ),
    'billing_hours'       => env( 'IDENTITY_BILLING_HOURS', \IXP\Models\Customer::NOC_HOURS_8x5 ),

    'sitename'      => env( 'IDENTITY_SITENAME', 'IXP Manager' ),
    'titlename'     => env( 'IDENTITY_TITLENAME', env( 'IDENTITY_SITENAME', 'IXP Manager' ) ),

    'corporate_url' => env( 'IDENTITY_CORPORATE_URL', 'https://www.example.com/' ),
    'url'           => env( 'APP_URL', '' ),
    'biglogo'       => env( 'IDENTITY_BIGLOGO', 'https://www.ixpmanager.org/images/logos/ixp-manager.png' ),

    'vlans'       => [
            'default' => env( 'IDENTITY_DEFAULT_VLAN', 1 ),
    ],

];
