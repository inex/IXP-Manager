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

use IXP\Models\Customer;
use IXP\Models\IPv4Address;
use IXP\Models\PhysicalInterface;
use IXP\Models\SwitchPort;
use IXP\Models\VirtualInterface;
use IXP\Models\Vlan;

use Tests\TestCase;

/**
 * Test creating and removing a member connection.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ConnectionTest extends TestCase
{
    private const string PREFIX = 'conntest';

    public function setUp(): void
    {
        parent::setUp();
        $this->purge();
    }

    public function tearDown(): void
    {
        $this->purge();
        parent::tearDown();
    }

    /**
     * Remove everything this class creates, honouring the foreign keys.
     */
    private function purge(): void
    {
        $custIds = DB::table( 'cust' )->where( 'shortname', 'like', self::PREFIX . '%' )->pluck( 'id' );

        if( $custIds->isEmpty() ) {
            return;
        }

        $viIds = DB::table( 'virtualinterface' )->whereIn( 'custid', $custIds )->pluck( 'id' );

        if( $viIds->isNotEmpty() ) {
            $vliIds = DB::table( 'vlaninterface' )->whereIn( 'virtualinterfaceid', $viIds )->pluck( 'id' );

            if( $vliIds->isNotEmpty() ) {
                DB::table( 'l2address' )->whereIn( 'vlan_interface_id', $vliIds )->delete();
            }

            // release the switch ports before the physical interfaces disappear
            $spIds = DB::table( 'physicalinterface' )->whereIn( 'virtualinterfaceid', $viIds )
                ->whereNotNull( 'switchportid' )->pluck( 'switchportid' );

            DB::table( 'vlaninterface' )->whereIn( 'virtualinterfaceid', $viIds )->delete();
            DB::table( 'physicalinterface' )->whereIn( 'virtualinterfaceid', $viIds )->delete();
            DB::table( 'macaddress' )->whereIn( 'virtualinterfaceid', $viIds )->delete();
            DB::table( 'virtualinterface' )->whereIn( 'id', $viIds )->delete();

            if( $spIds->isNotEmpty() ) {
                DB::table( 'switchport' )->whereIn( 'id', $spIds )->update( [ 'type' => SwitchPort::TYPE_UNSET ] );
            }
        }

        $details = DB::table( 'cust' )->whereIn( 'id', $custIds )
            ->get( [ 'company_registered_detail_id', 'company_billing_details_id' ] );

        DB::table( 'customer_to_users' )->whereIn( 'customer_id', $custIds )->delete();
        DB::table( 'cust' )->whereIn( 'id', $custIds )->delete();

        if( ( $ids = $details->pluck( 'company_registered_detail_id' )->filter() )->isNotEmpty() ) {
            DB::table( 'company_registration_detail' )->whereIn( 'id', $ids )->delete();
        }

        if( ( $ids = $details->pluck( 'company_billing_details_id' )->filter() )->isNotEmpty() ) {
            DB::table( 'company_billing_detail' )->whereIn( 'id', $ids )->delete();
        }
    }

    private function key(): static
    {
        return $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER );
    }

    private function createMember( string $shortname ): Customer
    {
        $this->key()->post( '/admin/api/v4/provisioning/member', [
            'name'              => "Connection Test {$shortname}",
            'shortname'         => $shortname,
            'abbreviatedName'   => $shortname,
            'type'              => Customer::TYPE_FULL,
            'status'            => Customer::STATUS_NORMAL,
            'datejoin'          => '2026-01-01',
            'autsys'            => 65551,
        ] )->assertStatus( 201 );

        return Customer::where( 'shortname', $shortname )->firstOrFail();
    }

    /**
     * A VLAN which has both a free IPv4 and a free IPv6 address, plus a free member port.
     */
    private function vlanWithFreeAddresses(): Vlan
    {
        $vlanId = IPv4Address::whereDoesntHave( 'vlanInterface' )->value( 'vlanid' );
        $vlan   = Vlan::find( $vlanId );

        $this->assertNotNull( $vlan, 'the CI dataset has no VLAN with free addresses' );

        return $vlan;
    }

    private function freeSwitchPort(): SwitchPort
    {
        $sp = SwitchPort::whereIn( 'type', [ SwitchPort::TYPE_UNSET, SwitchPort::TYPE_PEERING ] )
            ->whereDoesntHave( 'physicalInterface' )
            ->whereHas( 'switcher', fn( $q ) => $q->where( 'active', true ) )
            ->first();

        $this->assertNotNull( $sp, 'the CI dataset has no free switch port' );

        return $sp;
    }

    private function payload( Vlan $vlan, SwitchPort $sp, array $overrides = [] ): array
    {
        return array_merge( [
            'vlanid'            => $vlan->id,
            'switch'            => $sp->switchid,
            'switchportid'      => $sp->id,
            'speed'             => 10000,
            'ipv4enabled'       => true,
            'ipv4address'       => 'auto',
            'ipv4hostname'      => 'conntest.example.com',
            'ipv6enabled'       => false,
            'rsclient'          => true,
            'irrdbfilter'       => true,
        ], $overrides );
    }

    public function testCreateConnectionWithAutoAddress(): void
    {
        $cust = $this->createMember( self::PREFIX . 'a' );
        $vlan = $this->vlanWithFreeAddresses();
        $sp   = $this->freeSwitchPort();

        $expected = ( new \IXP\Support\Provisioning\IpAllocator() )->nextFree( $vlan );

        $response = $this->key()->post(
            "/admin/api/v4/provisioning/member/{$cust->id}/connection",
            $this->payload( $vlan, $sp )
        );

        $response->assertStatus( 201 );
        $response->assertJsonPath( 'connection.custid', $cust->id );

        $vi = VirtualInterface::where( 'custid', $cust->id )->first();
        $this->assertNotNull( $vi, 'no virtual interface was created' );

        $pi = PhysicalInterface::where( 'virtualinterfaceid', $vi->id )->first();
        $this->assertNotNull( $pi, 'no physical interface was created' );
        $this->assertSame( $sp->id, (int)$pi->switchportid );
        $this->assertSame( PhysicalInterface::STATUS_CONNECTED, (int)$pi->status );

        $vli = $vi->vlanInterfaces()->first();
        $this->assertNotNull( $vli, 'no vlan interface was created' );
        $this->assertSame( $vlan->id, (int)$vli->vlanid );
        $this->assertSame( $expected->address, $vli->ipv4address->address, '"auto" did not take the next free address' );

        // the switch port must now be marked as a peering port:
        $this->assertSame( SwitchPort::TYPE_PEERING, (int)SwitchPort::find( $sp->id )->type );
    }

    public function testCreateConnectionWithExplicitAddress(): void
    {
        $cust = $this->createMember( self::PREFIX . 'b' );
        $vlan = $this->vlanWithFreeAddresses();
        $sp   = $this->freeSwitchPort();

        $wanted = IPv4Address::where( 'vlanid', $vlan->id )->whereDoesntHave( 'vlanInterface' )
            ->orderByRaw( 'INET_ATON(address) DESC' )->first();

        $this->key()->post(
            "/admin/api/v4/provisioning/member/{$cust->id}/connection",
            $this->payload( $vlan, $sp, [ 'ipv4address' => $wanted->address ] )
        )->assertStatus( 201 );

        $vli = VirtualInterface::where( 'custid', $cust->id )->first()->vlanInterfaces()->first();

        $this->assertSame( $wanted->address, $vli->ipv4address->address );
    }

    /**
     * An address outside the VLAN's pool must be refused rather than silently created - the
     * inherited setIp() would create it.
     */
    public function testAddressOutsidePoolIsRejected(): void
    {
        $cust = $this->createMember( self::PREFIX . 'c' );
        $vlan = $this->vlanWithFreeAddresses();
        $sp   = $this->freeSwitchPort();

        $this->key()->post(
            "/admin/api/v4/provisioning/member/{$cust->id}/connection",
            $this->payload( $vlan, $sp, [ 'ipv4address' => '203.0.113.251' ] )
        )->assertStatus( 422 );

        $this->assertNull( VirtualInterface::where( 'custid', $cust->id )->first(), 'a rollback did not happen' );
        $this->assertSame( 0, IPv4Address::where( 'address', '203.0.113.251' )->count(), 'a stray address was created' );
    }

    /**
     * A switch port already carrying a connection must not be handed out twice.
     */
    public function testTakenSwitchPortIsRejected(): void
    {
        $cust = $this->createMember( self::PREFIX . 'd' );
        $vlan = $this->vlanWithFreeAddresses();

        $taken = PhysicalInterface::whereNotNull( 'switchportid' )->first();
        $this->assertNotNull( $taken );

        $sp = SwitchPort::find( $taken->switchportid );

        $this->key()->post(
            "/admin/api/v4/provisioning/member/{$cust->id}/connection",
            $this->payload( $vlan, $sp )
        )->assertStatus( 422 );

        $this->assertNull( VirtualInterface::where( 'custid', $cust->id )->first() );
    }

    /**
     * The switch id and the switch port must agree, or a caller could attach a port to a
     * switch it does not belong to.
     */
    public function testMismatchedSwitchAndPortIsRejected(): void
    {
        $cust = $this->createMember( self::PREFIX . 'e' );
        $vlan = $this->vlanWithFreeAddresses();
        $sp   = $this->freeSwitchPort();

        $otherSwitch = DB::table( 'switch' )->where( 'id', '!=', $sp->switchid )->value( 'id' );

        if( !$otherSwitch ) {
            $this->markTestSkipped( 'the CI dataset has only one switch' );
        }

        $this->key()->post(
            "/admin/api/v4/provisioning/member/{$cust->id}/connection",
            $this->payload( $vlan, $sp, [ 'switch' => $otherSwitch ] )
        )->assertStatus( 422 );
    }

    public function testMalformedAddressIsRejectedByValidation(): void
    {
        $cust = $this->createMember( self::PREFIX . 'f' );
        $vlan = $this->vlanWithFreeAddresses();
        $sp   = $this->freeSwitchPort();

        $this->key()->post(
            "/admin/api/v4/provisioning/member/{$cust->id}/connection",
            $this->payload( $vlan, $sp, [ 'ipv4address' => 'nonsense' ] )
        )
            ->assertStatus( 422 )
            ->assertJsonValidationErrors( [ 'ipv4address' ] );
    }

    public function testListAndDeleteConnection(): void
    {
        $cust = $this->createMember( self::PREFIX . 'g' );
        $vlan = $this->vlanWithFreeAddresses();
        $sp   = $this->freeSwitchPort();

        $this->key()->post(
            "/admin/api/v4/provisioning/member/{$cust->id}/connection",
            $this->payload( $vlan, $sp )
        )->assertStatus( 201 );

        $this->key()->get( "/admin/api/v4/provisioning/member/{$cust->id}/connection" )
            ->assertStatus( 200 )
            ->assertJsonCount( 1, 'connections' );

        $vi = VirtualInterface::where( 'custid', $cust->id )->first();

        $this->key()->delete( "/admin/api/v4/provisioning/connection/{$vi->id}" )
            ->assertStatus( 200 )
            ->assertJsonPath( 'deleted', true );

        $this->assertNull( VirtualInterface::find( $vi->id ) );
        $this->assertSame( 0, PhysicalInterface::where( 'virtualinterfaceid', $vi->id )->count() );

        // the address must return to the pool:
        $this->assertSame(
            0,
            DB::table( 'vlaninterface' )->where( 'virtualinterfaceid', $vi->id )->count()
        );
    }

    /**
     * A non-superuser key must be refused.
     *
     * Note this test makes no superuser call first, deliberately: ApiAuthenticate skips the
     * key check entirely when Auth::check() is already true, and that state survives between
     * requests within one test. Creating a member here first would leave the superuser
     * authenticated and the assertion would pass for the wrong reason.
     */
    public function testConnectionEndpointsRequireSuperuser(): void
    {
        $cust = Customer::firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_CUSTADMIN )
            ->post( "/admin/api/v4/provisioning/member/{$cust->id}/connection", [] )
            ->assertStatus( 403 );
    }
}
