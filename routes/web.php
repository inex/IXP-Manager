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

$auth = Zend_Auth::getInstance();

// phpunit trips up here:
if( php_sapi_name() !== 'cli' ) {
    if( $auth->hasIdentity() && \Auth::guest() ) {
        // log the user is for Laravel5
        // Note that we reload the user from the database as Zend uses a session cache
        // which breaks associations, etc.
        \Auth::login( d2r('User')->find( ($auth->getIdentity()['user'])->getId() ) );
    } else if( !$auth->hasIdentity() ) {
        \Auth::logout();
    }
}

if( Auth::check() && Auth::user()->isSuperUser() ) {
    // get an array of customer id => names
    if( !( $customers = Cache::get( 'admin_home_customers' ) ) ) {
        $customers = d2r( 'Customer' )->getNames( true );
        Cache::put( 'admin_home_customers', $customers, 3600 );
    }

    app()->make('Foil\Engine')->useData(['customers' => $customers]);
}


Route::group(['middleware' => ['web']], function () {
    // Route::get('/ltest', function() {
    //     return view('ltest');
    // });

});



Route::get('patch-panel/list', ['as' => 'patchPanelIndex', 'uses' => 'PatchPanel\PatchPanelController@index']);
Route::get('patch-panel/list/activeOnly/{active}', 'PatchPanel\PatchPanelController@index');
Route::get('patch-panel/edit', 'PatchPanel\PatchPanelController@edit');
Route::get('patch-panel/edit/{id}', 'PatchPanel\PatchPanelController@edit');
Route::post('patch-panel/add', 'PatchPanel\PatchPanelController@add');
Route::get('patch-panel/view/', 'PatchPanel\PatchPanelController@view');
Route::get('patch-panel/view/{id}', 'PatchPanel\PatchPanelController@view');
Route::get('patch-panel/delete/{id}', 'PatchPanel\PatchPanelController@delete');

Route::get('patch-panel-port/list', ['as' => 'patchPanelPortIndex', 'uses' => 'PatchPanel\PatchPanelPortController@index']);
Route::get('patch-panel-port/list/patch-panel/{id}', ['as' => 'patchPanelPortIndex', 'uses' => 'PatchPanel\PatchPanelPortController@index']);

Route::get('patch-panel-port/view/', 'PatchPanel\PatchPanelPortController@view');
Route::get('patch-panel-port/view/{id}', 'PatchPanel\PatchPanelPortController@view');
Route::get('patch-panel-port/edit/', 'PatchPanel\PatchPanelPortController@edit');
Route::get('patch-panel-port/edit/{id}', 'PatchPanel\PatchPanelPortController@edit');
Route::post('patch-panel-port/add/{id}', 'PatchPanel\PatchPanelPortController@add');
Route::post('patch-panel-port/add/{id}', 'PatchPanel\PatchPanelPortController@add');
Route::get('patch-panel-port/getSwitchPort/', 'PatchPanel\PatchPanelPortController@getSwitchPort');
Route::get('patch-panel-port/getCustomerForASwitchPort/', 'PatchPanel\PatchPanelPortController@getCustomerForASwitchPort');
Route::get('patch-panel-port/getSwitchForACustomer/', 'PatchPanel\PatchPanelPortController@getSwitchForACustomer');
Route::get('patch-panel-port/resetCustomer/', 'PatchPanel\PatchPanelPortController@resetCustomer');
