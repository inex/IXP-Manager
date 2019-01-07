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
| Web Routes - These Required an Auth'd User
|--------------------------------------------------------------------------
*/


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Customer
///


if( !config('ixp_fe.frontend.disabled.logo' ) ) {
    Route::group( [ 'prefix' => 'customer-logo', 'namespace' => 'Customer' ], function() {
        Route::get(     'manage/{id?}',     'LogoController@manage'     )->name( "logo@manage"  );
        Route::post(    'store',            'LogoController@store'      )->name( "logo@store"   );
        Route::post(    'delete/{id}',      'LogoController@delete'     )->name( 'logo@delete'  );
    } );
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Patch Panels
///

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port', 'middleware' => 'patch-panel-port'], function() {
    Route::get( 'download-loa/{id}',                'PatchPanelPortController@downloadLoA'      )->name( 'patch-panel-port@download-loa'     );
    Route::get( 'view-loa/{id}',                    'PatchPanelPortController@viewLoA'          )->name( 'patch-panel-port@view-loa'         );
    Route::get( 'download-file/{pppfid}',           'PatchPanelPortController@downloadFile'     )->name( 'patch-panel-port@download-file'    );
    Route::get( 'view/{id}',                        'PatchPanelPortController@view'             )->name( 'patch-panel-port@view'             );
});



/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Route Server Prefixes
///

Route::group( [ 'prefix' => 'rs-prefixes', 'middleware' => [ 'rs-prefixes' ] ], function() {
    Route::get(     'list',         'RsPrefixesController@list' )->name( 'rs-prefixes@list'  );
    Route::get(     'view/{cid}',   'RsPrefixesController@view' )->name( 'rs-prefixes@view'  );
});


Route::group( [ 'prefix' => 'profile' ], function() {

    Route::get( '', 'ProfileController@edit' )->name( 'profile@edit' );

    Route::post( 'update-password',                'ProfileController@updatePassword'               )->name( 'profile@update-password'                  );
    Route::post( 'update-profile',                 'ProfileController@updateProfile'                )->name( 'profile@update-profile'                   );
    Route::post( 'update-notification-preference', 'ProfileController@updateNotificationPreference' )->name( 'profile@update-notification-preference'   );
    Route::post( 'update-mailing-lists',           'ProfileController@updateMailingLists'           )->name( 'profile@update-mailing-lists'             );

});

Route::get(  'switch/configuration',       'Switches\SwitchController@configuration'       )->name( "switch@configuration" );

// Authentication routes...
Route::group( [ 'namespace' => 'Auth' ], function() {
    Route::get('switch-user/{id}',         'SwitchUserController@switch'                            )->name( "switch-user@switch"            );
    Route::get('switch-user-back',         'SwitchUserController@switchBack'                        )->name( "switch-user@switchBack"        );
});

Route::group( [ 'prefix' => 'dashboard' ], function() {
    Route::get(  '{tab?}',                          'DashboardController@index'                 )->name( "dashboard@index"                  );
    Route::post(  'store-noc-details',              'DashboardController@storeNocDetails'       )->name( "dashboard@store-noc-details"      );
    Route::post(  'store-billing-details',          'DashboardController@storeBillingDetails'   )->name( "dashboard@store-billing-details"  );

});

Route::get(  'peering-manager',                            'PeeringManagerController@index'            )->name( "peering-manager@index"            );
Route::get(  'peering-manager/{id}/mark-peering/{status}', 'PeeringManagerController@markPeering'      )->name( 'peering-manager@mark-peering'     );
Route::post( 'peering-manager/form',                       'PeeringManagerController@formEmailFrag'    )->name( 'peering-manager@form-email-frag'  );
Route::post( 'peering-manager/send-peering-email',      'PeeringManagerController@sendPeeringEmail' )->name( "peering-manager@send-peering-email" );
Route::post( 'peering-manager/notes',                   'PeeringManagerController@peeringNotes'     )->name( "peering-manager@notes" );

