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

    Route::get('/layout', function() {
        return view( 'layout' );
    });

});
