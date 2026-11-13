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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);
namespace Tests\Http\Controllers\Auth;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use IXP\Models\User;
use IXP\Models\User2FA;
use IXP\Models\UserRememberToken;
use Tests\TestCase;

/**
 * Remember me sessions are deemed safe: password throttling and 2FA lockout must not stop one from
 * being resumed, and resuming one must not change the account's throttle / lockout state.
 */
class RememberMeLockoutTest extends TestCase
{
    private string $username = 'travis';
    private string $password = 'travisci';

    /**
     * Provide custom configuration early so 2FA routes are registered
     */
    public function postBootstrapLoadConfigurationHook( Application $app ): void
    {
        $app['config']->set( [ 'google2fa.enabled' => true ] );
    }

    public function testResumeIsNotBlockedByPasswordThrottleAndDoesNotAlterIt(): void
    {
        $cookie = $this->loginWithRememberMe();
        $user   = User::whereUsername( $this->username )->firstOrFail();

        // account is throttled after the remember me token was issued
        $until = now()->addHour()->startOfSecond();
        $user->password_fail_count = 7;
        $user->no_login_until      = $until;
        $user->save();

        // the throttle does apply to a password login
        $this->startNewBrowserSession();
        $this->post( '/login', [ 'username' => $this->username, 'password' => $this->password ] );
        $this->assertGuest();

        // but not to the remember me cookie
        $this->startNewBrowserSession();
        $this->withCookie( Auth::getRecallerName(), $cookie )->get( route( 'profile@edit' ) );
        $this->assertAuthenticatedAs( $user );
        $this->assertTrue( Auth::viaRemember() );

        $user->refresh();
        $this->assertEquals( 7, $user->password_fail_count );
        $this->assertEquals( $until->getTimestamp(), $user->no_login_until->getTimestamp() );
    }

    public function testResumeOf2faCompleteTokenIsNotBlockedBy2faLockoutAndDoesNotAlterIt(): void
    {
        $user = $this->enable2fa( User::whereUsername( $this->username )->firstOrFail() );
        $cookie = $this->loginWithRememberMe();

        // this device completed 2FA at some point, and the account's 2FA was locked afterwards
        $user->userRememberTokens()->update( [ 'is_2fa_complete' => true ] );
        $user->user2FA->fail_count = config( 'auth.lockout.two_factor_attempts' );
        $user->user2FA->save();

        $this->startNewBrowserSession();
        $this->withCookie( Auth::getRecallerName(), $cookie )->get( route( 'profile@edit' ) )
            ->assertOk();
        $this->assertAuthenticatedAs( $user );

        $this->assertEquals( config( 'auth.lockout.two_factor_attempts' ), $user->user2FA->refresh()->fail_count );
    }

    public function test2faLockoutOnOneDeviceLeavesOtherRememberMeTokensAlone(): void
    {
        $user = $this->enable2fa( User::whereUsername( $this->username )->firstOrFail() );

        // the user's own device, 2FA complete
        $this->loginWithRememberMe();
        $user->userRememberTokens()->update( [ 'is_2fa_complete' => true ] );
        $ownTokenId = $user->userRememberTokens()->firstOrFail()->id;

        // a second device with the password but not the OTP, on an account that is already 2FA locked
        $this->startNewBrowserSession();
        $cookie = $this->loginWithRememberMe();
        $user->user2FA->fail_count = config( 'auth.lockout.two_factor_attempts' );
        $user->user2FA->save();

        $this->startNewBrowserSession();
        $this->withCookie( Auth::getRecallerName(), $cookie )->get( route( 'profile@edit' ) )
            ->assertRedirect( route( '2fa@authenticate' ) );

        $this->withCookie( Auth::getRecallerName(), $cookie )
            ->post( route( '2fa@authenticate' ), [ 'one_time_password' => '000000' ] )
            ->assertRedirect( route( 'login@showForm' ) );
        $this->assertGuest();

        // only the second device's token is gone
        $this->assertEquals( [ $ownTokenId ], UserRememberToken::whereUserId( $user->id )->pluck( 'id' )->all() );
        $this->assertEquals( config( 'auth.lockout.two_factor_attempts' ), $user->user2FA->refresh()->fail_count );
    }

    /**
     * Log in with remember me ticked and return the (decrypted) remember me cookie value.
     */
    private function loginWithRememberMe(): string
    {
        $response = $this->post( '/login', [
            'username' => $this->username,
            'password' => $this->password,
            'remember' => 'on',
        ] );
        $this->assertAuthenticated();

        return $response->getCookie( Auth::getRecallerName() )->getValue();
    }

    /**
     * Drop the session and the guard's cached user, as if the browser came back later with only its cookies.
     */
    private function startNewBrowserSession(): void
    {
        $this->flushSession();
        $this->app['auth']->forgetGuards();
    }

    private function enable2fa( User $user ): User
    {
        User2FA::setupForUser( $user, app( 'pragmarx.google2fa' )->generateSecretKey( 32 ) );
        $user->refresh()->user2FA->enable();
        return $user;
    }
}
