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



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// IX-F Member List Export

Route::get('member-export/ixf',            'MemberExportController@ixf');
Route::get('member-export/ixf/{version}',  'MemberExportController@ixf');



Route::get( 'test', function() {
    return response()->make( "API Test Function!\n\nAuthenticated: "
        . ( Auth::check() ? 'Yes, as: ' . Auth::user()->getUsername() : 'No' ) . "\n\n", 200 )
        ->header( 'Content-Type', 'text/plain; charset=utf-8' );
});


Route::get( 'peeringdb/ix', function() {
    return response()->json( Cache::remember('peeringdb/ix', 120, function() {
        $ixps = [];
        if( $ixs = file_get_contents('https://www.peeringdb.com/api/ix') ) {
            foreach( json_decode($ixs)->data as $ix ) {
                $ixps[$ix->id] = [
                    'pdb_id' => $ix->id,
                    'name' => $ix->name,
                    'city' => $ix->city,
                    'country' => $ix->country,
                ];
            }
        }
        return $ixps;
    })
    );
})->name('api-v4-peeringdb-ixs');


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
})->name('api-v4-ixf-ixs');


Route::get( 'peering-db/fac', function() {
    return response()->json( Cache::remember('peering-db/fac', 120, function() {
        $pdbs = [];
        if( $pdb = file_get_contents('https://api.peeringdb.com/api/fac') ) {
            foreach( json_decode( $pdb )->data as $db ) {
                $pdbs[ $db->id ] = [
                    'id' => $db->id,
                    'name' => $db->name,
                ];
            }
        }
        return $pdbs;
    }));
})->name('api-v4-peering-db-fac');

Route::post( 'l2-address/add',                                  'Layer2AddressController@add' );
Route::post( 'l2-address/delete/{id}',                          'Layer2AddressController@delete' );