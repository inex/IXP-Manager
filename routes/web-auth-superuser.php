<?php

/*
|--------------------------------------------------------------------------
| Web Routes - These Required an Auth'd User
|--------------------------------------------------------------------------
*/


Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel' ], function() {
    Route::get(     'list',                             'PatchPanelController@index'            )->name( 'patch-panel/list'         );
    Route::get(     'list/inactive',                    'PatchPanelController@indexInactive'    )->name( 'patch-panel/list/inactive');
    Route::get(     'add',                              'PatchPanelController@edit'             )->name( 'patch-panel/add'          );
    Route::get(     'edit/{id}',                        'PatchPanelController@edit'             )->name( 'patch-panel/edit'         );
    Route::get(     'view/{id}',                        'PatchPanelController@view'             );
    Route::get(     'change-status/{id}/{active}',      'PatchPanelController@changeStatus'     );
    Route::post(    'store',                            'PatchPanelController@store'            );
});

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port', 'middleware' => 'patch-panel-port' ], function() {
    Route::get(     'list',                             'PatchPanelPortController@index'                )->name('patch-panel-port/list'             );
    Route::post(    'advanced-list',                    'PatchPanelPortController@advancedIndex'        );
    Route::get(     'list/patch-panel/{ppid}',          'PatchPanelPortController@index'                )->name('patch-panel-port/list/patch-panel' );
    Route::get(     'edit/{id}',                        'PatchPanelPortController@edit'                 );
    Route::get(     'edit-to-allocate/{id}',            'PatchPanelPortController@editToAllocate'       );
    Route::get(     'edit-to-prewired/{id}',            'PatchPanelPortController@editToPrewired'       );
    Route::get(     'change-status/{id}/{status}',      'PatchPanelPortController@changeStatus'         );
    Route::get(     'email/{id}/{type}',                'PatchPanelPortController@email'                );
    Route::get(     'download-file/{id}',               'PatchPanelPortController@downloadFile'         );
    Route::get(     'move-form/{id}',                   'PatchPanelPortController@moveForm'             );
    Route::post(    'move',                             'PatchPanelPortController@move'                 );
    Route::post(    'store',                            'PatchPanelPortController@store'                );
    Route::get(     'email/{id}/{type}',                'PatchPanelPortController@email'                );
    Route::post(    'send-email/{id}/{type}',           'PatchPanelPortController@sendEmail'            );
    Route::post(    'delete-file/{fileid}',             'PatchPanelPortController@deleteFile'           );
    Route::post(    'delete-history-file/{fileid}',     'PatchPanelPortController@deleteHistoryFile'    );
    Route::post(    'delete/{id}',                      'PatchPanelPortController@delete'               );
    Route::post(    'split/{id}',                       'PatchPanelPortController@split'                );
    Route::post(    'toggle-file-privacy/{fileid}',     'PatchPanelPortController@toggleFilePrivacy'    );
    Route::post(    'upload-file/{id}',                 'PatchPanelPortController@uploadFile'           );
    Route::post(    'notes/{id}',                       'PatchPanelPortController@setNotes'             );
});

Route::group( [ 'prefix' => 'router' ], function() {
    Route::get(     'list',                             'RouterController@list'     )->name( 'router/list'  );
    Route::get(     'status',                           'RouterController@status'   )->name('router/status' );
    Route::get(     'add',                              'RouterController@edit'     )->name( 'router/add'   );
    Route::get(     'edit/{id}',                        'RouterController@edit'     )->name( 'router/edit'  );
    Route::get(     'view/{id}',                        'RouterController@view'     );
    Route::get(     'delete/{id}',                      'RouterController@delete'   );
    Route::get(     'gen-config/{id}',                  'RouterController@genConfig');
    Route::post(    'store',                            'RouterController@store'    );

});

