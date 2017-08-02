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
// DNS ARPA Entries
//
// Returns plain text from the given template (api/v4/dns/[template]):
Route::get('dns/arpa/{vlanid}/{protocol}/{template}',  'DnsController@arpaTemplated');
// Returns JSON object:
Route::get('dns/arpa/{vlanid}/{protocol}',             'DnsController@arpa');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Users
//
// Returns all users (or users with given integer privilege) as JSON
Route::get('user/json',         'UserController@json');
Route::get('user/json/{priv}',  'UserController@json');

// Returns all users (or users with given integer privilege) as a formatted template (e.g. for TACACS)
// see: http://docs.ixpmanager.org/features/tacacs/
Route::get( 'user/formatted',                   'UserController@formatted');
Route::get( 'user/formatted/{priv}',            'UserController@formatted');
Route::get( 'user/formatted/{priv}/{template}', 'UserController@formatted');
Route::post('user/formatted',                   'UserController@formatted');
Route::post('user/formatted/{priv}',            'UserController@formatted');
Route::post('user/formatted/{priv}/{template}', 'UserController@formatted');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// VLANs
//
// Returns a smokeping configuration for a given VLAN and protocol
Route::get('vlan/smokeping/{vlanid}/{protocol}',             'VlanController@smokepingTargets');
Route::get('vlan/smokeping/{vlanid}/{protocol}/{template}',  'VlanController@smokepingTargets');
Route::post('vlan/smokeping/{vlanid}/{protocol}',             'VlanController@smokepingTargets');
Route::post('vlan/smokeping/{vlanid}/{protocol}/{template}',  'VlanController@smokepingTargets');


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Routers
//
// Generate router configuration:
Route::get('router/gen_config/{handle}',                        'RouterController@genConfig' );
Route::get('router/gen-config/{handle}',                        'RouterController@genConfig' )
     ->name( 'apiv4-router-gen-config' );

// Get / set a routers last updated time:
Route::post('router/updated/{handle}',                          'RouterController@setLastUpdated' );
Route::get('router/updated/{handle}',                           'RouterController@getLastUpdated' );
Route::get('router/updated',                                    'RouterController@getAllLastUpdated' );
Route::get('router/updated-before/{threshold}',                 'RouterController@getAllLastUpdatedBefore' );







Route::get('sflow-receivers/pretag.map',                        'SflowReceiverController@pretagMap');
Route::get('sflow-receivers/receivers.lst',                     'SflowReceiverController@receiversLst');
Route::get('sflow-receiver/delete/{id}',                        'SflowReceiverController@delete' );

Route::get(  'patch-panel-port/delete-file/{fileid}',           'PatchPanelPortController@deleteFile' );
Route::get(  'patch-panel-port/delete-history-file/{fileid}',   'PatchPanelPortController@deleteHistoryFile' );
Route::get(  'patch-panel-port/delete/{id}',                    'PatchPanelPortController@delete' );
Route::get(  'patch-panel-port/split/{id}',                     'PatchPanelPortController@split' );
Route::get(  'patch-panel-port/toggle-file-privacy/{fileid}',   'PatchPanelPortController@toggleFilePrivacy' );
Route::post( 'patch-panel-port/upload-file/{id}',               'PatchPanelPortController@uploadFile' );
Route::post( 'patch-panel-port/notes/{id}',                     'PatchPanelPortController@setNotes' );
Route::get(  'patch-panel-port/{id}',                           'PatchPanelPortController@detail');
Route::get(  'patch-panel-port/deep/{id}',                      'PatchPanelPortController@detailDeep');

Route::post(  'patch-panel/{id}/patch-panel-port-free',         'PatchPanelController@getFreePatchPanelPort');

Route::get('provisioner/yaml/switch/{switchid}',        'Provisioner\YamlController@forSwitch');
Route::get('provisioner/yaml/switch-name/{switchname}', 'Provisioner\YamlController@forSwitchByName');

