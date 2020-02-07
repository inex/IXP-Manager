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


Route::get('filtered-prefixes/{customer}', 'FilteredPrefixesController@list' )->name( 'filtered-prefixes@list' );


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///

Route::group( [ 'prefix' => 'profile' ], function() {

    Route::get( '', 'ProfileController@edit' )->name( 'profile@edit' );

    Route::post( 'update-password',                'ProfileController@updatePassword'               )->name( 'profile@update-password'                  );
    Route::post( 'update-profile',                 'ProfileController@updateProfile'                )->name( 'profile@update-profile'                   );
    Route::post( 'update-notification-preference', 'ProfileController@updateNotificationPreference' )->name( 'profile@update-notification-preference'   );
    Route::post( 'update-mailing-lists',           'ProfileController@updateMailingLists'           )->name( 'profile@update-mailing-lists'             );

});


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///

Route::get(  'switch/configuration',       'Switches\SwitchController@configuration'       )->name( "switch@configuration" );

// Authentication routes...
Route::group( [ 'namespace' => 'Auth' ], function() {
    Route::get('switch-user/{id}',         'SwitchUserController@switch'                            )->name( "switch-user@switch"            );
    Route::get('switch-user-back',         'SwitchUserController@switchBack'                        )->name( "switch-user@switchBack"        );
    Route::get('switch-customer/{id}',     'SwitchCustomerController@switch'                        )->name( "switch-customer@switch"        );
});


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///

Route::group( [ 'prefix' => 'dashboard' ], function() {
    Route::get(  '{tab?}',                          'DashboardController@index'                 )->name( "dashboard@index"                  );
    Route::post(  'store-noc-details',              'DashboardController@storeNocDetails'       )->name( "dashboard@store-noc-details"      );
    Route::post(  'store-billing-details',          'DashboardController@storeBillingDetails'   )->name( "dashboard@store-billing-details"  );

});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///

Route::group( [ 'namespace' => 'User', 'prefix' => 'customer-to-user' ], function() {
    Route::get( 'add/{email?}', 'CustomerToUserController@add'                          )->name( "customer-to-user@add"    );
    Route::post('store',        'CustomerToUserController@store'                        )->name( "customer-to-user@store"  );
    Route::post('delete',       'CustomerToUserController@delete'                       )->name( "customer-to-user@delete" );
});

Route::group( [ 'namespace' => 'User', 'prefix' => 'user' ], function() {
    Route::get(     'list',                     'UserController@index'                 )->name("user@list"              );
    Route::get(     'view/{id}',                'UserController@view'                  )->name("user@view"              );
    Route::get(     'add',                      'UserController@add'                   )->name('user@add'               );
    Route::get(     'add-wizard/{custid?}',     'UserController@addForm'               )->name('user@add-wizard'        );
    Route::get(     'edit/{id}',                'UserController@edit'                  )->name('user@edit'              );
    Route::post(    'welcome-email',            'UserController@resendWelcomeEmail'    )->name('user@welcome-email'     );
    Route::post(    'add-store',                'UserController@addStore'              )->name('user@add-store'         );
    Route::post(    'edit-store',               'UserController@editStore'             )->name('user@edit-store'        );
    Route::post(    'delete',                   'UserController@delete'                )->name('user@delete'            );
    Route::post(     'add/check-email',         'UserController@addCheckEmail'         )->name('user@add-check-email'   );
});


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///


Route::get(  'peering-manager',                             'PeeringManagerController@index'            )->name( "peering-manager@index"            );
Route::get(  'peering-manager/{id}/mark-peering/{status}',  'PeeringManagerController@markPeering'      )->name( 'peering-manager@mark-peering'     );
Route::post( 'peering-manager/form',                        'PeeringManagerController@formEmailFrag'    )->name( 'peering-manager@form-email-frag'  );
Route::post( 'peering-manager/send-peering-email',          'PeeringManagerController@sendPeeringEmail' )->name( "peering-manager@send-peering-email" );
Route::post( 'peering-manager/notes',                       'PeeringManagerController@peeringNotes'     )->name( "peering-manager@notes" );


Route::group( [ 'prefix' => 'irrdb' ], function() {
    Route::get(  'customer/{customer}/{type}/{protocol}',   'IrrdbController@list'            )->name( "irrdb@list"            );
    Route::get(  'update/{customer}/{type}/{protocol}',     'IrrdbController@update'          )->name( "irrdb@update"          );
});


if( config( 'google2fa.enabled' ) ) {

    Route::group( [ 'namespace' => 'User', 'prefix' => '2fa' ], function() {

        Route::get('configure','User2FAController@configure')->name('2fa@configure');

        Route::post('enable',   'User2FAController@enable'   )->name( "2fa@enable"    );
        Route::post('disable',  'User2FAController@disable'  )->name( "2fa@disable"   );

        Route::post( '/authenticate', function() {
            if( Session::exists( "url.intended.2fa" ) ) {
                return redirect( Session::pull( "url.intended.2fa" ) );
            }
            return redirect( '' );

        } )->name( '2fa@authenticate' )->middleware( '2fa' );

    } );

}

if( !config( 'ixp_fe.frontend.disabled.docstore' ) ) {
    Route::group( [ 'prefix' => 'docstore' ], function() {
        Route::get( 'dir/list/{dir?}', 'DocstoreDirectoryController@list' )->name( 'docstore-dir@list' );
        Route::get( 'dir/{dir}/files', 'DocstoreDirectoryController@listFiles' )->name( 'docstore-dir@list-files' );

        Route::get( 'file/download/{file}', 'DocstoreFileController@download' )->name( 'docstore-file@download' );
    } );
}
