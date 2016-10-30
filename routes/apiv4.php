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

Route::group( ['middleware' => 'assert.privilege:' . Entities\User::AUTH_SUPERUSER ], function() {

    Route::get('sflow-receivers/pretag.map',    'Api\SflowReceiverController@pretagMap');
    Route::get('sflow-receivers/receivers.lst', 'Api\SflowReceiverController@receiversLst');



});