Route::get('switch-port/{id}/customer',                         'SwitchPortController@customer' );
Route::get('switch-port/{id}/physical-interface',               'SwitchPortController@physicalInterface' );

Route::post('customer/{id}/switches',                           'CustomerController@switches' );

Route::group( [  'prefix' => 'switch' ], function() {
    Route::get( '{id}/ports',                        'SwitchController@ports' );

    Route::post( '{id}/switch-port-for-ppp',          'SwitchController@switchPortForPPP' );
    Route::post( '{id}/switch-port-prewired',         'SwitchController@switchPortPrewired' );
    Route::post( '{id}/switch-port',                  'SwitchController@switchPort' );
});

Route::post( 'utils/markdown',                                  'UtilsController@markdown' );

Route::post( 'l2-address/add',                                  'Layer2AddressController@add' );
Route::get( 'l2-address/delete/{id}',                           'Layer2AddressController@delete' );
Route::get( 'l2-address/detail/{id}',                           'Layer2AddressController@detail' );

Route::get( 'vlan-interface/l2-addresses/{id}',                 'VlanInterfaceController@getL2A' );
Route::get( 'vlan-interface/delete/{id}',                       'VlanInterfaceController@delete' );
Route::get( 'vlan-interface/sflow-matrix',                      'VlanInterfaceController@sflowMatrix' );
Route::get( 'vlan-interface/sflow-mac-table',                   'VlanInterfaceController@sflowMacTable' );

Route::group( [  'prefix' => 'vlan' ], function() {
    Route::get( 'for-switch/{switchid}',                'Provisioner\YamlController@vlanForSwitch' );
    Route::get( '{id}/ip-addresses',                    'VlanController@getIPAddresses' );
});

Route::get('physical-interface/delete/{id}',                    'PhysicalInterfaceController@delete' );
Route::get('virtual-interface/delete/{id}',                    'VirtualInterfaceController@delete' );
Route::get('core-link/delete/{id}',                             'CoreLinkController@delete' );


Route::group( [ 'prefix' => 'nagios' ], function() {
    Route::get(  'customers/{vlanid}/{protocol}',            'NagiosController@customers' );
    Route::post( 'customers/{vlanid}/{protocol}',            'NagiosController@customers' );
    Route::get(  'customers/{vlanid}/{protocol}/{template}', 'NagiosController@customers' );
    Route::post( 'customers/{vlanid}/{protocol}/{template}', 'NagiosController@customers' );

    Route::get(  'switches/{infraid}',                      'NagiosController@switches' );
    Route::post( 'switches/{infraid}',                      'NagiosController@switches' );
    Route::get(  'switches/{infraid}/{template}',           'NagiosController@switches' );
    Route::post( 'switches/{infraid}/{template}',           'NagiosController@switches' );

    Route::get(  'birdseye-daemons',                        'NagiosController@birdseyeDaemons');
    Route::post( 'birdseye-daemons',                        'NagiosController@birdseyeDaemons');
    Route::get(  'birdseye-daemons/{template}',             'NagiosController@birdseyeDaemons');
    Route::post( 'birdseye-daemons/{template}',             'NagiosController@birdseyeDaemons');
    Route::get(  'birdseye-daemons/{template}/{vlanid}',    'NagiosController@birdseyeDaemons');
    Route::post( 'birdseye-daemons/{template}/{vlanid}',    'NagiosController@birdseyeDaemons');

    Route::get(  'birdseye-bgp-sessions/{vlanid}/{protocol}/{type}',            'NagiosController@birdseyeBgpSessions');
    Route::post( 'birdseye-bgp-sessions/{vlanid}/{protocol}/{type}',            'NagiosController@birdseyeBgpSessions');
    Route::get(  'birdseye-bgp-sessions/{vlanid}/{protocol}/{type}/{template}', 'NagiosController@birdseyeBgpSessions');
    Route::post( 'birdseye-bgp-sessions/{vlanid}/{protocol}/{type}/{template}', 'NagiosController@birdseyeBgpSessions');
});

