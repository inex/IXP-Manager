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


Route::post( 'l2-address/add',                                  'Layer2AddressController@add' );
Route::post( 'l2-address/delete/{id}',                          'Layer2AddressController@delete' );

Route::group( [ 'prefix' => 'customer-note', 'namespace' => 'Customer\Note'], function() {
    Route::get(    'ping/{id?}',            'CustomerNotesController@ping'      )->name( 'customer-notes@ping');
    Route::get(    'get/{id}',              'CustomerNotesController@get'       )->name( 'customer-notes@get');
});

Route::post( 'utils/markdown',                                  'UtilsController@markdown' )->name( "utils@markdown" );