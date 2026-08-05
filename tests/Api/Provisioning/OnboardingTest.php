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
use Illuminate\Support\Facades\Event;

use IXP\Events\User\UserCreated as UserCreatedEvent;
use IXP\Models\Customer;
use IXP\Models\IPv4Address;
use IXP\Models\SwitchPort;
use IXP\Models\User;
use IXP\Models\VirtualInterface;
use IXP\Models\Vlan;

use Tests\TestCase;

/**
 * Test the composite onboarding endpoint.
 *
 * The assertions that matter here are about atomicity and repeat calls: a partial failure must
 * leave nothing behind, and a retry must not create a second member.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class OnboardingTest extends TestCase
{
    private const string PREFIX = 'onbtest';

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

    private function purge(): void
    {
        DB::table( 'provisioning_references' )->where( 'reference', 'like', self::PREFIX . '%' )->delete();

        $custIds = DB::table( 'cust' )->where( 'shortname', 'like', self::PREFIX . '%' )->pluck( 'id' );
        $userIds = DB::table( 'user' )->where( 'username', 'like', self::PREFIX . '%' )->pluck( 'id' );

        if( $userIds->isNotEmpty() ) {
            DB::table( 'customer_to_users' )->whereIn( 'user_id', $userIds )->delete();
            DB::table( 'user' )->whereIn( 'id', $userIds )->delete();
        }

        if( $custIds->isEmpty() ) {
            return;
        }

        $viIds = DB::table( 'virtualinterface' )->whereIn( 'custid', $custIds )->pluck( 'id' );

        if( $viIds->isNotEmpty() ) {
            $vliIds = DB::table( 'vlaninterface' )->whereIn( 'virtualinterfaceid', $viIds )->pluck( 'id' );

            if( $vliIds->isNotEmpty() ) {
                DB::table( 'l2address' )->whereIn( 'vlan_interface_id', $vliIds )->delete();
            }

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

    private function vlan(): Vlan
    {
        return Vlan::findOrFail( IPv4Address::whereDoesntHave( 'vlanInterface' )->value( 'vlanid' ) );
    }

    private function freePort(): SwitchPort
    {
        return SwitchPort::whereIn( 'type', [ SwitchPort::TYPE_UNSET, SwitchPort::TYPE_PEERING ] )
            ->whereDoesntHave( 'physicalInterface' )
            ->whereHas( 'switcher', fn( $q ) => $q->where( 'active', true ) )
            ->firstOrFail();
    }

    private function order( string $suffix, array $overrides = [] ): array
    {
        $vlan = $this->vlan();
        $sp   = $this->freePort();

        return array_replace_recursive( [
            'reference'  => self::PREFIX . '-' . $suffix,
            'member'     => [
                'name'              => "Onboarding Test {$suffix}",
                'shortname'         => self::PREFIX . $suffix,
                'abbreviatedName'   => self::PREFIX . $suffix,
                'type'              => Customer::TYPE_FULL,
                'status'            => Customer::STATUS_NORMAL,
                'datejoin'          => '2026-01-01',
                'autsys'            => 65552,
            ],
            'user'       => [
                'name'      => 'Onboarding Test User',
                'username'  => self::PREFIX . $suffix . 'user',
                'email'     => self::PREFIX . $suffix . '@example.com',
                'privs'     => User::AUTH_CUSTUSER,
            ],
            'connection' => [
                'vlanid'        => $vlan->id,
                'switch'        => $sp->switchid,
                'switchportid'  => $sp->id,
                'speed'         => 10000,
                'ipv4enabled'   => true,
                'ipv4address'   => 'auto',
                'ipv4hostname'  => 'onbtest.example.com',
                'ipv6enabled'   => false,
                'rsclient'      => true,
                'irrdbfilter'   => true,
            ],
        ], $overrides );
    }

    public function testFullOrderCreatesEverything(): void
    {
        Event::fake( [ UserCreatedEvent::class ] );

        $response = $this->key()->post( '/admin/api/v4/provisioning/onboarding', $this->order( 'a' ) );

        $response->assertStatus( 201 );
        $response->assertJsonPath( 'created', true );

        $cust = Customer::where( 'shortname', self::PREFIX . 'a' )->first();
        $this->assertNotNull( $cust );

        $this->assertNotNull( User::where( 'username', self::PREFIX . 'auser' )->first() );
        $this->assertNotNull( VirtualInterface::where( 'custid', $cust->id )->first() );

        Event::assertDispatched( UserCreatedEvent::class );
    }

    /**
     * Member only: user and connection are optional.
     */
    public function testMemberOnlyOrder(): void
    {
        $order = $this->order( 'b' );
        unset( $order[ 'user' ], $order[ 'connection' ] );

        $this->key()->post( '/admin/api/v4/provisioning/onboarding', $order )->assertStatus( 201 );

        $cust = Customer::where( 'shortname', self::PREFIX . 'b' )->first();

        $this->assertNotNull( $cust );
        $this->assertSame( 0, VirtualInterface::where( 'custid', $cust->id )->count() );
    }

    /**
     * The same reference twice must return the first result, not create a second member.
     */
    public function testRepeatedReferenceIsIdempotent(): void
    {
        $order = $this->order( 'c' );

        $first = $this->key()->post( '/admin/api/v4/provisioning/onboarding', $order );
        $first->assertStatus( 201 )->assertJsonPath( 'created', true );

        $id = $first->json( 'member.id' );

        $second = $this->key()->post( '/admin/api/v4/provisioning/onboarding', $order );
        $second->assertStatus( 200 )
            ->assertJsonPath( 'created', false )
            ->assertJsonPath( 'member.id', $id );

        $this->assertSame( 1, Customer::where( 'shortname', self::PREFIX . 'c' )->count() );
    }

    /**
     * A failure in the connection section must roll back the member and the user with it.
     *
     * This is the whole reason the endpoint exists: calling the three endpoints in sequence
     * would leave the member and user behind when the third call failed.
     */
    public function testConnectionFailureRollsBackTheWholeOrder(): void
    {
        Event::fake( [ UserCreatedEvent::class ] );

        $order = $this->order( 'd', [ 'connection' => [ 'ipv4address' => '203.0.113.249' ] ] );

        $this->key()->post( '/admin/api/v4/provisioning/onboarding', $order )->assertStatus( 422 );

        $this->assertNull( Customer::where( 'shortname', self::PREFIX . 'd' )->first(), 'the member was not rolled back' );
        $this->assertNull( User::where( 'username', self::PREFIX . 'duser' )->first(), 'the user was not rolled back' );

        // and no welcome email may have gone out for a user which no longer exists:
        Event::assertNotDispatched( UserCreatedEvent::class );

        $this->assertSame(
            0,
            DB::table( 'provisioning_references' )->where( 'reference', self::PREFIX . '-d' )->count(),
            'a reference was recorded for an order which failed'
        );
    }

    /**
     * A taken switch port must fail the order rather than half-create it.
     */
    public function testTakenSwitchPortRollsBackTheOrder(): void
    {
        $taken = DB::table( 'physicalinterface' )->whereNotNull( 'switchportid' )->value( 'switchportid' );

        $order = $this->order( 'e', [ 'connection' => [ 'switchportid' => $taken ] ] );

        $this->key()->post( '/admin/api/v4/provisioning/onboarding', $order )->assertStatus( 422 );

        $this->assertNull( Customer::where( 'shortname', self::PREFIX . 'e' )->first() );
    }

    public function testInvalidMemberSectionReturnsNestedErrors(): void
    {
        $response = $this->key()->post( '/admin/api/v4/provisioning/onboarding', [
            'member' => [ 'name' => 'incomplete' ],
        ] );

        $response->assertStatus( 422 );
        $response->assertJsonValidationErrors( [ 'member.shortname', 'member.type', 'member.status' ] );
    }

    public function testMissingMemberSectionIsRejected(): void
    {
        $this->key()->post( '/admin/api/v4/provisioning/onboarding', [] )
            ->assertStatus( 422 )
            ->assertJsonValidationErrors( [ 'member' ] );
    }

    /**
     * An order without a reference still works - idempotency is opt-in.
     */
    public function testOrderWithoutReference(): void
    {
        $order = $this->order( 'f' );
        unset( $order[ 'reference' ], $order[ 'connection' ] );

        $this->key()->post( '/admin/api/v4/provisioning/onboarding', $order )
            ->assertStatus( 201 )
            ->assertJsonPath( 'reference', null );

        $this->assertNotNull( Customer::where( 'shortname', self::PREFIX . 'f' )->first() );
    }

    public function testOnboardingRequiresSuperuser(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_CUSTADMIN )
            ->post( '/admin/api/v4/provisioning/onboarding', [] )
            ->assertStatus( 403 );
    }
}
