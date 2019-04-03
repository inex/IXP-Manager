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

use Entities\{Customer as CustomerEntity, CustomerToUser as CustomerToUserEntity, CustomerToUser, User as UserEntity};

use IXP\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
//        foreach( [ 'testuser1', 'testuser2' ] as $name ) {
//            $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => $name ] );
//
//            if( $u ) {
//                D2EM::remove( $u );
//                D2EM::flush();
//            }
//        }

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
                    ->type('username', 'travis' )
                    ->type('password', 'travisci' )
                    ->press('#login-btn' )
                    ->assertPathIs( '/admin' );

            $browser->visit( 'user/list' )
                    ->assertSee( 'hecustadmin' )
                    ->assertSee( 'heanet-custadmin@example.com' );

            /**
             *
             * Add existing user
             *
             */

            $browser->click( '#add-user' )
                    ->assertSee( 'Users / Add' )
                    ->assertSee( 'Email' )
                    ->type( '#email' , 'test-user1example.com' )
                    ->click( '.btn-primary' )
                    ->assertPathIs('/user/add-wizard' )
                    ->assertSee( 'The email must be a valid email address' )
                    ->type( '#email' , 'test-user1@example.com' )
                    ->click( '.btn-primary' );

            $browser->assertSee( 'Users / Add' )
                    ->assertInputValue( 'email' , 'test-user1@example.com' )
                    ->type( 'name', 'Test User 1' )
                    ->select( 'custid', 5 )
                    ->type( 'username', 'testuser1' )
                    ->select( 'privs', UserEntity::AUTH_CUSTUSER )
                    ->check( 'enabled' )
                    ->type( 'authorisedMobile', '12125551000' )
                    ->press('Add' )
                    ->waitForLocation('/user/list' )
                    ->assertSee( 'User added successfully. A welcome email' )
                    ->assertSee( 'Test User 1' )
                    ->assertSee( 'testuser1' )
                    ->assertSee( 'test-user1@example.com' );

            /** @var UserEntity $u */
            $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ "username" => 'testuser1'] );

            /** @var CustomerToUserEntity $c2u */
            $c2u = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u , "customer" => 5 ] );

            $this->assertInstanceOf( UserEntity::class   , $u );
            $this->assertInstanceOf( CustomerToUser::class   , $c2u );
            $this->assertEquals( 'Test User 1',             $u->getName() );
            $this->assertEquals( 'testuser1',               $u->getUsername() );
            $this->assertEquals( 'test-user1@example.com',  $u->getEmail() );
            $this->assertEquals( '12125551000',             $u->getAuthorisedMobile() );
            $this->assertEquals( UserEntity::AUTH_CUSTUSER, $u->getPrivs() );
            $this->assertEquals( 5,                         $u->getCustomer()->getId() );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( $u->getDisabled() );


            /**
             *
             * Edit User
             *
             */

            $browser->click( "#d2f-list-edit-" . $u->getId() )
                    ->assertInputValue('name', 'Test User 1')
                    ->assertInputValue('username', 'testuser1')
                    ->assertInputValue('email', 'test-user1@example.com')
                    ->assertChecked( 'enabled' )
                    ->assertInputValue('authorisedMobile', '12125551000')
                    ->assertSelected('custid_5', '5')
                    ->assertSelected('privs_5', UserEntity::AUTH_CUSTUSER );


            $browser->type( 'name', 'Test User' )
                ->type( 'username', 'testuser' )
                ->type( 'email', 'test-user@example.com' )
                ->uncheck( 'enabled' )
                ->type( 'authorisedMobile', '12125551011' )
                ->select('custid_5', '4')
                ->select('privs_5', UserEntity::AUTH_CUSTADMIN )
                ->press('Save Changes' )
                ->waitForLocation('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User' )
                ->assertSee( 'testuser' )
                ->assertSee( 'test-user@example.com' );


            D2EM::refresh($u);
            D2EM::refresh($c2u);


            $this->assertInstanceOf( UserEntity::class   , $u );
            $this->assertInstanceOf( CustomerToUser::class   , $c2u );
            $this->assertEquals( 'Test User',             $u->getName() );
            $this->assertEquals( 'testuser',               $u->getUsername() );
            $this->assertEquals( 'test-user@example.com',  $u->getEmail() );
            $this->assertEquals( '12125551011',             $u->getAuthorisedMobile() );
            $this->assertEquals( UserEntity::AUTH_CUSTADMIN, $c2u->getPrivs() );
            $this->assertEquals( 4,                         $c2u->getCustomer()->getId() );
            $this->assertTrue( $u->getDisabled() );

            /**
             *
             * Add existing user
             *
             */

            $browser->click( '#add-user' )
                    ->assertSee( 'Users / Add' )
                    ->assertSee( 'Email' )
                    ->type( '#email' , $u->getEmail() )
                    ->click( '.btn-primary' );

            $browser->assertSee( $u->getEmail() )
                    ->assertSee( $u->getUsername())
                    ->click( "#user-" . $u->getId() )
                    ->select(   "#privs", UserEntity::AUTH_CUSTADMIN )
                    ->select(   "#cust",    4 )
                    ->click( ".btn-primary" );

            $browser->assertSee( "The association User/Customer already exist")
                    ->select( "#cust" , 2 )
                    ->click( ".btn-primary" );

            $browser->assertPathIs( "/user/list")
                    ->assertSee( "User added");


            /** @var CustomerToUserEntity $c2u */
            $c2u2 = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u , "customer" => 2 ] );

            // test the values:
            $this->assertInstanceOf( CustomerToUserEntity::class   , $c2u2 );
            $this->assertEquals( 2                                 , $c2u2->getCustomer()->getId() );
            $this->assertEquals( $u->getId()                                , $c2u2->getUser()->getId() );
            $this->assertEquals( UserEntity::AUTH_CUSTADMIN        , $c2u2->getPrivs() );
            $this->assertNotNull( $c2u->getCreatedAt() );


            /**
             *
             * test that editing while not making any changes and saving changes nothing
             *
             */

            $browser->click( "#d2f-list-edit-" . $u->getId() )
                ->assertInputValue('name', 'Test User')
                ->assertInputValue('username', 'testuser')
                ->assertInputValue('email', 'test-user@example.com')
                ->assertNotChecked( 'enabled' )
                ->assertInputValue('authorisedMobile', '12125551011')
                ->assertSelected('custid_4', '4')
                ->assertSelected('privs_4', UserEntity::AUTH_CUSTADMIN )
                ->assertSelected('custid_2', '2')
                ->assertSelected('privs_2', UserEntity::AUTH_CUSTADMIN )
                ->press( 'Save Changes' )
                ->waitForLocation('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User' )
                ->assertSee( 'testuser' );

            // test the values:
            D2EM::refresh($u);
            D2EM::refresh($c2u);
            D2EM::refresh($c2u2);

            $this->assertEquals( 'Test User'            , $u->getName() );
            $this->assertEquals( 'testuser'             , $u->getUsername() );
            $this->assertEquals( 'test-user@example.com', $u->getEmail() );
            $this->assertEquals( '12125551011'          , $u->getAuthorisedMobile() );
            $this->assertTrue( $u->getDisabled() );
            $this->assertInstanceOf( CustomerToUserEntity::class   , $c2u );
            $this->assertEquals( 4                                 , $c2u->getCustomer()->getId() );
            $this->assertEquals( $u->getId()                                , $c2u->getUser()->getId() );
            $this->assertEquals( UserEntity::AUTH_CUSTADMIN        , $c2u->getPrivs() );
            $this->assertNotNull( $c2u->getCreatedAt() );
            $this->assertEquals( 2                                 , $c2u2->getCustomer()->getId() );
            $this->assertEquals( $u->getId()                                , $c2u2->getUser()->getId() );
            $this->assertEquals( UserEntity::AUTH_CUSTADMIN        , $c2u2->getPrivs() );
            $this->assertNotNull( $c2u2->getCreatedAt() );


            /**
             *
             * Edit User
             *
             */
            $browser->click( "#d2f-list-edit-" . $u->getId() )
                ->type( 'name', 'Test User 1' )
                ->type( 'username', 'testuser1' )
                ->type( 'email', 'test-user1@example.com' )
                ->check( 'enabled' )
                ->type( 'authorisedMobile', '12125551000' )
                ->select('custid_4', '1')
                ->select('privs_4', UserEntity::AUTH_CUSTUSER )
                ->select('custid_2', '5')
                ->select('privs_2', UserEntity::AUTH_CUSTUSER )
                ->press('Save Changes' )
                ->waitForLocation('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User 1' )
                ->assertSee( 'testuser1' )
                ->assertSee( 'test-user1@example.com' );

            // test the values:
            D2EM::refresh($u);
            D2EM::refresh($c2u);
            D2EM::refresh($c2u2);

            $this->assertEquals( 'Test User 1'            , $u->getName() );
            $this->assertEquals( 'testuser1'             , $u->getUsername() );
            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
            $this->assertEquals( '12125551000'          , $u->getAuthorisedMobile() );
            $this->assertFalse( $u->getDisabled() );
            $this->assertInstanceOf( CustomerToUserEntity::class   , $c2u );
            $this->assertEquals( 1                                 , $c2u->getCustomer()->getId() );
            $this->assertEquals( $u->getId()                                , $c2u->getUser()->getId() );
            $this->assertEquals( UserEntity::AUTH_CUSTUSER        , $c2u->getPrivs() );
            $this->assertNotNull( $c2u->getCreatedAt() );
            $this->assertEquals( 5                                 , $c2u2->getCustomer()->getId() );
            $this->assertEquals( $u->getId()                                , $c2u2->getUser()->getId() );
            $this->assertEquals( UserEntity::AUTH_CUSTUSER        , $c2u2->getPrivs() );
            $this->assertNotNull( $c2u2->getCreatedAt() );


            /**
             *
             * Delete customer/user link
             *
             */

            $browser->click( "#d2f-list-delete-" . $u->getId() )
                ->waitForText( "Delete User")
                ->assertSee( "See Customer links" )
                ->press('See Customer links' );



            $browser->assertPathIs( "/user/edit/" . $u->getId() )
                ->click( "#d2f-list-delete-" . $c2u2->getCustomer()->getId() )
                ->waitForText( "Delete User" )
                ->assertSee( "Do you really want to delete" )
                ->press('Delete' );


            $browser->assertPathIs( "/user/list" )
                ->assertSee( "The link customer/user ( " . $c2u2->getCustomer()->getName() . "/" . $c2u2->getUser()->getName() . " ) has been deleted" );



            // test the values:
            D2EM::refresh($u);
            D2EM::refresh($c2u);
            D2EM::refresh($c2u2);


            $this->assertEquals( null   , D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u , "customer" => 5 ] ) );
            $this->assertEquals( 1      , count( $u->getCustomers2User() ) );


            /**
             *
             * Delete user and all links
             *
             */

            $browser->click( "#d2f-list-delete-" . $u->getId() )
                ->waitForText( "Delete User")
                ->assertSee( "Are you sure you want to delete this user and its 1 customer links" )
                ->press('Delete' );

            $browser->assertPathIs( "/user/list" )
                ->assertSee( "User deleted" );

            $this->assertEquals( null   , D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u , "customer" => 1 ] ) );
            $this->assertEquals( null   , D2EM::getRepository( UserEntity::class )->findOneBy( [ "username" => 'testuser1'] ) );


            /**
             *
             *  Add User Via customer overview
             *
             */
            $this->browse(function (Browser $browser) {

                $browser->visit( '/customer/overview/5/users' )
                    ->assertSee( 'imcustadmin' )
                    ->assertSee( 'imagine-custadmin@example.com' )
                    ->press( '#users-add-btn' )
                    ->assertSee( 'Users / Add' )
                    ->assertSee( 'Email' )
                    ->type( 'email',    'test-user2@example.com' )
                    ->click( '.btn-primary' );


            $browser->assertSee( 'Users / Add' )
                    ->assertInputValue( 'email', 'test-user2@example.com' )
                    ->assertSelected( 'custid', 5 )
                    ->type( 'name', 'Test User 2' )
                    ->type( 'username', 'testuser2' )
                    ->select( 'privs', UserEntity::AUTH_CUSTUSER )
                    ->check( 'enabled' )
                    ->type( 'authorisedMobile', '12125551000' )
                    ->press('Add' )
                    ->assertPathIs('/customer/overview/5/users' )
                    ->assertSee( 'User added successfully. A welcome email' )
                    ->assertSee( 'Test User 2' )
                    ->assertSee( 'testuser2' )
                    ->assertSee( 'test-user2@example.com' );

            /** @var UserEntity $u2 */
            $u2 = D2EM::getRepository( UserEntity::class )->findOneBy( [ "username" => 'testuser2'] );

            /** @var CustomerToUserEntity $c2u3 */
            $c2u3 = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u2 , "customer" => 5 ] );

            $this->assertInstanceOf( UserEntity::class   , $u2 );

            $this->assertEquals( 'Test User 2'            , $u2->getName() );
            $this->assertEquals( 'testuser2'             , $u2->getUsername() );
            $this->assertEquals( 'test-user2@example.com', $u2->getEmail() );
            $this->assertEquals( '12125551000'          , $u2->getAuthorisedMobile() );
            $this->assertFalse( $u2->getDisabled() );
            $this->assertInstanceOf( CustomerToUserEntity::class   , $c2u3 );
            $this->assertEquals( 5                                 , $c2u3->getCustomer()->getId() );
            $this->assertEquals( $u2->getId()                                , $c2u3->getUser()->getId() );
            $this->assertEquals( UserEntity::AUTH_CUSTUSER        , $c2u3->getPrivs() );
            $this->assertNotNull( $c2u3->getCreatedAt() );



            /**
             *
             *  Delete User Via customer overview
             *
             */
            $browser->press( '#usr-list-delete-' . $u2->getId() )
                ->waitForText( 'Do you really want to delete this user?' )
                ->press( 'Delete' )
                ->assertPathIs('/customer/overview/5/users' )
                ->assertSee( 'User deleted' )
                ->assertDontSee( 'Test User 1' )
                ->assertDontSee( 'testuser1' )
                ->assertDontSee( 'test-user1@example.com' );

            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser2' ] ) );
            $this->assertNull( D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u2 , "customer" => 5 ] ) );

            });
        });

    }







