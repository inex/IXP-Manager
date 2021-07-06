<?php

namespace IXP\Jobs\RipeAtlas;

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

use App;

use Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Queue\{
    InteractsWithQueue,
    SerializesModels
};

use IXP\Jobs\Job;

use IXP\Exceptions\GeneralException;

use IXP\Models\{
    AtlasProbe,
    Customer
};
use IXP\Services\RipeAtlas\ApiCall;

/**
 * UpdateProbes
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Jobs\RipeAtlas
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdateProbes extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * Create a new job instance.
     *
     * @param Customer $customer
     * @return void
     */
    public function __construct( Customer $customer )
    {
        $this->customer = $customer;
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
        foreach( [ 4, 6 ] as $protocol ) {
            $probes = App::make(ApiCall::class )->queryAtlasForProbes( $this->customer, $protocol );

            if( $probes[ 'error' ] === true ) {
                throw new GeneralException( $probes[ 'content' ] );
            }

            $probes = $probes[ 'response' ];

            // fn names for later:
            $attrVxEnabled  = "v{$protocol}_enabled";
            $attrVxIp       = "address_v{$protocol}";

            if( !$probes->results ) {
                // no probes - delete any if they exist
                $this->customer->AtlasProbes->each( function( $ap ) use( $attrVxEnabled ) {
                    if( $ap->$attrVxEnabled ) {
                        $ap->update( [ $attrVxEnabled => false ] );
                        //$this->comment( "Removed 'gone away' probe {$p->atlas_id} for { $this->customer->getName()} - IPv{$protocol}" );
                    }

                    if( !$ap->v4_enabled && !$ap->v6_enabled ) {
                        $ap->delete();
                    }
                });

            } else {
                $isNew = false;
                foreach( $probes->results as $probe ) {
                    if( !$ap = AtlasProbe::forCustomer( $this->customer->id )->forAtlas( $probe->id )->first() ) {
                        // probe not in database
                        $ap = AtlasProbe::create([
                            'cust_id'       => $this->customer->id,
                            'atlas_id'      => $probe->id,
                            'v4_enabled'    => false,
                            'v6_enabled'    => false,
                        ]);

                        $isNew = true;

                        //$this->info("Adding probe {$p->getAtlasId()} for {$network->getName()} - IPv{$protocol}" );
                    }

                    $old = $ap->$attrVxEnabled;
                    $key = 'address_v' . $protocol;

                    $ap->update([
                        $attrVxIp           => $probe->$key,
                        'address_v4'        => $probe->address_v4,
                        'address_v6'        => $probe->address_v6,
                        'is_anchor'         => $probe->is_anchor,
                        'is_public'         => $probe->is_public,
                        'status'            => $probe->status->name,
                        'api_data'          => json_encode($probe, JSON_THROW_ON_ERROR),
                        $attrVxEnabled      => true,
                        'asn'               => $probe->asn_v4,
                        'last_connected'    => new Carbon( $probe->last_connected ),
                    ]);

                    // now make sure it /really/ works
                    foreach( $probe->tags as $tag ) {
                        if( $tag->slug === "system-ipv{$protocol}-doesnt-work" ) {
                            $ap->update( [ $attrVxEnabled => false ] );
                            break;
                        }
                    }

                    if( $old !== $ap->$attrVxEnabled && !$isNew ) {
                        //$this->comment("Updated probe {$ap->atlas_id} for {$this->customer->getName()} - IPv{$protocol}" );
                    }
                }
            }
        }
    }
}