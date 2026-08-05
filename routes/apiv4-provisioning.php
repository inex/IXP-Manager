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

Route::group( [ 'prefix' => 'member' ], function() {
    Route::post( '',                'MemberController@storeCustomer' )->name( 'api-v4-provisioning@member-store'      );
    Route::get(  '{cust}',          'MemberController@showCustomer'  )->name( 'api-v4-provisioning@member-show'       );
    Route::post( '{cust}/user',     'MemberController@storeUser'     )->name( 'api-v4-provisioning@member-user-store' );
} );
