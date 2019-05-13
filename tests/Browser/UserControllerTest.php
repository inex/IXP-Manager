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

use Entities\{
    Customer as CustomerEntity,
    CustomerToUser as CustomerToUserEntity,
    User as UserEntity
};

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class UserControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
        foreach( [ 'test13@example.com' ] as $email ) {
            $u = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => $email ] );

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
            $this->assertInstanceOf( CustomerToUserEntity::class   , $c2u );
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
                    ->assertSee('Imagine' )
                    ->assertSelected('privs_5', UserEntity::AUTH_CUSTUSER );


            $browser->type( 'name', 'Test User' )
                ->type( 'username', 'testuser' )
                ->type( 'email', 'test-user@example.com' )
                ->uncheck( 'enabled' )
                ->type( 'authorisedMobile', '12125551011' )
                ->select('privs_5', UserEntity::AUTH_CUSTADMIN )
                ->press('Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User' )
                ->assertSee( 'testuser' )
                ->assertSee( 'test-user@example.com' );


            D2EM::refresh($u);
            D2EM::refresh($c2u);


            $this->assertInstanceOf( UserEntity::class   , $u );
            $this->assertInstanceOf( CustomerToUserEntity::class   , $c2u );
            $this->assertEquals( 'Test User',             $u->getName() );
            $this->assertEquals( 'testuser',               $u->getUsername() );
            $this->assertEquals( 'test-user@example.com',  $u->getEmail() );
            $this->assertEquals( '12125551011',             $u->getAuthorisedMobile() );
            $this->assertEquals( UserEntity::AUTH_CUSTADMIN, $c2u->getPrivs() );
            $this->assertEquals( 5,                         $c2u->getCustomer()->getId() );
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
                    ->select(   "#cust",    5 )
                    ->click( ".btn-primary" );

            sleep( 5 );

            $browser->assertSee( "This user is already associated with Imagine")
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
                ->assertSee('AS112' )
                ->assertSelected('privs_5', UserEntity::AUTH_CUSTADMIN )
                ->assertSee('Imagine' )
                ->assertSelected('privs_2', UserEntity::AUTH_CUSTADMIN )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
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
            $this->assertEquals( 5                                 , $c2u->getCustomer()->getId() );
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
                ->select('privs_5', UserEntity::AUTH_CUSTUSER )
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
            $this->assertEquals( 5                                 , $c2u->getCustomer()->getId() );
            $this->assertEquals( $u->getId()                                , $c2u->getUser()->getId() );
            $this->assertEquals( UserEntity::AUTH_CUSTUSER        , $c2u->getPrivs() );
            $this->assertNotNull( $c2u->getCreatedAt() );
            $this->assertEquals( 2                                 , $c2u2->getCustomer()->getId() );
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
                ->waitForText( 'Imagine' )
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


            $this->assertEquals( null   , D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u , "customer" => 2 ] ) );
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


                $browser->visit( '/customer/overview/5/users' )
                    ->pause(2000)
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
                    ->pause( 5000 )
                    ->assertPathIs('/customer/overview/5/users' )
                    ->assertSee( 'User deleted' )
                    ->assertDontSee( 'Test User 1' )
                    ->assertDontSee( 'testuser1' )
                    ->assertDontSee( 'test-user1@example.com' );

                $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser2' ] ) );
                $this->assertNull( D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u2 , "customer" => 5 ] ) );




            /**
             *
             *  Add Customer to the loggued user and then test the switch customer function, then delete new the customer
             *
             */

            /** @var  UserEntity $u3 */
            $u3 = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'travis' ] );

            $this->assertEquals( UserEntity::AUTH_SUPERUSER , $u3->getPrivs() );
            $this->assertEquals( "1" , $u3->getCustomer()->getId() );

            $browser->visit( 'user/list' )
                ->click( '#add-user' )
                ->assertSee( 'Users / Add' )
                ->assertSee( 'Email' )
                ->type( '#email' , $u3->getEmail() )
                ->click( '.btn-primary' );

            $browser->assertSee( $u3->getEmail() )
                ->assertSee( $u3->getUsername())
                ->click( "#user-" . $u3->getId() )
                ->select(   "#privs", UserEntity::AUTH_CUSTADMIN )
                ->select(   "#cust",    5 )
                ->click( ".btn-primary" );

            $browser->assertPathIs( "/user/list")
                ->assertSee( "User added");

            $browser->click( "#my-account" )
                ->assertSeeIn( "#my-account-dd .dropdown-header", "Switch to:" )
                ->assertSeeIn( "#my-account-dd", "INEX" )
                ->assertSeeIn( "#my-account-dd", "Imagine" );

            $browser->click( "#switch-cust-5" )
                ->assertPathIs( "/dashboard" )
                ->assertSee( "You are now logged in for Imagine." );

            // test the values:
            D2EM::refresh($u3);

            $this->assertEquals( UserEntity::AUTH_CUSTADMIN , $u3->getPrivs() );
            $this->assertEquals( "5" , $u3->getCustomer()->getId() );


            $browser->click( "#my-account" )
                ->assertSeeIn( "#my-account-dd .dropdown-header", "Switch to:" )
                ->assertSeeIn( "#my-account-dd", "INEX" )
                ->assertSeeIn( "#my-account-dd", "Imagine" );

            $browser->click( "#switch-cust-1" )
                ->assertPathIs( "/admin" )
                ->assertSee( "You are now logged in for INEX." );


            $browser->visit( 'user/list' )
                ->click( "#d2f-list-delete-" . $u3->getId() )
                ->waitForText( "Delete User")
                ->assertSee( "See Customer links" )
                ->press('See Customer links' );



            $browser->assertPathIs( "/user/edit/" . $u3->getId() )
                ->waitForText( 'Imagine' )
                ->click( "#d2f-list-delete-5" )
                ->waitForText( "Delete User" )
                ->assertSee( "Do you really want to delete" )
                ->press('Delete' );

            $browser->assertPathIs( "/user/list" )
                ->assertSee( "The link customer/user ( Imagine/ ) has been deleted" );



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
                ->press( '#login-btn' );



            $browser->visit( '/user/list' )
                ->assertSee( 'Your Users' )
                ->assertSee( 'imcustuser' )
                ->assertSee( 'imagine-custuser@example.com' );


            /**
             *
             *  Adding a new user
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


            // 1. test add :
            $browser->assertInputValue( "email", 'test-user1@example.com' )
                ->type( 'name', 'Test User 1' )
                ->type( 'username', 'testuser1' )
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
            $this->assertInstanceOf( UserEntity::class   , $u );
            $this->assertEquals( 5,                        $u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 1',            $u->getName() );
            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( $u->getDisabled() );

            // test that editing while not making any changes and saving changes nothing
            $browser->click( '#d2f-list-edit-' . $u->getId() )
                ->assertPathIs('/user/edit/' . $u->getId() )
                ->assertInputValue('name', 'Test User 1')
                ->assertInputValue('username', 'testuser1')
                ->assertInputValue('email', 'test-user1@example.com')
                ->assertChecked( 'enabled' )
                ->assertInputValue('authorisedMobile', '12125551000')
                ->assertSelected('privs', UserEntity::AUTH_CUSTUSER )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test User 1' )
                ->assertSee( 'testuser1' )
                ->assertSee( 'test-user1@example.com' );

            // test the values:
            D2EM::refresh($u);
            $c2u = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u , "customer" => 5 ] );

            $this->assertInstanceOf( CustomerToUserEntity::class   , $c2u );
            $this->assertEquals( 5,                        $c2u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 1',            $u->getName() );
            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( $u->getDisabled() );


            /**
             *
             *  Editing the new user
             *
             */
            $browser->click( '#d2f-list-edit-' . $u->getId() )
                ->assertPathIs('/user/edit/' . $u->getId() )
                ->select('privs', UserEntity::AUTH_CUSTADMIN )
                ->assertDisabled( "name" )
                ->assertDisabled( "username" )
                ->assertDisabled( "email" )
                ->assertDisabled( "authorisedMobile" )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User edited' );

            // test the values:
            D2EM::refresh($u);
            D2EM::refresh($c2u);

            $this->assertEquals( 5,                        $c2u->getCustomer()->getId() );
            $this->assertEquals( 'Test User 1',            $u->getName() );
            $this->assertEquals( 'test-user1@example.com', $u->getEmail() );
            $this->assertEquals( '12125551000',            $u->getAuthorisedMobile() );
            $this->assertTrue( $u->isCustAdmin() );
            $this->assertFalse( $u->getDisabled() );

            // delete this user
            $browser->press( '#d2f-list-delete-' . $u->getId() )
                ->waitForText( 'Do you really want to delete this Customer from this User' )
                ->press( 'Delete' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User deleted' )
                ->assertDontSee( 'Test User 1' )
                ->assertDontSee( 'testuser1' )
                ->assertDontSee( 'test-user1@example.com' );

            $this->assertNull( D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'testuser1' ] ) );
            $this->assertNull( D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $u , "customer" => 5 ] ) );


            /**
             *
             *  Editing Loggued user
             *
             */
            /** @var UserEntity $u */
            $u2 = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'imcustadmin' ] );

            $browser->click( '#d2f-list-edit-' . $u2->getId() )
                ->assertPathIs('/user/edit/' . $u2->getId() )
                ->assertInputValue('name', 'Test Test')
                ->assertInputValue('username', 'imcustadmin')
                ->assertInputValue('email', 'imagine-custadmin@example.com')
                ->assertChecked( 'enabled' )
                ->assertSelected('privs', UserEntity::AUTH_CUSTADMIN )
                ->assertDisabled( 'username' )
                ->assertDisabled( 'email' )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test Test' )
                ->assertSee( 'imcustadmin' )
                ->assertSee( 'imagine-custadmin@example.com' );

            // test the values:
            D2EM::refresh($u2);

            $this->assertEquals( 2                                  , $u2->getId() );
            $this->assertEquals( 'Test Test'                        , $u2->getName() );
            $this->assertEquals( 'imcustadmin'                      , $u2->getUserName() );
            $this->assertEquals( 'imagine-custadmin@example.com'    , $u2->getEmail() );
            $this->assertEquals( ''                                 , $u2->getAuthorisedMobile() );
            $this->assertTrue(  $u2->isCustAdmin() );
            $this->assertFalse( $u2->getDisabled() );


            $browser->click( '#d2f-list-edit-' . $u2->getId() )
                ->type( 'name', 'Test Test 1' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press('Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User edited' )
                ->assertSee( 'Test Test 1' )
                ->assertSee( 'imcustadmin' )
                ->assertSee( 'imagine-custadmin@example.com' );

            // test the values:
            D2EM::refresh($u2);

            $this->assertEquals( 2                                  , $u2->getId() );
            $this->assertEquals( 'Test Test 1'                        , $u2->getName() );
            $this->assertEquals( 'imcustadmin'                      , $u2->getUserName() );
            $this->assertEquals( 'imagine-custadmin@example.com'    , $u2->getEmail() );
            $this->assertEquals( '12125551000'                      , $u2->getAuthorisedMobile() );
            $this->assertTrue(  $u2->isCustAdmin() );
            $this->assertFalse( $u2->getDisabled() );

        });


    }


    public function testSuperAdminPrivs(){
        $this->browse(function (Browser $browser) {

            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->assertPathIs( '/admin' );

            /** @var CustomerEntity $nonInternalCust */
            $nonInternalCust = D2EM::getRepository( CustomerEntity::class )->findOneBy( [ 'type' => CustomerEntity::TYPE_FULL ] );

            /** @var CustomerEntity $nonInternalCust */
            $internalCust = D2EM::getRepository( CustomerEntity::class )->findOneBy( [ 'type' => CustomerEntity::TYPE_INTERNAL ] );

            /** @var UserEntity $existingUser */
            $existingUser = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'travis' ] );


            // 1. customer overview -> non internal customer -> add user from tab -> no super option
            $browser->visit( 'customer/overview/' . $nonInternalCust->getId() . '/users' )
                    ->click( "#users-add-btn" )
                    ->type( '#email' , 'test@example.com' )
                    ->click( '.btn-primary' );

            $browser->assertSelectMissingOption( "#privs" , UserEntity::AUTH_SUPERUSER );

            // 2. customer overview -> non internal customer -> add existing user from tab -> no super option

            $browser->visit( 'customer/overview/' . $nonInternalCust->getId() . '/users' )
                ->click( "#users-add-btn" )
                ->type( '#email' , $existingUser->getEmail() )
                ->click( '.btn-primary' );

            $browser->assertSelectMissingOption( "#privs" , UserEntity::AUTH_SUPERUSER );

            // 3. customer overview -> internal customer -> add user from tab -> super option


            $browser->visit( 'customer/overview/' . $internalCust->getId() . '/users' )
                ->click( "#users-add-btn" )
                ->type( '#email' , 'test2@example.com' )
                ->click( '.btn-primary' );

            $browser->assertSelectHasOption( "#privs" , UserEntity::AUTH_SUPERUSER );

            // 4. customer overview -> internal customer -> add existing user from tab -> super option

            $browser->visit( 'customer/overview/' . $internalCust->getId() . '/users' )
                ->click( "#users-add-btn" )
                ->type( '#email' , $existingUser->getEmail() )
                ->click( '.btn-primary' );

            $browser->assertSelectHasOption( "#privs" , UserEntity::AUTH_SUPERUSER );


            // 5. lhs users menu option -> add -> non existing user as super user set on non-internal -> error
            $browser->visit( 'user/list' )
                    ->click( "#add-user" )
                    ->type( "#email", "test12@example.com" )
                    ->click( '.btn-primary' )
                    ->type( 'name', 'Test User 12' )
                    ->type( 'username', 'testuser12' )
                    ->select( 'privs', 3 )
                    ->select( 'custid', 4 )
                    ->check( 'enabled' )
                    ->type( 'authorisedMobile', '12125551000' )
                    ->press('Add' );

            $browser->assertSee( "You are not allowed to set this User as a Super User" );

            // 6. lhs users menu option -> add -> existing user as super user set on non-internal -> error

            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->type( "#email", $existingUser->getEmail() )
                ->click( '.btn-primary' )
                ->click( '#user-' . $existingUser->getId()  )
                ->select( 'privs', 3 )
                ->select( 'custid', 4 )
                ->press('Add User' );

            $browser->assertSee( "You are not allowed to set this User as a Super User" );


            // 7. lhs users menu option -> add -> non existing user super set on internal -> success + warning
            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->type( "#email", "test13@example.com" )
                ->click( '.btn-primary' )
                ->type( 'name', 'Test User 13' )
                ->type( 'username', 'testuser13' )
                ->select( 'privs', 3 )
                ->select( 'custid', 1 )
                ->check( 'enabled' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press('Add' );

            $browser->assertSee( "Please note that you have given this user full administrative access" );

            // 8. lhs users menu option -> add -> existing user super set on internal -> success + warning
            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->type( "#email", "heanet-custadmin@example.com" )
                ->click( '.btn-primary' )
                ->click( '#user-5' )
                ->select( 'privs', 3 )
                ->select( 'custid', 1 )
                ->press('Add User' );

            $browser->assertSee( "Please note that you have given this user full administrative access" );

            // 9. lhs users menu option -> edit -> non-internal -> no super option
            $browser->visit( 'user/list' )
                ->click( "#d2f-list-edit-3" );

            $browser->assertSelectMissingOption( "#privs_5" , UserEntity::AUTH_SUPERUSER );

            // 10. lhs users menu option -> edit -> internal -> super option (for originally non super user)
            $browser->visit( 'user/edit/2' )
                    ->select( 'privs_5' , UserEntity::AUTH_CUSTADMIN )
                    ->press('Save Changes' );

            $browser->visit( 'user/list' )
                ->click( "#d2f-list-edit-5" );

            $browser->assertSelectHasOption( "#privs_1" , UserEntity::AUTH_SUPERUSER );

            // 10. customer admin -> add non existing user -> no super option
            $browser->visit( 'user/list' )
                ->click( "#d2f-more-options-2" )
                ->click( '#d2f-option-login-as-2');

            $browser->assertPathIs( "/dashboard" );

            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->pause( 5000)
                ->type( "#email" , "test2@example.com" )
                ->click( '.btn-primary' );

            // 11. customer admin -> add existing user -> no super option*/
            $browser->visit( 'user/list' )
                ->pause(2000)
                ->click( "#add-user" )
                ->type( "#email" , "joe@siep.com" )
                ->click( '.btn-primary' );

            $browser->assertSelectMissingOption( "#privs" , UserEntity::AUTH_SUPERUSER );


            // 12. customer admin -> edit user -> no super option
            $browser->visit( 'user/list' )
                    ->click( "#d2f-list-edit-3" );

            $browser->assertSelectMissingOption( "#privs" , UserEntity::AUTH_SUPERUSER );


            // Deleting created users for the tests


            $browser->visit( '/switch-user-back' );

            $addedUser = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'email' => 'test13@example.com' ] );
            $addedUser2 = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'email' => 'heanet-custadmin@example.com' ] );

            $browser->visit( '/user/list' )
                ->click( "#d2f-list-delete-" . $addedUser->getId() )
                ->press( "Delete" );

            $browser->visit( "/user/edit/" . $addedUser2->getId() )
                ->pause( 2000 )
                ->click( "#d2f-list-delete-1" )
                ->press( "Delete" );
        });











    }
}
