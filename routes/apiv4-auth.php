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

