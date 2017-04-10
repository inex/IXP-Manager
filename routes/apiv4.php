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


Route::get( 'peeringdb/ix', function() {
    return response()->json( Cache::remember('peeringdb/ix', 120, function() {
        $ixps = [];
        if( $ixs = file_get_contents('https://www.peeringdb.com/api/ix') ) {
            foreach( json_decode($ixs)->data as $ix ) {
                $ixps[$ix->id] = [
                    'ixp_id' => $ix->id,
                    'name' => $ix->name,
                    'city' => $ix->city,
                    'country' => $ix->country,
                ];
            }
        }
        return $ixps;
    })
    );
});


Route::get( 'ix-f/ixp', function() {
    return response()->json( Cache::remember('ix-f/ixp', 120, function() {
            $ixps = [];
            if( $ixs = file_get_contents('https://db.ix-f.net/api/ixp') ) {
                foreach( json_decode($ixs)->data as $ix ) {
                    $ixps[$ix->id] = [
                        'ixf_id' => $ix->id,
                        'name' => $ix->short_name,
                        'city' => $ix->city,
                        'country' => $ix->country,
                    ];
                }
            }
            return $ixps;
        })
    );
});

