<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Tests\Console\User;

use Illuminate\Support\Facades\Event;
use IXP\Models\CustomerToUser;
use IXP\Models\User;
use Tests\TestCase;


class CreateTest extends TestCase
{
    private function cleanup() {
        CustomerToUser::query()
            ->join( 'user', 'user.id', '=', 'customer_to_users.user_id' )
            ->where( 'user.username', 'testuser' )
            ->delete();

        User::query()->whereUsername( 'testuser' )->delete();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->cleanup();
    }

    public function tearDown(): void
    {
        $this->cleanup();
        parent::tearDown();
    }

    public function testOkProvidedAllArguments_ok()
    {
        Event::fake();

        $customer = $this->getCustAdminUser()->customer;

        $this->artisan( 'user:create', [
            '--email'    => 'administrator@ixp.local',
            '--name'     => 'IXP Operator',
            '--username' => 'testuser',
            '--mobile'   => '+353809999999',
            '--custid'   => $customer->id,
            '--priv'     => User::AUTH_CUSTADMIN,
            '--password' => 'securepassword',
        ] )
            ->expectsConfirmation( 'Do you want to send the welcome email to the user?', false )
            ->expectsOutput( 'User created.' )
            ->assertOk();

        $user = User::whereUsername( 'testuser' )->firstOrFail();
        $this->assertEquals( 'artisan', $user->creator );
        $this->assertTrue(password_verify( 'securepassword', $user->password ));
        $this->assertEquals( 'IXP Operator', $user->name );
        $this->assertEquals( '+353809999999', $user->authorisedMobile );
        $this->assertEquals( 'administrator@ixp.local', $user->email );
        $this->assertEquals( '0', $user->disabled );
        $this->assertEquals( User::AUTH_CUSTADMIN, $user->privs() );
        $this->assertEquals( $customer->id, $user->custid );

        $c2u = $user->customerToUser->first();
        $this->assertEquals( $customer->id, $c2u->customer_id );
        $this->assertEquals( $user->id, $c2u->user_id );
        $this->assertEquals( User::AUTH_CUSTADMIN, $c2u->privs );
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            [ 'created_by' => [ 'type' => 'artisan' , 'user_id' => $user->id ] ],
            $c2u->extra_attributes, [] );

