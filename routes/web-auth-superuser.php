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

    Route::get(  'email/{id}/{type}',               'PatchPanelPortController@email' );
    Route::post( 'send-email/{id}/{type}',          'PatchPanelPortController@sendEmail' );

});


Route::group( [ 'prefix' => 'layer2-address' ], function() {
    Route::get( 'vlan-interface/{vliid}',            'Layer2AddressController@index' );
    Route::get( 'list/{vlid?}',                      'Layer2AddressController@list' );
});

Route::group( [ 'prefix' => 'router' ], function() {
    Route::get( 'list',                             'RouterController@list' );
    Route::get( 'add',                              'RouterController@edit' );
    Route::get( 'edit/{id}',                        'RouterController@edit' );
    Route::get( 'view/{id}',                        'RouterController@view' );
    Route::get( 'delete/{id}',                      'RouterController@delete' );

    Route::post( 'store',                           'RouterController@store'  );
});

Route::group( [  'prefix' => 'virtualInterface' ], function() {
    Route::get( 'list',                             'VirtualInterfaceController@list' );
    Route::get( 'add',                              'VirtualInterfaceController@edit' );
    Route::get( 'edit/{id}',                        'VirtualInterfaceController@edit' );
    Route::get( 'view/{id}',                        'VirtualInterfaceController@view' );
    Route::get( 'add-wizard',                       'VirtualInterfaceController@editWizard' );
    Route::get( 'edit-wizard/{id}',                 'VirtualInterfaceController@editWizard' );

    Route::post( 'store',                           'VirtualInterfaceController@store'  );
    Route::post( 'storeWizard',                     'VirtualInterfaceController@storeInterfaceWizard'  );
});

Route::group( [  'prefix' => 'physicalInterface' ], function() {
    Route::get( 'list',                             'PhysicalInterfaceController@list' );
    Route::get( 'view/{id}',                        'PhysicalInterfaceController@list' );
    Route::get( 'edit/{id}',                        'PhysicalInterfaceController@edit' );
    Route::get( 'add/{id}/vintid/{viid}',           'PhysicalInterfaceController@edit' );
    Route::post( 'store',                           'PhysicalInterfaceController@store'  );
});

Route::group( [  'prefix' => 'vlanInterface' ], function() {
    Route::get( 'list',                             'VlanInterfaceController@list' );
    Route::get( 'view/{id}',                        'VlanInterfaceController@list' );
    Route::get( 'edit/{id}',                        'VlanInterfaceController@edit' );
    Route::get( 'add/{id}/vintid/{viid}',           'VlanInterfaceController@edit' );
    Route::post( 'store',                           'VlanInterfaceController@store'  );
});

Route::group( [  'prefix' => 'sflowReceiver' ], function() {
    Route::get( 'list',                             'SflowReceiverController@list' );
    Route::get( 'view/{id}',                        'SflowReceiverController@view' );
    Route::get( 'edit/{id}/',                       'SflowReceiverController@edit' );
    Route::get( 'add/{id}/vintid/{viid}',           'SflowReceiverController@edit' );
    Route::post( 'store',                           'SflowReceiverController@store'  );
});