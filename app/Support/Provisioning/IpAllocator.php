<?php

namespace IXP\Support\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use IXP\Models\IPv4Address;
use IXP\Models\IPv6Address;
use IXP\Models\Vlan;

/**
 * Allocates the next free address from a VLAN's peering pool.
 *
 * IXP Manager has no server-side equivalent of this: the web wizard requires an explicit
 * address, and the "which ones are free" question is answered in the browser from
 * VlanAggregator::ipAddresses(). That is fine for an operator choosing from a list and no use
 * at all for unattended provisioning, so this is genuinely new code rather than a rearranged
 * copy of something upstream.
 *
 * Free means: the address row exists in the pool for this VLAN and no VlanInterface points at
 * it. Addresses are created ahead of time (see the ipv4/ipv6 address generator commands), so
 * allocation never mints a new address - it only claims an existing, unclaimed one.
 *
 * Concurrency: two orders being provisioned at the same moment must not receive the same
 * address. Selecting with a row lock inside the caller's transaction is what prevents that;
 * the unique indexes on vlaninterface.ipv4addressid / ipv6addressid are the backstop if it
 * ever does happen. Callers must therefore run inside a transaction - claim() says so.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Support\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IpAllocator
{
    /**
     * Find the next free address of the given protocol in a VLAN, locking the row.
     *
     * MUST be called inside a transaction: the lock is released at commit, and until then
     * nothing else can claim the same row. Called outside one, the lock is pointless and two
     * concurrent callers can be handed the same address.
     *
     * Ordering is numeric rather than lexical - INET_ATON / INET6_ATON, matching
     * VlanAggregator::ipAddresses() - so that .10 does not come before .9.
     *
     * @param  Vlan  $vlan
     * @param  bool  $ipv6
     *
     * @return IPv4Address|IPv6Address|null  null when the pool is exhausted
     */
    public function nextFree( Vlan $vlan, bool $ipv6 = false ): IPv4Address|IPv6Address|null
    {
        /** @var class-string<IPv4Address|IPv6Address> $model */
        $model   = $ipv6 ? IPv6Address::class : IPv4Address::class;
        $orderBy = $ipv6 ? 'INET6_ATON' : 'INET_ATON';

        return $model::where( 'vlanid', $vlan->id )
            ->whereDoesntHave( 'vlanInterface' )
            ->orderByRaw( "{$orderBy}(address) ASC" )
            ->lockForUpdate()
            ->first();
    }

    /**
     * Resolve an address request to a concrete address.
     *
     * Accepts either an explicit address - which must exist in this VLAN's pool and be free -
     * or the literal string "auto", which takes the next free one. Anything else, including
     * an address belonging to a different VLAN, is a caller error.
     *
     * @param  Vlan         $vlan
     * @param  string|null  $requested  an address, "auto", or null for no address at all
     * @param  bool         $ipv6
     *
     * @return IPv4Address|IPv6Address|null
     *
     * @throws IpAllocationException
     */
    public function claim( Vlan $vlan, ?string $requested, bool $ipv6 = false ): IPv4Address|IPv6Address|null
    {
        $requested = $requested === null ? null : trim( $requested );

        if( $requested === null || $requested === '' ) {
            return null;
        }

        $proto = $ipv6 ? 'IPv6' : 'IPv4';

        if( strtolower( $requested ) === 'auto' ) {
            if( !( $ip = $this->nextFree( $vlan, $ipv6 ) ) ) {
                throw new IpAllocationException(
                    "No free {$proto} address left in VLAN {$vlan->name} (id {$vlan->id})."
                );
            }

            return $ip;
        }

        return $this->claimExplicit( $vlan, $requested, $ipv6, $proto );
    }

    /**
     * Claim a specific address from the VLAN's pool.
     *
     * @param  Vlan    $vlan
     * @param  string  $address
     * @param  bool    $ipv6
     * @param  string  $proto
     *
     * @return IPv4Address|IPv6Address
     *
     * @throws IpAllocationException
     */
    private function claimExplicit( Vlan $vlan, string $address, bool $ipv6, string $proto ): IPv4Address|IPv6Address
    {
        /** @var class-string<IPv4Address|IPv6Address> $model */
        $model = $ipv6 ? IPv6Address::class : IPv4Address::class;

        $normalised = $this->normalise( $address, $ipv6 );

        if( $normalised === null ) {
            throw new IpAllocationException( "{$address} is not a valid {$proto} address." );
        }

        // Stored addresses are compared as raw strings everywhere in the existing code, so an
        // IPv6 address written differently to the stored form (2001:db8::1 vs 2001:0db8::1)
        // would not match. Compare against both the given and the normalised form:
        $candidates = array_unique( [ $address, $normalised ] );

        $ip = $model::where( 'vlanid', $vlan->id )
            ->whereIn( 'address', $candidates )
            ->lockForUpdate()
            ->first();

        if( !$ip ) {
            throw new IpAllocationException(
                "{$proto} address {$address} is not in the address pool of VLAN {$vlan->name} (id {$vlan->id})."
            );
        }

        if( $ip->vlanInterface ) {
            throw new IpAllocationException(
                "{$proto} address {$address} is already assigned to another VLAN interface."
            );
        }

        return $ip;
    }

    /**
     * Normalise an address to its canonical form, or null if it is not valid.
     *
     * @param  string  $address
     * @param  bool    $ipv6
     *
     * @return string|null
     */
    private function normalise( string $address, bool $ipv6 ): ?string
    {
        $flag = $ipv6 ? FILTER_FLAG_IPV6 : FILTER_FLAG_IPV4;

        if( filter_var( $address, FILTER_VALIDATE_IP, $flag ) === false ) {
            return null;
        }

        if( ( $packed = @inet_pton( $address ) ) === false ) {
            return null;
        }

        $canonical = @inet_ntop( $packed );

        return $canonical === false ? null : $canonical;
    }
}
