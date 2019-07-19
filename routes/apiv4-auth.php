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

use Illuminate\Http\Request;
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


Route::post( 'l2-address/add',                                  'Layer2AddressController@add' );
Route::post( 'l2-address/delete/{id}',                          'Layer2AddressController@delete' );

Route::group( [ 'prefix' => 'customer-note', 'namespace' => 'Customer\Note'], function() {
    Route::get(    'ping/{id?}',            'CustomerNotesController@ping'      )->name( 'customer-notes@ping');
    Route::get(    'get/{id}',              'CustomerNotesController@get'       )->name( 'customer-notes@get');
});

Route::post( 'utils/markdown',                                  'UtilsController@markdown' )->name( "utils@markdown" );



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Whois look ups for Prefix and ASN Number
//
Route::get( 'aut-num/{asn}', function( $asn ) {
    $whois = new IXP\Utils\Whois( config('ixp_api.whois.asn.host'), config('ixp_api.whois.asn.port') );
    return response( $whois->whois( 'AS' . (int)$asn ), 200 )->header('Content-Type', 'text/plain');
})->name('api-v4-aut-num');

Route::get( 'prefix-whois/{prefix}/{mask}', function( $prefix, $mask ) {
    $whois = new IXP\Utils\Whois( config('ixp_api.whois.prefix.host'), config('ixp_api.whois.prefix.port') );
    return response( $whois->whois( $prefix .'/' . $mask ), 200 )->header('Content-Type', 'text/plain');
})->name('api-v4-prefix-whois');

