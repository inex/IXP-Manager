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


Route::any( 'ping', 'PublicController@ping' );
Route::any( 'test', 'PublicController@test' );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// IX-F Member List Export

Route::get('member-export/ixf',            'MemberExportController@ixf')->name('ixf-member-export');
Route::get('member-export/ixf/{version}',  'MemberExportController@ixf');


Route::get( 'peeringdb/ix', function() {
    return response()->json( Cache::remember('peeringdb/ix', 120, function() {
        $ixps = [];
        if( $ixs = file_get_contents(config( 'ixp_api.peeringDB.ixp_api' )) ) {
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
            if( $ixs = file_get_contents(config('ixp_api.IXPDB.ixp_api')) ) {
                foreach( json_decode($ixs) as $ix ) {
                    $ixps[$ix->id] = [
                        'ixf_id' => $ix->id,
                        'name' => $ix->name,
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
        if( $pdb = file_get_contents(config( 'ixp_api.peeringDB.fac_api' )) ) {
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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Statistics
//

// get overall stats by month as a JSON response
Route::get( 'statistics/overall-by-month', 'StatisticsController@overallByMonth' );


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ASN Number
//
Route::get( 'aut-num/{asn}', function( $asn ) {

    $infos = [];

    if( $values = file_get_contents("https://rest.db.ripe.net/ripe/aut-num/". $asn . ".json" ) ) {
        $i = 0;

        foreach( json_decode( $values)->objects->object[0]->attributes->attribute as $val ) {
            $infos[ $i ][ 'name' ] = $val->name;
            $infos[ $i ][ 'value' ] = $val->value;
            if( isset( $val->link ) ){
                $infos[ $i ][ 'link' ] = $val->link->href;
            }

            if( isset( $val->comment ) ){
                $infos[ $i ][ 'comment' ] = $val->comment;
            }

            $i++;
        }
    }

    return response()->json(  $infos );
})->name('api-v4-aut-num');

