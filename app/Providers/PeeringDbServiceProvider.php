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
                $faker = $_ENV["APP_ENV"] === 'testing';
                $url = 'https://api.peeringdb.com/api/ix';
                $structure = [
                    ["name" => 'pdb_id', "cell" => 'id'],
                    ["name" => 'name', "cell" => 'name'],
                    ["name" => 'city', "cell" => 'city'],
                    ["name" => 'country', "cell" => 'country'],
                ];
                return generalApiGet($url,null,$structure,$faker);
            }) );
        })->name('api-v4-peeringdb-ixs');


        Route::get( 'peering-db/fac', function() {
            return response()->json( Cache::remember('peering-db/fac', 3600, function() {
                $faker = $_ENV["APP_ENV"] === 'testing';
                $url = 'https://api.peeringdb.com/api/fac';
                $structure = [
                    ["name" => 'id', "cell" => 'id'],
                    ["name" => 'name', "cell" => 'name'],
                ];
                return generalApiGet($url,null,$structure,$faker);
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