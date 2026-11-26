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

use Tests\TestCase;
use Tests\Trait\SetsPasswordPolicy;

class SetPasswordTest extends TestCase
{
    use SetsPasswordPolicy;

    public function testSetPasswordUsingUid(): void
    {
        $user = $this->getSuperUser();
        $oldPassword = $user->password;

        $this->artisan('user:set-password', [
            '--uid' => $user->id,
            '--password' => 'newpassword',
        ])
            ->assertExitCode(0)
            ->expectsOutput('Password set.');

        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);
        $this->assertTrue(password_verify( 'newpassword', $user->password ));
    }

    public function testSetPasswordUsingNonexistentUid(): void
    {
        $user = $this->getSuperUser();

        $this->artisan('user:set-password', [
            '--uid' => '9999999',
            '--password' => 'newpassword',
        ])
            ->assertExitCode(-1)
            ->expectsOutput('UID does not exist !');
    }

    public function testSetPasswordUsingSearch(): void
    {
        $user = $this->getSuperUser();
        $oldPassword = $user->password;

        $this->artisan('user:set-password', [
            '--search' => "tra",
            '--password' => 'newpassword',
        ])
            ->expectsQuestion('Enter ID to change password for', '1')
            ->assertExitCode(0)
            ->expectsOutput('Password set.');
        ;

        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);
        $this->assertTrue(password_verify( 'newpassword', $user->password ));
    }

    public function testSetPasswordInteractive(): void
    {
        $user = $this->getSuperUser();
        $oldPassword = $user->password;

        $this->artisan('user:set-password', [
            '--search' => "tra",
        ])
            ->expectsQuestion('Enter ID to change password for', '1')
            ->expectsQuestion('Password or (return to have one generated)', 'newpassword')
            ->expectsQuestion('Confirm password', 'newpassword')

            ->assertExitCode(0)
            ->expectsOutput('Password set.');

        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);
        $this->assertTrue(password_verify( 'newpassword', $user->password ));
    }

    public function testSetPasswordInteractiveDontMatch(): void
    {
        $user = $this->getSuperUser();

        $this->artisan('user:set-password', [
            '--search' => "tra",
        ])
            ->expectsQuestion('Enter ID to change password for', '1')
            ->expectsQuestion('Password or (return to have one generated)', 'newpassword')
            ->expectsQuestion('Confirm password', 'differentpassword')
            ->assertExitCode(-1)
            ->expectsOutput('The passwords does not match!');
    }

    public function testSetPasswordUsingSearchInvalidUID(): void
    {
        $user = $this->getSuperUser();

        $this->artisan('user:set-password', [
            '--search' => "tra",
            '--password' => 'newpassword',
        ])
            ->expectsQuestion('Enter ID to change password for', '1000')
            ->assertExitCode(-1)
            ->expectsOutput('That UID was not in the list of returned options');
    }

    public function testSetPasswordValidMethod(): void
    {
        $this->artisan('user:set-password')
            ->expectsOutput('Search or UID must be set (and not both).')
            ->assertExitCode(-1);

        $this->artisan('user:set-password', [
            '--uid' => 1,
            '--search' => 'tra',
        ])
            ->expectsOutput('Search or UID must be set (and not both).')
            ->assertExitCode(-1);
    }

    public function testSetPasswordResetsLockoutState(): void
    {
        $user = $this->getSuperUser();
        $user->password_fail_count          = 7;
        $user->no_login_until               = now()->addMinutes( 3 );
        $user->password_last_fail_at        = now();
        $user->logins_throttled_notified_at = now();
        $user->save();

        $this->artisan('user:set-password', [
            '--uid' => $user->id,
            '--password' => 'newpassword',
        ])
            ->assertExitCode(0)
            ->expectsOutput('Password set.');

        $user->refresh();
        $this->assertEquals(0, $user->password_fail_count);
        $this->assertNull($user->no_login_until);
        $this->assertNull($user->password_last_fail_at);
        $this->assertNull($user->logins_throttled_notified_at);
    }

    public function testImplementsPasswordPolicy(): void
    {
        $args = [
            '--search' => "travis",
            '--password' => 'abcd',
        ];

        $this->artisan('user:set-password', $args)
            ->expectsQuestion('Enter ID to change password for', '1')
            ->assertExitCode(-1)
            ->expectsOutput('The password must be at least 8 characters.');

        $this->setPasswordPolicy( 9 );

        $this->artisan('user:set-password', $args)
            ->expectsQuestion('Enter ID to change password for', '1')
            ->assertExitCode(-1)
            ->expectsOutput('The password must be at least 9 characters.');
    }

    public function testGeneratedPasswordRespectsPasswordPolicy(): void
    {
        $user = $this->getSuperUser();
        $oldPassword = $user->password;

        $this->artisan('user:set-password', [
            '--search' => "travis",
        ])
            ->expectsQuestion('Enter ID to change password for', '1')
            ->expectsQuestion('Password or (return to have one generated)', '')
            ->expectsOutputToContain('Generated password: ')
            ->assertExitCode(0)
            ->expectsOutput('Password set.');

        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);

        // Create a condition where we have to defer to the maximum when generating a random password!
        $this->setPasswordPolicy( 72, 72 );

        $oldPassword2 = $user->password;

        $this->artisan('user:set-password', [
            '--search' => "travis",
        ])
            ->expectsQuestion('Enter ID to change password for', '1')
            ->expectsQuestion('Password or (return to have one generated)', '')
            ->expectsOutputToContain('Generated password: ')
            ->assertExitCode(0)
            ->expectsOutput('Password set.');

        $user->refresh();
        $this->assertNotEquals($oldPassword2, $user->password);
    }
}