//    /**
//     * A Dusk test example.
//     *
//     * @return void
//     * @throws \Throwable
//     */
//    public function testAddCustAdmin()
//    {
//
//        $this->browse(function (Browser $browser) {
//            $browser->resize( 1600,1200 )
//                ->visit('/logout')
//                ->visit('/login')
//                ->type( 'username', 'imcustadmin' )
//                ->type( 'password', 'travisci' )
//                ->press( '#login-btn' )
//                ->visit( '/user/list' )
//                ->assertSee( 'Your Users' )
//                ->assertSee( 'imcustuser' )
//                ->assertSee( 'imagine-custuser@example.com' );
//
//            $browser->visit( '/user/add' )
//                ->assertSee( 'Add User' )
//                ->assertSee( 'Name' )
//                ->assertSee( 'Username' );
//
//
//            // 1. test add :
//            $browser->type( 'name', 'Test User 1' )
//                ->type( 'username', 'testuser1' )
//                ->type( 'email',    'test-user1@example.com' )
//                ->select( 'privs', 1 )
//                ->check( 'enabled' )
//                ->type( 'authorisedMobile', '12125551000' )
//                ->press('Add' )
//                ->assertPathIs('/user/list' )
//                ->assertSee( 'User added successfully. A welcome email' )
//                ->assertSee( 'Test User 1' )
//                ->assertSee( 'testuser1' )
//                ->assertSee( 'test-user1@example.com' );
//
//            // get the user:
//            /** @var UserEntity $u */
//            $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] );
//
//            // test the values:
//            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
//            $this->assertEquals( 'Test User 1',            $u->getName() );
//            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
//            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
//            $this->assertTrue( $u->isCustUser() );
//            $this->assertFalse( $u->getDisabled() );
//
//            // test that editing while not making any changes and saving changes nothing
//            $browser->visit( '/user/edit/' . $u->getId() )
//                ->assertPathIs('/user/edit/' . $u->getId() )
//                ->press( 'Save Changes' )
//                ->assertPathIs('/user/list' )
//                ->assertSee( 'User edited' )
//                ->assertSee( 'Test User 1' )
//                ->assertSee( 'testuser1' )
//                ->assertSee( 'test-user1@example.com' );
//
//            // test the values:
//            D2EM::refresh($u);
//            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
//            $this->assertEquals( 'Test User 1',            $u->getName() );
//            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
//            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
//            $this->assertTrue( $u->isCustUser() );
//            $this->assertFalse( $u->getDisabled() );
//
//
//            // now test that editing while making changes works
//            $browser->visit( '/user/edit/' . $u->getId() )
//                ->assertPathIs('/user/edit/' . $u->getId() )
//                ->type( 'name', 'Test User 2' )
//                ->type( 'username', 'testuser2' )
//                ->type( 'email',    'test-user2@example.com' )
//                ->select( 'privs', 2 )
//                ->uncheck( 'enabled' )
//                ->type( 'authorisedMobile', '12125551001' )
//                ->press( 'Save Changes' )
//                ->assertPathIs('/user/list' )
//                ->assertSee( 'User edited' )
//                ->assertSee( 'Test User 2' )
//                ->assertSee( 'testuser2' )
//                ->assertSee( 'test-user2@example.com' );
//
//            // test the values:
//            D2EM::refresh($u);
//            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
//            $this->assertEquals( 'Test User 2',            $u->getName() );
//            $this->assertEquals( 'test-user2@example.com', $u->getEmail() );
//            $this->assertEquals( '12125551001',            $u->getAuthorisedMobile() );
//            $this->assertTrue( $u->isCustAdmin() );
//            $this->assertTrue( $u->getDisabled() );
//
//
//            // test that editing while not making any changes and saving changes nothing
//            // (this is a retest for, e.g. unchecked checkboxes)
//            $browser->visit( '/user/edit/' . $u->getId() )
//                ->assertPathIs('/user/edit/' . $u->getId() )
//                ->press( 'Save Changes' )
//                ->assertPathIs('/user/list' )
//                ->assertSee( 'User edited' );
//
//            // test the values:
//            D2EM::refresh($u);
//            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
//            $this->assertEquals( 'Test User 2',            $u->getName() );
//            $this->assertEquals( 'test-user2@example.com', $u->getEmail() );
//            $this->assertEquals( '12125551001',            $u->getAuthorisedMobile() );
//            $this->assertTrue( $u->isCustAdmin() );
//            $this->assertTrue( $u->getDisabled() );
//
//            // delete this user
//            $browser->press( '#d2f-list-delete-' . $u->getId() )
//                ->waitForText( 'Do you really want to delete this user?' )
//                ->press( 'Delete' )
//                ->assertPathIs('/user/list' )
//                ->assertSee( 'User deleted' )
//                ->assertDontSee( 'Test User 1' )
//                ->assertDontSee( 'testuser1' )
//                ->assertDontSee( 'test-user1@example.com' )
//                ->assertDontSee( 'Test User 2' )
//                ->assertDontSee( 'testuser2' )
//                ->assertDontSee( 'test-user2@example.com' );
//
//            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] ) );
//            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser2' ] ) );
//
//
//            $browser->visit('/logout')
//                ->assertPathIs( '/login' );
//
//        });
//
//
//    }
}
