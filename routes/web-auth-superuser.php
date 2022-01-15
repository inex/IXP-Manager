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
/// 2FA
///
if( config( 'google2fa.enabled' ) ) {
    Route::group( [ 'namespace' => 'User', 'prefix' => '2fa' ], function() {
        Route::post('delete/{user}',  'User2FAController@delete'     )->name( "2fa@delete" );
    });
}



/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// IP Address
///
Route::group( [ 'prefix' => 'ip-address' ], function() {
    Route::get(     'list/{protocol}/{vlanid?}',                'IpAddressController@list'              )->name( 'ip-address@list'                 );
    Route::get(     'delete-by-network/vlan/{vlan}',            'IpAddressController@deleteByNetwork'   )->name( 'ip-address@delete-by-network'    );
    Route::post(    'delete-by-network/vlan/{vlan}',            'IpAddressController@deleteByNetwork'   );
    Route::get(     'create/{protocol}',                        'IpAddressController@create'            )->name( 'ip-address@create'               );
    Route::post(    'store',                                    'IpAddressController@store'             )->name( 'ip-address@store'                );
    Route::delete(  'delete/{id}',                              'IpAddressController@delete'            )->name( 'ip-address@delete'               );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Patch Panel
///
Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel' ], function() {
    Route::get(     'list',                             'PatchPanelController@index'            )->name( 'patch-panel@list'             );
    Route::get(     'list/inactive',                    'PatchPanelController@indexInactive'    )->name( 'patch-panel@list-inactive'    );
    Route::get(     'create',                           'PatchPanelController@create'           )->name( 'patch-panel@create'           );
    Route::get(     'edit/{pp}',                        'PatchPanelController@edit'             )->name( 'patch-panel@edit'             );
    Route::get(     'view/{pp}',                        'PatchPanelController@view'             )->name( 'patch-panel@view'             );
    Route::get(     'change-status/{pp}/{active}',      'PatchPanelController@changeStatus'     )->name( 'patch-panel@change-status'    );
    Route::post(    'store',                            'PatchPanelController@store'            )->name( 'patch-panel@store'            );
    Route::put(    'update/{pp}',                      'PatchPanelController@update'           )->name( 'patch-panel@update'           );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Patch Panel Port
///
Route::group( [ 'namespace' => 'PatchPanel\Port', 'prefix' => 'patch-panel-port' ], function() {
    Route::get(     'list',                             'PortController@index'                  )->name('patch-panel-port@list'                     );
    Route::post(    'advanced-list',                    'PortController@advancedIndex'          )->name('patch-panel-port@advanced-list'            );
    Route::get(     'list/patch-panel/{pp}',            'PortController@index'                  )->name('patch-panel-port@list-for-patch-panel'     );
    Route::get(     'edit/{ppp}',                       'PortController@edit'                   )->name('patch-panel-port@edit'                     );
    Route::get(     'edit/{ppp}/allocate',              'PortController@editAllocate'           )->name('patch-panel-port@edit-allocate'            );
    Route::get(     'edit/{ppp}/prewired',              'PortController@editPrewired'           )->name('patch-panel-port@edit-prewired'            );
    Route::get(     'change-status/{ppp}/{status}',     'PortController@changeStatus'           )->name('patch-panel-port@change-status'            );
    Route::get(     'move-form/{ppp}',                  'DangerActionsController@moveForm'      )->name('patch-panel-port@move-form'                );
    Route::put(     'move/{ppp}',                       'DangerActionsController@move'          )->name('patch-panel-port@move'                     );
    Route::put(     'split/{ppp}',                      'DangerActionsController@split'         )->name('patch-panel-port@split'                    );
    Route::post(    'notes/{ppp}',                      'PortController@setNotes'               )->name('patch-panel-port/set-notes'                );
    Route::put(    'update/{ppp}',                       'PortController@update'                )->name('patch-panel-port@update'                   );
    Route::delete(    'delete/{ppp}',                   'DangerActionsController@delete'        )->name('patch-panel-port@delete'                   );

    Route::group( [  'prefix' => 'file' ], function() {
        Route::delete(  'delete/{file}',           'FileController@delete'              )->name('patch-panel-port-file@delete'                      );
        Route::post(    'toggle-privacy/{file}',   'FileController@togglePrivacy'       )->name('patch-panel-port-file@toggle-privacy'              );
        Route::post(    'upload/{ppp}',            'FileController@upload'              )->name('patch-panel-port-file@upload'                      );
    });

    Route::group( [  'prefix' => 'history-file' ], function() {
        Route::delete(  'delete/{file}',            'HistoryFileController@delete'          )->name(    'patch-panel-port-history-file@delete'          );
        Route::post(    'toggle-privacy/{file}',    'HistoryFileController@togglePrivacy'   )->name(    'patch-panel-port-history-file@toggle-privacy'  );
        Route::get(     'download/{file}',          'HistoryFileController@download'        )->name(    'patch-panel-port-history-file@download'        );
    });

    Route::group( [  'prefix' => 'email' ], function() {
        Route::post(    'send/{ppp}/{type}',           'EmailController@send'            )->name('patch-panel-port-email@send'                  );
        Route::get(     '{ppp}/{type}',                'EmailController@email'           )->name('patch-panel-port-email@form'                  );
    });
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Router
///
Route::group( [ 'prefix' => 'router' ], function() {
    Route::get(     'list',             'RouterController@list'     )->name( 'router@list'   );
    Route::get(     'status',           'RouterController@status'   )->name( 'router@status' );
    Route::get(     'create',           'RouterController@create'   )->name( 'router@create' );
    Route::get(     'edit/{router}',    'RouterController@edit'     )->name( 'router@edit'   );
    Route::get(     'view/{router}',    'RouterController@view'     )->name( 'router@view'   );
    Route::get(     'gen-config/{id}',  'RouterController@genConfig');
    Route::delete(  'delete/{router}',  'RouterController@delete'   )->name( 'router@delete'  );
    Route::post(   'store',             'RouterController@store'    )->name( 'router@store'   );
    Route::put(    'update/{router}',   'RouterController@update'   )->name( 'router@update'  );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Statistics
///
Route::group( [ 'prefix' => 'statistics' ], function() {
    Route::get(  'league-table', 'StatisticsController@leagueTable' );
    Route::post( 'league-table', 'StatisticsController@leagueTable' )->name( 'statistics@league-table'      );
    Route::get(  'utilisation',  'StatisticsController@utilisation' )->name( 'statistics@utilisation'       );
    Route::post( 'utilisation',  'StatisticsController@utilisation' )->name( 'statistics@utilisation:post'  );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Interfaces
///
Route::group( [  'namespace' => 'Interfaces', 'prefix' => 'interfaces' ], function() {
    Route::group( [  'prefix' => 'virtual' ], function() {
        Route::get(     'list',                     'VirtualInterfaceController@list'                   )->name(    'virtual-interface@list'                    );
        Route::get(     'edit/{vi}',                'VirtualInterfaceController@edit'                   )->name(    'virtual-interface@edit'                    );
        Route::get(     'create',                   'VirtualInterfaceController@create'                 )->name(    'virtual-interface@create'                  );
        Route::get(     'create/cust/{cust}',       'VirtualInterfaceController@createForCust'          )->name(    'virtual-interface@create-for-cust'         );
        Route::get(     'wizard-create',            'VirtualInterfaceController@wizard'                 )->name(   'virtual-interface@wizard'                  );
        Route::get(     'wizard-create/cust/{cust}','VirtualInterfaceController@createWizardForCust'    )->name(   'virtual-interface@create-wizard-for-cust'  );
        Route::post(    'store',                    'VirtualInterfaceController@store'                  )->name(   'virtual-interface@store'                   );
        Route::put(     'update/{vi}',              'VirtualInterfaceController@update'                 )->name(   'virtual-interface@update'                  );
        Route::post(    'wizard-store',             'VirtualInterfaceController@storeWizard'            )->name(    'virtual-interface@wizard-store'           );
        Route::delete(  'delete/{vi}',              'VirtualInterfaceController@delete'                 )->name(    'virtual-interface@delete'                 );
    });

    Route::group( [  'prefix' => 'physical' ], function() {
        Route::get('list',                  'PhysicalInterfaceController@list'          )->name( 'physical-interface@list'                          );
        Route::get('view/{pi}',             'PhysicalInterfaceController@view'          )->name('physical-interface@view'                           );
        Route::get('create/vintid/{vi}',    'PhysicalInterfaceController@create'        )->name( 'physical-interface@create'                        );
        Route::get('edit/{pi}',             'PhysicalInterfaceController@edit'          )->name( 'physical-interface@edit'                          );
        Route::get('edit/{pi}/cb/{cb}',     'PhysicalInterfaceController@editFromCb'    )->name( 'physical-interface@edit-from-core-bundle'         );
        Route::get('edit/{pi}/vintid/{vi}', 'PhysicalInterfaceController@edit'          )->name( 'physical-interface@edit-from-virtual-interface'   );
        Route::post(    'store',            'PhysicalInterfaceController@store'         )->name( 'physical-interface@store'                         );
        Route::put(     'update/{pi}',      'PhysicalInterfaceController@update'        )->name( 'physical-interface@update'                        );
        Route::delete(  'delete/{pi}',      'PhysicalInterfaceController@delete'        )->name( 'physical-interface@delete'                        );
    });

    Route::group( [  'prefix' => 'vlan' ], function() {
        Route::get(     'list',                             'VlanInterfaceController@list'          )->name(    'vlan-interface@list'                           );
        Route::get(     'view/{vli}',                       'VlanInterfaceController@view'          )->name(    'vlan-interface@view'                           );
        Route::get(     'edit/{vli}',                       'VlanInterfaceController@edit'          )->name(    'vlan-interface@edit'                           );
        Route::get(     'duplicate/{vli}/to/{v}',           'VlanInterfaceController@duplicateForm' )->name(    'vlan-interface@duplicate-form'                 );
        Route::get(     'edit/{vli}/vintid/{vi}',           'VlanInterfaceController@edit'          )->name(     'vlan-interface@edit-from-virtual-interface'   );
        Route::get(     'create/vintid/{vi}',               'VlanInterfaceController@create'        )->name(    'vlan-interface@create'                         );
        Route::post(    'store',                            'VlanInterfaceController@store'         )->name(    'vlan-interface@store'                          );
        Route::put(     'duplicate/{vli}',                  'VlanInterfaceController@duplicate'     )->name(     'vlan-interface@duplicate'                     );
        Route::put(     'update/{vli}',                     'VlanInterfaceController@update'        )->name(     'vlan-interface@update'                       );
        Route::delete(  'delete/{vli}',                     'VlanInterfaceController@delete'        )->name(    'vlan-interface@delete'                         );
    });

    Route::group( [  'prefix' => 'sflow-receiver' ], function() {
        Route::get(     'list',                     'SflowReceiverController@list'      )->name( 'sflow-receiver@list'                          );
        Route::get(     'edit/{sflr}/',             'SflowReceiverController@edit'      )->name( 'sflow-receiver@edit'                          );
        Route::get(     'edit/{sflr}/vintid/{vi}',  'SflowReceiverController@edit'      )->name( 'sflow-receiver@edit-from-virtual-interface'   );
        Route::get(     'create/vintid/{vi}',       'SflowReceiverController@create'    )->name( 'sflow-receiver@create'                        );
        Route::post(    'store',                    'SflowReceiverController@store'     )->name( 'sflow-receiver@store'                         );
        Route::put(    'update/{sflr}',             'SflowReceiverController@update'    )->name( 'sflow-receiver@update'                        );
        Route::delete(  'delete/{sflr}',            'SflowReceiverController@delete'    )->name( 'sflow-receiver@delete'                        );
    });

    Route::group( [  'prefix' => 'core-bundle' ], function() {
        Route::get(     'list',              'CoreBundleController@list'             )->name(    'core-bundle@list'          );
        Route::get(     'create-wizard',     'CoreBundleController@createWizard'     )->name(    'core-bundle@create-wizard' );
        Route::get(     'edit/{cb}',         'CoreBundleController@edit'             )->name(    'core-bundle@edit'          );
        Route::post(    'store-wizard',      'CoreBundleController@storeWizard'      )->name(    'core-bundle@store'         );
        Route::put(     'update-wizard/{cb}','CoreBundleController@updateWizard'     )->name(    'core-bundle@update'        );
        Route::delete(  'delete/{cb}',       'CoreBundleController@delete'           )->name(    'core-bundle@delete'        );

        Route::group( [  'prefix' => '{cb}/core-link' ], function() {
            Route::post(   'add',           'CoreLinkController@store'   )->name( 'core-link@store'      );
            Route::put(    'update',        'CoreLinkController@update'  )->name( 'core-link@update'     );
            Route::delete( 'delete/{cl}',   'CoreLinkController@delete'  )->name( 'core-link@delete'     );
        });
    });
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Customer
///
Route::group( [ 'namespace' => 'Customer'  ], function() {
    Route::group( [ 'prefix' => 'customer' ], function() {
        Route::get(     'list',                                 'CustomerController@list'                       )->name( 'customer@list'                            );
        Route::get(     'create',                               'CustomerController@create'                     )->name( 'customer@create'                          );
        Route::get(     'edit/{cust}',                          'CustomerController@edit'                       )->name( 'customer@edit'                            );
        Route::get(     'billing-registration/{cust}',          'CustomerController@billingAndRegDetails'       )->name( 'customer@billing-registration'            );
        Route::get(     'welcome-email/{cust}',                 'CustomerController@welcomeEmail'               )->name( 'customer@welcome-email'                   );
        Route::get(     'overview/{cust}/{tab?}',               'CustomerController@overview'                   )->name( 'customer@overview'                        );
        Route::get(     'peers/{cust}',                         'CustomerController@loadPeersFrag'              )->name( 'customer@load-peers'                      );
        Route::get(     'delete-recap/{cust}',                  'CustomerController@deleteRecap'                )->name( 'customer@delete-recap'                    );
        Route::post(    'store',                                'CustomerController@store'                      )->name( 'customer@store'                           );
        Route::put(     'update/{cust}',                        'CustomerController@update'                     )->name( 'customer@update'                         );
        Route::post(    '{cust}/store-billing-and-reg-details', 'CustomerController@storeBillingAndRegDetails'  )->name( 'customer@store-billing-and-reg-details'   );
        Route::post(    '{cust}/send-welcome-email',            'CustomerController@sendWelcomeEmail'           )->name( 'customer@send-welcome-email'              );
        Route::delete(    'delete/{cust}',                      'CustomerController@delete'                     )->name( 'customer@delete'                          );
    });

    if( !config('ixp_fe.frontend.disabled.logo' ) ) {
        Route::group( [ 'prefix' => 'customer-logo' ], function() {
            Route::get( 'logos', 'LogoController@logos' )->name( "logo@logos" );
        } );
    }

    Route::group( [ 'prefix' => 'customer-note' ], function() {
        Route::get(    'read-all',                          'CustomerNotesController@readAll'                )->name( 'customerNotes@readAll');
        Route::get(    'unread-notes',                      'CustomerNotesController@unreadNotes'            )->name( "customerNotes@unreadNotes" );
    });
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// User
///
Route::group( [ 'namespace' => 'User' ], function() {
    Route::group( [ 'prefix' => 'customer-to-user' ], function() {
        Route::post('privs',     'CustomerToUserController@updatePrivs' )->name( "customer-to-user@privs" );
    });
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Admin
///
Route::get( 'admin', 'AdminController@dashboard' )->name( 'admin@dashboard' );

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Search
///
Route::get( 'search', 'SearchController@do' )->name( 'search' );

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// CUSTOMER DOCUMENT STORE
///
if( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) ) {
    Route::group( [ 'namespace' => 'DocstoreCustomer', 'prefix' => 'docstorec' ], function() {
        Route::get( '',                           'DirectoryController@listCustomers'   )->name( 'docstore-c-dir@customers'                 );

        Route::get( '{cust}/dir/create',          'DirectoryController@create'              )->name( 'docstore-c-dir@create'                );
        Route::get( '{cust}/dir/{dir}/edit',      'DirectoryController@edit'                )->name( 'docstore-c-dir@edit'                  );

        Route::post(    '{cust}/dir/store',       'DirectoryController@store'               )->name( 'docstore-c-dir@store'                 );
        Route::put(     '{cust}/dir/update/{dir}','DirectoryController@update'              )->name( 'docstore-c-dir@update'                );
        Route::delete(  '/dir/{dir}',             'DirectoryController@delete'              )->name( 'docstore-c-dir@delete'                );
        Route::delete(  '{cust}/dir',             'DirectoryController@deleteForCustomer'   )->name( 'docstore-c-dir@delete-for-customer'   );

        Route::get(  '{cust}/file/upload',       'FileController@upload'                    )->name( 'docstore-c-file@upload'               );
        Route::get(  '{cust}/file/{file}/edit',  'FileController@edit'                      )->name( 'docstore-c-file@edit'                 );
        Route::post( '{cust}/file/store',        'FileController@store'                     )->name( 'docstore-c-file@store'                );
        Route::put(  '{cust}/file/update/{file}','FileController@update'                    )->name( 'docstore-c-file@update'               );
        Route::delete( '/file/{file}',           'FileController@delete'                    )->name( 'docstore-c-file@delete'               );
        Route::get(    '/file/info/{file}',      'FileController@info'                      )->name( 'docstore-c-file@info'                 );

    } );
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// LOGS
///
if( !config( 'ixp_fe.frontend.disabled.logs' ) ){
    Route::group( [ 'prefix' => 'log' ], function() {
        Route::get(     'list',         'LogController@list'    )->name( 'log@list'     );
        Route::get(     'view/{log}',   'LogController@view'    )->name( 'log@view'     );
    } );
}
/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Utilities
///
Route::get( 'phpinfo', function() { phpinfo(); } )->name('phpinfo' );

Route::group( [ 'prefix' => 'utils', 'namespace' => 'Utils' ], function() {
    Route::get( 'phpinfo', function() {
        return view( 'utils/phpinfo' );
    })->name('utils/phpinfo');

    Route::get( 'ixf-compare', 'IxfCompareController@index' )->name('utils/ixf-compare');
    Route::post( 'do-ixf-compare', 'IxfCompareController@compare' )->name('utils/do-ixf-compare');

});