<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes - External
|--------------------------------------------------------------------------
|
| ** EXTERNAL ROUTES **
|
| These routes are intended for authenticated superusers (e.g. via API key)
| from an external source. I.e. no cookie, no CSRF, no AJAX.
|
|
*/



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
// Mailing Lists
//
Route::group( [  'prefix' => 'mailing-list' ], function() {
    Route::get( 'subscribers/{listname}',      'MailingListController@subscribers' );
    Route::get( 'subscribers/json/{listname}', 'MailingListController@subscribers' );

    Route::get( 'unsubscribed/{listname}',      'MailingListController@unsubscribed' );
    Route::get( 'unsubscribed/json/{listname}', 'MailingListController@unsubscribed' );

    Route::post( 'init/{listname}',      'MailingListController@init' );
    Route::post( 'init/json/{listname}', 'MailingListController@init' );
});



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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Sflow Receiver
//
Route::get('sflow-receivers/pretag.map',                        'SflowReceiverController@pretagMap');
Route::get('sflow-receivers/receivers.lst',                     'SflowReceiverController@receiversLst');
Route::get('sflow-receivers.{format}',               		'SflowReceiverController@getReceiverList');




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Vlan Interface
//
Route::get( 'vlan-interface/l2-addresses/{id}',                 'VlanInterfaceController@getL2A' );
Route::get( 'sflow-db-mapper/learned-macs',                     'VlanInterfaceController@sflowLearnedMacs' );
Route::get( 'sflow-db-mapper/configured-macs',                  'VlanInterfaceController@sflowConfiguredMacs' );



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




