<?php

declare(strict_types=1);
namespace IXP\Tasks\Irrdb;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use D2EM;
use Illuminate\Support\Facades\Cache;
use IXP\Models\IrrdbAsn;
use Log;

/**
 * UpdateAsnDb
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Irrdb
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdateAsnDb extends UpdateDb
{
    /**
     * Update the prefix database
     *
     * @return array
     *
      * @throws
     */
    public function update(): array
    {
        foreach( $this->protocols() as $protocol ) {
            if( $this->customer()->irrdbConfig && $this->customer()->routeServerClient( $protocol ) && $this->customer()->irrdbFiltered() ) {
                $this->bgpq3()->setWhois( $this->customer()->irrdbConfig->host );
                $this->bgpq3()->setSources( $this->customer()->irrdbConfig->source );

                $this->startTimer();
                $asns = $this->bgpq3()->getAsnList( $this->customer()->asMacro( $protocol, 'as' ), $protocol );
                $this->result[ 'netTime' ] += $this->timeElapsed();

                $this->result[ 'v' . $protocol ][ 'count' ] = count( $asns );

                if( $this->updateDb( $asns, $protocol, 'asn' ) ) {
                    $this->result[ 'v' . $protocol ][ 'dbUpdated' ] = true;
                }
            } else {
                // This customer is not appropriate for IRRDB filtering.
                // Delete any pre-existing entries just in case this has changed recently:
                $this->startTimer();

                Cache::store('file')->forget( 'irrdb:asns:ipv' . $protocol . ':' . $this->customer()->asMacro( $protocol ) );

                IrrdbAsn::whereCustomerId( $this->customer()->id )
                    ->whereProtocol( $protocol )->delete();

                $this->result[ 'dbTime' ] += $this->timeElapsed();
                $this->result[ 'v' . $protocol ][ 'dbUpdated' ] = true;
                $this->result[ 'msg' ] = "Customer not a RS client or IRRDB filtered for IPv{$protocol}. IPv{$protocol} ASNs, if any, wiped from database.";
            }
        }

        return $this->result;
    }


    /**
     * Validate a given array of CIDR formatted prefixes for the given protocol and
     * remove (and alert on) any elements failing validation.
     *
     * @param array $asns ASNs from IRRDB
     * @param int $protocol Either 4/6
     *
     * @return array Valid ASNs
     */
    protected function validate( array $asns, int $protocol ) : array
    {
        foreach( $asns as $i => $a ) {
            if( !is_numeric( $a ) || $a <= 0 || $a > 4294967294 ) {
                unset( $asns[ $i ] );
                Log::alert( 'IRRDB CLI action - removing invalid ASN ' . $a . ' from IRRDB result set!' );
            }
        }
        return $asns;
    }
}
