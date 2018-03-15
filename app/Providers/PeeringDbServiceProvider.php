<?php

namespace IXP\Providers;

use Cache, Route;
use Illuminate\Support\ServiceProvider;

class PeeringDbServiceProvider extends ServiceProvider{

    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(){

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
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(){
        $this->app->singleton( 'IXP\Services\PeeringDb', function( $app ) {
            return new \IXP\Services\PeeringDb();
        });
    }
}