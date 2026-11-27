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

namespace Tests\Rules;

use Illuminate\Foundation\Testing\TestCase;
use IXP\Models\User;
use IXP\Rules\ValidPasswordMinLength;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestUtils\ValidationErrorLogger;

class ValidPasswordMinLengthTest extends TestCase
{
    const string MESSAGE_2FA_ENABLED = 'The :attribute must be at least :value characters.';
    const string MESSAGE_2FA_DISABLED = 'The :attribute must be at least 15 characters unless 2FA is on for all users.';

    public static function dataProviderMinPasswordLength(): array
    {
        return [
            // Enabled in config, not present in request
            'on in config, right priv, no request data'          => ['twoFactorEnabled' => true, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => null, 'twoFactorRequestPrivLevel' => null, 'mandatoryPasswordLength' => 8, 'validationMessage' => self::MESSAGE_2FA_ENABLED],
            'on in config, wrong priv 1, no request data'        => ['twoFactorEnabled' => true, 'twoFactorPrivLevel' => User::AUTH_CUSTADMIN, 'twoFactorRequestEnabled' => null, 'twoFactorRequestPrivLevel' => null, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'on in config, wrong priv 2, no request data'        => ['twoFactorEnabled' => true, 'twoFactorPrivLevel' => User::AUTH_SUPERUSER, 'twoFactorRequestEnabled' => null, 'twoFactorRequestPrivLevel' => null, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // Enabled in config, disabled in request
            'on in config, right priv, off in request'           => ['twoFactorEnabled' => true, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => false, 'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTUSER,  'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'on in config, wrong priv 1, off in request'         => ['twoFactorEnabled' => true, 'twoFactorPrivLevel' => User::AUTH_CUSTADMIN, 'twoFactorRequestEnabled' => false, 'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTADMIN, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'on in config, wrong priv 2, off in request'         => ['twoFactorEnabled' => true, 'twoFactorPrivLevel' => User::AUTH_SUPERUSER, 'twoFactorRequestEnabled' => false, 'twoFactorRequestPrivLevel' => (string) User::AUTH_SUPERUSER, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // Disabled in config, not present in request
            'off in config, off in request'                      => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER, 'twoFactorRequestEnabled' => null, 'twoFactorRequestPrivLevel' => null, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // Disabled in config, disabled in request
            'off in config, off in request, right priv'          => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => false, 'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTUSER,  'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'off in config, off in request, wrong priv 1'        => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => false, 'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTADMIN, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'off in config, off in request, wrong priv 2'        => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => false, 'twoFactorRequestPrivLevel' => (string) User::AUTH_SUPERUSER, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // Disabled in config, enabled in request
            'off in config, on in request, right priv'           => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => true, 'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTUSER,  'mandatoryPasswordLength' => 8,  'validationMessage' => self::MESSAGE_2FA_ENABLED],
            'off in config, on in request, wrong priv 1'         => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => true, 'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTADMIN, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'off in config, on in request, wrong priv 2'         => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => true, 'twoFactorRequestPrivLevel' => (string) User::AUTH_SUPERUSER, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // 4 is a valid setting from the UI - check that it gives the 15 character limit
            'on in request, disable priv, no request data'       => ['twoFactorEnabled' => true,  'twoFactorPrivLevel' => 4,                    'twoFactorRequestEnabled' => null, 'twoFactorRequestPrivLevel' => null,                           'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'off in request, on in request, disable priv'        => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => true, 'twoFactorRequestPrivLevel' => (string) 4,                     'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // Empty privilege level in request (allowed by required_if_accepted when 2FA is off; must fail safe when on)
            'on in request, empty privilege level in request'    => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => false, 'twoFactorRequestPrivLevel' => '',                            'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'on in config, empty privilege level in request'     => ['twoFactorEnabled' => true,  'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => true,  'twoFactorRequestPrivLevel' => '',                            'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // Invalid privilege level in request (rejected by the 'in' rule, but must fail safe here)
            'on in config, invalid privilege level in request 1' => ['twoFactorEnabled' => true,  'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => true,  'twoFactorRequestPrivLevel' => 'abc',                         'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'on in config, invalid privilege level in request 2' => ['twoFactorEnabled' => true,  'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => true,  'twoFactorRequestPrivLevel' => '1.5',                         'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // Only 2fa_enabled in request: privilege level falls back to config
            'on in request, right priv in config'                => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => true,  'twoFactorRequestPrivLevel' => null,                          'mandatoryPasswordLength' => 8,  'validationMessage' => self::MESSAGE_2FA_ENABLED],
            'on in request, wrong priv 1 in config'              => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTADMIN, 'twoFactorRequestEnabled' => true,  'twoFactorRequestPrivLevel' => null,                          'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'on in request, wrong priv 2 in config'              => ['twoFactorEnabled' => true,  'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => false, 'twoFactorRequestPrivLevel' => null,                          'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],

            // Only privilege level in request: 2fa_enabled falls back to config
            'on in config, right priv in request'                => ['twoFactorEnabled' => true,  'twoFactorPrivLevel' => User::AUTH_CUSTADMIN, 'twoFactorRequestEnabled' => null,  'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTUSER,  'mandatoryPasswordLength' => 8,  'validationMessage' => self::MESSAGE_2FA_ENABLED],
            'on in config, wrong priv in request'                => ['twoFactorEnabled' => true,  'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => null,  'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTADMIN, 'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
            'off in request, right priv in config'               => ['twoFactorEnabled' => false, 'twoFactorPrivLevel' => User::AUTH_CUSTUSER,  'twoFactorRequestEnabled' => null,  'twoFactorRequestPrivLevel' => (string) User::AUTH_CUSTUSER,  'mandatoryPasswordLength' => 15, 'validationMessage' => self::MESSAGE_2FA_DISABLED],
        ];
    }

    #[DataProvider('dataProviderMinPasswordLength')]
    public function testMinLengthData(bool $twoFactorEnabled, int $twoFactorPrivLevel, ?bool $twoFactorRequestEnabled, ?string $twoFactorRequestPrivLevel, int $mandatoryPasswordLength, string $validationMessage): void
    {
        $rule = new ValidPasswordMinLength();

        // Apply config settings, ie, simulating .env settings
        config()->set('google2fa.enabled', $twoFactorEnabled);
        config()->set('google2fa.ixpm_2fa_enforce_for_users', $twoFactorPrivLevel);

        // build data to be validated.
        $requestData = [];
        if (!is_null($twoFactorRequestEnabled)) {
            $requestData['2fa_enabled'] = $twoFactorRequestEnabled ? '1' : '0';
        }
        if (!is_null($twoFactorRequestPrivLevel)) {
            $requestData['ixpm_2fa_enforce_for_users'] = $twoFactorRequestPrivLevel;
        }
        $rule->setData($requestData);

        $rule->validate('', $mandatoryPasswordLength - 1, ($fails = new ValidationErrorLogger)(...));
        $this->assertSame( [ str_replace(":value", (string) $mandatoryPasswordLength, $validationMessage) ], $fails->fails);

        $rule->validate('', $mandatoryPasswordLength, ($fails = new ValidationErrorLogger)(...));
        $this->assertEmpty($fails->fails);

        $rule->validate('', $mandatoryPasswordLength+1, ($fails = new ValidationErrorLogger)(...));
        $this->assertEmpty($fails->fails);

        // form submissions are strings
        $rule->validate('', (string) ($mandatoryPasswordLength - 1), ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);

        $rule->validate('', (string) $mandatoryPasswordLength, ($fails = new ValidationErrorLogger)(...));
        $this->assertEmpty($fails->fails);
    }

    public static function dataProviderEmptyValues(): array
    {
        return [ 'null' => [ null ], 'empty string' => [ '' ] ];
    }

    /**
     * Empty values are left to 'nullable' / 'required'; the rule must not fail them.
     */
    #[DataProvider('dataProviderEmptyValues')]
    public function testEmptyValueIsIgnored( ?string $value ): void
    {
        config()->set('google2fa.enabled', false);
        config()->set('google2fa.ixpm_2fa_enforce_for_users', User::AUTH_CUSTUSER);

        $rule = ( new ValidPasswordMinLength() )->setData( [] );
        $rule->validate('', $value, ($fails = new ValidationErrorLogger)(...));
        $this->assertEmpty($fails->fails);
    }
}