<?php

namespace IXP\Jobs;

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

use Cache;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
 * @category   Jobs
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdateIrrdb extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var CustomerModel
     */
    protected $customer;

    /**
     * @var String
     */
    protected $type;

    /**
     * @var int
     */
    protected $proto;

    /**
     * Create a new job instance.
     *
     * @param CustomerModel     $customer
     * @param string            $type
     * @param int               $proto
     *
     * @return void
     */
    public function __construct( CustomerModel $customer, string $type, int $proto )
    {
        $this->customer = $customer;
        $this->type     = $type;
        $this->proto    = $proto;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws
     */
    public function handle(): void
    {
        if( !$this->havePersistentCache() ) {
            throw new GeneralException('A persistent cache is required to fetch filtered prefixes' );
        }

        Cache::put( 'updating-irrdb-' . $this->type . '-' . $this->proto . '-' . $this->customer->id, true, 3600 );

        $updater = $this->type === "asn" ? new UpdateAsnDb( $this->customer ) : new UpdatePrefixDb( $this->customer );

        $result = $updater->update();
        $result[ "found_at" ] = now();

        Cache::put( 'updated-irrdb-'  . $this->type . '-' . $this->proto . '-' . $this->customer->id, $result , 900 );
        Cache::put( 'updating-irrdb-' . $this->type . '-' . $this->proto . '-' . $this->customer->id, false, 3600 );
    }
}