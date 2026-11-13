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

namespace Tests\Services\Auth;

use Carbon\Carbon;
use IXP\Services\Auth\PasswordLockoutPolicy;
use Tests\TestCase;

class PasswordLockoutPolicyTest extends TestCase
{
    /**
     * threshold is a direct read from config
     */
    public function testThreshold(): void
    {
        config()->set('auth.lockout.password_attempts', 5);
        $this->assertEquals(config('auth.lockout.password_attempts'), PasswordLockoutPolicy::threshold());

        config()->set('auth.lockout.password_attempts', 10);
        $this->assertEquals(config('auth.lockout.password_attempts'), PasswordLockoutPolicy::threshold());
    }

    /**
     * expiryCutoff should always return the larger of the two expiry cutoffs
     */
    public function testExpiryCutoff(): void
    {
        // static now for time tests
        Carbon::setTestNow(now());

        config()->set('auth.lockout.max_login_throttle', 5);
        config()->set('auth.lockout.password_attempts_expiry', 15);
        $this->assertEquals(now()->subMinutes(15), PasswordLockoutPolicy::expiryCutoff());

        config()->set('auth.lockout.max_login_throttle', 35);
        config()->set('auth.lockout.password_attempts_expiry', 30);
        $this->assertEquals(now()->subMinutes(35), PasswordLockoutPolicy::expiryCutoff());
    }

    public function testNoLoginUntil(): void
    {
        // static now for time tests
        Carbon::setTestNow(now());

        config()->set('auth.lockout.password_attempts', 3);

        $this->assertNull(PasswordLockoutPolicy::noLoginUntil(0));
        $this->assertNull(PasswordLockoutPolicy::noLoginUntil(1));
        $this->assertNull(PasswordLockoutPolicy::noLoginUntil(2));
        $this->assertEquals(now()->addMinute(), PasswordLockoutPolicy::noLoginUntil(3));
        $this->assertEquals(now()->addMinutes(2), PasswordLockoutPolicy::noLoginUntil(4));
        $this->assertEquals(now()->addMinutes(3), PasswordLockoutPolicy::noLoginUntil(5));
    }
}