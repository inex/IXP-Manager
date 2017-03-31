<?php

/*
|--------------------------------------------------------------------------
| Web Routes - These Required an Auth'd User
|--------------------------------------------------------------------------
*/


Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel' ], function() {
    Route::get( 'list',                             'PatchPanelController@index' );
    Route::get( 'list/inactive',                    'PatchPanelController@indexInactive' );

    Route::get( 'add',                              'PatchPanelController@edit'   );
    Route::get( 'edit/{id}',                        'PatchPanelController@edit'   );
    Route::get( 'view/{id}',                        'PatchPanelController@view'   );
    Route::get( 'change-status/{id}/{active}',      'PatchPanelController@changeStatus' );

    Route::post( 'store',                           'PatchPanelController@store'  );
});

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port', 'middleware' => 'patch-panel-port' ], function() {
    Route::get( 'list',                             'PatchPanelPortController@index' )->name('patchPanelPortIndex');
    Route::post('advanced-list',                    'PatchPanelPortController@advancedIndex' );
    Route::get( 'list/patch-panel/{ppid}',          'PatchPanelPortController@index' );

    Route::get( 'edit/{id}',                        'PatchPanelPortController@edit' );
    Route::get( 'edit-to-allocate/{id}',            'PatchPanelPortController@editToAllocate' );
    Route::get( 'edit-to-prewired/{id}',            'PatchPanelPortController@editToPrewired' );
    Route::get( 'change-status/{id}/{status}',      'PatchPanelPortController@changeStatus' );
    Route::get( 'email/{id}/{type}',                'PatchPanelPortController@email' );

    Route::get( 'download-file/{id}',               'PatchPanelPortController@downloadFile' );

    Route::post( 'store',                           'PatchPanelPortController@store' );

    Route::get(  'email/{id}/{type}',                'PatchPanelPortController@email' );
    Route::post( 'send-email/{id}/{type}',           'PatchPanelPortController@sendEmail' );

});


Route::group( [ 'prefix' => 'layer-2-address' ], function() {
    Route::get( 'list/{id}',                        'Layer2AddressController@index' );
});
