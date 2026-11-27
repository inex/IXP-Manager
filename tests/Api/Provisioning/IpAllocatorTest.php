<?php

namespace Tests\Api\Provisioning;

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

use Illuminate\Support\Facades\DB;

use IXP\Models\IPv4Address;
use IXP\Models\IPv6Address;
use IXP\Models\Vlan;
use IXP\Support\Provisioning\IpAllocationException;
use IXP\Support\Provisioning\IpAllocator;

use Tests\TestCase;

/**
 * Test address allocation.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IpAllocatorTest extends TestCase
{
    private function allocator(): IpAllocator
    {
        return new IpAllocator();
    }

    private function vlanWithAddresses(): Vlan
    {
        $vlan = Vlan::whereHas( 'ipv4Addresses' )->first()
            ?? Vlan::has( 'ipv4Addresses' )->first();

        if( !$vlan ) {
            $id   = IPv4Address::query()->value( 'vlanid' );
            $vlan = Vlan::find( $id );
        }

        $this->assertNotNull( $vlan, 'the CI dataset has no VLAN with an address pool' );

        return $vlan;
    }

    public function testNextFreeReturnsAnUnassignedAddress(): void
    {
        $vlan = $this->vlanWithAddresses();

        DB::transaction( function() use ( $vlan ) {
            $ip = $this->allocator()->nextFree( $vlan );

            $this->assertNotNull( $ip );
            $this->assertSame( $vlan->id, (int)$ip->vlanid );
            $this->assertNull( $ip->vlanInterface, 'an address already in use was handed out as free' );
        } );
    }

    /**
     * Ordering must be numeric, not lexical: string ordering puts .10 before .9.
     */
    public function testNextFreeOrdersNumerically(): void
    {
        $vlan = $this->vlanWithAddresses();

        // Comparing against the single lowest address proves little: in most pools the lowest
        // is the same either way. The difference between numeric and lexical ordering only
        // shows up across a digit boundary - lexically ".10" sorts before ".9" - so compare a
        // whole run of addresses against a numerically sorted expectation.
        $expected = IPv4Address::where( 'vlanid', $vlan->id )
            ->whereDoesntHave( 'vlanInterface' )
            ->get()
            ->sort( fn( IPv4Address $a, IPv4Address $b ) =>
                ( ip2long( $a->address ) <=> ip2long( $b->address ) ) )
            ->pluck( 'address' )
            ->take( 15 )
            ->values()
            ->all();

        $lexical = IPv4Address::where( 'vlanid', $vlan->id )
            ->whereDoesntHave( 'vlanInterface' )
            ->orderBy( 'address' )
            ->limit( 15 )
            ->pluck( 'address' )
            ->all();

        $this->assertNotSame(
            $lexical,
            $expected,
            'this pool cannot distinguish numeric from lexical ordering - the test proves nothing'
        );

        $actual = IPv4Address::where( 'vlanid', $vlan->id )
            ->whereDoesntHave( 'vlanInterface' )
            ->orderByRaw( 'INET_ATON(address) ASC' )
            ->limit( 15 )
            ->pluck( 'address' )
            ->all();

        $this->assertSame( $expected, $actual, 'addresses are not ordered numerically' );

        DB::transaction( function() use ( $vlan, $expected ) {
            $this->assertSame( $expected[ 0 ], $this->allocator()->nextFree( $vlan )->address );
        } );
    }

    public function testAutoClaimsTheNextFreeAddress(): void
    {
        $vlan = $this->vlanWithAddresses();

        DB::transaction( function() use ( $vlan ) {
            $auto = $this->allocator()->claim( $vlan, 'auto' );
            $next = $this->allocator()->nextFree( $vlan );

            $this->assertNotNull( $auto );
            $this->assertSame( $next->id, $auto->id );
        } );
    }

    public function testNullOrEmptyRequestYieldsNoAddress(): void
    {
        $vlan = $this->vlanWithAddresses();

        $this->assertNull( $this->allocator()->claim( $vlan, null ) );
        $this->assertNull( $this->allocator()->claim( $vlan, '' ) );
        $this->assertNull( $this->allocator()->claim( $vlan, '   ' ) );
    }

    public function testExplicitFreeAddressIsClaimed(): void
    {
        $vlan = $this->vlanWithAddresses();

        DB::transaction( function() use ( $vlan ) {
            $free = IPv4Address::where( 'vlanid', $vlan->id )->whereDoesntHave( 'vlanInterface' )->first();

            $claimed = $this->allocator()->claim( $vlan, $free->address );

            $this->assertSame( $free->id, $claimed->id );
        } );
    }

    public function testAddressAlreadyInUseIsRejected(): void
    {
        $used = IPv4Address::whereHas( 'vlanInterface' )->first();

        if( !$used ) {
            $this->markTestSkipped( 'the CI dataset has no assigned IPv4 address' );
        }

        $vlan = Vlan::find( $used->vlanid );

        $this->expectException( IpAllocationException::class );
        $this->expectExceptionMessageMatches( '/already assigned/' );

        $this->allocator()->claim( $vlan, $used->address );
    }

    public function testAddressOutsideThePoolIsRejected(): void
    {
        $vlan = $this->vlanWithAddresses();

        $this->expectException( IpAllocationException::class );
        $this->expectExceptionMessageMatches( '/not in the address pool/' );

        $this->allocator()->claim( $vlan, '203.0.113.253' );
    }

    public function testMalformedAddressIsRejected(): void
    {
        $vlan = $this->vlanWithAddresses();

        $this->expectException( IpAllocationException::class );
        $this->expectExceptionMessageMatches( '/not a valid/' );

        $this->allocator()->claim( $vlan, 'not-an-address' );
    }

    /**
     * An IPv6 address written in a different but equivalent form must still match the pool
     * entry: every comparison in the existing code is a raw string comparison.
     */
    public function testIpv6IsMatchedRegardlessOfNotation(): void
    {
        $free = IPv6Address::whereDoesntHave( 'vlanInterface' )->first();

        if( !$free ) {
            $this->markTestSkipped( 'the CI dataset has no free IPv6 address' );
        }

        $vlan = Vlan::find( $free->vlanid );

        // expand the stored form into its fully written, equivalent notation
        // (2001:db8::1 -> 2001:0db8:0000:0000:0000:0000:0000:0001):
        $expanded = implode( ':', str_split( bin2hex( inet_pton( $free->address ) ), 4 ) );

        $this->assertNotSame( $free->address, $expanded, 'the stored address is already fully written out' );

        DB::transaction( function() use ( $vlan, $expanded, $free ) {
            $claimed = $this->allocator()->claim( $vlan, $expanded, true );

            $this->assertSame( $free->id, $claimed->id );
        } );
    }
}
