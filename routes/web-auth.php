<?php

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
