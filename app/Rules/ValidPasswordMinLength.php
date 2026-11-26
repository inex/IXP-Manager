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

namespace IXP\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use IXP\Models\User;
use Override;

/**
 * This validation rule requires the minimum length to be 15, unless:2FA is switched on
 * and active for all user levels.
 *
 * This can happen because config allows it, or the request is switching them on.
 */
class ValidPasswordMinLength implements ValidationRule, DataAwareRule
{
    const int FLOOR_WITH_2FA    = 8;
    const int FLOOR_WITHOUT_2FA = 15;

    /**
     * All the data under validation.
     */
    private array $data = [];

    #[Override]
    public function setData( array $data ): static
    {
        $this->data = $data;
        return $this;
    }

    #[Override]
    public function validate( string $attribute, mixed $value, Closure $fail ): void
    {
        if( $value === null || $value === '' ) {
            return;
        }

        $length = (int) $value;
        $is2FallUsersHave2FA = $this->is2faEnabled() && $this->is2faActiveForEveryone();
        $minAllowed = $this->getLowerLimit( $is2FallUsersHave2FA );

        if( $length < $minAllowed ) {
            $fail( $is2FallUsersHave2FA
                    ? "The :attribute must be at least {$minAllowed} characters."
                    : "The :attribute must be at least {$minAllowed} characters unless 2FA is on for all users."
            );
        }
    }

    /**
     * Determine if 2FA is enabled by taking the setting in the request if present, otherwise using config
     */
    private function is2faEnabled(): bool
    {
        return array_key_exists( '2fa_enabled', $this->data )
            ? filter_var( $this->data[ '2fa_enabled' ], FILTER_VALIDATE_BOOLEAN )
            : config( 'google2fa.enabled' );
    }

    /**
     * Determine if 2FA is enabled for everyone by taking the setting in the request if present, otherwise using config
     */
    private function is2faActiveForEveryone(): bool
    {
        $mandatory2FAPrivs = array_key_exists( 'ixpm_2fa_enforce_for_users', $this->data )
            ? filter_var( $this->data[ 'ixpm_2fa_enforce_for_users' ], FILTER_VALIDATE_INT )
            : config( 'google2fa.ixpm_2fa_enforce_for_users' );
        return $mandatory2FAPrivs === User::AUTH_CUSTUSER;
    }

    /**
     * Get the lower limit for the password length depending on whether 2FA is enabled.
     */
    private function getLowerLimit( bool $allUsersHave2FA ): int
    {
        if( $allUsersHave2FA ) {
            return self::FLOOR_WITH_2FA;
        }
        return self::FLOOR_WITHOUT_2FA;
    }
}