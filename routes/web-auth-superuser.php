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

Route::group( [ 'prefix' => 'ip-address' ], function() {
    Route::get(     'list/{protocol}/{vlanid?}',                'IpAddressController@list'              )->name( 'ip-address@list'                 );
    Route::get(     'delete-by-network/vlan/{vlanid}',          'IpAddressController@deleteByNetwork'   )->name( 'ip-address@delete-by-network'    );
    Route::post(    'delete-by-network/vlan/{vlanid}',          'IpAddressController@deleteByNetwork'   );
    Route::get(     'add/{protocol}',                           'IpAddressController@add'               )->name( 'ip-address@add'                  );
    Route::post(    'store',                                    'IpAddressController@store'             )->name( 'ip-address@store'                );
    Route::post(    'delete',                                   'IpAddressController@delete'            )->name( 'ip-address@delete'               );
});

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel' ], function() {
    Route::get(     'list',                             'PatchPanelController@index'            )->name( 'patch-panel/list'         )   ;
    Route::get(     'list/inactive',                    'PatchPanelController@indexInactive'    )->name( 'patch-panel/list/inactive'    );
    Route::get(     'add',                              'PatchPanelController@edit'             )->name( 'patch-panel/add'              );
    Route::get(     'edit/{id}',                        'PatchPanelController@edit'             )->name( 'patch-panel/edit'             );
    Route::get(     'view/{id}',                        'PatchPanelController@view'             )->name( 'patch-panel@view'             );
    Route::get(     'change-status/{id}/{active}',      'PatchPanelController@changeStatus'     )->name( 'patch-panel@change-status'    );
    Route::post(    'store',                            'PatchPanelController@store'            )->name( 'patch-panel@store'            );
});

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port', 'middleware' => 'patch-panel-port' ], function() {
    Route::get(     'list',                             'PatchPanelPortController@index'                )->name('patch-panel-port/list'                 );
    Route::post(    'advanced-list',                    'PatchPanelPortController@advancedIndex'        )->name('patch-panel-port@advanced-list'       );
    Route::get(     'list/patch-panel/{ppid}',          'PatchPanelPortController@index'                )->name('patch-panel-port/list/patch-panel'     );
    Route::get(     'edit/{id}',                        'PatchPanelPortController@edit'                 )->name('patch-panel-port@edit'                 );
    Route::get(     'edit-to-allocate/{id}',            'PatchPanelPortController@editToAllocate'       )->name('patch-panel-port@edit-allocate'        );
    Route::get(     'edit-to-prewired/{id}',            'PatchPanelPortController@editToPrewired'       )->name('patch-panel-port@edit-prewired'        );
    Route::get(     'change-status/{id}/{status}',      'PatchPanelPortController@changeStatus'         )->name('patch-panel-port@change-status'        );
    Route::get(     'email/{id}/{type}',                'PatchPanelPortController@email'                )->name('patch-panel-port@email'                );
    Route::get(     'move-form/{id}',                   'PatchPanelPortController@moveForm'             )->name('patch-panel-port@move-form'            );
    Route::post(    'move',                             'PatchPanelPortController@move'                 )->name('patch-panel-port@move'                 );
    Route::post(    'store',                            'PatchPanelPortController@store'                )->name('patch-panel-port@store'                );
    Route::post(    'send-email/{id}/{type}',           'PatchPanelPortController@sendEmail'            )->name('patch-panel-port@send-email'           );
    Route::post(    'delete-file/{fileid}',             'PatchPanelPortController@deleteFile'           )->name('patch-panel-port@delete-file'          );
    Route::post(    'delete-history-file/{fileid}',     'PatchPanelPortController@deleteHistoryFile'    )->name('patch-panel-port@delete-history-file'  );
    Route::post(    'delete/{id}',                      'PatchPanelPortController@delete'               )->name('patch-panel-port@delete'               );
    Route::post(    'split/{id}',                       'PatchPanelPortController@split'                )->name('patch-panel-port@split'                );
    Route::post(    'toggle-file-privacy/{fileid}',     'PatchPanelPortController@toggleFilePrivacy'    )->name('patch-panel-port@toggle-file-privacy'  );
    Route::post(    'upload-file/{id}',                 'PatchPanelPortController@uploadFile'           )->name('patch-panel-port/upload-file'          );
    Route::post(    'notes/{id}',                       'PatchPanelPortController@setNotes'             )->name('patch-panel-port/set-notes'            );
});


