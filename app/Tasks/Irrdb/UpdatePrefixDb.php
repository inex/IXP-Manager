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

use Illuminate\Support\Facades\Cache;
use Log;

use IXP\Models\IrrdbPrefix;

use IXP\Rules\{
    IPv4Cidr as ValidateIPv4Cidr,
    IPv6Cidr as ValidateIPv6Cidr
};

/**
 * UpdatePrefixDb
 *
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   Tasks
 * @package    IXP\Tasks\Irrdb
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UpdatePrefixDb extends UpdateDb
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
                $prefixes = $this->bgpq3()->getPrefixList( $this->customer()->asMacro( $protocol, 'as' ), $protocol );
                $this->result[ 'netTime' ] += $this->timeElapsed();

                $this->result[ 'v' . $protocol ][ 'count' ] = count( $prefixes );

                if( $this->updateDb( $prefixes, $protocol, 'prefix' ) ) {
                    $this->result[ 'v' . $protocol ][ 'dbUpdated' ] = true;
                }
            } else {
                // This customer is not appropriate for IRRDB filtering.
                // Delete any pre-existing entries just in case this has changed recently:
                $this->startTimer();

                Cache::store('file')->forget( 'irrdb:prefix:ipv' . $protocol . ':' . $this->customer()->asMacro( $protocol ) );

                IrrdbPrefix::whereCustomerId( $this->customer()->id )
                    ->whereProtocol( $protocol )->delete();

                $this->result[ 'dbTime' ] += $this->timeElapsed();
                $this->result[ 'v' . $protocol ][ 'dbUpdated' ] = true;
                $this->result[ 'msg' ] = "{$this->customer()->name} not a RS client or IRRDB filtered for IPv{$protocol}. IPv{$protocol} prefixes, if any, wiped from database.";
            }
        }

        return $this->result;
    }


    /**
     * Validate a given array of CIDR formatted prefixes for the given protocol and
     * remove (and alert on) any elements failing validation.
     *
     * @param array $prefixes Prefixes in CIDR notation
     * @param int $protocol Either 4/6
     *
     * @return array Valid prefixes
     */
    protected function validate( array $prefixes, int $protocol ): array
    {
        if( $protocol === 4 ) {
            $validator = new ValidateIPv4Cidr;
        } else {
            $validator = new ValidateIPv6Cidr;
        }

        foreach( $prefixes as $i => $p ) {
            if( !$validator->passes( [], $p ) ) {
                unset( $prefixes[$i] );
                Log::alert( 'IRRDB CLI action - removing invalid prefix ' . $p . ' from IRRDB result set!' );
            }
        }

        return $prefixes;
    }
}
