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


Route::get('dns/arpa/{vlanid}/{protocol}',             'DnsController@arpa');   // ?format=json also works

Route::get('nagios/birdseye_daemons',                           'NagiosController@birdseyeDaemons');
Route::get('nagios/birdseye_daemons/{vlanid}',                  'NagiosController@birdseyeDaemons');
Route::get('nagios/birdseye_bgp_sessions/rs',                   'NagiosController@birdseyeRsBgpSessions');
Route::get('nagios/birdseye_bgp_sessions/rs/{vlanid}',          'NagiosController@birdseyeRsBgpSessions');

Route::get('router/gen_config/{handle}',                        'RouterController@genConfig' );

Route::get('sflow-receivers/pretag.map',                        'SflowReceiverController@pretagMap');
Route::get('sflow-receivers/receivers.lst',                     'SflowReceiverController@receiversLst');

Route::get(  'patch-panel-port/delete-file/{fileid}',           'PatchPanelPortController@deleteFile' );
Route::get(  'patch-panel-port/toggle-file-privacy/{fileid}',   'PatchPanelPortController@toggleFilePrivacy' );
Route::post( 'patch-panel-port/upload-file/{id}',               'PatchPanelPortController@uploadFile' );
Route::post( 'patch-panel-port/notes/{id}',                     'PatchPanelPortController@setNotes' );
Route::get(  'patch-panel-port/{id}',                           'PatchPanelPortController@detail');
Route::get(  'patch-panel-port/deep/{id}',                      'PatchPanelPortController@detailDeep');

// remove the following two after INEX updated to yaml
Route::get('provisioner/salt/switch/{switchid}',        'Provisioner\YamlController@forSwitch');
Route::get('provisioner/salt/switch-name/{switchname}', 'Provisioner\YamlController@forSwitchByName');

Route::get('provisioner/yaml/switch/{switchid}',        'Provisioner\YamlController@forSwitch');
Route::get('provisioner/yaml/switch-name/{switchname}', 'Provisioner\YamlController@forSwitchByName');

Route::get('switch-port/{id}/customer',                         'SwitchPortController@customer' );
Route::get('switch-port/{id}/physical-interface',               'SwitchPortController@physicalInterface' );

Route::post('customer/{id}/switches',                           'CustomerController@switches' );

Route::post('switcher/{id}/switch-port',                        'SwitcherController@switchPort' );
Route::post('switcher/{id}/switch-port-prewired',               'SwitcherController@switchPortPrewired' );


Route::post( 'utils/markdown',                                  'UtilsController@markdown' );
