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

use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use IXP\Models\UnknownUserLoginAttempt;
use IXP\Models\User;
use Tests\TestCase;

class LoginRateLimitTest extends TestCase
{
    private string $username = 'travis';
    private string $password = 'travisci';

    private function reduceFailedLoginDurationForTests(): void
    {
        config()->set('auth.failed_login_min_duration', 1);
    }

    /**
     * This test does a couple of bad login attempts, then checks a legitimate login attempt resets all the lockout state
     */
    public function testValidLoginResetsThrottleState(): void
    {
        $this->reduceFailedLoginDurationForTests();

        // limits start off empty/zero
        $testT0 = Carbon::now();
        Carbon::setTestNow($testT0);

        $user = User::whereUsername( $this->username )->firstOrFail();
        $this->assertEquals( 0, $user->password_fail_count );
        $this->assertNull( $user->no_login_until );

        // failed attempt 1
        $testT1 = $testT0->clone()->addSecond();
        Carbon::setTestNow($testT1);
        $this->submitLoginForm( $this->username, 'wrong-password' )
            ->assertRedirectToRoute( 'login@showForm' );

        $user->refresh();
        $this->assertEquals( 1, $user->password_fail_count );
        $this->assertNull( $user->no_login_until );
        $this->assertEquals($testT1->getTimestamp(), $user->password_last_fail_at->getTimestamp());

        // failed attempt 2
        $testT2 = $testT1->clone()->addSecond();
        Carbon::setTestNow($testT2);
        $this->submitLoginForm( $this->username, 'wrong-password' )
            ->assertRedirectToRoute( 'login@showForm' );

        $user->refresh();
        $this->assertEquals( 2, $user->password_fail_count );
        $this->assertNull( $user->no_login_until );
        $this->assertEquals($testT2->getTimestamp(), $user->password_last_fail_at->getTimestamp());

        // valid attempt
        $this->submitLoginForm( $this->username, $this->password )
            ->assertRedirect( '' );

        $user->refresh();
        $this->assertEquals( 0, $user->password_fail_count );
        $this->assertNull( $user->no_login_until );
        $this->assertNull($user->password_last_fail_at);
    }

    /**
     * This test checks that after $threshold attempts, the user is made to wait a duration before logging in again.
     */
    public function testThrottlesRealUsers(): void
    {
        $this->reduceFailedLoginDurationForTests();

        $user = $this->getSuperUser();
        $epoch = Carbon::now();

        for ($i = 0; $i < 4; $i++) {
            $testTN = $epoch->clone()->addSeconds($i);
            Carbon::setTestNow( $testTN );

            // failed login attempts..
            $this->submitLoginForm( $this->username, 'wrong-password' )
                ->assertRedirectToRoute( 'login@showForm' );

            $this->get( route( 'login@showForm' ) )
                ->assertSeeText( 'Invalid username or password. Please try again.' );

            $user->refresh();
            $this->assertEquals( $i + 1, $user->password_fail_count );
            $this->assertNull( $user->no_login_until );
            $this->assertEquals($testTN->getTimestamp(), $user->password_last_fail_at->getTimestamp());
        }

        // hit threshold - now we receive warning about lockout
        $testTN = $epoch->clone()->addSeconds(5);
        Carbon::setTestNow( $testTN );

        $this->submitLoginForm( $this->username, 'wrong-password' )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->get( route( 'login@showForm' ) )
            ->assertSeeText( 'Invalid username or password.' )
            ->assertSeeText( 'Your account is temporarily locked due to failed login attempts - try again in 1 minute.' );

        $user->refresh();
        $this->assertEquals( 5, $user->password_fail_count );
        $this->assertEquals( $testTN->clone()->addMinute()->getTimestamp(), $user->no_login_until->getTimestamp() );

        // valid credentials are rejected during lockout period
        $this->submitLoginForm( $this->username, $this->password )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->get( route( 'login@showForm' ) )
            ->assertDontSeeText( 'Invalid username or password' )
            ->assertSeeText( 'Your account is temporarily locked due to failed login attempts - try again in 1 minute.' );

        $user->refresh();
        $this->assertEquals( 5, $user->password_fail_count );
        $this->assertEquals( $testTN->clone()->addMinute()->getTimestamp(), $user->no_login_until->getTimestamp() );

        // user waits 45 seconds into lock, tries again, still locked out
        $someSecondsIntoLock = $testTN->clone()->addSeconds(45);
        Carbon::setTestNow( $someSecondsIntoLock );

        $this->submitLoginForm( $this->username, $this->password )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->get( route( 'login@showForm' ) )
            ->assertDontSeeText( 'Invalid username or password' )
            ->assertSeeText( 'Your account is temporarily locked due to failed login attempts - try again in 14 seconds.' );

        $user->refresh();
        $this->assertEquals( 5, $user->password_fail_count );
        $this->assertEquals( $testTN->clone()->addMinute()->getTimestamp(), $user->no_login_until->getTimestamp() );

        // user waits 1 minute into lock
        $oneMinuteLater = $testTN->clone()->addMinute();
        Carbon::setTestNow( $oneMinuteLater );

        // can login now and state will be reset
        $this->submitLoginForm( $this->username, $this->password )
            ->assertRedirect( '' );

        $user->refresh();
        $this->assertEquals( 0, $user->password_fail_count );
        $this->assertNull( $user->no_login_until );
        $this->assertNull( $user->password_last_fail_at );
    }

