<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

if( env('IDENTITY_FORCE_URL') ) { 
    app('url')->forceRootUrl(env('APP_URL'));
    app('url')->forceSchema(substr(env('APP_URL'), 0, strpos(env('APP_URL'),':')));
}            

$auth = Zend_Auth::getInstance();

// phpunit trips up here:
if( php_sapi_name() !== 'cli' ) {
    if( $auth->hasIdentity() && \Auth::guest() ) {
        // log the user is for Laravel5
        \Auth::login( $auth->getIdentity()['user'] );
    } else if( !$auth->hasIdentity() ) {
        \Auth::logout();
    }
}

Route::group(['middleware' => ['web']], function () {
    Route::get('/test', function() {
        dd(url('dd'));
        return view( 'test' );
    });

    Route::get('/layout', function() {
        return view( 'layout' );
    });

});


Route::group(['prefix' => 'apitmp', 'namespace' => 'Api' ], function () {

    Route::get('sflow-receivers/pretag.map',    'SflowReceiverController@pretagMap');
    Route::get('sflow-receivers/receivers.lst', 'SflowReceiverController@receiversLst');

});

Route::group(['prefix' => 'api2', 'namespace' => 'Api2' ], function () {

    Route::get('nagios/birdseye_daemons',             'NagiosController@birdseyeDaemons');
    Route::get('nagios/birdseye_daemons/{vlanid}',    'NagiosController@birdseyeDaemons');

    Route::get('nagios/birdseye_bgp_sessions/rs',          'NagiosController@birdseyeRsBgpSessions');
    Route::get('nagios/birdseye_bgp_sessions/rs/{vlanid}', 'NagiosController@birdseyeRsBgpSessions');

});
