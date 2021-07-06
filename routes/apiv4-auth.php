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

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api/v4" middleware group. Enjoy building your API!
|
*/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Layer2Address
//
Route::group( [ 'prefix' => 'ixps' ], function() {
    Route::post(    'store/{showFeMessage?}',    'Layer2AddressController@store'  )->name( 'l2-address@create' );
    Route::delete(  '{l2a}/{showFeMessage?}',    'Layer2AddressController@delete' )->name( 'l2-address@delete' );
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Customer Note
//
Route::group( [ 'prefix' => 'customer-note', 'namespace' => 'Customer\Note'], function() {
    Route::get(    'ping/{c?}',            'CustomerNotesController@ping'      )->name( 'customer-notes@ping');
    Route::get(    'get/{cn}',             'CustomerNotesController@get'       )->name( 'customer-notes@get');
});

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Utils
//
Route::post( 'utils/markdown',  'UtilsController@markdown' )->name( "utils@markdown" );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Customer
//
Route::post( 'customer/by-vlan-and-protocol', 'CustomerController@byVlanAndProtocol' )->name("customer@byVlanAndProtocol" );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// IrrdbPrefix
//
Route::post( 'irrdb-prefix/by-customer-and-protocol', 'IrrdbPrefixController@byCustomerAndProtocol' )->name("irrdb-prefix@by-customer-and-protocol" );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Whois look ups for Prefix and ASN Number
//
Route::get( 'aut-num/{asn}',                'WhoisController@asn'       )->name('api-v4-aut-num');
Route::get( 'prefix-whois/{prefix}/{mask?}', 'WhoisController@prefix' )->name('api-v4-prefix-whois');