    public function testThrottlesNonexistentUsers()
    {
        $this->reduceFailedLoginDurationForTests();

        $epoch = Carbon::now();
        Carbon::setTestNow( $epoch );

        for ($i = 0; $i < 4; $i++) {
            $this->submitLoginForm( 'wrong-user', 'wrong-password' )
                ->assertRedirectToRoute( 'login@showForm' );

            $this->get( route( 'login@showForm' ) )
                ->assertSeeText( 'Invalid username or password. Please try again.' );

            $state = UnknownUserLoginAttempt::whereUsername( 'wrong-user' )->firstOrFail();
            $this->assertEquals( $i + 1, $state->password_fail_count );
            $this->assertNull( $state->no_login_until );
        }

        // Starts showing throttle warnings
        $this->submitLoginForm( 'wrong-user', 'wrong-password' )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->get( route( 'login@showForm') )
            ->assertSeeText( 'Invalid username or password' )
            ->assertSeeText( 'Your account is temporarily locked due to failed login attempts - try again in 1 minute.' );

        $state = UnknownUserLoginAttempt::whereUsername( 'wrong-user' )->firstOrFail();
        $this->assertEquals( 5, $state->password_fail_count );
        $this->assertEquals( $epoch->clone()->addMinute()->getTimestamp(), $state->no_login_until->getTimestamp() );

        // Denies attempts without attempting login until no_login_until has passed.
        $this->submitLoginForm( 'wrong-user', 'wrong-password' )
            ->assertRedirectToRoute( 'login@showForm' );

        $this->get( route( 'login@showForm') )
            ->assertDontSeeText( "Invalid username or password." )
            ->assertSeeText( 'Your account is temporarily locked due to failed login attempts - try again in 1 minute.' );

        $state = UnknownUserLoginAttempt::whereUsername( 'wrong-user' )->firstOrFail();
        $this->assertEquals( 5, $state->password_fail_count );
        $this->assertEquals( $epoch->clone()->addMinute()->getTimestamp(), $state->no_login_until->getTimestamp() );

        $this->assertEquals( 1, UnknownUserLoginAttempt::count() );
    }

