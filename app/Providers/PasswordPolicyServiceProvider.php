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

namespace IXP\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\NotPwnedVerifier;
use Illuminate\Validation\Rules\Password;
use Override;

/**
 * This class initializes the password policy for the application based on application configuration.
 * By default, each policy has a minimum length, a maximum length.
 * The Not Compromised rule is recommended by NIST as of 2025 guidelines.
 * Composition requirements such as requiring numbers/letters/mixed case/symbols are no longer recommended by NIST as of 2025.
 */
class PasswordPolicyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Password::defaults( function (): Password {
            return Password::min(config('password_policy.minimum_length'))
                ->max(config('password_policy.maximum_length'))
                ->when(config('password_policy.require_numbers'),    fn( Password $policy ) => $policy->numbers()       )
                ->when(config('password_policy.require_mixed_case'), fn( Password $policy ) => $policy->mixedCase()     )
                ->when(config('password_policy.require_symbols'),    fn( Password $policy ) => $policy->symbols()       )
                ->when(config('password_policy.not_compromised'),    fn( Password $policy ) => $policy->uncompromised() )
            ;
        } );
    }

    #[Override]
    public function register(): void
    {
        $this->app->extend( UncompromisedVerifier::class, function( UncompromisedVerifier $verifier, Application $app ): NotPwnedVerifier {
            return new NotPwnedVerifier( $app[ HttpFactory::class ], 10 );
        } );
    }
}