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
        Route::get( 'manage/{id?}', 'LogoController@manage' )->name( "logo@manage" );
        Route::post( 'store', 'LogoController@store' )->name( "logo@store" );
        Route::post( 'delete/{id}', 'LogoController@delete' )->name( 'logo@delete' );
    } );
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Patch Panels
///

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port', 'middleware' => 'patch-panel-port'], function() {
    Route::get( 'download-loa/{id}',                'PatchPanelPortController@downloadLoA' );
    Route::get( 'view-loa/{id}',                    'PatchPanelPortController@viewLoA' );

    Route::get( 'download-file/{pppfid}',           'PatchPanelPortController@downloadFile' );

    Route::get( 'view/{id}',                        'PatchPanelPortController@view' )->name( 'patch-panel-port@view' );
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