Route::group( [ 'prefix' => 'router' ], function() {
    Route::get(     'list',                             'RouterController@list'     )->name( 'router@list'  );
    Route::get(     'status',                           'RouterController@status'   )->name( 'router@status' );
    Route::get(     'add',                              'RouterController@edit'     )->name( 'router@add'   );
    Route::get(     'edit/{id}',                        'RouterController@edit'     )->name( 'router@edit'  );
    Route::get(     'view/{id}',                        'RouterController@view'     )->name( 'router@view'  );
    Route::get(     'gen-config/{id}',                  'RouterController@genConfig');
    Route::post(     'delete',                          'RouterController@delete'   )->name( 'router@delete'  );
    Route::post(    'store',                            'RouterController@store'    )->name( 'router@store'  );

});


Route::group( [ 'prefix' => 'statistics' ], function() {
    Route::get(  'league-table', 'StatisticsController@leagueTable' );
    Route::post( 'league-table', 'StatisticsController@leagueTable' )->name( 'statistics/league-table' );

    Route::get(  'utilisation', 'StatisticsController@utilisation' )->name( 'statistics/utilisation' );
    Route::post( 'utilisation', 'StatisticsController@utilisation' )->name( 'statistics/utilisation:post' );
});


Route::group( [  'namespace' => 'Interfaces', 'prefix' => 'interfaces' ], function() {
    Route::group( [  'prefix' => 'virtual' ], function() {
        Route::get(     'list',                             'VirtualInterfaceController@list'               )->name(    'interfaces/virtual/list'               );
        Route::get(     'view/{id}',                        'VirtualInterfaceController@add'                )->name(    'virtual-interface@view'         );
        Route::get(     'edit/{id}',                        'VirtualInterfaceController@add'                )->name(    'interfaces/virtual/edit'               );
        Route::get(     'add/',                             'VirtualInterfaceController@add'                )->name(    'interfaces/virtual/add'                );
        Route::get(     'add/custid/{custid}',              'VirtualInterfaceController@addCustId'          )->name(    'interfaces/virtual/add/custid'         );

        Route::get(     'wizard-add',                       'VirtualInterfaceController@wizard'             )->name(    'interfaces/virtual/wizard'             );
        Route::get(     'wizard-add/custid/{custid}',       'VirtualInterfaceController@addWizardCustId'    )->name(   'interfaces/virtual/add-wizard/custid'   );
        Route::post(    'store',                            'VirtualInterfaceController@store'              )->name(   'interfaces/virtual/store'   );
        Route::post(    'wizard-add',                       'VirtualInterfaceController@storeWizard'        )->name(    'interfaces/virtual/wizard-save'        );
        Route::post(    'delete',                      'VirtualInterfaceController@delete'             )->name(    'virtual-interface@delete' );
        
    });

    Route::group( [  'prefix' => 'physical' ], function() {
        Route::get(     'list',                             'PhysicalInterfaceController@list'          )->name( 'interfaces/physical/list'                         );
        Route::get(     'view/{id}',                        'PhysicalInterfaceController@view'          )->name( 'interfaces/physical/view'                         );
        Route::get(     'edit/{id}',                        'PhysicalInterfaceController@edit'          )->name( 'interfaces/physical/edit'                         );
        Route::get(     'edit/{id}/from-cb/{cb}',           'PhysicalInterfaceController@editFromCb'    )->name( 'interfaces/physical/edit/from-core-bundle'        );
        Route::get(     'edit/{id}/vintid/{viid}',          'PhysicalInterfaceController@edit'          )->name( 'interfaces/physical/edit/from-virtual-interface'  );
        Route::get(     'add/{id}/vintid/{viid}',           'PhysicalInterfaceController@edit'          )->name( 'interfaces/physical/add'                          );
        Route::post(    'store',                            'PhysicalInterfaceController@store'         )->name( 'interfaces/physical/store'                        );
        Route::post(    'delete',                           'PhysicalInterfaceController@delete'        )->name( 'interfaces/physical/delete'                       );
    });

    Route::group( [  'prefix' => 'vlan' ], function() {
        Route::get(     'list',                             'VlanInterfaceController@list'      )->name(    'interfaces/vlan/list'                          );
        Route::get(     'view/{id}',                        'VlanInterfaceController@view'      )->name(    'interfaces/vlan/view'                          );
        Route::get(     'edit/{id}',                        'VlanInterfaceController@edit'      )->name(    'interfaces/vlan/edit'                          );
        Route::get(     'duplicate/{fromid}/to/{toid}',     'VlanInterfaceController@duplicate' )->name(    'interfaces/vlan/duplicate'                     );
        Route::get(     'edit/{id}/vintid/{viid}',          'VlanInterfaceController@edit'      )->name(    'interfaces/vlan/edit/from-virtual-interface'   );
        Route::get(     'add/{id}/vintid/{viid}',           'VlanInterfaceController@edit'      )->name(    'interfaces/vlan/add'                           );
        Route::post(    'store',                            'VlanInterfaceController@store'     )->name(    'interfaces/vlan/store'                         );
        Route::post(    'delete',                           'VlanInterfaceController@delete'    )->name(    'vlan-interface@delete'                         );
    });

    Route::group( [  'prefix' => 'sflow-receiver' ], function() {
        Route::get(     'list',                             'SflowReceiverController@list'  )->name( 'interfaces/sflow-receiver/list'                        );
        Route::get(     'edit/{id}/',                       'SflowReceiverController@edit'  )->name( 'interfaces/sflow-receiver/edit'                        );
        Route::get(     'edit/{id}/vintid/{viid}',          'SflowReceiverController@edit'  )->name( 'interfaces/sflow-receiver/edit/from-virtual-interface' );
        Route::get(     'add/{id}/vintid/{viid}',           'SflowReceiverController@edit'  )->name( 'interfaces/sflow-receiver/add'                         );
        Route::post(    'store',                            'SflowReceiverController@store' )->name( 'sflow-receiver@store'                                  );
        Route::post(    'delete',                           'SflowReceiverController@delete')->name( 'sflow-receiver@delete'                                 );
    });

    Route::group( [  'prefix' => 'core-bundle' ], function() {
        Route::get(     'list',                             'CoreBundleController@list'             )->name(    'core-bundle@list'          );
        Route::get(     'add-wizard',                       'CoreBundleController@addWizard'        )->name(    'core-bundle@add-wizard'    );
        Route::get(     'edit/{id}',                        'CoreBundleController@edit'             )->name(    'core-bundle@edit'          );
        Route::post(    'add-store-wizard',                 'CoreBundleController@addStoreWizard'   )->name(    'core-bundle@add-store'     );
        Route::post(    'edit-store-wizard',                'CoreBundleController@editStoreWizard'  )->name(    'core-bundle@edit-store'    );
        Route::post(    'delete',                           'CoreBundleController@delete'           )->name(    'core-bundle@delete'        );
    });

    Route::group( [  'prefix' => 'core-link' ], function() {
        Route::post(    'add',              'CoreLinkController@addStore'           )->name( 'core-link@add-store'      );
        Route::post(    'store',            'CoreLinkController@editStore'          )->name( 'core-link@edit-store'     );
        Route::post(    'delete',           'CoreLinkController@delete'             )->name( 'core-link@delete'         );
    });
});

