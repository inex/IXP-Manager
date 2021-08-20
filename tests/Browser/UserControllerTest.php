<?php

namespace Tests\Browser;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
use Throwable;

use IXP\Models\{
    Customer,
    CustomerToUser,
    User
};

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test User Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserControllerTest extends DuskTestCase
{

    public function tearDown(): void
    {
        foreach( [ 'test-user1@example.com' , 'test13@example.com' ] as $email ) {
            if( $u = User::whereEmail( $email )->first() ) {
                $u->delete();
            }
        }
        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testAdd(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize( 1600,1200 )
                    ->visit('/logout')
                    ->visit('/login')
                    ->type('username', 'travis' )
                    ->type('password', 'travisci' )
                    ->press('#login-btn' )
                    ->assertPathIs( '/admin' );

            $browser->visit( '/user/list' )
                    ->assertSee( 'hecustadmin' )
                    ->assertSee( 'heanet-custadmin@example.com' );

            /**
             *
             * Add existing user
             *
             */

            $browser->click( '#add-user'         )
                    ->assertSee( 'Users / Create'   )
                    ->assertSee( 'Email'            )
                    ->type( '#email' , 'test-user1example.com' )
                    ->click( '.btn-primary'         )
                    ->assertPathIs('/user/create-wizard'   )
                    ->assertSee( 'The email must be a valid email address' )
                    ->type( '#email' , 'test-user1@example.com' )
                    ->click( '.btn-primary' );

            $browser->assertSee( 'Users / Create' )
                    ->assertInputValue( 'email' , 'test-user1@example.com' )
                    ->type(     'name',     'Test User 1'       )
                    ->select(   'custid',   5                   )
                    ->type(     'username','testuser1'          )
                    ->select(   'privs',    User::AUTH_CUSTUSER )
                    ->check(    'disabled' )
                    ->type(     'authorisedMobile', '12125551000' )
                    ->press(    'Create' )
                    ->waitForLocation('/user/list' )
                    ->assertSee( 'User created. A welcome email' )
                    ->assertSee( 'Test User 1'  )
                    ->assertSee( 'testuser1'    )
                    ->assertSee( 'test-user1@example.com' );

            $u = User::whereUsername( 'testuser1' )->first();

            $c2u = CustomerToUser::where( 'user_id', $u->id )->where( "customer_id", 5 )->first();

            $this->assertInstanceOf( User::class              , $u      );
            $this->assertInstanceOf( CustomerToUser::class    , $c2u    );
            $this->assertEquals( 'Test User 1',             $u->name               );
            $this->assertEquals( 'testuser1',               $u->username           );
            $this->assertEquals( 'test-user1@example.com',  $u->email              );
            $this->assertEquals( '12125551000',             $u->authorisedMobile   );
            $this->assertEquals( User::AUTH_CUSTUSER,       $u->privs              );
            $this->assertEquals( 5,                         $u->custid             );
            $this->assertTrue(  $u->isCustUser()    );
            $this->assertFalse( (bool)$u->disabled        );


            /**
             *
             * Edit User
             *
             */
            $browser->visit(        "/user/list" )
                    ->waitForText( 'Privileges' )
                    ->click(    "#btn-edit-" . $u->id )
                    ->assertInputValue('name',              'Test User 1'   )
                    ->assertInputValue('username',          'testuser1'     )
                    ->assertInputValue('email',             'test-user1@example.com')
                    ->assertInputValue( 'authorisedMobile', '12125551000'   )
                    ->assertChecked(    'disabled' )
                    ->assertSee(        'Imagine' )
                    ->assertSelected(   'privs_' . $c2u->id , User::AUTH_CUSTUSER );


            $browser->select('privs_' . $c2u->id,         User::AUTH_CUSTADMIN )
                    ->waitForText( "The user's privilege has been updated." )
                    ->type( 'name',             'Test User' )
                    ->type( 'username',         'testuser'  )
                    ->type( 'email',            'test-user@example.com' )
                    ->type( 'authorisedMobile', '12125551011'           )
                    ->uncheck( 'disabled'            )
                    ->press(  'Save Changes'      )
                    ->assertPathIs('/user/list'     )
                    ->assertSee( 'User updated'     )
                    ->assertSee( 'Test User'        )
                    ->assertSee( 'testuser'         )
                    ->assertSee( 'test-user@example.com' );

            $u->refresh();
            $c2u->refresh();

            $this->assertInstanceOf( User::class   ,              $u    );
            $this->assertInstanceOf( CustomerToUser::class   ,    $c2u  );
            $this->assertEquals( User::AUTH_CUSTADMIN,    $c2u->privs   );
            $this->assertEquals( 5,                             $c2u->customer_id );
            $this->assertEquals( 'Test User',                   $u->name );
            $this->assertEquals( 'testuser',                    $u->username );
            $this->assertEquals( 'test-user@example.com',       $u->email );
            $this->assertEquals( '12125551011',                 $u->authorisedMobile );
            $this->assertTrue( (bool)$u->disabled );


            /**
             *
             * Add existing user
             *
             */
            $browser->visit( "/user/list" )
                    ->click( '#add-user' )
                    ->assertSee( 'Users / Create' )
                    ->assertSee( 'Email' )
                    ->type(     '#email' , $u->email )
                    ->click( '.btn-primary' );

            $browser->assertSee( $u->email )
                    ->assertSee( $u->username )
                    ->click( "#user-" . $u->id )
                    ->select(   "#privs",   User::AUTH_CUSTADMIN )
                    ->select(   "#customer_id",    5 )
                    ->click( ".btn-primary" );


            $browser->waitForText( "This user is already associated with Imagine")
                    ->select( "#customer_id" , 2 )
                    ->click( ".btn-primary" );

            $browser->assertPathIs( "/user/list")
                    ->assertSee( "has been created");

            $c2u2 = CustomerToUser::where( 'user_id', $u->id )->where( "customer_id", 2 )->first();
            // test the values:
            $this->assertInstanceOf(CustomerToUser::class       , $c2u2                 );
            $this->assertEquals(    2                           , $c2u2->customer_id    );
            $this->assertEquals(             $u->id                     , $c2u2->user_id        );
            $this->assertEquals(    User::AUTH_CUSTADMIN        , $c2u2->privs          );
            $this->assertNotNull(           $c2u->created_at );


            /**
             *
             * test that editing while not making any changes and saving changes nothing
             *
             */
            $browser->visit( "/user/list" )
                    ->click( "#btn-edit-" . $u->id )
                    ->assertInputValue('name', 'Test User'              )
                    ->assertInputValue('username', 'testuser'           )
                    ->assertInputValue('email', 'test-user@example.com' )
                    ->assertNotChecked( 'disabled' )
                    ->assertInputValue('authorisedMobile', '12125551011')
                    ->assertSee('AS112' )
                    ->assertSelected('privs_' . $c2u->id , User::AUTH_CUSTADMIN )
                    ->assertSee('Imagine' )
                    ->assertSelected('privs_' . $c2u2->id, User::AUTH_CUSTADMIN )
                    ->press( 'Save Changes' )
                    ->assertPathIs('/user/list' )
                    ->assertSee( 'User updated' )
                    ->assertSee( 'Test User' )
                    ->assertSee( 'testuser' );

            // test the values:
            $u->refresh();
            $c2u->refresh();
            $c2u2->refresh();

            $this->assertEquals(    'Test User'                         , $u->name              );
            $this->assertEquals(    'testuser'                          , $u->username          );
            $this->assertEquals(    'test-user@example.com'             , $u->email             );
            $this->assertEquals(    '12125551011'                       , $u->authorisedMobile  );
            $this->assertInstanceOf(CustomerToUser::class               , $c2u                  );
            $this->assertEquals(    5                                   , $c2u->customer_id     );
            $this->assertEquals(             $u->id                              , $c2u->user_id            );
            $this->assertEquals(    User::AUTH_CUSTADMIN                , $c2u->privs           );
            $this->assertEquals(    2                                   , $c2u2->customer_id    );
            $this->assertEquals(             $u->id                              , $c2u2->user_id       );
            $this->assertEquals(    User::AUTH_CUSTADMIN                , $c2u2->privs          );
            $this->assertNotNull(            $c2u2->created_at      );
            $this->assertNotNull(            $c2u->created_at       );
            $this->assertTrue(               (bool)$u->disabled     );


            /**
             *
             * Edit User
             *
             */
            $browser->click( "#btn-edit-" . $u->id )
                    ->select(   'privs_' . $c2u->id           , User::AUTH_CUSTUSER )
                    ->waitForText( "The user's privilege has been updated." )
                    ->select(   'privs_' . $c2u2->id           , User::AUTH_CUSTUSER )
                    ->waitForText( "The user's privilege has been updated." )
                    ->type(     'name'      , 'Test User 1' )
                    ->type(     'username'  , 'testuser1' )
                    ->type(     'email'     , 'test-user1@example.com' )
                    ->check(    'disabled' )
                    ->type(     'authorisedMobile'  , '12125551000' )
                    ->press(    'Save Changes'    )
                    ->waitForLocation('/user/list' )
                    ->assertSee( 'User updated' )
                    ->assertSee( 'Test User 1' )
                    ->assertSee( 'testuser1' )
                    ->assertSee( 'test-user1@example.com' );

            // test the values:
            $u->refresh();
            $c2u->refresh();
            $c2u2->refresh();

            $this->assertEquals(        'Test User 1'                      , $u->name                  );
            $this->assertEquals(        'testuser1'                        , $u->username              );
            $this->assertEquals(        'test-user1@example.com'           , $u->email                 );
            $this->assertEquals(        '12125551000'                      , $u->authorisedMobile      );
            $this->assertInstanceOf(    CustomerToUser::class               , $c2u                      );
            $this->assertEquals(        5                                  , $c2u->customer_id          );
            $this->assertEquals(                $u->id                              , $c2u->user_id             );
            $this->assertEquals(        User::AUTH_CUSTUSER                , $c2u->privs               );
            $this->assertEquals(        2                                  , $c2u2->customer_id         );
            $this->assertEquals(                 $u->id                             , $c2u2->user_id            );
            $this->assertEquals(        User::AUTH_CUSTUSER                 , $c2u2->privs              );
            $this->assertNotNull(   $c2u2->created_at  );
            $this->assertNotNull(   $c2u->created_at   );
            $this->assertFalse(     (bool)$u->disabled       );



            /**
             *
             * Add customer to a user
             *
             */
            $browser->click( "#btn-edit-" . $u->id )
                    ->click( "#add-c2u-btn")
                    ->click( "#user-" . $u->id )
                    ->select(   "#privs",   User::AUTH_CUSTADMIN )
                    ->select(   "customer_id",    3 )
                    ->click( ".btn-primary" );

            $c2u3 = CustomerToUser::where( 'user_id', $u->id )->where( "customer_id", 3 )->first();
            // test the values:
            $this->assertInstanceOf(CustomerToUser::class   , $c2u3 );
            $this->assertEquals(    3                       , $c2u3->customer_id );
            $this->assertEquals(             $u->id                 , $c2u3->user_id      );
            $this->assertEquals(    User::AUTH_CUSTADMIN    , $c2u3->privs        );
            $this->assertNotNull(           $c2u3->created_at );


            /**
             *
             * Delete customer/user link
             *
             */
            $browser->click(        "#btn-delete-" . $u->id )
                    ->waitForText(     "Delete User")
                    ->assertSee(       'See ' . config( 'ixp_fe.lang.customer.one' ) . ' links' )
                    ->press(         'See ' . config( 'ixp_fe.lang.customer.one' ) . ' links' );

            $browser->assertPathIs(     "/user/edit/" . $u->id )
                    ->waitForText(      'Imagine' )
                    ->click(         "#btn-delete-c2u-" . $c2u2->id )
                    ->waitForText(      "Delete " . ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " To User" )
                    ->assertSee(        "Do you really want to unlink" )
                    ->press(          "Delete" );


            $browser->assertPathIs( "/user/list" )
                    ->assertSee(     $c2u2->user->name . "/" . $c2u2->user->username . " deleted from" );


            // test the values:
            $u->refresh();
            $c2u->refresh();


            $this->assertEquals( null   , CustomerToUser::where( 'user_id', $u->id )->where( "customer_id", 2 )->first() );
            $this->assertEquals( 2      , $u->customerToUser()->count() );


            /**
             *
             * Delete user and all links
             *
             */

            $browser->click(    "#btn-delete-" . $u->id )
                    ->waitForText( "Delete User" )
                    ->assertSee(   "Are you sure you want to delete this user and its 2 " . config( 'ixp_fe.lang.customer.one' ) . " links" )
                    ->press(     'Delete' );

            $browser->assertPathIs("/user/list" )
                    ->assertSee(    "User deleted" );

            $this->assertEquals( null   , CustomerToUser::where( 'user_id', $u->id )->where( "customer_id", 1 )->first() );
            $this->assertEquals( null   , User::whereUsername( 'testuser1' )->first() );


            /**
             *
             *  Add User Via customer overview
             *
             */
            $browser->visit(        '/customer/overview/5/users' )
                    ->waitForText(    'imcustadmin' )
                    ->assertSee(    'imagine-custadmin@example.com' )
                    ->press(      '#users-add-btn' )
                    ->assertSee(    'Users / Create' )
                    ->assertSee(    'Email' )
                    ->type(         'email',    'test-user2@example.com' )
                    ->click(     '.btn-primary' );


            $browser->waitForText( 'Username', 2000 )
                    ->assertInputValue( 'email', 'test-user2@example.com' )
                    ->assertSelected(   'custid', 5 )
                    ->type(             'name', 'Test User 2'           )
                    ->type(             'username', 'testuser2'         )
                    ->select(           'privs', User::AUTH_CUSTUSER    )
                    ->check(            'disabled' )
                    ->type(             'authorisedMobile', '12125551000' )
                    ->press(           'Create' )
                    ->assertPathIs(     '/customer/overview/5/users'    )
                    ->assertSee(        'User created. A welcome email' )
                    ->assertSee(        'Test User 2'   )
                    ->assertSee(        'testuser2'     )
                    ->assertSee(        'test-user2@example.com' );

            /** @var User $u2 */
            $u2 = User::whereUsername( 'testuser2' )->first();

            /** @var CustomerToUser $c2u3 */
            $c2u3 = CustomerToUser::where( 'user_id', $u2->id )->where( 'customer_id', 5 )->first();

            $this->assertInstanceOf( User::class                , $u2 );
            $this->assertEquals(    'Test User 2'               , $u2->name             );
            $this->assertEquals(    'testuser2'                 , $u2->username         );
            $this->assertEquals(    'test-user2@example.com'    , $u2->email            );
            $this->assertEquals(    '12125551000'               , $u2->authorisedMobile );
            $this->assertFalse(               (bool)$u2->disabled );
            $this->assertInstanceOf( CustomerToUser::class      , $c2u3                 );
            $this->assertEquals(     5                          , $c2u3->customer_id    );
            $this->assertEquals(              $u2->id                    , $c2u3->user_id       );
            $this->assertEquals(     User::AUTH_CUSTUSER        , $c2u3->privs          );
            $this->assertNotNull(             $c2u3->created_at );



            /**
             *
             *  Delete User Via customer overview
             *
             */
            $browser->press(        '#btn-delete-' . $c2u3->user_id )
                    ->waitForText(    'Do you really want to delete this user?' )
                    ->press(        'Delete' )
                    ->assertPathIs(   '/customer/overview/5/users' )
                    ->assertSee(       'User deleted' )
                    ->assertDontSee(   'Test User 1' )
                    ->assertDontSee(   'testuser1' )
                    ->assertDontSee(   'test-user1@example.com' );

            $this->assertNull( User::whereUsername( 'testuser2' )->first() );
            $this->assertNull( CustomerToUser::where( 'user_id', $u2->id )->where( 'customer_id', 5 )->first() );

            /**
             *
             *  Add Customer to the loggued user and then test the switch customer function, then delete new the customer
             *
             */

            /** @var  User $u3 */
            $u3 = User::whereUsername( 'travis' )->first();

            $this->assertEquals( User::AUTH_SUPERUSER   , $u3->privs()  );
            $this->assertEquals( "1"                    , $u3->custid   );

            $browser->visit(        'user/list' )
                    ->click(    '#add-user' )
                    ->assertSee(   'Users / Create' )
                    ->assertSee(   'Email'          )
                    ->type(        '#email' , $u3->email   )
                    ->click(    '.btn-primary'          );

            $browser->assertSee(        $u3->email      )
                    ->assertSee(        $u3->username   )
                    ->click(    "#user-" . $u3->id )
                    ->select(     "#privs"      , User::AUTH_CUSTADMIN )
                    ->select(     "customer_id"       , 5 )
                    ->click(   ".btn-primary" );

            $browser->assertPathIs( "/user/list")
                    ->assertSee(    "has been created");

            $browser->click(       "#my-account" )
                    ->assertSeeIn( "#my-account-dd .dropdown-header", "Switch to:" )
                    ->assertSeeIn( "#my-account-dd"                 , "INEX" )
                    ->assertSeeIn( "#my-account-dd"                 , "Imagine" );

            $browser->click(        "#switch-cust-5" )
                    ->waitForText(       "You are now logged in for Imagine." )
                    ->assertPathIs(   "/dashboard" );

            // test the values:
            $u3->refresh();

            $this->assertEquals( User::AUTH_CUSTADMIN , $u3->privs() );
            $this->assertEquals( "5",                   $u3->custid );


            $browser->click( "#my-account" )
                    ->assertSeeIn( "#my-account-dd .dropdown-header", "Switch to:"  )
                    ->assertSeeIn( "#my-account-dd"                 , "INEX"        )
                    ->assertSeeIn( "#my-account-dd"                 , "Imagine"     );

            $browser->click(         "#switch-cust-1" )
                    ->assertPathIs(     "/admin" )
                    ->assertSee(        "You are now logged in for INEX." );


            $browser->visit(        'user/list' )
                    ->click(    "#btn-delete-" . $u3->id )
                    ->waitForText( "Delete User")
                    ->assertSee(   'See ' . config( 'ixp_fe.lang.customer.one' ) . ' links' )
                    ->press(     'See ' . config( 'ixp_fe.lang.customer.one' ) . ' links' );


            /** @var CustomerToUser $c2u4 */
            $c2u4 = CustomerToUser::where( 'user_id', $u3->id )->where( 'customer_id', 5 )->first();

            $browser->assertPathIs(    "/user/edit/" . $u3->id )
                    ->waitForText(     'Imagine' )
                    ->click(        "#btn-delete-c2u-" . $c2u4->id )
                    ->waitForText(     "Delete " . ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . " To User" )
                    ->assertSee(       "Do you really want to unlink" )
                    ->press(        'Delete' );

            $browser->assertPathIs( "/user/list" )
                    ->assertSee(    "deleted" );

        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testAddCustAdmin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize( 1600,1200 )
                    ->visit(    '/logout'   )
                    ->visit(    '/login'    )
                    ->type(    'username', 'imcustadmin'    )
                    ->type(    'password', 'travisci'       )
                    ->press(  '#login-btn' );

            $browser->visit(        '/user/list'     )
                    ->assertSee(    'Users'         )
                    ->assertSee(    'imcustuser'    )
                    ->assertSee(    'imagine-custuser@example.com' );

            /**
             *
             *  Adding a new user
             *
             */
            $browser->click(     '#add-user' )
                    ->assertSee(    'Users / Create' )
                    ->assertSee(    'Email' )
                    ->type(         '#email' , 'test-user11example.com' )
                    ->click(     '.btn-primary' )
                    ->assertPathIs( '/user/create-wizard' )
                    ->assertSee(     'The email must be a valid email address' )
                    ->type(         '#email' , 'test-user11@example.com' )
                    ->click(    '.btn-primary' );


            // 1. test add :
            $browser->assertInputValue( "email", 'test-user11@example.com' )
                    ->type( 'name', 'Test User 11'      )
                    ->type( 'username', 'testuser11'    )
                    ->select( 'privs', 1    )
                    ->check( 'disabled'            )
                    ->type( 'authorisedMobile', '12125551000' )
                    ->press('Create' )
                    ->assertPathIs('/user/list' )
                    ->assertSee( 'User created. A welcome email' )
                    ->assertSee( 'Test User 1' )
                    ->assertSee( 'testuser1' )
                    ->assertSee( 'test-user11@example.com' );

            // get the user:
            /** @var User $u */
            $u = User::whereUsername( 'testuser11' )->first();

            // test the values:
            $this->assertInstanceOf( User::class   , $u );
            $this->assertEquals( 5,                         $u->custid );
            $this->assertEquals( 'Test User 11',            $u->name );
            $this->assertEquals( 'test-user11@example.com', $u->email );
            $this->assertEquals( '12125551000',             $u->authorisedMobile );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( (bool)$u->disabled );

            // test that editing while not making any changes and saving changes nothing
            $browser->click( '#btn-edit-' . $u->id        )
                ->assertPathIs('/user/edit/' . $u->id             )
                ->assertInputValue('name', 'Test User 11'   )
                ->assertInputValue('username', 'testuser11' )
                ->assertInputValue('email', 'test-user11@example.com')
                ->assertChecked( 'disabled' )
                ->assertInputValue('authorisedMobile', '12125551000')
                ->assertSelected('privs', User::AUTH_CUSTUSER )
                ->press( 'Save Changes'     )
                ->assertPathIs('/user/list'  )
                ->assertSee( 'User updated' )
                ->assertSee( 'Test User 11' )
                ->assertSee( 'testuser11' )
                ->assertSee( 'test-user11@example.com' );

            // test the values:
            $u->refresh();
            $c2u = CustomerToUser::where( 'user_id', $u->id )->where( 'customer_id', 5 )->first();

            $this->assertInstanceOf( CustomerToUser::class, $c2u );
            $this->assertEquals( 5,                        $c2u->customer_id    );
            $this->assertEquals( 'Test User 11',            $u->name            );
            $this->assertEquals( 'test-user11@example.com', $u->email           );
            $this->assertEquals( '12125551000',            $u->authorisedMobile );
            $this->assertTrue( $u->isCustUser() );
            $this->assertFalse( (bool)$u->disabled );


            /**
             *
             *  Editing the new user
             *
             */
            $browser->click( '#btn-edit-' . $u->id )
                ->assertPathIs('/user/edit/' . $u->id )
                ->select('privs', User::AUTH_CUSTADMIN )
                ->assertDisabled( "name"    )
                ->assertDisabled( "username" )
                ->assertDisabled( "email"   )
                ->assertDisabled( "authorisedMobile" )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User updated' );

            // test the values:
            $u->refresh();
            $c2u->refresh();

            $this->assertEquals( 5,                         $c2u->customer_id );
            $this->assertEquals( 'Test User 11',            $u->name );
            $this->assertEquals( 'test-user11@example.com', $u->email );
            $this->assertEquals( '12125551000',            $u->authorisedMobile );
            $this->assertTrue( $u->isCustAdmin() );
            $this->assertFalse( (bool)$u->disabled );

            // delete this user
            $browser->press( '#btn-delete-' . $u->id )
                ->waitForText( 'Do you really want to unlink this ' . config( 'ixp_fe.lang.customer.one' ) . ' from this user' )
                ->press( 'Delete' )
                ->assertPathIs('/user/list'     )
                ->assertSee( 'User deleted'     )
                ->assertDontSee( 'Test User 11' )
                ->assertDontSee( 'testuser11'   )
                ->assertDontSee( 'test-user11@example.com' );

            $this->assertNull( User::whereUsername( 'testuser11' )->first() );
            $this->assertNull( CustomerToUser::where( 'user_id', $u->id )->where( 'customer_id', 5 )->first() );


            /**
             *
             *  Editing Loggued user
             *
             */
            /** @var User $u2 */
            $u2 = User::whereUsername( 'imcustadmin' )->first();

            $browser->click( '#btn-edit-' . $u2->id )
                ->assertPathIs('/user/edit/' . $u2->id )
                ->assertInputValue('name', 'Test Test')
                ->assertInputValue('username', 'imcustadmin')
                ->assertInputValue('email', 'imagine-custadmin@example.com')
                ->assertChecked( 'disabled' )
                ->assertSelected('privs', User::AUTH_CUSTADMIN )
                ->assertDisabled( 'username' )
                ->assertDisabled( 'email' )
                ->press( 'Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User updated' )
                ->assertSee( 'Test Test' )
                ->assertSee( 'imcustadmin' )
                ->assertSee( 'imagine-custadmin@example.com' );

            // test the values:
            $u2->refresh();

            $this->assertEquals( 2                                  , $u2->id );
            $this->assertEquals( 'Test Test'                        , $u2->name );
            $this->assertEquals( 'imcustadmin'                      , $u2->username );
            $this->assertEquals( 'imagine-custadmin@example.com'    , $u2->email );
            $this->assertEquals( ''                                 , $u2->authorisedMobile );
            $this->assertTrue(  $u2->isCustAdmin() );
            $this->assertFalse( (bool)$u2->disabled );


            $browser->click( '#btn-edit-' . $u2->id )
                ->type( 'name', 'Test Test 1' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press('Save Changes' )
                ->assertPathIs('/user/list' )
                ->assertSee( 'User updated' )
                ->assertSee( 'Test Test 1' )
                ->assertSee( 'imcustadmin' )
                ->assertSee( 'imagine-custadmin@example.com' );

            // test the values:
            $u2->refresh();

            $this->assertEquals( 2                                  , $u2->id );
            $this->assertEquals( 'Test Test 1'                      , $u2->name );
            $this->assertEquals( 'imcustadmin'                      , $u2->username );
            $this->assertEquals( 'imagine-custadmin@example.com'    , $u2->email );
            $this->assertEquals( '12125551000'                      , $u2->authorisedMobile );
            $this->assertTrue(  $u2->isCustAdmin() );
            $this->assertFalse( (bool)$u2->disabled );

        });
    }

    /**
     * Test SuperAdmin
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testSuperAdminPrivs(): void
    {
        $this->browse( function ( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                    ->visit( '/logout' )
                    ->visit( '/login' )
                    ->type( 'username', 'travis' )
                    ->type( 'password', 'travisci' )
                    ->press( '#login-btn' )
                    ->assertPathIs( '/admin' );

            /** @var Customer $nonInternalCust */
            $nonInternalCust = Customer::whereType( Customer::TYPE_FULL )->first();

            /** @var Customer $nonInternalCust */
            $internalCust = Customer::whereType( Customer::TYPE_INTERNAL )->first();

            /** @var User $existingUser */
            $existingUser = User::whereUsername( 'travis' )->first();

            // 1. customer overview -> non internal customer -> add user from tab -> no super option
            $browser->visit( 'customer/overview/' . $nonInternalCust->id . '/users' )
                    ->pause(5000    )
                    ->press( "#users-add-btn" )
                    ->type( '#email' , 'test@example.com' )
                    ->click( '.btn-primary' );

            $browser->assertSelectMissingOption( "#privs" , User::AUTH_SUPERUSER );

            // 2. customer overview -> non internal customer -> add existing user from tab -> no super option

            $browser->visit( 'customer/overview/' . $nonInternalCust->id . '/users' )
                ->click( "#users-add-btn" )
                ->type( '#email' , $existingUser->email )
                ->click( '.btn-primary' );

            //$browser->assertSelectMissingOption( "#privs" , UserEntity::AUTH_SUPERUSER );

            // 3. customer overview -> internal customer -> add user from tab -> super option


            $browser->visit( 'customer/overview/' . $internalCust->id . '/users' )
                ->click( "#users-add-btn" )
                ->type( '#email' , 'test2@example.com' )
                ->click( '.btn-primary' );

            $browser->assertSelectHasOption( "#privs" , User::AUTH_SUPERUSER );

            // 4. customer overview -> internal customer -> add existing user from tab -> super option

            $browser->visit( 'customer/overview/' . $internalCust->id . '/users' )
                ->click( "#users-add-btn" )
                ->type( '#email' , $existingUser->email )
                ->click( '.btn-primary' );

            $browser->assertSelectHasOption( "#privs" , User::AUTH_SUPERUSER );


            // 5. lhs users menu option -> add -> non existing user as super user set on non-internal -> error
            $browser->visit( 'user/list' )
                    ->click( "#add-user" )
                    ->type( "#email", "test12@example.com" )
                    ->press( 'Create' )
                    ->type( 'name', 'Test User 12' )
                    ->type( 'username', 'testuser12' )
                    ->select( 'privs', 3 )
                    ->select( 'custid', 4 )
                    ->check( 'disabled' )
                    ->type( 'authorisedMobile', '12125551000' )
                    ->press('Create' );

            $browser->assertSee( "You are not allowed to set this User as a Super User" );

            // 6. lhs users menu option -> add -> existing user as super user set on non-internal -> error

            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->type( "#email", $existingUser->email )
                ->click( '.btn-primary' )
                ->click( '#user-' . $existingUser->id  )
                ->select( 'privs', 3 )
                ->select( 'customer_id', 4 )
                ->press('Create User' );

            $browser->assertSee( "You are not allowed to set super user privileges" );


            // 7. lhs users menu option -> add -> non existing user super set on internal -> success + warning
            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->type( "#email", "test13@example.com" )
                ->click( '.btn-primary' )
                ->type( 'name', 'Test User 13' )
                ->type( 'username', 'testuser13' )
                ->select( 'privs', 3 )
                ->select( 'custid', 1 )
                ->check( 'disabled' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press('Create' );

            $browser->assertSee( "Please note that you have given this user full administrative access" );

            // 8. lhs users menu option -> add -> existing user super set on internal -> success + warning
            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->type( "#email", "heanet-custadmin@example.com" )
                ->click( '.btn-primary' )
                ->click( '#user-5' )
                ->select( 'privs', 3 )
                ->select( 'customer_id', 1 )
                ->press('Create User' );

            $browser->assertSee( "Please note that you have given this user full administrative access" );

            // 9. lhs users menu option -> edit -> non-internal -> no super option
            $browser->visit( 'user/list' )
                ->click( "#btn-edit-3" );

            $browser->assertSelectMissingOption( "#privs_3" , User::AUTH_SUPERUSER );

            // 10. lhs users menu option -> edit -> internal -> super option (for originally non super user)
            $browser->visit( 'user/edit/2' )
                    ->select( 'privs_2' , User::AUTH_CUSTADMIN );

            $browser->visit( 'user/list' )
                ->click( "#btn-edit-5" );

            $c2u = CustomerToUser::where( 'user_id', 5 )->where( 'customer_id', 1 )->first();
            $browser->assertSelectHasOption( "#privs_" . $c2u->id , User::AUTH_SUPERUSER );

            // 10. customer admin -> add non existing user -> no super option
            $browser->visit( 'user/list' )
                ->click( "#d2f-more-options-2" )
                ->click( '#d2f-option-login-as-2');

            $browser->assertPathIs( "/dashboard" );

            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->waitForText( "Users / Create" )
                ->type( "#email" , "test2@example.com" )
                ->click( '.btn-primary' );

            // 11. customer admin -> add existing user -> no super option*/
            $browser->visit( 'user/list' )
                ->waitForText('Privileges' )
                ->click( "#add-user" )
                ->waitForText( "Users / Create" )
                ->type( "#email" , "joe@siep.com" )
                ->press( 'Create' )
                ->waitForText( "travis" );

            $browser->assertSelectMissingOption( "#privs" , User::AUTH_SUPERUSER );


            // 12. customer admin -> edit user -> no super option
            $browser->visit( 'user/list' )
                    ->click( "#btn-edit-3" )
                    ->waitForText( "Users / Edit" );

            $browser->assertSelectMissingOption( "#privs" , User::AUTH_SUPERUSER );


            // Deleting created users for the tests


            $browser->visit( '/switch-user-back' );

            $addedUser  = User::whereEmail( 'test13@example.com' )->first();
            $addedUser2 = User::whereEmail( 'heanet-custadmin@example.com' )->first();

            $browser->visit( '/user/list' )
                ->click( "#btn-delete-" . $addedUser->id )
                ->press( "Delete" );

            $browser->visit( "/user/edit/" . $addedUser2->id )
                ->pause( 2000 )
                ->click( "#btn-delete-c2u-" . $c2u->id )
                ->waitForText( "Delete " . ucfirst( config( 'ixp_fe.lang.customer.one' ) ). " To User" )
                ->press( "Delete" );
        });
    }
}