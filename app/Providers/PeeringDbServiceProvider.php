<?php

namespace IXP\Providers;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Cache, Route;

use Illuminate\Support\ServiceProvider;

use IXP\Services\PeeringDb;

/**
 * PeeringDB Service Provider
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Providers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringDbServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Route::get( 'peeringdb/ix', function() {
            return response()->json( Cache::remember('peeringdb/ix', 3600, function() {
                $ixps = [];
                if( $ixs = file_get_contents('https://www.peeringdb.com/api/ix') ) {
                    foreach( json_decode( $ixs, false )->data as $ix ) {
                        $ixps[ $ix->id ] = [
                            'pdb_id'    => $ix->id,
                            'name'      => htmlentities( $ix->name, ENT_QUOTES ),
                            'city'      => htmlentities( $ix->city, ENT_QUOTES ),
                            'country'   => htmlentities( $ix->country,ENT_QUOTES ),
                        ];
                    }
                }
                return $ixps;
            }) );
        })->name('api-v4-peeringdb-ixs');


        Route::get( 'peering-db/fac', function() {
            return response()->json( Cache::remember('peering-db/fac', 3600, function() {
                $pdbs = [];
                if( $pdb = file_get_contents('https://api.peeringdb.com/api/fac') ) {
                    foreach( json_decode( $pdb, false )->data as $db ) {
                        $pdbs[ $db->id ] = [
                            'id'    => $db->id,
                            'name'  => htmlentities( $db->name, ENT_QUOTES ),
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
    public function register(): void
    {
        $this->app->singleton( PeeringDb::class, function( $app ) {
            return new PeeringDb();
        });
    }
}