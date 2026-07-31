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

namespace Tests\Browser;

use IXP\Models\User;
use IXP\Models\User2FA;
use Laravel\Dusk\Browser;
use PragmaRX\Google2FALaravel\Google2FA;
use Tests\DuskTestCase;
use Tests\Trait\ModifiesEnv;

class SwitchUserControllerTest extends DuskTestCase
{

    use ModifiesEnv;

    /**
     * Test basic login-as and switch back flow
     */
    public function testLoginAs(): void
    {
        $this->browse( function( Browser $browser ) {

            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            // 1. Login as CUSTUSER successfully, then switch back
            $custUser = User::whereUsername( "hecustuser" )->get()->first();

            $browser->visit( 'user/list' )
                ->waitForText("Users")
                ->click( "#d2f-more-options-" . $custUser->id )
                ->click( '#d2f-option-login-as-' . $custUser->id )
                ->assertPathIs( "/dashboard" )
                ->assertSee("Switch Back")
                ->assertSee("You are now logged in as " . $custUser->username . " (" . $custUser->name . ") for the ".config('ixp_fe.lang.customer.one')." " . $custUser->customer->name)
                ->click("#nav-item-switch-user-back")
                ->waitForText("Users")
                ->assertRouteIs("user@list");

            // 2. Attempt to login as disabled user? denied
            $custUser->disabled = 1;
            $custUser->save();

            $browser->click( "#d2f-more-options-" . $custUser->id )
                ->click( '#d2f-option-login-as-' . $custUser->id )
                ->assertPathIs( "/admin/dashboard" )
                ->assertSee("You cannot login as this user");

            $custUser->disabled = 0;
            $custUser->save();

            // 3. Login as CUSTADMIN successfully, then switch back
            $custAdmin = User::whereUsername( "hecustadmin" )->get()->first();

            $browser->visit( 'user/list' )
                ->waitForText("Users")
                ->click( "#d2f-more-options-" . $custAdmin->id )
                ->click( '#d2f-option-login-as-' . $custAdmin->id )
                ->assertPathIs( "/dashboard" )
                ->assertSee("Switch Back")
                ->assertSee("You are now logged in as " . $custAdmin->username . " (" . $custAdmin->name . ") for the ".config('ixp_fe.lang.customer.one')." " . $custAdmin->customer->name)
                ->click("#nav-item-switch-user-back")
                ->waitForText("Users")
                ->assertRouteIs("user@list");

            // Add superuser from non-existing user
            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->waitForText( 'Users / Create' )
                ->type( "#email", "test13@example.com" )
                ->click( '.btn-primary' )
                ->waitForText( 'Privilege' )
                ->type( 'name', 'Test User 13' )
                ->type( 'username', 'testuser13' )
                ->select( 'privs', 3 )
                ->select( 'custid', 1 )
                ->check( 'disabled' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press( 'Create' )
                ->waitForText("Please note that you have given this user full administrative access" );

            // 4. Test we can login as a super user successfully
            $testSuperUser = User::whereUsername( "testuser13" )->get()->first();

            $browser->visit( 'user/list' )
                ->waitForText("Users")
                ->click( "#d2f-more-options-" . $testSuperUser->id )
                ->click( '#d2f-option-login-as-' . $testSuperUser->id )
                ->assertPathIs( "/admin/dashboard" )
                ->assertSee("Switch Back")
                ->assertSee("You are now logged in as " . $testSuperUser->username . " (" . $testSuperUser->name . ") for the ".config('ixp_fe.lang.customer.one')." " . $testSuperUser->customer->name);

            // 5. While already logged in as a different SUPERUSER user, denied login to a different user, then logout from that user
            $browser->visit( 'user/list' )
                ->waitForText("Users")
                ->click( "#d2f-more-options-" . $custUser->id )
                ->waitForText("Login as");

            // Frontend has link disabled
            $class = $browser->element( '#d2f-option-login-as-' . $custUser->id)->getAttribute( 'class' );
            $this->assertTrue(in_array("disabled", explode(" ", $class)), "a href class contains disabled class") ;

            // and backend rejects
            $browser->visit( "/admin/switch-user/" . $custUser->customerToUser()->first()->id)
                ->assertPathIs( "/admin/dashboard" )
                ->assertSee( "Switch Back" )
                ->assertSee( "You are already logged in as another user. If you want to login as someone else, switch back first." );

            $browser->click("#nav-item-switch-user-back")
                ->assertRouteIs("user@list");

            // 6. Error visiting switch-back-user when not logged in as another user
            $travis = User::whereUsername( "travis" )->get()->first();

            $browser->visit( "/admin/switch-user-back")
                ->assertPathIs( "/admin/dashboard" )
                ->screenshot("shotty")
                ->assertSee( "You are not currently logged in as another user. You are logged in as: " . $travis->username . "( " . $travis->name . ")" );

            // cleanup tests
            $browser->visit( 'user/list' )
                ->click( "#btn-delete-" . $testSuperUser->id )
                ->waitForText("Are you sure you want to delete this user")
                ->click('#btn-delete-user-submit')
                ->waitForText("Users")
                ->assertRouteIs("user@list");
        } );
    }


    /**
     * Test that superadmin can change to another user, even if 2FA is enforced and
     * the user hasn't setup 2FA yet
     */
    public function testLoginAs2FA(): void
    {
        $this->replaceEnvAttr( '2FA_ENABLED=false', '2FA_ENABLED=true' );
        $this->awaitArtisanEnvReload();

        $this->browse( function( Browser $browser ) {

            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation('/2fa/configure');

            // Configure 2FA for this user
            $u2fa = User2FA::whereUserId( 1 )->first();
            $google2FA = new Google2FA( request() );
            $otp = $google2FA->getCurrentOtp( $u2fa->secret );

            $browser->type( '#one_time_password', $otp )
                ->type( 'password', "travisci" )
                ->press( 'Enable 2FA' )
                ->waitForLocation( '/admin/dashboard')
                ->assertSee( '2FA successfully enabled.',1);

            // Create a new user also a superuser
            $browser->visit( 'user/list' )
                ->click( "#add-user" )
                ->waitForText( 'Users / Create' )
                ->type( "#email", "test13@example.com" )
                ->click( '.btn-primary' )
                ->waitForText( 'Privilege' )
                ->type( 'name', 'Test User 13' )
                ->type( 'username', 'testuser13' )
                ->select( 'privs', 3 )
                ->select( 'custid', 1 )
                ->check( 'disabled' )
                ->type( 'authorisedMobile', '12125551000' )
                ->press( 'Create' )
                ->waitForText("Please note that you have given this user full administrative access" );

            // Attempt to switch to that user
            $testSuperUser = User::whereUsername( "testuser13" )->get()->first();

            $browser->visit( 'user/list' )
                ->waitForText("Users")
                ->click( "#d2f-more-options-" . $testSuperUser->id )
                ->click( '#d2f-option-login-as-' . $testSuperUser->id )
                ->assertPathIs( "/admin/dashboard" )
                ->assertSee("Switch Back")
                ->assertSee("You are now logged in as " . $testSuperUser->username . " (" . $testSuperUser->name . ") for the ".config('ixp_fe.lang.customer.one')." " . $testSuperUser->customer->name);

            $browser->click("#nav-item-switch-user-back")
                ->assertRouteIs("user@list");

            // cleanup tests - delete user, remove 2fa from travis user
            $browser->visit( 'user/list' )
                ->click( "#btn-delete-" . $testSuperUser->id )
                ->waitForText("Are you sure you want to delete this user")
                ->click('#btn-delete-user-submit')
                ->waitForText("Users")
                ->assertRouteIs("user@list");

            $u2fa->refresh();
            $u2fa->delete();

        } );
    }

}