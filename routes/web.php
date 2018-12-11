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

// phpunit trips up here without the cli test:
if( php_sapi_name() !== 'cli' ) {

    // this if equates to: if the user is logged into Zend but not Laravel:
    if( $auth->hasIdentity() && \Auth::guest() ) {
        // log the user is for Laravel5
        // Note that we reload the user from the database as Zend uses a session cache
        // which breaks associations, etc.
        \Auth::login( d2r('User')->find( ($auth->getIdentity()['user'])->getId() ) );
    } else if( !$auth->hasIdentity() ) {
        \Auth::logout();
    }
}
if( ( Auth::check() && Auth::user()->isSuperUser() ) || env( 'IXP_PHPUNIT_RUNNING', false ) ) {
    // get an array of customer id => names
    if( !( $customers = Cache::get( 'admin_home_customers' ) ) ) {
        $customers = d2r( 'Customer' )->getNames( true );
        Cache::put( 'admin_home_customers', $customers, 3600 );
    }

    app()->make('Foil\Engine')->useData(['customers' => $customers]);
}

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Customers
///

Route::group( [ 'prefix' => 'customer', 'namespace' => 'Customer'], function() {
    Route::get( 'details',                  'CustomerController@details'        )->name( "customer@details"    );
    Route::get( 'associates',               'CustomerController@associates'     )->name( "customer@associates" );
    Route::get( 'detail/{id}',              'CustomerController@detail'         )->name( "customer@detail"     );
});

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Peering Matrix
///

Route::get( 'peering-matrix', 'PeeringMatrixController@index' )->name( "peering-matrix@index" );


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Patch Panels
///

Route::group( [ 'namespace' => 'PatchPanel', 'prefix' => 'patch-panel-port' ], function() {
    Route::get( 'view/{id}',                    'PatchPanelPortController@view'         )->name( "patch-panel-port@view"        );
    Route::get( 'loa-pdf/{id}',                 'PatchPanelPortController@loaPDF'       )->name( "patch-panel-port@loa-pdf"     );
    Route::get( 'verify-loa/{id}/{code}',       'PatchPanelPortController@verifyLoa'    )->name( "patch-panel-port@verify-loa"  );
});

Route::get( 'verify-loa/{id}/{code}',       'PatchPanel\PatchPanelPortController@verifyLoa'    )->name( "patch-panel-port@verify-loa"  );


Route::get( 'weather-map/{id}',                    'WeatherMapController@index' )->name( 'weathermap');


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Static content
///
/// See: http://docs.ixpmanager.org/features/static-content/
///
///
Route::get( 'content/{priv}/{page}',     'ContentController@index' )->name( 'content' );
Route::get( 'public-content/{page}',     'ContentController@public' )->name( 'public-content' );

Route::get( 'content/members/{priv}/{page}', 'ContentController@members' )->name( 'content/members' );

/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Statistics -> a dedicated request object manages authorization
///

Route::group( [ 'prefix' => 'statistics' ], function() {
    Route::get(  'ixp/{category?}',                             'StatisticsController@ixp'               )->name( 'statistics/ixp'                );
    Route::get(  'infrastructure/{graphid?}/{category?}',       'StatisticsController@infrastructure'    )->name( 'statistics/infrastructure'     );
    Route::get(  'vlan/{vlanid?}/{protocol?}/{category?}',      'StatisticsController@vlan'              )->name( 'statistics/vlan'               );
    Route::get(  'switch/{switchid?}/{category?}',              'StatisticsController@switch'            )->name( 'statistics/switch'             );
    Route::get(  'trunk/{trunkid?}/{category?}',                'StatisticsController@trunk'             )->name( 'statistics/trunk'              );

    Route::get(  'members', 'StatisticsController@members' );
    Route::post( 'members', 'StatisticsController@members' )->name( 'statistics/members' );

    Route::get(  'p2p/{cid}', 'StatisticsController@p2p' )->name( 'statistics@p2p-get' );
    Route::post( 'p2p/{cid}', 'StatisticsController@p2p' )->name( 'statistics@p2p' );

    Route::get(  'member/{id?}',                                'StatisticsController@member'            )->name( 'statistics@member'             );

    Route::get(  'member-drilldown/{type}/{typeid}',            'StatisticsController@memberDrilldown'   )->name( 'statistics@member-drilldown'   );
    Route::get(  'latency/{vliid}/{protocol}',                  'StatisticsController@latency'           )->name( 'statistics@latency'            );
});


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// MEMBER EXPORT
///


Route::get( 'participants.json', function() { return redirect(route('ixf-member-export')); });



/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
///
/// DEFAULT ROUTE
///

Route::get( '/', function() {

    if( Auth::guest() ) {
        return redirect('auth/login' );
    }

    if( Auth::user()->isSuperUser() ) {
        return redirect( 'admin' );
    } else if( Auth::user()->isCustAdmin() ) {
        return redirect( 'contact/list' );
    } else {
        return redirect( 'dashboard/index' );
    }
})->name( 'default' );

/////////////////////////////////////////////////////////////////////////////////////////////////



