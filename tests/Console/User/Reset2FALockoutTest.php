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

use IXP\Models\User2FA;
use Tests\TestCase;

class Reset2FALockoutTest extends TestCase
{
    public function testResetsState(): void
    {
        $user = $this->getSuperUser();
        $user2FA = User2FA::setupForUser($user, "secret");
        $user2FA->fail_count = 5;
        $user2FA->save();

        $this->artisan('user:reset-2fa-lockout', [
            'search' => $user->username
        ])
            ->expectsQuestion('Enter ID to reset 2FA lockout for', '1')
            ->assertExitCode(0)
            ->expectsOutput('2FA lockout state reset for user with ID 1');

        $user2FA->refresh();
        $this->assertEquals(0, $user2FA->fail_count);
    }

    public function testUserHasNo2FA(): void
    {
        $user = $this->getSuperUser();

        $this->artisan('user:reset-2fa-lockout', [
            'search' => $user->username
        ])
            ->expectsQuestion('Enter ID to reset 2FA lockout for', '1')
            ->assertExitCode(5)
            ->expectsOutput('No 2FA configured for user with ID 1');
    }

    public function testUnknownUid(): void
    {
        $user = $this->getSuperUser();

        $this->artisan('user:reset-2fa-lockout', [
            'search' => $user->username
        ])
            ->expectsQuestion('Enter ID to reset 2FA lockout for', '100')
            ->assertExitCode(3)
            ->expectsOutput('ID 100 is not a valid option');
    }

    public function testNoMatches(): void
    {
        $this->artisan('user:reset-2fa-lockout', [
            'search' => "idkwhoyourlookingfor"
        ])
            ->assertExitCode(2)
            ->expectsOutput('No matching users found');
    }

    public function testNoSearchArg(): void
    {
        $this->artisan('user:reset-2fa-lockout', [
            'search' => '',
        ])
            ->assertExitCode(1)
            ->expectsOutput('No search term provided');
    }
}