        Event::assertNotDispatched( \IXP\Events\User\UserCreated::class );
    }

    public function testOkProvidedAllArguments_UserCreatedEvent()
    {
        Event::fake();

        $customer = $this->getCustAdminUser()->customer;

        $this->artisan( 'user:create', [
            '--email'    => 'adminstrator@ixp.local',
            '--name'     => 'IXP Operator',
            '--username' => 'testuser',
            '--mobile'   => '+353809999999',
            '--custid'   => $customer->id,
            '--priv'     => User::AUTH_CUSTADMIN,
            '--password' => 'securepassword',
        ] )
            ->expectsConfirmation( 'Do you want to send the welcome email to the user?', 'yes' )
            ->assertOk();

        Event::assertDispatched(\IXP\Events\User\UserCreated::class);
    }

    public function testOkProvidedAllArguments_UserCreatedEvent_Opt()
    {
        Event::fake();

        $customer = $this->getCustAdminUser()->customer;

        $this->artisan( 'user:create', [
            '--email'    => 'adminstrator@ixp.local',
            '--name'     => 'IXP Operator',
            '--username' => 'testuser',
            '--mobile'   => '+353809999999',
            '--custid'   => $customer->id,
            '--priv'     => User::AUTH_CUSTADMIN,
            '--password' => 'securepassword',
            '--send-welcome-email' => '1'
        ] )
            ->assertOk();

        Event::assertDispatched(\IXP\Events\User\UserCreated::class);
    }

    public function testOkPrompt()
    {
        Event::fake();

        $customer = $this->getCustAdminUser()->customer;

        $this
            ->artisan( 'user:create' )
            ->expectsQuestion( 'Enter email', 'administrator@ixp.local' )
            ->expectsQuestion( 'Enter name', 'IXP Operator' )
            ->expectsQuestion( 'Enter username', 'testuser' )
            ->expectsQuestion( 'Enter mobile', '+353809999999' )
            ->expectsQuestion( 'Search Customer by ASN or Name', $customer->id )
            ->expectsQuestion( 'Enter custid', $customer->id )
            ->expectsOutput( '[ 1 => member, read only; 2 => member, read/write; 3 => super admin (careful!!)' )
            ->expectsQuestion( 'Enter priv', User::AUTH_CUSTADMIN )
            ->expectsQuestion( 'Enter password', 'securepassword' )
            ->expectsConfirmation( 'Do you want to send the welcome email to the user?', false )
            ->expectsOutput( 'User created.' )
            ->assertOk();

        $user = User::whereUsername( 'testuser' )->firstOrFail();
        $this->assertEquals( 'artisan', $user->creator );
        $this->assertTrue( password_verify( 'securepassword', $user->password ) );
        $this->assertEquals( 'IXP Operator', $user->name );
        $this->assertEquals( '+353809999999', $user->authorisedMobile );
        $this->assertEquals( 'administrator@ixp.local', $user->email );
        $this->assertEquals( '0', $user->disabled );
        $this->assertEquals( User::AUTH_CUSTADMIN, $user->privs() );
        $this->assertEquals( $customer->id, $user->custid );

        $c2u = $user->customerToUser->first();
        $this->assertEquals( $customer->id, $c2u->customer_id );
        $this->assertEquals( $user->id, $c2u->user_id );
        $this->assertEquals( User::AUTH_CUSTADMIN, $c2u->privs );
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            [ 'created_by' => [ 'type' => 'artisan' , 'user_id' => $user->id ] ],
            $c2u->extra_attributes, [] );

        Event::assertNotDispatched( \IXP\Events\User\UserCreated::class );
    }


    public function testOkPrompt_Recurring()
    {
        Event::fake();

        $customer = $this->getCustAdminUser()->customer;

        $this
            ->artisan( 'user:create' )

            ->expectsQuestion( 'Enter email', '' )                 // rule: required
            ->expectsOutput( 'The email field is required.' )
            ->expectsQuestion( 'Enter email', 'test' )             // rule: email
            ->expectsOutput( 'The email must be a valid email address.' )
            ->expectsQuestion( 'Enter email', 'administrator@ixp.local' )

            ->expectsQuestion( 'Enter name', '' )                  // rule: required
            ->expectsOutput( 'The name field is required.' )
            ->expectsQuestion( 'Enter name', 'IXP Operator' )

            ->expectsQuestion( 'Enter username', '' )              // rule: required
            ->expectsOutput( 'The username field is required.' )
            ->expectsQuestion( 'Enter username', 'testuser' )

            ->expectsQuestion( 'Enter mobile', '+353809999999' )

            ->expectsQuestion( 'Search Customer by ASN or Name', $customer->id )
            ->expectsQuestion( 'Enter custid', '9999999' )         // rule: exists:cust,id
            ->expectsOutput( 'The selected custid is invalid.' )
            ->expectsQuestion( 'Enter custid', $customer->id )

            ->expectsOutput( '[ 1 => member, read only; 2 => member, read/write; 3 => super admin (careful!!)' )
            ->expectsQuestion( 'Enter priv', '' )                  // rule: required
            ->expectsOutput( 'The priv field is required.' )
            ->expectsQuestion( 'Enter priv', 'unknown' )           // rule: integer
            ->expectsOutput( 'The priv must be an integer.' )
            ->expectsQuestion( 'Enter priv', '10' )                // in: ...
            ->expectsOutput( 'The selected priv is invalid.' )
            ->expectsQuestion( 'Enter priv', User::AUTH_CUSTADMIN)

            ->expectsQuestion( 'Enter password', '' )              // rule: required
            ->expectsOutput( 'The password field is required.' )
            ->expectsQuestion( 'Enter password', '12' )            // rule: min:8
            ->expectsOutput( 'The password must be at least 8 characters.' )
            ->expectsQuestion( 'Enter password', 'securepassword' )

            ->expectsConfirmation( 'Do you want to send the welcome email to the user?', false)
            ->expectsOutput( 'User created.' )
            ->assertOk();

        $user = User::whereUsername( 'testuser' )->firstOrFail();
        $this->assertEquals( 'artisan', $user->creator);
        $this->assertTrue(password_verify( 'securepassword', $user->password));
        $this->assertEquals( 'IXP Operator', $user->name);
        $this->assertEquals( '+353809999999', $user->authorisedMobile);
        $this->assertEquals( 'administrator@ixp.local', $user->email);
        $this->assertEquals( '0', $user->disabled);
        $this->assertEquals(User::AUTH_CUSTADMIN, $user->privs() );
        $this->assertEquals( $customer->id, $user->custid);

        $c2u = $user->customerToUser->first();
        $this->assertEquals( $customer->id, $c2u->customer_id);
        $this->assertEquals( $user->id, $c2u->user_id);
        $this->assertEquals(User::AUTH_CUSTADMIN, $c2u->privs);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            [ 'created_by' => [ 'type' => 'artisan' , 'user_id' => $user->id ] ],
            $c2u->extra_attributes, []);

        Event::assertNotDispatched(\IXP\Events\User\UserCreated::class);
    }
}