    /**
     * MySQL treats differently cased, accented and space padded spellings as the same username when looking a
     * user up. Failed attempts must be counted the same way, for real and for unknown usernames.
     */
    public function testSpellingsOfAUsernameShareOneCounter(): void
    {
        $this->reduceFailedLoginDurationForTests();

        $user = User::whereUsername( $this->username )->firstOrFail();

        foreach( [ 'travis', 'TRAVIS', 'trávis', 'travis  ' ] as $i => $spelling ) {
            $this->submitLoginForm( $spelling, 'wrong-password' );
            $this->assertEquals( $i + 1, $user->refresh()->password_fail_count, $spelling );
        }

        foreach( [ 'wrong-user', 'WRONG-USER', 'wróng-user', 'wrong-user  ' ] as $i => $spelling ) {
            $this->submitLoginForm( $spelling, 'wrong-password' );
            $this->assertEquals( 1, UnknownUserLoginAttempt::count(), $spelling );
            $this->assertEquals( $i + 1, UnknownUserLoginAttempt::firstOrFail()->password_fail_count, $spelling );
        }
    }

    /**
     * Failed attempts older than the expiry window no longer count, for real and for unknown usernames.
     */
    public function testFailedAttemptsExpire(): void
    {
        $this->reduceFailedLoginDurationForTests();

        config( [ 'auth.lockout.password_attempts' => 2, 'auth.lockout.password_attempts_expiry' => 60 ] );

        $epoch = Carbon::now()->startOfSecond();
        $user  = User::whereUsername( $this->username )->firstOrFail();

        foreach( [ $this->username, 'wrong-user' ] as $username ) {
            $state = fn() => $username === $this->username ? $user->refresh() : UnknownUserLoginAttempt::whereUsername( $username )->firstOrFail();

            Carbon::setTestNow( $epoch );
            $this->submitLoginForm( $username, 'wrong-password' );
            $this->submitLoginForm( $username, 'wrong-password' );
            $this->assertEquals( 2, $state()->password_fail_count, $username );
            $this->assertNotNull( $state()->no_login_until, $username );

            // just inside the window: the count carries on
            Carbon::setTestNow( $epoch->clone()->addMinutes( 59 ) );
            $this->submitLoginForm( $username, 'wrong-password' );
            $this->assertEquals( 3, $state()->password_fail_count, $username );

            // outside the window, measured from the last failure: starts again from one, no throttle
            Carbon::setTestNow( $epoch->clone()->addMinutes( 59 + 61 ) );
            $this->submitLoginForm( $username, 'wrong-password' );
            $this->assertEquals( 1, $state()->password_fail_count, $username );
            $this->assertNull( $state()->no_login_until, $username );
            $this->assertEquals( now()->getTimestamp(), $state()->password_last_fail_at->getTimestamp(), $username );
        }
    }

    /**
     * A count stored before the time of the last failure was recorded is treated as expired.
     */
    public function testFailCountWithoutLastFailureTimeIsTreatedAsExpired(): void
    {
        $user = User::whereUsername( $this->username )->firstOrFail();
        $user->password_fail_count = 4;
        $user->save();

        $this->submitLoginForm( $this->username, 'wrong-password' );

        $this->assertEquals( 1, $user->refresh()->password_fail_count );
        $this->assertNull( $user->no_login_until );
    }

