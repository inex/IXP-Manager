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
     * Test the superuser can login and see the dashboard
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
     */
    public function testRememberMeTokenDeletedOnLogout(): void
    {
        $this->browse(function (Browser $browser1) {

            $user = User::where('username', 'imcustadmin')->first();

            $this->assertDatabaseMissing('user_remember_tokens', ['user_id' => $user->id]);

            $browser1->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $this->assertDatabaseHas('user_remember_tokens', ['user_id' => $user->id]);

            $browser1->visit('/logout')
                ->waitForLocation( route('login@showForm') )
                ->assertSee('You have been logged out.');

            $this->assertDatabaseMissing('user_remember_tokens', ['user_id' => $user->id]);
        });
    }


    /**
     * Test that we are logged out if we try to present an expired remember me token
     */
    public function testCannotUseExpiredRememberMeToken(): void
    {
        $this->browse(function (Browser $browser1) {
            $browser1->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $user = User::where('username', 'imcustadmin')->first();
            $token = UserRememberToken::whereUserId($user->id)->first();
            $token->expires = now()->subMonth(6);
            $token->save();

            $browser1->refresh()
                ->waitForLocation( route('login@showForm') )
                ->assertSee('Your login session has expired or was terminated from another device. Please log in again.');
        });
    }

    /**
     * Test that we are logged out if we present a token which doesn't exist (was deleted)
     */
    public function testCannotUseDeletedRememberMeToken(): void
    {
        $this->browse(function (Browser $browser1) {
            $browser1->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $user = User::where('username', 'imcustadmin')->first();
            $token = UserRememberToken::whereUserId($user->id)->first();
            $token->delete();

            $browser1->refresh()
                ->waitForLocation( route('login@showForm') )
                ->assertSee('Your login session has expired or was terminated from another device. Please log in again.');
        });
    }


    /**
     * Test that we are logged out if we have a remember me token with invalid syntax
     */
    public function testCannotUseInvalidRememberMeToken(): void
    {
        $this->browse(function (Browser $browser1) {
            $browser1->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->check( 'remember' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' )
                ->assertPathIs( '/dashboard' );

            $browser1->deleteCookie(Auth::getRecallerName());
            $browser1->addCookie(Auth::getRecallerName(), "somegibberish");

            $browser1->refresh()
                ->waitForLocation( route('login@showForm') )
                ->assertSee('Your login session has expired or was terminated from another device. Please log in again.');
        });
    }
}