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

namespace Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use IXP\Jobs\SendLoginsThrottledNotifications;
use IXP\Mail\Auth\LoginsThrottled;
use IXP\Models\User;
use Tests\TestCase;

class SendLoginsThrottledNotificationsTest extends TestCase
{
    /**
     * This test checks that a notification email is only sent once, not with every throttled login attempt
     */
    public function testNotifiesAThrottledUserOnce(): void
    {
        Mail::fake();

        $user = $this->throttledUser( $this->getCustUser() );
        $this->assertNull( $user->logins_throttled_notified_at );

        new SendLoginsThrottledNotifications()->handle();

        Mail::assertSent( LoginsThrottled::class, 1 );
        Mail::assertSent( LoginsThrottled::class, fn( LoginsThrottled $mail ) => $mail->hasTo( $user->email ) );

        $user->refresh();
        $this->assertNotNull( $user->logins_throttled_notified_at );

        // further failures while still throttled do not send another email
        $user->password_fail_count++;
        $user->save();

        new SendLoginsThrottledNotifications()->handle();

        Mail::assertSent( LoginsThrottled::class, 1 );
    }

    /**
     * This test checks that a notification email will not be sent if
     *  - user has not reached failed attempts threshold
     *  - the last failed attempt is older than the attempts decay period
     */
    public function testDoesNotNotifyBelowThresholdOrAfterExpiry(): void
    {
        Mail::fake();

        $below = $this->throttledUser( $this->getCustUser() );
        $below->password_fail_count = config( 'auth.lockout.password_attempts' ) - 1;
        $below->save();

        $expired = $this->throttledUser( $this->getCustAdminUser() );
        $expired->password_last_fail_at = now()->subMinutes( config( 'auth.lockout.password_attempts_expiry' ) + 1 );
        $expired->save();

        new SendLoginsThrottledNotifications()->handle();

        Mail::assertNothingSent();
        $this->assertNull( $below->refresh()->logins_throttled_notified_at );
        $this->assertNull( $expired->refresh()->logins_throttled_notified_at );
    }

    /**
     * Failed logins through the login form lead to one email, sent by the job and not by the request.
     */
    public function testFailedLoginsLeadToOneNotification(): void
    {
        Mail::fake();
        config( [ 'auth.lockout.password_attempts' => 2 ] );

        $user = $this->getSuperUser();

        for( $i = 0; $i < 2; $i++ ) {
            $this->post( '/login', [ 'username' => $user->username, 'password' => 'wrong-password' ] );
        }
        $this->app->terminate();

        Mail::assertNothingSent();

        // try twice, to see if we get multiple notifications?
        new SendLoginsThrottledNotifications()->handle();
        new SendLoginsThrottledNotifications()->handle();

        Mail::assertSent( LoginsThrottled::class, 1 );
        Mail::assertSent( LoginsThrottled::class, fn( LoginsThrottled $mail ) => $mail->hasTo( $user->email ) );

        // a successful login clears the marker, so a later episode is notified again
        $user->refresh()->resetLockoutState();
        $this->assertNull( $user->refresh()->logins_throttled_notified_at );
    }


    /**
     * This test ensures if a user has a missing email, the run doesn't crash.
     */
    public function testMissingEmailDoesntBotchRun(): void
    {
        Mail::fake();

        $user1 = $this->throttledUser( $this->getCustUser() );

        $user2 = $this->throttledUser( $this->getCustAdminUser() );
        $user2->email = null;
        $user2->save();

        new SendLoginsThrottledNotifications()->handle();

        Mail::assertSent( LoginsThrottled::class, 1 );
        Mail::assertSent( LoginsThrottled::class, fn( LoginsThrottled $mail ) => $mail->hasTo( $user1->email ) );
    }

    private function throttledUser( User $user ): User
    {
        $user->password_fail_count   = config( 'auth.lockout.password_attempts' );
        $user->no_login_until        = now()->addMinute();
        $user->password_last_fail_at = now();
        $user->logins_throttled_notified_at = null;
        $user->save();

        return $user;
    }
}
