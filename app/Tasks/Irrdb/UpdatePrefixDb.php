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
use Log;

use IXP\Rules\{IPv4Cidr as ValidateIPv4Cidr, IPv6Cidr as ValidateIPv6Cidr};

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
     * @throws \IXP\Exceptions\Services\Grapher\GeneralException
     * @return array
     */
    public function update(): array {

        foreach( $this->protocols() as $protocol ) {
            if( $this->customer()->isRouteServerClient( $protocol ) && $this->customer()->isIrrdbFiltered() && $this->customer()->getIRRDB() ) {
                $this->bgpq3()->setSources( $this->customer()->getIRRDB()->getSource() );

                $this->startTimer();
                $prefixes = $this->bgpq3()->getPrefixList( $this->customer()->resolveAsMacro( $protocol, 'as' ), $protocol );
                $this->result[ 'netTime' ] += $this->timeElapsed();

                $this->result[ 'v' . $protocol ][ 'count' ] = count( $prefixes );

                if( $this->updateDb( $prefixes, $protocol, 'prefix' ) ) {
                    $this->result[ 'v' . $protocol ][ 'dbUpdated' ] = true;
                }
            } else {
                // This customer is not appropriate for IRRDB filtering.
                // Delete any pre-existing entries just in case this has changed recently:
                $this->startTimer();
                D2EM::getConnection()->executeUpdate(
                    "DELETE FROM `irrdb_prefix` WHERE customer_id = ? AND protocol = ?", [ $this->customer()->getId(), $protocol ]
                );
                $this->result[ 'dbTime' ] += $this->timeElapsed();
                $this->result[ 'v' . $protocol ][ 'dbUpdated' ] = true;
                $this->result[ 'msg' ] = "Customer not a RS client or IRRDB filtered for IPv{$protocol}. IPv{$protocol} prefixes, if any, wiped from database.";
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
     * @return array Valid prefixes
     */
    protected function validate( array $prefixes, int $protocol ): array {
        if( $protocol == 4 ) {
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
