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

use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function testSentToResetIfPasswordFailsPolicy(): void
    {
        config()->set('password_policy.minimum_length', 35);

        $this
            ->submitLoginForm( 'travis', 'travisci' )
            ->assertRedirect( '/' )
        ;

        $this->get(route( 'login@showForm'))
            ->assertSee('Your password no longer meets the password policy requirements.')
            ->assertSee('Reset your password to login');
    }

    public function testRehashesPasswordIfRoundsChange(): void
    {
        $user = $this->getSuperUser();

        Hash::setRounds(4);
        $user->password = $originalPassword = Hash::make('travisci');
        $user->save();

        $info = password_get_info($originalPassword);
        $this->assertEquals(4, $info['options']['cost']);

        Hash::setRounds(5);

        $this->submitLoginForm('travis', 'travisci')
            ->assertRedirect('');

        $this->assertNotEquals($originalPassword, $user->refresh()->password);
        $info = password_get_info($user->password);
        $this->assertEquals(5, $info['options']['cost']);
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