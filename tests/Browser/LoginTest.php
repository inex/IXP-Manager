<?php
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Support\Facades\Auth;
use IXP\Models\User;
use IXP\Models\UserRememberToken;
use Laravel\Dusk\Browser;

use Tests\DuskTestCase;
use Throwable;

/**
 * Test Login Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LoginTest extends DuskTestCase
{
    /**
     * Test invalid credentials are rejected with a generic message
     * @throws \Throwable
     */
    public function testLoginFailed(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'invalid' )
                ->type( 'password', 'invalid' )
                ->press( '#login-btn' )
                ->waitForLocation( '/login' )
                ->assertSee('Invalid username or password. Please try again.')
            ;
        });
    }

    /**
     * Test the superuser can login and see the dashboard
     * @throws \Throwable
     */
    public function testLoginToDashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout')
                    ->visit('/login')
                    ->type( 'username', 'travis' )
                    ->type( 'password', 'travisci' )
                    ->press( '#login-btn' )
                    ->waitForLocation( '/admin/dashboard' )
                    ->assertPathIs( '/admin/dashboard' );
        });
    }

    /**
     * Test that we are logged out if we try to present a remember me token belonging to a different user
     * @throws \Throwable
     */
    public function testCannotUseAnotherUsersRememberMeToken(): void
    {
        $this->browse(function (Browser $browser1, Browser $browser2) {
            $browser1->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' )
                ->assertPathIs( '/admin/dashboard' );

            $browser2->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $browser1->deleteCookie(Auth::getRecallerName());
            $browser1->addCookie(Auth::getRecallerName(), $browser2->cookie(Auth::getRecallerName()));

            $browser1->refresh()
                ->waitForLocation( route('login@showForm') )
                ->assertSee('Your login session has expired or was terminated from another device. Please log in again.');
        });
    }


    /**
     * Test that the current remember me token is deleted on logout
     * @throws \Throwable
     */
    public function testRememberMeTokenDeletedOnLogout(): void
    {
        $this->browse(function (Browser $browser) {

            $user = User::where('username', 'imcustadmin')->first();

            $this->assertDatabaseMissing('user_remember_tokens', ['user_id' => $user->id]);
            $browser->assertCookieMissing(Auth::guard('web')->getRecallerName());

            $browser->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $this->assertDatabaseHas('user_remember_tokens', ['user_id' => $user->id]);
            $browser->assertHasCookie(Auth::guard('web')->getRecallerName());

            $browser->visit('/logout')
                ->waitForLocation( route('login@showForm') )
                ->assertSee('You have been logged out.');

            $this->assertDatabaseMissing('user_remember_tokens', ['user_id' => $user->id]);
            $browser->assertCookieMissing(Auth::guard('web')->getRecallerName());
        });
    }


    /**
     * Test that we are logged out if we try to present an expired remember me token
     * @throws \Throwable
     */
    public function testCannotUseExpiredRememberMeToken(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $browser->assertHasCookie(Auth::guard('web')->getRecallerName());

            $user = User::where('username', 'imcustadmin')->first();
            $token = UserRememberToken::whereUserId($user->id)->first();
            $token->expires = now()->subMonth(6);
            $token->save();

            $browser->refresh()
                ->waitForLocation( route('login@showForm') )
                ->assertSee('Your login session has expired or was terminated from another device. Please log in again.');

            $browser->assertCookieMissing(Auth::guard('web')->getRecallerName());
        });
    }

    /**
     * Test that we are logged out if we present a token which doesn't exist (was deleted)
     * @throws \Throwable
     */
    public function testCannotUseDeletedRememberMeToken(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $browser->assertHasCookie(Auth::guard('web')->getRecallerName());

            $user = User::where('username', 'imcustadmin')->first();
            $token = UserRememberToken::whereUserId($user->id)->first();
            $token->delete();

            $browser->refresh()
                ->waitForLocation( route('login@showForm') )
                ->assertSee('Your login session has expired or was terminated from another device. Please log in again.');

            $browser->assertCookieMissing(Auth::guard('web')->getRecallerName());
        });
    }


    /**
     * Test that we are logged out if we have a remember me token with invalid syntax
     * @throws \Throwable
     */
    public function testCannotUseInvalidRememberMeToken(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $browser->assertHasCookie(Auth::guard('web')->getRecallerName());
            $browser->deleteCookie(Auth::getRecallerName());
            $browser->addCookie(Auth::getRecallerName(), "somegibberish");
            $browser->assertHasCookie(Auth::guard('web')->getRecallerName());

            $browser->refresh()
                ->waitForLocation( route('login@showForm') )
                ->assertSee('Your login session has expired or was terminated from another device. Please log in again.');

            $browser->assertCookieMissing(Auth::guard('web')->getRecallerName());
        });
    }

    /**
     * Test that we are denied login when account is marked disabled
     * @throws \Throwable
     */
    public function testCannotLogInToDisabledAccount(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('username', 'travis')->first();
            $user->disabled = true;
            $user->save();

            $browser->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/login' )
                ->assertSee('Your account is disabled.');
        });
    }

    /**
     * Test the superuser can login and see the dashboard
     * @throws \Throwable
     */
    public function testSessionTerminatedIfAccountIsDisabled(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' )
                ->assertPathIs( '/admin/dashboard' );

            $user = User::where('username', 'travis')->first();
            $user->disabled = true;
            $user->save();

            $browser->refresh()
                ->waitForLocation( '/login' )
                ->assertSee('Your account is disabled.');
        });
    }
}