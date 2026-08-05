<?php

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Support\Facades\Route;

use IXP\Http\Middleware\Provisioning\ShortCircuitKnownReference;

/*
|--------------------------------------------------------------------------
| Provisioning API Routes
|--------------------------------------------------------------------------
|
| ** EXTERNAL ROUTES **
|
| Stateless endpoints for authenticated superusers via API key, intended to be called
| from an external ordering / billing system. No cookie, no CSRF, no AJAX.
|
| Registered by IXP\Providers\ProvisioningRouteServiceProvider under the prefix
| `admin/api/v4/provisioning`.
|
| Anything which creates, changes or removes state MUST use POST, PUT or DELETE -
| never GET.
|
*/

Route::get( 'ping', 'PingController@ping' )->name( 'api-v4-provisioning@ping' );

Route::get( 'switch',           'PortController@switches'        )->name( 'api-v4-provisioning@switches'        );
Route::get( 'infrastructure',   'PortController@infrastructures' )->name( 'api-v4-provisioning@infrastructures' );
Route::get( 'port/free',        'PortController@free'            )->name( 'api-v4-provisioning@port-free'      );

Route::get( 'vlan/{vlan}/address', 'IpController@addresses' )->name( 'api-v4-provisioning@vlan-addresses' );

Route::group( [ 'prefix' => 'member' ], function() {
    Route::post( '',                'MemberController@storeCustomer' )->name( 'api-v4-provisioning@member-store'      );
    Route::get(  '{cust}',          'MemberController@showCustomer'  )->name( 'api-v4-provisioning@member-show'       );
    Route::post( '{cust}/user',     'MemberController@storeUser'     )->name( 'api-v4-provisioning@member-user-store' );

    Route::post( '{cust}/connection', 'ConnectionController@store' )->name( 'api-v4-provisioning@connection-store' );
    Route::get(  '{cust}/connection', 'ConnectionController@index' )->name( 'api-v4-provisioning@connection-index' );
} );

Route::delete( 'connection/{vi}', 'ConnectionController@destroy' )->name( 'api-v4-provisioning@connection-destroy' );

Route::post( 'onboarding', 'OnboardingController@store' )
    ->middleware( ShortCircuitKnownReference::class )
    ->name( 'api-v4-provisioning@onboarding' );
