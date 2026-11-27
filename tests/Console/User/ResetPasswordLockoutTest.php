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

namespace Tests\Console\User;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ResetPasswordLockoutTest extends TestCase
{
    public function testResetsState(): void
    {
        Log::spy();

        $user = $this->getSuperUser();
        $user->password_fail_count          = 7;
        $user->no_login_until               = now()->addMinutes( 3 );
        $user->password_last_fail_at        = now();
        $user->logins_throttled_notified_at = now();
        $user->save();

        $this->artisan('user:reset-password-lockout', [
            'search' => $user->username
        ])
            ->expectsQuestion('Enter ID to reset password lockout for', '1')
            ->assertExitCode(0)
            ->expectsOutput('Password lockout state reset for user with ID 1');

        $user->refresh();
        $this->assertEquals(0, $user->password_fail_count);
        $this->assertNull($user->no_login_until);
        $this->assertNull($user->password_last_fail_at);
        $this->assertNull($user->logins_throttled_notified_at);

        Log::shouldHaveReceived( 'notice' )
            ->with( "User password lockout state reset via console command for {$user->username}" );
    }

    /**
     * After a reset the user can log in at once, even though logins were throttled.
     */
    public function testUserCanLoginAfterReset(): void
    {
        $user = $this->getSuperUser();
        $user->password_fail_count   = 7;
        $user->no_login_until        = now()->addMinutes( 3 );
        $user->password_last_fail_at = now();
        $user->save();

        $this->post( '/login', [ 'username' => 'travis', 'password' => 'travisci' ] );
        $this->assertGuest();

        $this->artisan('user:reset-password-lockout', [
            'search' => $user->username
        ])
            ->expectsQuestion('Enter ID to reset password lockout for', '1')
            ->assertExitCode(0);

        $this->post( '/login', [ 'username' => 'travis', 'password' => 'travisci' ] );
        $this->assertAuthenticated();
    }

    public function testUnknownUid(): void
    {
        $user = $this->getSuperUser();

        $this->artisan('user:reset-password-lockout', [
            'search' => $user->username
        ])
            ->expectsQuestion('Enter ID to reset password lockout for', '100')
            ->assertExitCode(3)
            ->expectsOutput('ID 100 is not a valid option');
    }

    public function testNoMatches(): void
    {
        $this->artisan('user:reset-password-lockout', [
            'search' => "idkwhoyourlookingfor"
        ])
            ->assertExitCode(2)
            ->expectsOutput('No matching users found');
    }

    public function testNoSearchArg(): void
    {
        $this->artisan('user:reset-password-lockout', [
            'search' => '',
        ])
            ->assertExitCode(1)
            ->expectsOutput('No search term provided');
    }
}
