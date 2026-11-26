<?php
/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace Tests\Providers;

use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\NotPwnedVerifier;
use Illuminate\Validation\Rules\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Trait\SetsPasswordPolicy;

class PasswordPolicyProviderTest extends TestCase
{
    use SetsPasswordPolicy;

    public function testUncompromisedVerifierHasCustomTimeout(): void
    {
        $verifier = app(UncompromisedVerifier::class);
        $this->assertInstanceOf(NotPwnedVerifier::class, $verifier);
        $reflectionClass = new \ReflectionClass($verifier);
        $this->assertEquals(10, $reflectionClass->getProperty("timeout")->getValue($verifier));
    }

    public function testUncompromisedVerifierWorks(): void
    {
        $this->setPasswordPolicy( notCompromised: true );

        $validator = $this->validate("password");
        $this->assertTrue($validator->fails());
        $this->assertContains(
            "The given password has appeared in a data leak. Please choose a different password.",
            $validator->errors()->get("password")
        );
    }

    public function testUncompromisedVerifierFailsOpen(): void
    {
        Http::fake( fn() => throw new ConnectionException( 'timeout' ) );
        $this->setPasswordPolicy( notCompromised: true );
        $validator = $this->validate("password");
        $this->assertFalse($validator->fails());
        $this->assertEmpty($validator->errors()->get("password"));
    }

    public function testPasswordPolicyProviderIsRegistered(): void
    {
        $this->assertNotNull(Password::$defaultCallback);
    }

    public function testPolicyIsConfigDriven(): void
    {
        $tooShort = $this
            ->setPasswordPolicy( minLength: 12, maxLength: 72 )
            ->validate('Abcdef1!');
        $this->assertTrue($tooShort->fails());
        $this->assertStringContainsString('The password must be at least 12 characters.', $tooShort->errors()->first('password'));

        $tooLong = $this
            ->setPasswordPolicy( minLength: 12, maxLength: 72 )
            ->validate(str_repeat('A', 73));
        $this->assertTrue($tooLong->fails());
        $this->assertStringContainsString('The password must not be greater than 72 characters.', $tooLong->errors()->first('password'));

        $missingMixedCase = $this
            ->setPasswordPolicy( minLength: 12, maxLength: 72, requireMixedCase: true )
            ->validate(str_repeat('A', 13));
        $this->assertTrue($missingMixedCase->fails());
        $this->assertStringContainsString('The password must contain at least one uppercase and one lowercase letter.', $missingMixedCase->errors()->first('password'));

        $missingNumbers = $this
            ->setPasswordPolicy( minLength: 12, maxLength: 72, requireMixedCase: false, requireNumbers: true )
            ->validate(str_repeat('A', 13));
        $this->assertTrue($missingNumbers->fails());
        $this->assertStringContainsString('The password must contain at least one number.', $missingNumbers->errors()->first('password'));

        $missingSymbols = $this
            ->setPasswordPolicy( minLength: 12, maxLength: 72, requireMixedCase: false, requireNumbers: false, requireSymbols: true )
            ->validate(str_repeat('A', 13));
        $this->assertTrue($missingSymbols->fails());
        $this->assertStringContainsString('The password must contain at least one symbol.', $missingSymbols->errors()->first('password'));

        $this->setPasswordPolicy( minLength: 12, maxLength: 72, requireMixedCase: true, requireNumbers: true, requireSymbols: true );
        $onlyMissingMixedCase = $this->validate("12345678901#");
        $this->assertTrue($onlyMissingMixedCase->fails());
        $this->assertStringContainsString('The password must contain at least one uppercase and one lowercase letter.', $onlyMissingMixedCase->errors()->first('password'));

        $onlyMissingSymbols = $this->validate("1234567890Aa");
        $this->assertTrue($onlyMissingSymbols->fails());
        $this->assertStringContainsString('The password must contain at least one symbol.', $onlyMissingSymbols->errors()->first('password'));

        $passes = $this->validate("123456789#Aa");
        $this->assertFalse($passes->fails());
    }

    private function validate(string $password): Validator
    {
        return ValidatorFacade::make(
            ['password' => $password],
            ['password' => ['required', Password::default()]]
        );
    }
}