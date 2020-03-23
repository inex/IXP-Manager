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


    'legalname'   => env( 'IDENTITY_LEGALNAME', '*** CONFIG IDENTITY IN .env ***' ),

    'location'    => [
            'city'       => env( 'IDENTITY_CITY', '*** CONFIG IDENTITY IN .env ***' ),
            'country'    => env( 'IDENTITY_COUNTRY', '*** CONFIG IDENTITY IN .env ***' ),
        ],

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
    'titlename'     => env( 'IDENTITY_TITLENAME', env( 'IDENTITY_SITENAME', 'IXP Manager' ) ),

    'corporate_url' => env( 'IDENTITY_CORPORATE_URL', '*** CONFIG IDENTITY IN .env ***' ),
    'url'           => env( 'APP_URL', '*** CONFIG APP_URL IN .env ***' ),
    'biglogo'       => env( 'IDENTITY_BIGLOGO', '*** CONFIG IDENTITY IN .env ***' ),

    'vlans'       => [
            'default' => env( 'IDENTITY_DEFAULT_VLAN', 1 ),
    ],

];