Route::group( [ 'namespace' => 'Customer' , 'prefix' => 'customer' ], function() {
    Route::get(     'list',                             'CustomerController@list'                       )->name( 'customer@list');
    Route::get(     'add',                              'CustomerController@edit'                       )->name( 'customer@add');
    Route::get(     'edit/{id}',                        'CustomerController@edit'                       )->name( 'customer@edit');
    Route::get(     'billing-registration/{id}',        'CustomerController@editBillingAndRegDetails'   )->name( 'customer@billing-registration');
    Route::get(     'welcome-email/{id}',               'CustomerController@welcomeEmail'               )->name( "customer@welcome-email" );
    Route::get(     'delete-recap/{id}',                'CustomerController@deleteRecap'                )->name( "customer@delete-recap" );
    Route::get(     'overview/{id}/{tab?}',             'CustomerController@overview'                   )->name( "customer@overview" );
    Route::get(     '{id}/tags',                       'CustomerController@tags'                        )->name( "customer@tags" );
    Route::post(    'store',                            'CustomerController@store'                      )->name( 'customer@store');
    Route::post(    'store-billing-and-reg-details',    'CustomerController@storeBillingAndRegDetails'  )->name( 'customer@store-billing-and-reg-details');
    Route::post(    'send-welcome-email',               'CustomerController@sendWelcomeEmail'           )->name( 'customer@send-welcome-email');
    Route::post(    'delete',                           'CustomerController@delete'                     )->name( 'customer@delete');
    Route::post(    'store-tags',                       'CustomerController@storeTags'                  )->name( 'customer@store-tags');
});