    /**
     * The same sequence of requests must get the same responses whether or not the username exists.
     */
    public function testRealAndUnknownUsernamesAreIndistinguishable(): void
    {
        $this->reduceFailedLoginDurationForTests();

        config( [ 'auth.lockout.password_attempts' => 3 ] );

        $epoch = Carbon::now()->startOfSecond();

        $run = function( string $username, string $otherSpelling ) use ( $epoch ): array {
            $steps = [];
            $step  = function( string $label, string $name ) use ( &$steps ) {
                // each step starts with an empty session, so only this request's alerts are recorded
                $this->flushSession();
                $response = $this->submitLoginForm( $name, 'wrong-password' );
                $steps[ $label ] = [
                    'status'   => $response->getStatusCode(),
                    'location' => $response->headers->get( 'Location' ),
                    'alerts'   => array_map( fn( $a ) => [ $a->class(), $a->message() ], session( 'ixp.utils.view.alerts', [] ) ),
                ];
            };

            Carbon::setTestNow( $epoch );
            $step( 'fail 1', $username );
            $step( 'fail 2', $username );
            $step( 'fail 3, throttled', $username );
            $step( 'refused', $username );
            $step( 'refused, other spelling', $otherSpelling );

            Carbon::setTestNow( $epoch->clone()->addSeconds( 61 ) );
            $step( 'fail 4, longer throttle', $username );

            Carbon::setTestNow( $epoch->clone()->addDays( 2 ) );
            $step( 'fail after expiry', $username );

            return $steps;
        };

        $real    = $run( $this->username, strtoupper( $this->username ) );
        $unknown = $run( 'wrong-user', 'WRONG-USER' );

        $this->assertSame( $real, $unknown );

        // and the sequence did exercise each state
        $message = fn( string $label ) => $real[ $label ][ 'alerts' ][ 0 ][ 1 ];
        $this->assertSame( 'Invalid username or password. Please try again.', $message( 'fail 2' ) );
        $this->assertStringContainsString( 'try again in 1 minute.', $message( 'fail 3, throttled' ) );
        $this->assertStringStartsWith( 'Your account is temporarily locked', $message( 'refused' ) );
        $this->assertStringStartsWith( 'Your account is temporarily locked', $message( 'refused, other spelling' ) );
        $this->assertStringContainsString( 'try again in 2 minutes.', $message( 'fail 4, longer throttle' ) );
        $this->assertSame( 'Invalid username or password. Please try again.', $message( 'fail after expiry' ) );
    }

    /**
     * Pruning removes unknown usernames whose count has expired, and nothing else.
     */
    public function testPruneRemovesExpiredUnknownUsernames(): void
    {
        $make = function( string $username, ?Carbon $lastFail ): void {
            $row = UnknownUserLoginAttempt::create( [ 'username' => $username ] );
            $row->password_fail_count   = 1;
            $row->password_last_fail_at = $lastFail;
            $row->save();
        };

        $make( 'recent',  now()->subMinutes( 5 ) );
        $make( 'expired', now()->subDays( 2 ) );
        $make( 'no-time', null );

        $this->artisan( 'model:prune', [ '--model' => [ UnknownUserLoginAttempt::class ] ] )->assertExitCode( 0 );

        $this->assertEquals( [ 'recent' ], UnknownUserLoginAttempt::pluck( 'username' )->all() );
    }

    /**
     * Failed logins must take the same minimum time whether or not the username exists, and
     * whether or not it is locked out, so response time cannot be used to enumerate users.
     *
     * Currently configured to 1.2s
     */
    public function testFailedLoginsArePaddedToMinimumDuration(): void
    {
        config( [ 'auth.lockout.password_attempts' => 1 ] );

        foreach( [ $this->username, 'wrong-user' ] as $username ) {
            // first request is a failed attempt, second is refused by the lockout before the password is checked
            foreach( [ 'failed attempt', 'locked out' ] as $case ) {
                $start = microtime( true );
                $this->submitLoginForm( $username, 'wrong-password' )
                    ->assertRedirectToRoute( 'login@showForm' );
                $this->assertGreaterThanOrEqual( 1.1, microtime( true ) - $start, "{$username}: {$case}" );
            }
        }
    }

    /**
     * Test helper to submit a login form, with session context of the page we were previously on (for redirect()->back())
     */
    private function submitLoginForm( string $username, string $password ): TestResponse
    {
        return $this
            ->withSession( [ '_previous' => [ 'url' => route( 'login@showForm') ] ] )
            ->post( '/login', [
                'username' => $username,
                'password' => $password,
            ] );
    }
}