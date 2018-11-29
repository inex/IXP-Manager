<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api/v4" middleware group. Enjoy building your API!
|
*/

// API key can be passed in the header (preferred) or on the URL.
//
//     curl -X GET -H "X-IXP-Manager-API-Key: mySuperSecretApiKey" http://ixpv.dev/api/v4/test
//     wget http://ixpv.dev/api/v4/test?apikey=mySuperSecretApiKey




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Customers
//
Route::group( [  'prefix' => 'customer' ], function() {
    Route::get( 'query-peeringdb/asn/{asn}',   'CustomerController@queryPeeringDbWithAsn' );

    Route::post( '{id}/switches',               'CustomerController@switches' );
});




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Patch Panel Port
//
Route::post(  'patch-panel-port/delete-file/{fileid}',           'PatchPanelPortController@deleteFile' );
Route::post(  'patch-panel-port/delete-history-file/{fileid}',   'PatchPanelPortController@deleteHistoryFile' );
Route::post(  'patch-panel-port/delete/{id}',                    'PatchPanelPortController@delete' );
Route::post(  'patch-panel-port/split/{id}',                     'PatchPanelPortController@split' );
Route::post(  'patch-panel-port/toggle-file-privacy/{fileid}',   'PatchPanelPortController@toggleFilePrivacy' );
Route::post(  'patch-panel-port/upload-file/{id}',               'PatchPanelPortController@uploadFile' );
Route::post(  'patch-panel-port/notes/{id}',                     'PatchPanelPortController@setNotes' );

Route::get(  'patch-panel-port/{id}',                           'PatchPanelPortController@detail');
Route::get(  'patch-panel-port/deep/{id}',                      'PatchPanelPortController@detailDeep');
Route::post( 'patch-panel/{id}/patch-panel-port-free',          'PatchPanelController@getFreePatchPanelPort');

Route::get('provisioner/layer2interfaces/switch/{switchid}.{outformat}',        'Provisioner\YamlController@forSwitch');
Route::get('provisioner/layer2interfaces/switch-name/{switchname}.{outformat}', 'Provisioner\YamlController@forSwitchByName')->where(['switchname' => '[A-Za-z0-9\.\-]+']);

Route::get('provisioner/layer3interfaces/switch-id/{switchid}.{outformat}',     'Provisioner\YamlController@coreLinkForSwitch');
Route::get('provisioner/layer3interfaces/switch-name/{switchname}.{outformat}', 'Provisioner\YamlController@coreLinkForSwitchByName')->where(['switchname' => '[A-Za-z0-9\.\-]+']);

Route::get('provisioner/vlans/switch-id/{switchid}.{outformat}',                'Provisioner\YamlController@vlansForSwitch');
Route::get('provisioner/vlans/switch-name/{switchname}.{outformat}',            'Provisioner\YamlController@vlansForSwitchByName')->where(['switchname' => '[A-Za-z0-9\.\-]+']);

Route::get('provisioner/routing/switch-id/{switchid}.{outformat}',              'Provisioner\YamlController@bgpForSwitch');
Route::get('provisioner/routing/switch-name/{switchname}.{outformat}',          'Provisioner\YamlController@bgpForSwitchByName')->where(['switchname' => '[A-Za-z0-9\.\-]+']);

Route::get( 'provisioner/switch/switch-id/{switchid}.{outformat}',               'Provisioner\YamlController@showSwitch');
Route::get( 'provisioner/switch/switch-name/{switchname}.{outformat}',           'Provisioner\YamlController@showSwitchByName')->where(['switchname' => '[A-Za-z0-9\.\-]+']);

Route::get('switch-port/{id}/customer',                         'SwitchPortController@customer' );
Route::get('switch-port/{id}/physical-interface',               'SwitchPortController@physicalInterface' );

Route::group( [  'prefix' => 'switch' ], function() {
    Route::get( '{id}/ports',                        'SwitchController@ports' );
    Route::post( '{id}/switch-port-for-ppp',          'SwitchController@switchPortForPPP' );
    Route::post( '{id}/switch-port-prewired',         'SwitchController@switchPortPrewired' );
    Route::post( '{id}/switch-port',                  'SwitchController@switchPort' );
});


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Layer 2 Address
//
Route::get(  'l2-address/detail/{id}',                          'Layer2AddressController@detail' );




Route::group( [  'prefix' => 'vlan' ], function() {
    Route::get( '{id}/ip-addresses',                    'VlanController@getIPAddresses' );

    Route::post( 'ip-address/used-across-vlans',        'VlanController@UsedAcrossVlans' );
});




Route::group( [ 'namespace' => 'Customer\Note', 'prefix' => 'customer-note' ], function() {

    Route::get(    'notify-toggle/customer/{id}', 'CustomerNotesController@notifyToggleCustomer' )->name( 'customer-notes@notify-toggle-customer');
    Route::get(    'notify-toggle/note/{id}',     'CustomerNotesController@notifyToggleNote'     )->name( 'customer-notes@notify-toggle-note');

    Route::post(    'add',                             'CustomerNotesController@add'                    )->name( 'customer-notes@add');
    Route::post(    'delete/{id}',                     'CustomerNotesController@delete'                 )->name( 'customer-notes@delete');

});

