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
        Route::delete(  'delete/{id}',      'LogoController@delete'     )->name( 'logo@delete'  );
    } );
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Patch Panels
///
Route::group( [ 'namespace' => 'PatchPanel\Port', 'prefix' => 'patch-panel-port' ], function() {
    Route::get( 'view/{ppp}',                     'PortController@view'             )->name( 'patch-panel-port@view'             );

    Route::group( [  'prefix' => 'file' ], function() {
        Route::get( 'download/{file}',           'FileController@download'     )->name( 'patch-panel-port-file@download'    );
    });

    Route::group( [  'prefix' => 'loa' ], function() {
        Route::get( 'download/{ppp}',                'LoaController@download'      )->name( 'patch-panel-port-loa@download'     );
        Route::get( 'view/{ppp}',                    'LoaController@view'          )->name( 'patch-panel-port-loa@view'         );
    });
});



/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Route Server Prefixes
///
Route::group( [ 'prefix' => 'rs-prefixes', 'middleware' => [ 'rs-prefixes' ] ], function() {
    Route::get(     'list',          'RsPrefixesController@list' )->name( 'rs-prefixes@list'  );
    Route::get(     'view/{cust}',   'RsPrefixesController@view' )->name( 'rs-prefixes@view'  );
});

Route::get('filtered-prefixes/{cust}', 'FilteredPrefixesController@list' )->name( 'filtered-prefixes@list' );


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Profile
///
Route::group( [ 'prefix' => 'profile' ], function() {
    Route::get(   '',                              'ProfileController@edit'                         )->name( 'profile@edit'                             );
    Route::post( 'update-password',                'ProfileController@updatePassword'               )->name( 'profile@update-password'                  );
    Route::post( 'update-profile',                 'ProfileController@updateProfile'                )->name( 'profile@update-profile'                   );
    Route::post( 'update-notification-preference', 'ProfileController@updateNotificationPreference' )->name( 'profile@update-notification-preference'   );
    Route::post( 'update-mailing-lists',           'ProfileController@updateMailingLists'           )->name( 'profile@update-mailing-lists'             );
});


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
/// Switch
Route::get(  'switch/configuration',       'Switches\SwitchController@configuration'       )->name( "switch@configuration" );

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Authentication
///
Route::group( [ 'namespace' => 'Auth' ], function() {
    Route::get('switch-user/{c2u}',        'SwitchUserController@switch'       )->name( 'switch-user@switch'            );
    Route::get('switch-user-back',         'SwitchUserController@switchBack'    )->name( 'switch-user@switchBack'        );
    Route::get('switch-customer/{cust}',   'SwitchCustomerController@switch'    )->name( 'switch-customer@switch'        );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Dashboard
///
Route::group( [ 'prefix' => 'dashboard' ], function() {
    Route::get(  '{tab?}',                          'DashboardController@index'                 )->name( "dashboard@index"                  );
    Route::post(  'store-noc-details',              'DashboardController@storeNocDetails'       )->name( "dashboard@store-noc-details"      );
    Route::post(  'store-billing-details',          'DashboardController@storeBillingDetails'   )->name( "dashboard@store-billing-details"  );

});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Cust to User
///
Route::group( [ 'namespace' => 'User', 'prefix' => 'customer-to-user' ], function() {
    Route::get( 'create/{email}',   'CustomerToUserController@create'       )->name( "customer-to-user@create" );
    Route::post('store',            'CustomerToUserController@store'        )->name( "customer-to-user@store"  );
    Route::delete('delete/{c2u}',   'CustomerToUserController@delete'       )->name( "customer-to-user@delete" );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// User
///
Route::group( [ 'namespace' => 'User', 'prefix' => 'user' ], function() {
    Route::get(     'list',                     'UserController@list'                  )->name('user@list'              );
    Route::get(     'view/{u}',                 'UserController@view'                  )->name('user@view'              );
    Route::get(     'create',                   'UserController@create'                )->name('user@create'            );
    Route::get(     'create-wizard/{cust?}',    'UserController@createForm'            )->name('user@create-wizard'     );
    Route::get(     'edit/{u}',                 'UserController@edit'                  )->name('user@edit'              );
    Route::post(    'welcome-email/{u}',        'UserController@resendWelcomeEmail'    )->name('user@welcome-email'     );
    Route::post(    'create/check-email',       'UserController@createCheckEmail'      )->name('user@create-check-email'   );
    Route::post(    'store',                    'UserController@store'                 )->name('user@store'             );
    Route::put(     'update/{u}',               'UserController@update'                )->name('user@update'            );
    Route::delete(  'delete/{u}',               'UserController@delete'                )->name('user@delete'            );

});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Peering manager
///
Route::group( [ 'prefix' => 'peering-manager' ], function() {
    Route::get(  '',                            'PeeringManagerController@index'            )->name( 'peering-manager@index'                );
    Route::get(  '{id}/mark-peering/{status}',  'PeeringManagerController@markPeering'      )->name( 'peering-manager@mark-peering'         );
    Route::post( 'form',                        'PeeringManagerController@formEmailFrag'    )->name( 'peering-manager@form-email-frag'      );
    Route::post( 'send-peering-email',          'PeeringManagerController@sendPeeringEmail' )->name( 'peering-manager@send-peering-email'   );
    Route::post( 'notes',                       'PeeringManagerController@peeringNotes'     )->name( 'peering-manager@notes'                );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// IRRDB
///
Route::group( [ 'prefix' => 'irrdb' ], function() {
    Route::get(  'customer/{cust}/{type}/{protocol}',   'IrrdbController@list'            )->name( 'irrdb@list'            );
    Route::get(  'update/{cust}/{type}/{protocol}',     'IrrdbController@update'          )->name( 'irrdb@update'          );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// 2FA
///
if( config( 'google2fa.enabled' ) ) {
    Route::group( [ 'namespace' => 'User', 'prefix' => '2fa' ], function() {
        Route::get('configure', 'User2FAController@configure'   )->name('2fa@configure'     );
        Route::post('enable',   'User2FAController@enable'      )->name( "2fa@enable"       );
        Route::post('disable',  'User2FAController@disable'     )->name( "2fa@disable"      );
        Route::post( '/authenticate', function() {
            if( Session::exists( "url.intended.2fa" ) ) {
                return redirect( Session::pull( "url.intended.2fa" ) );
            }
            return redirect( '' );

        } )->name( '2fa@authenticate' )->middleware( '2fa' );
    } );
}


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// CUSTOMER DOCUMENT STORE
///
if( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) ) {
    Route::group( [ 'namespace' => 'DocstoreCustomer', 'prefix' => 'docstorec' ], function() {
        Route::get( '{cust}/patch-panel-port-files',        'DirectoryController@listPatchPanelPortFiles'           )->name( 'docstore-c-dir@list-patch-panel-port-file'         );
        Route::get( '{cust}/patch-panel-port-history-files','DirectoryController@listPatchPanelPortHistoryFiles'    )->name('docstore-c-dir@list-patch-panel-port-history-file' );
        Route::get( '{cust}/{dir?}',                        'DirectoryController@list'                              )->name( 'docstore-c-dir@list'                               );

        Route::get(    '{cust}/file/download/{file}',   'FileController@download'    )->name( 'docstore-c-file@download'    );
        Route::get(    '{cust}/file/view/{file}',       'FileController@view'        )->name( 'docstore-c-file@view'        );
    } );
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Rs Filtering
///
//Route::get( 'rs-filtering/{cust}', 'RsFilterController@list' )->name( 'rs-filter@list' );
//
//Route::group( [ 'prefix' => 'rs-filter' ], function() {
//    Route::get('create/{cust}',                  'RsFilterController@create'            )->name("rs-filter@create"             );
//    Route::get('edit/{rsf}',                     'RsFilterController@edit'           )->name("rs-filter@edit"            );
//    Route::get('view/{rsf}',                     'RsFilterController@view'           )->name("rs-filter@view"            );
//    Route::get('toogle-enable/{rsf}/{enable}',   'RsFilterController@toggleEnable'   )->name("rs-filter@toggle-enable"   );
//    Route::get('change-order/{rsf}/{up}',        'RsFilterController@changeOrderBy'  )->name("rs-filter@change-order"    );
//
//    Route::post('store',                        'RsFilterController@store'          )->name("rs-filter@store"           );
//    Route::put('update/{rsf}',                  'RsFilterController@update'         )->name("rs-filter@update"          );
//    Route::delete('delete/{rsf}',               'RsFilterController@delete'       )->name("rs-filter@delete"          );
//
//    Route::view( 'grant-cust-user',              'rs-filter/grant-cust-user'        )->name( 'rs-filter@grant-cust-user' );
//});