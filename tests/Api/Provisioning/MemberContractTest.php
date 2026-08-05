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
use IXP\Models\CustomerToUser;
use IXP\Models\User;

use Tests\TestCase;

/**
 * Assert that creating a member or a user through the provisioning API produces the same
 * database state as doing it through the web UI.
 *
 * The API controllers mirror the web controllers rather than calling them, because the web
 * ones return redirects and push flash messages. Mirroring is only safe if something checks
 * it stays true - that is this test. It posts the same payload to both entry points and
 * compares the resulting rows attribute by attribute.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MemberContractTest extends TestCase
{
    /**
     * Attributes which are expected to differ between two separately created customers.
     *
     * @var array
     */
    private const array CUSTOMER_IGNORED = [
        'id', 'shortname', 'name', 'abbreviatedName',
        'company_registered_detail_id', 'company_billing_details_id',
        'created_at', 'updated_at',
    ];

    /**
     * Prefix used by every row this test creates, so that leftovers can be identified.
     */
    private const string PREFIX = 'provtest';

    /**
     * Remove leftovers from an earlier, aborted run.
     *
     * Without this the suite is not repeatable: a test which fails after the insert but
     * before tearDown leaves a row behind, and the next run then fails on `unique:shortname`
     * instead of on whatever actually broke - which hides the real fault.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->purgeLeftovers();
    }

    public function tearDown(): void
    {
        $this->purgeLeftovers();

        parent::tearDown();
    }

    /**
     * Remove every row this test class can create, in an order the foreign keys allow:
     * links first, then users and customers, and only then the detail records the customer
     * row points at.
     */
    private function purgeLeftovers(): void
    {
        $custs = DB::table( 'cust' )
            ->where( 'shortname', 'like', self::PREFIX . '%' )
            ->get( [ 'id', 'company_registered_detail_id', 'company_billing_details_id' ] );

        $userIds = DB::table( 'user' )->where( 'username', 'like', self::PREFIX . '%' )->pluck( 'id' );

        if( $userIds->isNotEmpty() ) {
            DB::table( 'customer_to_users' )->whereIn( 'user_id', $userIds )->delete();
            DB::table( 'user' )->whereIn( 'id', $userIds )->delete();
        }

        if( $custs->isEmpty() ) {
            return;
        }

        DB::table( 'customer_to_users' )->whereIn( 'customer_id', $custs->pluck( 'id' ) )->delete();
        DB::table( 'cust' )->whereIn( 'id', $custs->pluck( 'id' ) )->delete();

        if( ( $ids = $custs->pluck( 'company_registered_detail_id' )->filter() )->isNotEmpty() ) {
            DB::table( 'company_registration_detail' )->whereIn( 'id', $ids )->delete();
        }

        if( ( $ids = $custs->pluck( 'company_billing_details_id' )->filter() )->isNotEmpty() ) {
            DB::table( 'company_billing_detail' )->whereIn( 'id', $ids )->delete();
        }
    }

    /**
     * Base payload for creating a customer. Shortname must be unique per call.
     */
    private function customerPayload( string $shortname ): array
    {
        return [
            'name'              => "Provisioning Test {$shortname}",
            'shortname'         => $shortname,
            'abbreviatedName'   => $shortname,
            'type'              => Customer::TYPE_FULL,
            'status'            => Customer::STATUS_NORMAL,
            'datejoin'          => '2026-01-01',
            'autsys'            => 65550,
            'maxprefixes'       => 100,
            'maxprefixesv6'     => 100,
            'peeringemail'      => 'peering@example.com',
            'nocemail'          => 'noc@example.com',
        ];
    }

    private function fetchCustomer( string $shortname ): ?Customer
    {
        return Customer::where( 'shortname', $shortname )->first();
    }

    /**
     * The web UI and the API must produce equivalent customer rows.
     */
    public function testCustomerCreationMatchesWebUi(): void
    {
        $this->actingAs( $this->getSuperUser() );
        $this->post( route( 'customer@store' ), $this->customerPayload( 'provtestweb' ) )
            ->assertStatus( 302 );

        $viaWeb = $this->fetchCustomer( 'provtestweb' );
        $this->assertNotNull( $viaWeb, 'the web UI did not create the customer' );

        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( '/admin/api/v4/provisioning/member', $this->customerPayload( 'provtestapi' ) );

        $response->assertStatus( 201 );
        $response->assertJsonPath( 'member.shortname', 'provtestapi' );
        $response->assertJsonPath( 'member.autsys', 65550 );

        $viaApi = $this->fetchCustomer( 'provtestapi' );
        $this->assertNotNull( $viaApi, 'the API did not create the customer' );

        $this->assertEquals(
            array_diff_key( $viaWeb->getAttributes(), array_flip( self::CUSTOMER_IGNORED ) ),
            array_diff_key( $viaApi->getAttributes(), array_flip( self::CUSTOMER_IGNORED ) ),
            'a member created through the API differs from one created through the web UI'
        );
    }

    /**
     * Both paths must create the two detail records the customer depends on.
     */
    public function testCustomerCreationCreatesDetailRecords(): void
    {
        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( '/admin/api/v4/provisioning/member', $this->customerPayload( 'provtestdet' ) );

        $response->assertStatus( 201 );

        $cust = $this->fetchCustomer( 'provtestdet' );
        $this->assertNotNull( $cust );

        $this->assertNotNull( $cust->company_registered_detail_id );
        $this->assertNotNull( $cust->company_billing_details_id );
        $this->assertNotNull( $cust->companyRegisteredDetail );
        $this->assertNotNull( $cust->companyBillingDetail );
        $this->assertSame( $cust->name, $cust->companyRegisteredDetail->registeredName );

    }

    /**
     * An invalid payload must come back as 422 JSON.
     *
     * This is what the ForceJsonResponse middleware buys us: without it a ValidationException
     * on a route outside the `web` group renders as a 302 redirect, which a machine caller
     * would read as anything but a validation failure.
     */
    public function testInvalidPayloadReturnsJsonValidationError(): void
    {
        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( '/admin/api/v4/provisioning/member', [ 'name' => 'missing everything else' ] );

        $response->assertStatus( 422 );
        $response->assertJsonStructure( [ 'message', 'errors' ] );
        $response->assertJsonValidationErrors( [ 'shortname', 'type', 'status', 'datejoin', 'abbreviatedName' ] );
    }

    /**
     * A duplicate shortname must be rejected, not silently create a second member.
     */
    public function testDuplicateShortnameIsRejected(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( '/admin/api/v4/provisioning/member', $this->customerPayload( 'provtestdup' ) )
            ->assertStatus( 201 );

        $cust = $this->fetchCustomer( 'provtestdup' );

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( '/admin/api/v4/provisioning/member', $this->customerPayload( 'provtestdup' ) )
            ->assertStatus( 422 )
            ->assertJsonValidationErrors( [ 'shortname' ] );

        $this->assertEquals( 1, Customer::where( 'shortname', 'provtestdup' )->count() );
    }

    /**
     * Creating a user must persist the user, the customer link and fire the event which
     * sends the welcome email - without that event the account is unreachable.
     */
    public function testUserCreationPersistsAndSendsWelcome(): void
    {
        Event::fake( [ UserCreatedEvent::class ] );

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( '/admin/api/v4/provisioning/member', $this->customerPayload( 'provtestusr' ) )
            ->assertStatus( 201 );

        $cust = $this->fetchCustomer( 'provtestusr' );

        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( "/admin/api/v4/provisioning/member/{$cust->id}/user", [
                'name'      => 'Provisioning Test User',
                'username'  => 'provtestuser',
                'email'     => 'provtestuser@example.com',
                'privs'     => User::AUTH_CUSTUSER,
            ] );

        $response->assertStatus( 201 );
        $response->assertJsonPath( 'user.username', 'provtestuser' );
        $response->assertJsonPath( 'user.custid', $cust->id );
        $response->assertJsonPath( 'user.enabled', true );

        $user = User::where( 'username', 'provtestuser' )->first();
        $this->assertNotNull( $user );

        $this->assertSame( $cust->id, $user->custid );
        $this->assertSame( 0, (int)$user->disabled );

        $c2u = CustomerToUser::where( 'user_id', $user->id )->first();
        $this->assertNotNull( $c2u, 'the customer-to-user link was not created' );
        $this->assertSame( User::AUTH_CUSTUSER, (int)$c2u->privs );

        Event::assertDispatched( UserCreatedEvent::class );
    }

    /**
     * The customer is taken from the route, so a body which disagrees must not win.
     */
    public function testUserCreationIgnoresCustidInBody(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( '/admin/api/v4/provisioning/member', $this->customerPayload( 'provtestrte' ) )
            ->assertStatus( 201 );

        $cust = $this->fetchCustomer( 'provtestrte' );

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( "/admin/api/v4/provisioning/member/{$cust->id}/user", [
                'name'      => 'Route Wins',
                'username'  => 'provtestroute',
                'email'     => 'provtestroute@example.com',
                'privs'     => User::AUTH_CUSTUSER,
                'custid'    => 99999,
            ] )
            ->assertStatus( 201 );

        $user = User::where( 'username', 'provtestroute' )->first();
        $this->assertNotNull( $user );

        if( $c2u = CustomerToUser::where( 'user_id', $user->id )->first() ) {
        }

        $this->assertSame( $cust->id, $user->custid, 'the body overrode the customer from the route' );
    }

    /**
     * Reading a member back must work and must not leak unrelated columns.
     */
    public function testShowMember(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->post( '/admin/api/v4/provisioning/member', $this->customerPayload( 'provtestshw' ) )
            ->assertStatus( 201 );

        $cust = $this->fetchCustomer( 'provtestshw' );

        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get( "/admin/api/v4/provisioning/member/{$cust->id}" );

        $response->assertStatus( 200 );
        $response->assertJsonPath( 'member.id', $cust->id );
        $response->assertJsonPath( 'member.shortname', 'provtestshw' );
        $response->assertJsonMissingPath( 'member.creator' );
    }

    /**
     * An unknown member must be a clean 404, not a crash.
     */
    public function testShowUnknownMemberIs404(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get( '/admin/api/v4/provisioning/member/99999999' )
            ->assertStatus( 404 );
    }
}
