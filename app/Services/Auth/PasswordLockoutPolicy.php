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

namespace IXP\Services\Auth;

use Carbon\CarbonInterface;

/**
 * Failed password attempt policy, derived from the auth.lockout config. Applies equally to real users
 * and to unknown usernames (UnknownUserLoginAttempt).
 */
final class PasswordLockoutPolicy
{
    /**
     * The number of failed password attempts at which logins are throttled.
     */
    public static function threshold(): int
    {
        return config( 'auth.lockout.password_attempts' );
    }

    /**
     * Failed password attempts older than this no longer count. The next failure starts again from one.
     *
     * The window is never shorter than the longest throttle, otherwise the count could expire while the
     * account is still throttled.
     */
    public static function expiryCutoff(): CarbonInterface
    {
        return now()->subMinutes( max(
            config( 'auth.lockout.password_attempts_expiry' ),
            config( 'auth.lockout.max_login_throttle' ),
        ) );
    }

    /**
     * The time until which logins are blocked after the given number of failed attempts, or null if
     * the threshold has not been reached. Each attempt past the threshold adds a minute, up to
     * max_login_throttle.
     */
    public static function noLoginUntil( int $attempts ): ?CarbonInterface
    {
        if( $attempts < self::threshold() ) {
            return null;
        }

        return now()->addMinutes( min(
            $attempts - self::threshold() + 1,
            config( 'auth.lockout.max_login_throttle' ),
        ) );
    }
}
