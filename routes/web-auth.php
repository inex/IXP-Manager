<?php

/*
|--------------------------------------------------------------------------
| Web Routes - These Required an Auth'd User
|--------------------------------------------------------------------------
*/

Route::group( [ 'prefix' => 'customer', 'namespace' => 'Customer'], function() {
    Route::get( 'details',                  'CustomerController@details'        )->name( "customer@details" );
    Route::get( 'detail/{id}',              'CustomerController@detail'         )->name( "customer@detail" );
});

Route::group( [ 'prefix' => 'customer-logo', 'namespace' => 'Customer'], function() {
    Route::get( 'manage/{id?}',         'LogoController@manage'     )->name( "logo@manage" );
    Route::post('store',                'LogoController@store'      )->name( "logo@store" );
    Route::post('delete/{id}',          'LogoController@delete'     )->name( 'logo@delete');
});

Route::group( [ 'prefix' => 'customer-note', 'namespace' => 'Customer'], function() {
    Route::get(    'ping/{id?}',            'CustomerNotesController@ping'      )->name( 'customerNotes@ping');
    Route::get(    'get/{id}',              'CustomerNotesController@get'       )->name( 'customerNotes@get');

});



Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port', 'middleware' => 'patch-panel-port'], function() {
    Route::get( 'download-loa/{id}',                'PatchPanelPortController@downloadLoA' );
    Route::get( 'view-loa/{id}',                    'PatchPanelPortController@viewLoA' );

    Route::get( 'download-file/{pppfid}',           'PatchPanelPortController@downloadFile' );

    Route::get( 'view/{id}',                        'PatchPanelPortController@view' )->name('patch-panel-port/view' );;
});

