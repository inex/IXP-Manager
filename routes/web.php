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
    Route::get( 'detail/{cust}',            'CustomerController@detail'         )->name( 'customer@detail'     );
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
/// Patch Panel Port
///
Route::group( [ 'namespace' => 'PatchPanel\Port', 'prefix' => 'patch-panel-port' ], function() {
    Route::get( 'view/{ppp}',                    'PortController@view'     )->name( "patch-panel-port@view"        );
    Route::get( '{ppp}/loa/verify/{code}',       'LoaController@verify'    )->name( "patch-panel-port-loa@verify"  );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// weather-map
///
Route::get( 'weather-map/{id}',                  'WeatherMapController@index' )->name( 'weathermap');


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Static content
///
/// See: http://docs.ixpmanager.org/features/static-content/
///
///
Route::get( 'content/{priv}/{page}',        'ContentController@index'   )->name( 'content'          );
Route::get( 'public-content/{page}',        'ContentController@public'  )->name( 'public-content'   );
Route::get( 'content/members/{priv}/{page}','ContentController@members' )->name( 'content/members'  );

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Statistics -> a dedicated request object manages authorization
///
Route::group( [ 'prefix' => 'statistics' ], function() {
    Route::get(  'ixp/{category?}',                             'StatisticsController@ixp'                  )->name( 'statistics@ixp'                   );
    Route::get(  'infrastructure/{infra?}/{category?}',         'StatisticsController@infrastructure'       )->name( 'statistics@infrastructure'        );
    Route::get(  'vlan/{vlan?}/{protocol?}/{category?}',        'StatisticsController@vlan'                 )->name( 'statistics@vlan'                  );
    Route::get(  'location/{location?}/{category?}',            'StatisticsController@location'             )->name( 'statistics@location'              );
    Route::get(  'switch/{switch?}/{category?}',                'StatisticsController@switch'               )->name( 'statistics@switch'                );
    Route::get(  'trunk/{trunk?}/{category?}',                  'StatisticsController@trunk'                )->name( 'statistics@trunk'                 );
    Route::get(  'members',                                     'StatisticsController@members'              );
    Route::post( 'members',                                     'StatisticsController@members'              )->name( 'statistics@members'               );
    Route::get(  'p2p/{cust}',                                  'StatisticsController@p2p'                  )->name( 'statistics@p2p-get'               );
    Route::post( 'p2p/{cust}',                                  'StatisticsController@p2p'                  )->name( 'statistics@p2p'                   );
    Route::get(  'member/{cust?}',                              'StatisticsController@member'               )->name( 'statistics@member'                );
    Route::get(  'member-drilldown/{type}/{typeid}',            'StatisticsController@memberDrilldown'      )->name( 'statistics@member-drilldown'      );
    Route::get(  'latency/{vli}/{protocol}',                    'StatisticsController@latency'              )->name( 'statistics@latency'               );
    Route::get(  'core-bundle/{cb}',                            'StatisticsController@coreBundle'           )->name( 'statistics@core-bundle'           );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Authentication routes
///
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

    // PeeringDB OAuth
    Route::group( [ 'prefix' => 'auth/login/peeringdb' ], function() {
        Route::get('',          'LoginController@peeringdbRedirectToProvider'       )->name('auth:login-peeringdb' );
        Route::get('callback',  'LoginController@peeringdbHandleProviderCallback'   );
    });

    // IXP Manager <v4.9 aliases for static links
    Route::redirect( 'auth/logout',        url( '' ) . '/logout',          301 );
    Route::redirect( 'auth/login',         url( '' ) . '/login',           301 );
    Route::redirect( 'auth/lost-password', url( '' ) . '/password/forget', 301 );
    Route::redirect( 'auth/lost-username', url( '' ) . '/username',        301 );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// MEMBER EXPORT
///
Route::get( 'participants.json', function() { return redirect(route('ixf-member-export')); });

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// DOCUMENT STORE
///
if( !config( 'ixp_fe.frontend.disabled.docstore' ) ) {
    Route::group( [ 'namespace' => 'Docstore', 'prefix' => 'docstore' ], function() {
        Route::get( '/{dir?}',          'DirectoryController@list'      )->name( 'docstore-dir@list' );

        Route::get( '/dir/create',          'DirectoryController@create'    )->name( 'docstore-dir@create'  );
        Route::get( '/dir/{dir}/edit',      'DirectoryController@edit'      )->name( 'docstore-dir@edit'    );

        Route::post(    '/dir/store',       'DirectoryController@store'     )->name( 'docstore-dir@store'   );
        Route::put(     '/dir/update/{dir}','DirectoryController@update'    )->name( 'docstore-dir@update'  );
        Route::delete(  '/dir/{dir}',       'DirectoryController@delete'    )->name( 'docstore-dir@delete'  );

        Route::get(    '/file/download/{file}',    'FileController@download'    )->name( 'docstore-file@download'    );
        Route::get(    '/file/view/{file}',        'FileController@view'        )->name( 'docstore-file@view'        );
        Route::get(    '/file/info/{file}',        'FileController@info'        )->name( 'docstore-file@info'        );
        Route::delete( '/file/{file}',             'FileController@delete'      )->name( 'docstore-file@delete'      );

        Route::get(  '/file/upload',       'FileController@upload' )->name( 'docstore-file@upload'  );
        Route::get(  '/file/{file}/edit',  'FileController@edit'   )->name( 'docstore-file@edit'    );
        Route::post( '/file/store',        'FileController@store'  )->name( 'docstore-file@store'   );
        Route::put(  '/file/update/{file}','FileController@update' )->name( 'docstore-file@update'  );

        Route::get(    '/file/{file}/logs',        'LogController@list'           )->name( 'docstore-log@list'         );
        Route::get(    '/file/{file}/unique-logs', 'LogController@uniqueList'     )->name( 'docstore-log@unique-list'  );
    } );
}

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
    }
    return redirect( route( "dashboard@index" ) );
})->name( 'default' );

/////////////////////////////////////////////////////////////////////////////////////////////////