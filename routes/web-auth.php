<?php

/*
|--------------------------------------------------------------------------
| Web Routes - These Required an Auth'd User
|--------------------------------------------------------------------------
*/


Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port', 'middleware' => 'patch-panel-port'], function() {
    Route::get( 'download-loa/{id}',                'PatchPanelPortController@downloadLoA' );
    Route::get( 'view-loa/{id}',                    'PatchPanelPortController@viewLoA' );

    Route::get( 'download-file/{pppfid}',           'PatchPanelPortController@downloadFile' );

    Route::get( 'view/{id}',                        'PatchPanelPortController@view' );
});

Route::get(     'vlan-interface/{vliid}',   'Layer2AddressController@forVlanInterface' )->name( "Layer2AddressController@forVlanInterface" );
Route::post(    'delete/{id}',              'Layer2AddressController@delete' );
Route::post(    'add/{id}',              'Layer2AddressController@add' );