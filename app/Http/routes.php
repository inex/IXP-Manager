<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$auth = Zend_Auth::getInstance();

if( $auth->hasIdentity() && \Auth::guest() ) {
    // log the user is for Laravel5
    \Auth::login( $auth->getIdentity()['user'] );
} else if( !$auth->hasIdentity() ) {
    \Auth::logout();
}

Route::group(['middleware' => ['web']], function () {
    Route::get('/test', function() {
        return view( 'test' );
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
