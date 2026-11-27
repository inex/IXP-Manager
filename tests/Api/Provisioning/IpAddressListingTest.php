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

use IXP\Models\IPv4Address;
use IXP\Models\Vlan;

use Tests\TestCase;

/**
 * Test the address listing endpoint.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IpAddressListingTest extends TestCase
{
    private function key(): static
    {
        return $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER );
    }

    private function vlan(): Vlan
    {
        $vlan = Vlan::find( IPv4Address::query()->value( 'vlanid' ) );

        $this->assertNotNull( $vlan, 'the CI dataset has no VLAN with an address pool' );

        return $vlan;
    }

    public function testListingReturnsBothFamiliesByDefault(): void
    {
        $response = $this->key()->get( "/admin/api/v4/provisioning/vlan/{$this->vlan()->id}/address" );

        $response->assertStatus( 200 );
        $response->assertJsonStructure( [
            'vlan' => [ 'id', 'name', 'number' ],
            'ipv4' => [ 'addresses', 'total', 'truncated' ],
            'ipv6' => [ 'addresses', 'total', 'truncated' ],
        ] );
    }

    public function testProtocolFilterReturnsOnlyThatFamily(): void
    {
        $vlan = $this->vlan();

        $this->key()->get( "/admin/api/v4/provisioning/vlan/{$vlan->id}/address?protocol=4" )
            ->assertStatus( 200 )
            ->assertJsonStructure( [ 'ipv4' ] )
            ->assertJsonMissingPath( 'ipv6' );

        $this->key()->get( "/admin/api/v4/provisioning/vlan/{$vlan->id}/address?protocol=6" )
            ->assertStatus( 200 )
            ->assertJsonStructure( [ 'ipv6' ] )
            ->assertJsonMissingPath( 'ipv4' );
    }

    /**
     * free=1 must exclude assigned addresses.
     */
    public function testFreeFilterExcludesAssignedAddresses(): void
    {
        $used = IPv4Address::whereHas( 'vlanInterface' )->first();

        if( !$used ) {
            $this->markTestSkipped( 'the CI dataset has no assigned IPv4 address' );
        }

        $response = $this->key()->get(
            "/admin/api/v4/provisioning/vlan/{$used->vlanid}/address?protocol=4&free=1&limit=2000"
        );

        $response->assertStatus( 200 );

        $addresses = array_column( $response->json( 'ipv4.addresses' ), 'address' );

        $this->assertNotContains( $used->address, $addresses, 'an assigned address was listed as free' );

        foreach( $response->json( 'ipv4.addresses' ) as $entry ) {
            $this->assertTrue( $entry[ 'free' ], "address {$entry['address']} is listed but not free" );
        }
    }

    /**
     * Without the filter, an assigned address appears and is marked as not free.
     */
    public function testAssignedAddressIsMarkedNotFree(): void
    {
        $used = IPv4Address::whereHas( 'vlanInterface' )->first();

        if( !$used ) {
            $this->markTestSkipped( 'the CI dataset has no assigned IPv4 address' );
        }

        $response = $this->key()->get(
            "/admin/api/v4/provisioning/vlan/{$used->vlanid}/address?protocol=4&limit=2000"
        );

        $entries = collect( $response->json( 'ipv4.addresses' ) )->firstWhere( 'address', $used->address );

        $this->assertNotNull( $entries, 'the assigned address is missing from the unfiltered listing' );
        $this->assertFalse( $entries[ 'free' ] );
    }

    public function testLimitTruncates(): void
    {
        $response = $this->key()->get( "/admin/api/v4/provisioning/vlan/{$this->vlan()->id}/address?protocol=4&limit=1" );

        $response->assertStatus( 200 );

        $this->assertCount( 1, $response->json( 'ipv4.addresses' ) );
        $this->assertTrue( $response->json( 'ipv4.truncated' ) );
        $this->assertGreaterThan( 1, $response->json( 'ipv4.total' ) );
    }

    public function testUnknownVlanIs404(): void
    {
        $this->key()->get( '/admin/api/v4/provisioning/vlan/99999999/address' )->assertStatus( 404 );
    }

    public function testInvalidProtocolIsRejected(): void
    {
        $this->key()->get( "/admin/api/v4/provisioning/vlan/{$this->vlan()->id}/address?protocol=5" )
            ->assertStatus( 422 )
            ->assertJsonValidationErrors( [ 'protocol' ] );
    }

    public function testListingRequiresSuperuser(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_CUSTADMIN )
            ->get( '/admin/api/v4/provisioning/vlan/1/address' )
            ->assertStatus( 403 );
    }
}
