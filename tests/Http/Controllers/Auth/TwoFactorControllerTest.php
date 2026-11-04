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

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use IXP\Models\User;
use IXP\Models\User2FA;
use Tests\TestCase;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFactorControllerTest extends TestCase
{
    /** @var string Two Factor Secret for user generated during the test */
    private string $secret;

    /**
     * Configuration needed at boot time (routes are only registered if these are set)
     */
    private const array BOOT_CONFIG = [
            'google2fa.enabled'           => true,
        ];

    /**
     * Provide custom configuration early so 2FA routes are registered
     */
    #[\Override]
    public function createApplication(): Application
    {
        // Set the configuration after it is loaded but before the service providers boot and
        // register routes. Unlike environment variables, this cannot be shadowed by state left
        // behind by other tests in the same process.
        date_default_timezone_set( 'Europe/Dublin' );

        if( !defined( 'LARAVEL_START' ) ) {
            define( 'LARAVEL_START', microtime( true ) );
        }

        $app = require __DIR__ . '/../../../../bootstrap/app.php';

        $app->afterBootstrapping( LoadConfiguration::class, function( Application $app ) {
                $app['config']->set( self::BOOT_CONFIG );
           } );

        $app->make( ConsoleKernel::class )->bootstrap();

        Hash::setRounds( 4 );

        return $app;
    }

    public function testLoadFormWhen2FASetup(): void
    {
        $user = $this->userWith2fa( $this->getCustUser() );

        $this->actingAs( $user )
            ->get( route( '2fa@showForm' ) )
            ->assertViewIs( 'user.2fa.login-form');
    }

    public function testFormRedirectsAwayIf2FAIsNotSetup(): void
    {
        $user = $this->getCustUser();

        $this->actingAs( $user )
            ->get( route( '2fa@showForm' ) )
            ->assertRedirect( '' );
    }

    public function testValidOtpOnAuthenticateRouteCompletes2fa(): void
    {
        $user = $this->userWith2fa( $this->getCustUser() );

        $this->actingAs( $user )
            ->post( route( '2fa@authenticate' ), [ 'one_time_password' => $this->validOtp() ] )
            ->assertRedirect('');
    }

    public function testAuthenticateRouteRedirectsAwayIf2faIsNotSetup(): void
    {
        // Providing the correct OTP resets the rate limit
        session()->put('url.intended.2fa', route('validation@start'));

        $user = $this->getCustUser();

        $this->actingAs( $user )
            ->post( route( '2fa@authenticate' ), [ 'one_time_password' => '000000' ] )
            ->assertRedirect( '' );
    }

    public function testAuthenticateRouteRedirectsToIntendedIfPresent(): void
    {
        // Providing the correct OTP resets the rate limit
        session()->put('url.intended', route('validation@start'));

        $user = $this->userWith2fa( $this->getCustUser() );

        $this->actingAs( $user )
            ->post( route( '2fa@authenticate' ), [ 'one_time_password' => $this->validOtp() ] )
            ->assertRedirectToRoute( 'validation@start' );
    }

    public function testInvalidOtpRedirectsBackToForm(): void
    {
        $user = $this->userWith2fa( $this->getCustUser() );

        session()->put('_previous.url', route('2fa@showForm'));

        $this->actingAs( $user )
            ->post( route( '2fa@authenticate' ), [ 'one_time_password' => $this->invalidOtp() ] )
            ->assertRedirectToRoute( '2fa@showForm' );
    }

    public function testAuthenticateRateLimitsAndLocksOut(): void
    {
        $user = $this->userWith2fa( $this->getCustUser() );

        // First few fails redirect to 2FA token form
        for ($i = 0; $i < 4; $i++) {
            session()->put('_previous.url', route('2fa@showForm'));
            $this->actingAs( $user )
                ->post( route( '2fa@authenticate' ), [ 'one_time_password' => $this->invalidOtp() ] )
                ->assertRedirectToRoute( '2fa@showForm' );
            $this->assertEquals($i + 1, RateLimiter::attempts('2fa:failed-otp:user:' . $user->id));
        }

        // Hit too many failed attempts, it's back to login
        session()->put('_previous.url', route('2fa@showForm'));
        $this->actingAs( $user )
            ->post( route( '2fa@authenticate' ), [ 'one_time_password' => $this->invalidOtp() ] )
            ->assertRedirectToRoute( 'login@showForm' );
        $this->assertEquals(5, RateLimiter::attempts('2fa:failed-otp:user:' . $user->id));

        // after too many failed attempts, even correct token is rejected
        session()->put('_previous.url', route('2fa@showForm'));
        $this->actingAs( $user )
            ->post( route( '2fa@authenticate' ), [ 'one_time_password' => $this->validOtp() ] )
            ->assertRedirectToRoute( 'login@showForm' );
        $this->assertEquals(5, RateLimiter::attempts('2fa:failed-otp:user:' . $user->id));
    }

    public function testCorrect2FAResetsRateLimit(): void
    {
        $user = $this->userWith2fa( $this->getCustUser() );

        // First few fails redirect to 2FA token form
        for ($i = 0; $i < 4; $i++) {
            session()->put('_previous.url', route('2fa@showForm'));
            $this->actingAs( $user )
                ->post( route( '2fa@authenticate' ), [ 'one_time_password' => $this->invalidOtp() ] )
                ->assertRedirectToRoute( '2fa@showForm' );
            $this->assertEquals($i + 1, RateLimiter::attempts('2fa:failed-otp:user:' . $user->id));
        }

        // Providing the correct OTP resets the rate limit
        session()->put('_previous.url', route('2fa@showForm'));
        $this->actingAs( $user )
            ->post( route( '2fa@authenticate' ), [ 'one_time_password' => $this->validOtp() ] )
            ->assertRedirect( '' );
        $this->assertEquals(0, RateLimiter::attempts('2fa:failed-otp:user:' . $user->id));
    }


    /**
     * Enable 2FA for the given user and return it
     */
    private function userWith2fa( User $user ): User
    {
        $this->secret = app( 'pragmarx.google2fa' )->generateSecretKey( 32 );
        User2FA::setupForUser( $user, $this->secret )->enable();
        return $user->fresh();
    }

    private function validOtp(): string
    {
        return ( new Google2FA( request() ) )->getCurrentOtp( $this->secret );
    }

    private function invalidOtp(): string
    {
        return $this->validOtp() === '000000' ? '111111' : '000000';
    }
}