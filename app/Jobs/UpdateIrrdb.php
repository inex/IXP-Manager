<?php

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace IXP\Jobs;

use Cache;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use IXP\Contracts\IrrQuerier;
use IXP\Exceptions\ConfigurationException;
use Illuminate\Queue\{
    SerializesModels,
    InteractsWithQueue
};

use IXP\Exceptions\GeneralException;

use IXP\Models\{
    Customer as CustomerModel
};

use IXP\Tasks\Irrdb\{
    UpdateAsnDb,
    UpdatePrefixDb
};

/**
 * UpdateIrrdb
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 * @category   Jobs
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdateIrrdb extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param CustomerModel     $customer
     * @param string            $type
     * @param int               $protocol
     *
     * @return void
     */
    public function __construct( public readonly CustomerModel $customer, public readonly string $type, public readonly int $protocol )
    {
        if( $connection = config( 'irrdb-performance.connection' ) ) {
            $this->onConnection( $connection )->onQueue( 'irrdb' );
        }
    }

    public int $timeout = 1500;
    public int $tries = 1;

    public function failed( ?\Throwable $exception ): void
    {
        $key = $this->type . '-' . $this->protocol . '-' . $this->customer->id;
        Cache::forget( 'updating-irrdb-' . $key );
        Cache::put( 'failed-irrdb-' . $key, true, 3600 );
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws GeneralException
     * @throws ConfigurationException
     */
    public function handle(IrrQuerier $irrdb): void
    {
        if( !app_env_is('testing') && !$this->havePersistentCache() ) {
            throw new GeneralException('A persistent cache is required to fetch filtered prefixes' );
        }

        $key = $this->type . '-' . $this->protocol . '-' . $this->customer->id;
        Cache::put( 'updating-irrdb-' . $key, true, 1800 );
        Cache::forget( 'failed-irrdb-' . $key );
        try {
            $updater = $this->type === 'asn'
                ? new UpdateAsnDb( $irrdb, $this->customer, [ $this->protocol ] )
                : new UpdatePrefixDb( $irrdb, $this->customer, [ $this->protocol ] );
            $result = $updater->update();
            $result['found_at'] = now();
            foreach( [4, 6] as $family ) {
                foreach( ['new', 'stale'] as $change ) {
                    $entries = $result['v' . $family][$change];
                    $result['v' . $family][$change . '_count'] = count( $entries );
                    $result['v' . $family][$change] = array_slice( $entries, 0, 50 );
                }
            }
            Cache::put( 'updated-irrdb-' . $key, $result, 900 );
        } catch( \Throwable $e ) {
            $this->failed( $e );
            throw $e;
        } finally {
            Cache::forget( 'updating-irrdb-' . $key );
        }
    }
}
