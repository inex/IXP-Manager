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

use IXP\Models\PhysicalInterface;
use IXP\Models\SwitchPort;

use Tests\TestCase;

/**
 * Test the read-only port discovery endpoints.
 *
 * The important assertions here are the negative ones: a port which is already in use, or
 * which belongs to the infrastructure rather than to a member, must never be offered to an
 * ordering system.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PortTest extends TestCase
{
    private function key(): static
    {
        return $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER );
    }

    public function testFreePortsExcludeThoseInUse(): void
    {
        $used = PhysicalInterface::whereNotNull( 'switchportid' )->first();
        $this->assertNotNull( $used, 'the CI dataset has no physical interface to test against' );

        $response = $this->key()->get( '/admin/api/v4/provisioning/port/free?limit=1000' );
        $response->assertStatus( 200 );

        $offered = array_column( $response->json( 'ports' ), 'switchport' );

        $this->assertNotContains(
            $used->switchportid,
            $offered,
            'a switch port which already has a physical interface was offered as free'
        );
    }

    /**
     * Core, management, monitor, fanout and reseller ports are infrastructure, not member
     * capacity - offering them would let a caller reassign the fabric.
     */
    public function testFreePortsOnlyOfferUnsetOrPeeringTypes(): void
    {
        $response = $this->key()->get( '/admin/api/v4/provisioning/port/free?limit=1000' );
        $response->assertStatus( 200 );

        foreach( $response->json( 'ports' ) as $port ) {
            $this->assertContains(
                $port[ 'type' ],
                [ SwitchPort::TYPE_UNSET, SwitchPort::TYPE_PEERING ],
                "port {$port['name']} on {$port['switch_name']} has type {$port['type']}, which must not be offered"
            );
        }
    }

    /**
     * A genuinely free port must actually appear, or the endpoint is uselessly conservative.
     */
    public function testAKnownFreePortIsOffered(): void
    {
        $free = SwitchPort::whereIn( 'type', [ SwitchPort::TYPE_UNSET, SwitchPort::TYPE_PEERING ] )
            ->whereDoesntHave( 'physicalInterface' )
            ->whereHas( 'switcher', fn( $q ) => $q->where( 'active', true ) )
            ->first();

        $this->assertNotNull( $free, 'the CI dataset has no free switch port to test against' );

        $response = $this->key()->get( '/admin/api/v4/provisioning/port/free?limit=1000' );

        $this->assertContains( $free->id, array_column( $response->json( 'ports' ), 'switchport' ) );
    }

    public function testFreePortsRespectLimit(): void
    {
        $response = $this->key()->get( '/admin/api/v4/provisioning/port/free?limit=2' );

        $response->assertStatus( 200 );
        $this->assertLessThanOrEqual( 2, count( $response->json( 'ports' ) ) );
    }

    public function testFreePortsRejectUnknownSwitch(): void
    {
        $this->key()->get( '/admin/api/v4/provisioning/port/free?switch=99999999' )
            ->assertStatus( 422 )
            ->assertJsonValidationErrors( [ 'switch' ] );
    }

    public function testSwitchListing(): void
    {
        $response = $this->key()->get( '/admin/api/v4/provisioning/switch' );

        $response->assertStatus( 200 );
        $response->assertJsonStructure( [ 'switches' => [ [ 'id', 'name', 'infrastructure' ] ] ] );
    }

    public function testInfrastructureListingIncludesVlans(): void
    {
        $response = $this->key()->get( '/admin/api/v4/provisioning/infrastructure' );

        $response->assertStatus( 200 );
        $response->assertJsonStructure( [ 'infrastructures' => [ [ 'id', 'name', 'vlans' ] ] ] );
    }

    public function testPortEndpointsRequireSuperuser(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_CUSTADMIN )
            ->get( '/admin/api/v4/provisioning/port/free' )
            ->assertStatus( 403 );
    }
}