Route::group( [  'namespace' => 'Interfaces', 'prefix' => 'interfaces' ], function() {
    Route::group( [  'prefix' => 'virtual' ], function() {
        Route::get(     'list',                             'VirtualInterfaceController@list'               )->name(    'interfaces/virtual/list'               );
        Route::get(     'edit/{id}',                        'VirtualInterfaceController@add'                )->name(    'interfaces/virtual/edit'               );
        Route::get(     'add/',                             'VirtualInterfaceController@add'                )->name(    'interfaces/virtual/add'                );
        Route::get(     'add/custid/{custid}',              'VirtualInterfaceController@addCustId'          )->name(    'interfaces/virtual/add/custid'         );
        Route::get(     'view/{id}',                        'VirtualInterfaceController@view'               );
        Route::get(     'wizard-add',                       'VirtualInterfaceController@wizard'             )->name(    'interfaces/virtual/wizard'             );
        Route::get(     'wizard-add/custid/{custid}',       'VirtualInterfaceController@addWizardCustId'    )->name(   'interfaces/virtual/add-wizard/custid'   );
        Route::post(    'store',                            'VirtualInterfaceController@store'              );
        Route::post(    'wizard-add',                       'VirtualInterfaceController@storeWizard'        )->name(    'interfaces/virtual/wizard-save'        );
        Route::post(    'delete/{id}',                      'VirtualInterfaceController@delete'             );
        
    });

    Route::group( [  'prefix' => 'physical' ], function() {
        Route::get(     'list',                             'PhysicalInterfaceController@list'          )->name( 'interfaces/physical/list'                         );
        Route::get(     'view/{id}',                        'PhysicalInterfaceController@view'          )->name( 'interfaces/physical/view'                         );
        Route::get(     'edit/{id}',                        'PhysicalInterfaceController@edit'          )->name( 'interfaces/physical/edit'                         );
        Route::get(     'edit/{id}/from-cb/{cb}',           'PhysicalInterfaceController@editFromCb'    )->name( 'interfaces/physical/edit/from-core-bundle'        );
        Route::get(     'edit/{id}/vintid/{viid}',          'PhysicalInterfaceController@edit'          )->name( 'interfaces/physical/edit/from-virtual-interface'  );
        Route::get(     'add/{id}/vintid/{viid}',           'PhysicalInterfaceController@edit'          )->name( 'interfaces/physical/add'                          );
        Route::post(    'store',                            'PhysicalInterfaceController@store'         );
        Route::post(    'delete/{id}',                      'PhysicalInterfaceController@delete'        );
    });

    Route::group( [  'prefix' => 'vlan' ], function() {
        Route::get(     'list',                             'VlanInterfaceController@list'      )->name(    'interfaces/vlan/list'                          );
        Route::get(     'view/{id}',                        'VlanInterfaceController@view'      )->name(    'interfaces/vlan/view'                          );
        Route::get(     'edit/{id}',                        'VlanInterfaceController@edit'      )->name(    'interfaces/vlan/edit'                          );
        Route::get(     'duplicate/{fromid}/to/{toid}',     'VlanInterfaceController@duplicate' )->name(    'interfaces/vlan/duplicate'                     );
        Route::get(     'edit/{id}/vintid/{viid}',          'VlanInterfaceController@edit'      )->name(    'interfaces/vlan/edit/from-virtual-interface'   );
        Route::get(     'add/{id}/vintid/{viid}',           'VlanInterfaceController@edit'      )->name(    'interfaces/vlan/add'                           );
        Route::post(    'store',                            'VlanInterfaceController@store'     )->name(    'interfaces/vlan/store'                         );
        Route::post(    'delete/{id}',                      'VlanInterfaceController@delete'    );
        
        
    });

    Route::group( [  'prefix' => 'sflow-receiver' ], function() {
        Route::get(     'list',                             'SflowReceiverController@list'  )->name( 'interfaces/sflow-receiver/list'                        );
        Route::get(     'edit/{id}/',                       'SflowReceiverController@edit'  )->name( 'interfaces/sflow-receiver/edit'                        );
        Route::get(     'edit/{id}/vintid/{viid}',          'SflowReceiverController@edit'  )->name( 'interfaces/sflow-receiver/edit/from-virtual-interface' );
        Route::get(     'add/{id}/vintid/{viid}',           'SflowReceiverController@edit'  )->name( 'interfaces/sflow-receiver/add'                         );
        Route::post(    'store',                            'SflowReceiverController@store' );
        Route::post(    'delete/{id}',                      'SflowReceiverController@delete');
    });

    Route::group( [  'prefix' => 'core-bundle' ], function() {
        Route::get(     'list',                             'CoreBundleController@list'             )->name(    'core-bundle/list');
        Route::get(     'add-wizard',                       'CoreBundleController@addWizard'        );
        Route::get(     'edit/{id}',                        'CoreBundleController@edit'             )->name(    'core-bundle/edit');
        Route::post(    'add-core-link-frag',               'CoreBundleController@addCoreLinkFrag'  );
        Route::post(    'store-wizard',                     'CoreBundleController@storeWizard'      );
        Route::post(    'add-core-link',                    'CoreBundleController@addCoreLink'      );
        Route::post(    '{id}/store-core-links',            'CoreBundleController@storeCoreLinks'   );
        Route::post(    'delete/{id}',                      'CoreBundleController@deleteCoreBundle' )->name(    'core-bundle/delete');
        Route::post(    'core-link/delete/{id}',            'CoreBundleController@delete'           );
    });
});

Route::group( [ 'prefix' => 'address' ], function() {
    Route::get(     'list/{protocol}/{vlanid?}',                'IpAddressController@list'              )->name(   'ipAddress@list'                 );
    Route::get(     'delete/vlan/{vlan}/{network?}',            'IpAddressController@preDeleteForVlan'  )->name(   'ipAddress@preDeleteForVlan'     );
    Route::post(    'delete/vlan/{vlan}/{network}',            'IpAddressController@preDeleteForVlan'  )->name(   'ipAddress@preDeleteForVlanPost' );
    Route::get(     'add/{protocol}',                           'IpAddressController@add'               )->name(   'ipAddress@add'                  );
    Route::post(    'store',                                    'IpAddressController@store'             )->name(   'ipAddress@store'                );
    Route::post(    'delete/{protocol}/{id}',                   'IpAddressController@delete'            )->name(   'ipAddress@delete'               );
    Route::post(    'delete/vlan',                              'IpAddressController@deleteForVlan'     )->name(   'ipAddress@deleteForVlan'        );

});



Route::get( 'admin', 'AdminController@dashboard' )->name( 'admin@dashboard' );

Route::get( 'search', 'SearchController@do' )->name( 'search' );


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Utilities
///


Route::get( 'phpinfo', function() { phpinfo(); })->name('phpinfo');

Route::group( [ 'prefix' => 'utils' ], function() {

    Route::get( 'phpinfo', function() {
        return view( 'utils/phpinfo' );
    })->name('utils/phpinfo');

});