if( !config('ixp_fe.frontend.disabled.logo' ) ) {
    Route::group( [ 'namespace' => 'Customer', 'prefix' => 'customer-logo' ], function() {
        Route::get( 'logos', 'LogoController@logos' )->name( "logo@logos" );
    } );
}

Route::group( [ 'namespace' => 'Customer', 'prefix' => 'customer-note' ], function() {
    Route::get(    'read-all',                          'CustomerNotesController@readAll'                )->name( 'customerNotes@readAll');
    Route::get(    'unread-notes',                      'CustomerNotesController@unreadNotes'            )->name( "customerNotes@unreadNotes" );
});


Route::group( [ 'namespace' => 'User', 'prefix' => 'customer-to-user' ], function() {
    Route::post('privs',     'CustomerToUserController@updatePrivs' )->name( "customer-to-user@privs" );
});

Route::get( 'admin', 'AdminController@dashboard' )->name( 'admin@dashboard' );

Route::get( 'search', 'SearchController@do' )->name( 'search' );


if( config( 'google2fa.enabled' ) ) {
    Route::group( [ 'namespace' => 'User', 'prefix' => '2fa' ], function() {
        Route::post('delete',   'User2FAController@delete'   )->name( "2fa@delete"    );
    });
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// CUSTOMER DOCUMENT STORE
///
if( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) ) {
    Route::group( [ 'namespace' => 'DocstoreCustomer', 'prefix' => 'docstorec' ], function() {
        Route::get( '',                           'DirectoryController@listCustomers'   )->name( 'docstore-c-dir@customers'  );

        Route::get( '{cust}/dir/create',          'DirectoryController@create'    )->name( 'docstore-c-dir@create'  );
        Route::get( '{cust}/dir/{dir}/edit',      'DirectoryController@edit'            )->name( 'docstore-c-dir@edit'       );

        Route::post(    '{cust}/dir/store',       'DirectoryController@store'               )->name( 'docstore-c-dir@store'   );
        Route::put(     '{cust}/dir/update/{dir}','DirectoryController@update'              )->name( 'docstore-c-dir@update'  );
        Route::delete(  '/dir/{dir}',             'DirectoryController@delete'              )->name( 'docstore-c-dir@delete'  );
        Route::delete(  '{cust}/dir',             'DirectoryController@deleteForCustomer'   )->name( 'docstore-c-dir@delete-for-customer'  );

        Route::get(  '{cust}/file/upload',       'FileController@upload' )->name( 'docstore-c-file@upload'  );
        Route::get(  '{cust}/file/{file}/edit',  'FileController@edit'   )->name( 'docstore-c-file@edit'    );
        Route::post( '{cust}/file/store',        'FileController@store'  )->name( 'docstore-c-file@store'   );
        Route::put(  '{cust}/file/update/{file}','FileController@update' )->name( 'docstore-c-file@update'  );
        Route::delete( '/file/{file}',             'FileController@delete'      )->name( 'docstore-c-file@delete'      );
        Route::get(    '/file/info/{file}',              'FileController@info'        )->name( 'docstore-c-file@info'        );

    } );
}

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




