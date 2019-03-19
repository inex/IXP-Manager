<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

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
                    'name' => htmlentities( $ix->name, ENT_QUOTES ),
                    'city' => htmlentities( $ix->city, ENT_QUOTES ),
                    'country' => htmlentities( $ix->country, ENT_QUOTES ),
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
                        'name' => htmlentities( $ix->name, ENT_QUOTES ),
                        'city' => htmlentities( $ix->city, ENT_QUOTES ),
                        'country' => htmlentities( $ix->country, ENT_QUOTES ),
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
                    'name' => htmlentities( $db->name, ENT_QUOTES ),
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




