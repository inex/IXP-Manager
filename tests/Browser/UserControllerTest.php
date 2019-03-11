<?php

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Tests\Browser;

use D2EM;

use Entities\User as UserEntity;

use IXP\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
        foreach( [ 'testuser1', 'testuser2' ] as $name ) {
            $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => $name ] );

            if( $u ) {
                D2EM::remove( $u );
                D2EM::flush();
            }
        }

        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testAdd()
    {

        $this->browse(function (Browser $browser) {
            $browser->resize( 1600,1200 )
                ->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( 'Login' )
                ->assertPathIs( '/admin' );

            $browser->visit( '/user/list' )
                ->assertSee( 'hecustadmin' )
                ->assertSee( 'heanet-custadmin@example.com' );

            $browser->visit( '/user/add' )
                ->assertSee( 'Add User' )
                ->assertSee( 'Username' )
                ->assertSee( 'Password' );


            // 1. test add :
            $browser->type( 'name', 'Test User 1' )
                ->select( 'custid', 5 )
                ->type( 'username', 'testuser1' )
                ->type( 'email',    'test-user1example.com' )
                ->select( 'privs', 1 )
                ->check( 'enabled' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press('Add' )
                ->assertPathIs('/user/add' )
                ->assertSee( 'The email must be a valid email address' );

            $browser->assertPathIs('/user/add' )
                ->type( 'email',    'test-user1@example.com' )
                ->press('Add' )
                ->waitForLocation('/user/list' )
                ->assertSee( 'User added successfully. A welcome email' )
                ->assertSee( 'Test User 1' )
                ->assertSee( 'testuser1' )
                ->assertSee( 'test-user1@example.com' );

            // get the user:
            /** @var UserEntity $u */
            $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] );

            // test the values:
            $this->assertEquals( 'Test User 1',            $u->getName() );
            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( $u->getDisabled() );

            // test that editing while not making any changes and saving changes nothing

            $browser->visit( '/user/edit/' . $u->getId() )
                ->assertPathIs('/user/edit/' . $u->getId() )
                ->press( 'Save Changes' )
                ->waitForLocation('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User 1' )
                ->assertSee( 'testuser1' )
                ->assertSee( 'test-user1@example.com' );

            // test the values:
            D2EM::refresh($u);
            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 1',            $u->getName() );
            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( $u->getDisabled() );



            // now test that editing while making changes works
            $browser->visit( '/user/edit/' . $u->getId() )
                ->assertPathIs('/user/edit/' . $u->getId() )
                ->type( 'name', 'Test User 2' )
                ->type( 'username', 'testuser2' )
                ->type( 'email',    'test-user2@example.com' )
                ->select( 'custid', 2 )
                ->select( 'privs', 2 )
                ->uncheck( 'enabled' )
                ->type( 'authorisedMobile', '12125551001' )
                ->press( 'Save Changes' )
                ->waitForLocation('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User 2' )
                ->assertSee( 'testuser2' )
                ->assertSee( 'test-user2@example.com' );

            // test the values:
            D2EM::refresh($u);
            $this->assertEquals( 2,                        $u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 2',            $u->getName() );
            $this->assertEquals( 'test-user2@example.com', $u->getEmail() );
            $this->assertEquals( '12125551001',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustAdmin() );
            $this->assertTrue( $u->getDisabled() );


            // test that editing while not making any changes and saving changes nothing
            // (this is a retest for, e.g. unchecked checkboxes)
            $browser->visit( '/user/edit/' . $u->getId() )
                ->assertPathIs('/user/edit/' . $u->getId() )
                ->press( 'Save Changes' )
                ->waitForLocation('/user/list' )
                ->assertSee( 'User edited' );

            // test the values:
            D2EM::refresh($u);
            $this->assertEquals( 2,                        $u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 2',            $u->getName() );
            $this->assertEquals( 'test-user2@example.com', $u->getEmail() );
            $this->assertEquals( '12125551001',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustAdmin() );
            $this->assertTrue( $u->getDisabled() );

            // delete this user
            $browser->press( '#d2f-list-delete-' . $u->getId() )
                ->waitForText( 'Do you really want to delete this user?' )
                ->press( 'Delete' )
                ->waitForLocation('/user/list' )
                ->assertSee( 'User deleted' )
                ->assertDontSee( 'Test User 1' )
                ->assertDontSee( 'testuser1' )
                ->assertDontSee( 'test-user1@example.com' )
                ->assertDontSee( 'Test User 2' )
                ->assertDontSee( 'testuser2' )
                ->assertDontSee( 'test-user2@example.com' );

            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] ) );
            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser2' ] ) );

        });


        $this->browse(function (Browser $browser) {

            $browser->visit( '/customer/overview/5/users' )
                ->assertSee( 'imcustadmin' )
                ->assertSee( 'imagine-custadmin@example.com' )
                ->press( '#users-add-btn' )
                ->assertSee( 'Add User' )
                ->assertSee( 'Imagine' )
                ->assertSelected( 'custid', 5 );


            // 1. test add :
            $browser->type( 'name', 'Test User 1' )
                ->type( 'username', 'testuser1' )
                ->type( 'email',    'test-user1@example.com' )
                ->select( 'privs', 1 )
                ->check( 'enabled' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press('Add' )
                ->assertPathIs('/customer/overview/5/users' )
                ->assertSee( 'User added successfully. A welcome email' )
                ->assertSee( 'Test User 1' )
                ->assertSee( 'test-user1@example.com' );

            //            // get the user:
            /** @var UserEntity $u */
            $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] );

            // delete this user
            $browser->press( '#usr-list-delete-' . $u->getId() )
                ->waitForText( 'Do you really want to delete this user?' )
                ->press( 'Delete' )
                ->assertPathIs('/customer/overview/5/users' )
                ->assertSee( 'User deleted' )
                ->assertDontSee( 'Test User 1' )
                ->assertDontSee( 'testuser1' )
                ->assertDontSee( 'test-user1@example.com' );

            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] ) );
            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser2' ] ) );

            $browser->visit('/logout')
                ->assertPathIs( '/login' );
        });

    }







    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function testAddCustAdmin()
    {

        $this->browse(function (Browser $browser) {
            $browser->resize( 1600,1200 )
                ->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->press( 'Login' )
                ->assertPathIs( '/contact/list' )
                ->visit( '/user/list' )
                ->assertSee( 'Your Users' )
                ->assertSee( 'imcustuser' )
                ->assertSee( 'imagine-custuser@example.com' );

            $browser->visit( '/user/add' )
                ->assertSee( 'Add User' )
                ->assertSee( 'Name' )
                ->assertSee( 'Username' );


            // 1. test add :
            $browser->type( 'name', 'Test User 1' )
                ->type( 'username', 'testuser1' )
                ->type( 'email',    'test-user1@example.com' )
                ->select( 'privs', 1 )
                ->check( 'enabled' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press('Add' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User added successfully. A welcome email' )
                ->assertSee( 'Test User 1' )
                ->assertSee( 'testuser1' )
                ->assertSee( 'test-user1@example.com' );

            // get the user:
            /** @var UserEntity $u */
            $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] );

            // test the values:
            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 1',            $u->getName() );
            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( $u->getDisabled() );

            // test that editing while not making any changes and saving changes nothing
            $browser->visit( '/user/edit/' . $u->getId() )
                ->assertPathIs('/user/edit/' . $u->getId() )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User 1' )
                ->assertSee( 'testuser1' )
                ->assertSee( 'test-user1@example.com' );

            // test the values:
            D2EM::refresh($u);
            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 1',            $u->getName() );
            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( $u->getDisabled() );


            // now test that editing while making changes works
            $browser->visit( '/user/edit/' . $u->getId() )
                ->assertPathIs('/user/edit/' . $u->getId() )
                ->type( 'name', 'Test User 2' )
                ->type( 'username', 'testuser2' )
                ->type( 'email',    'test-user2@example.com' )
                ->select( 'privs', 2 )
                ->uncheck( 'enabled' )
                ->type( 'authorisedMobile', '12125551001' )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User 2' )
                ->assertSee( 'testuser2' )
                ->assertSee( 'test-user2@example.com' );

            // test the values:
            D2EM::refresh($u);
            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 2',            $u->getName() );
            $this->assertEquals( 'test-user2@example.com', $u->getEmail() );
            $this->assertEquals( '12125551001',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustAdmin() );
            $this->assertTrue( $u->getDisabled() );


            // test that editing while not making any changes and saving changes nothing
            // (this is a retest for, e.g. unchecked checkboxes)
            $browser->visit( '/user/edit/' . $u->getId() )
                ->assertPathIs('/user/edit/' . $u->getId() )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User edited' );

            // test the values:
            D2EM::refresh($u);
            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 2',            $u->getName() );
            $this->assertEquals( 'test-user2@example.com', $u->getEmail() );
            $this->assertEquals( '12125551001',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustAdmin() );
            $this->assertTrue( $u->getDisabled() );

            // delete this user
            $browser->press( '#d2f-list-delete-' . $u->getId() )
                ->waitForText( 'Do you really want to delete this user?' )
                ->press( 'Delete' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User deleted' )
                ->assertDontSee( 'Test User 1' )
                ->assertDontSee( 'testuser1' )
                ->assertDontSee( 'test-user1@example.com' )
                ->assertDontSee( 'Test User 2' )
                ->assertDontSee( 'testuser2' )
                ->assertDontSee( 'test-user2@example.com' );

            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] ) );
            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser2' ] ) );


            $browser->visit('/logout')
                ->assertPathIs( '/login' );

        });


    }
}
