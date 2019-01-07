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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Customers
///

Route::group( [ 'prefix' => 'customer', 'namespace' => 'Customer'], function() {
    Route::get( 'details',                  'CustomerController@details'        )->name( "customer@details"    );
    Route::get( 'associates',               'CustomerController@associates'     )->name( "customer@associates" );
    Route::get( 'detail/{id}',              'CustomerController@detail'         )->name( "customer@detail"     );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Peering Matrix
///

Route::get( 'peering-matrix', 'PeeringMatrixController@index' )->name( "peering-matrix@index" );


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Patch Panels
///

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port' ], function() {
    Route::get( 'view/{id}',                    'PatchPanelPortController@view'         )->name( "patch-panel-port@view"        );
    Route::get( 'loa-pdf/{id}',                 'PatchPanelPortController@loaPDF'       )->name( "patch-panel-port@loa-pdf"     );
    Route::get( 'verify-loa/{id}/{code}',       'PatchPanelPortController@verifyLoa'    )->name( "patch-panel-port@verify-loa"  );
});

Route::get( 'verify-loa/{id}/{code}',       'PatchPanel\PatchPanelPortController@verifyLoa'    )->name( "patch-panel-port@verify-loa"  );


Route::get( 'weather-map/{id}',                    'WeatherMapController@index' )->name( 'weathermap');


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Static content
///
/// See: http://docs.ixpmanager.org/features/static-content/
///
///
Route::get( 'content/{priv}/{page}',     'ContentController@index' )->name( 'content' );
Route::get( 'public-content/{page}',     'ContentController@public' )->name( 'public-content' );

Route::get( 'content/members/{priv}/{page}', 'ContentController@members' )->name( 'content/members' );

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Statistics -> a dedicated request object manages authorization
///

Route::group( [ 'prefix' => 'statistics' ], function() {
    Route::get(  'ixp/{category?}',                             'StatisticsController@ixp'               )->name( 'statistics/ixp'                );
    Route::get(  'infrastructure/{graphid?}/{category?}',       'StatisticsController@infrastructure'    )->name( 'statistics/infrastructure'     );
    Route::get(  'vlan/{vlanid?}/{protocol?}/{category?}',      'StatisticsController@vlan'              )->name( 'statistics/vlan'               );
    Route::get(  'switch/{switchid?}/{category?}',              'StatisticsController@switch'            )->name( 'statistics/switch'             );
    Route::get(  'trunk/{trunkid?}/{category?}',                'StatisticsController@trunk'             )->name( 'statistics/trunk'              );

    Route::get(  'members', 'StatisticsController@members' );
    Route::post( 'members', 'StatisticsController@members' )->name( 'statistics/members' );

    Route::get(  'p2p/{cid}', 'StatisticsController@p2p' )->name( 'statistics@p2p-get' );
    Route::post( 'p2p/{cid}', 'StatisticsController@p2p' )->name( 'statistics@p2p' );

    Route::get(  'member/{id?}',                                'StatisticsController@member'            )->name( 'statistics@member'             );

    Route::get(  'member-drilldown/{type}/{typeid}',            'StatisticsController@memberDrilldown'   )->name( 'statistics@member-drilldown'   );
    Route::get(  'latency/{vliid}/{protocol}',                  'StatisticsController@latency'           )->name( 'statistics@latency'            );
});


// Authentication routes...
Route::group( [ 'namespace' => 'Auth' ], function() {
    Route::get( 'logout',                   'LoginController@logout'                                )->name( "login@logout"                     );
    Route::get( 'login',                    'LoginController@showLoginForm'                         )->name( "login@showForm"                   );
    Route::post('login',                    'LoginController@login'                                 )->name( "login@login"                      );

    Route::get( 'password/forgot',          'ForgotPasswordController@showLinkRequestForm'          )->name( "forgot-password@show-form"        );
    Route::post('password/forgot',          'ForgotPasswordController@sendResetLinkEmail'           )->name( "forgot-password@reset-email"      );

    Route::get( 'password/reset/{token}',   'ResetPasswordController@showResetForm'                 )->name( "reset-password@show-reset-form"   );
    Route::post('password/reset',           'ResetPasswordController@reset'                         )->name( "reset-password@reset"             );

    Route::get( 'username',                 'ForgotPasswordController@showUsernameForm'             )->name( "forgot-password@showUsernameForm" );
    Route::post('forgot-username',          'ForgotPasswordController@sendUsernameEmail'            )->name( "forgot-password@username-email"   );


    // IXP Manager <v4.9 aliases for static links
    Route::redirect( '/auth/logout',        '/logout',          301 );
    Route::redirect( '/auth/login',         '/login',           301 );
    Route::redirect( '/auth/lost-password', '/password/forget', 301 );
    Route::redirect( '/auth/lost-username', '/username',        301 );
});





/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// MEMBER EXPORT
///


Route::get( 'participants.json', function() { return redirect(route('ixf-member-export')); });

//Auth::routes();


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// DEFAULT ROUTE
///

Route::get( '/', function() {

    if( Auth::guest() ) {
        return redirect(route( "login@showForm" ) );
    }

    if( Auth::getUser()->isSuperUser() ) {
        return redirect( route( "admin@dashboard" ) );
    } else if( Auth::getUser()->isCustAdmin() ) {
        return redirect( route( 'contact@list' ) );
    } else {
        return redirect( route( "dashboard@index" ) );
    }
})->name( 'default' );

/////////////////////////////////////////////////////////////////////////////////////////////////



