<?php

namespace IXP\Console\Commands;

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
use Illuminate\Support\Facades\DB;

use IXP\Models\{
    Customer,
    Log
};
use IXP\Services\PeeringDb;

/**
  * Artisan command to update the in_peeringdb flag of members
  *
  * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
  * @author     Yann Robin <yann@islandbridgenetworks.ie>
  * @package    IXP\Console\Commands
  * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
  */
class InPeeringDb extends  Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ixp-manager:update-in-peeringdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update customers 'in peeringdb' state";

    /**
     * Execute the console command.
     *
     * @throws
     *
     * @psalm-return 0|2
     */
    public function handle(): int
    {
        $pdb = app()->make( PeeringDb::class );
        
        foreach( $pdb->warnOnBadAuthMethods() as $w ) {
            $this->warn( $w );
        }

        $asns = $pdb->getPeeringAsns();

        if( $asns === false ) {
            $this->error( $pdb->error );
            return 2;
        }

        // only update cust in_peeringdb if it actually changes
        DB::transaction( function () use ( $asns ) {
            $customers  = Customer::trafficking()->current()->get();
            $qualifying = $customers->count();
            $before     = $customers->filter( fn( Customer $c ) => (bool)$c->in_peeringdb )->count();
            $after      = 0;

            // customers outside the qualifying set should not be flagged
            $stale = Customer::where( 'in_peeringdb', true )
                ->whereNotIn( 'id', $customers->pluck( 'id' ) )
                ->get();

            foreach( $stale as $c ) {
                $this->setInPeeringDb( $c, false );
            }

            foreach( $customers as $c ) {
                $inPdb = in_array( $c->autsys, $asns, false );

                if( $inPdb ) {
                    $after++;
                }

                if( (bool)$c->in_peeringdb !== $inPdb ) {
                    $this->setInPeeringDb( $c, $inPdb );
                }
            }

            $this->info( "PeeringDB membership updated - before/after/missing: {$before}/{$after}/" . ( $qualifying - $after ) );

        });
        return 0;
    }

    /**
     * Update cust in_peeringdb and record the change in the log.
     *
     * @param Customer  $c
     * @param bool      $inPdb
     *
     * @return void
     */
    private function setInPeeringDb( Customer $c, bool $inPdb ): void
    {
        $c->in_peeringdb = (int)$inPdb;
        $c->save();

        // config value varies per installation; psalm sees only the local value
        /** @psalm-suppress RedundantCondition */
        if( !config( 'ixp_fe.frontend.disabled.logs' ) ) {
            Customer::logChange( $c, Log::ACTION_UPDATED );
        }

        if( $this->getOutput()->isVerbose() ) {
            $this->line( "{$c->name} (AS{$c->autsys}): in_peeringdb " . ( $inPdb ? 'set' : 'cleared' ) );
        }
    }
}