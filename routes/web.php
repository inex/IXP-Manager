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


Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel' ], function() {
    Route::get( 'list',                     'PatchPanelController@index' )->name('patchPanelIndex');
    Route::get( 'list/activeOnly/{active}', 'PatchPanelController@index'  );
    Route::get( 'edit/{id?}',               'PatchPanelController@edit'   );
    Route::get( 'view/{id?}',               'PatchPanelController@view'   );
    Route::get( 'delete/{id}',              'PatchPanelController@delete' );

    Route::post( 'add',                     'PatchPanelController@add'    );
});

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port' ], function() {
    Route::get( 'list',                       'PatchPanelPortController@index' )->name('patchPanelPortIndex');
    Route::get( 'list/patch-panel/{id}',      'PatchPanelPortController@index' )->name('patchPanelPortIndex');
    Route::get( 'view/{id?}',                 'PatchPanelPortController@view' );
    Route::get( 'edit/{id?}',                 'PatchPanelPortController@edit' );
    Route::get( 'getSwitchPort/',             'PatchPanelPortController@getSwitchPort' );
    Route::get( 'getCustomerForASwitchPort/', 'PatchPanelPortController@getCustomerForASwitchPort' );
    Route::get( 'getSwitchForACustomer/',     'PatchPanelPortController@getSwitchForACustomer' );
    Route::get( 'resetCustomer/',             'PatchPanelPortController@resetCustomer' );

    Route::post( 'add/{id}', 'PatchPanelPortController@add' );
    Route::post( 'add/{id}', 'PatchPanelPortController@add' );
});
