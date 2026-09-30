<?php
/*
 * Copyright (C)lat 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Rules\IsCountry;
use Tests\LiteTestCase;
use Tests\TestUtils\ValidationErrorLogger;
use Webpatser\Countries\CountriesFacade;

class IsCountryTest extends LiteTestCase
{
    public function testRuleWithUnknownKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Provided key is not a unique country identifier: gibberish");
        new IsCountry("gibberish");
    }

    public function testRule(): void
    {
        // Test default which is iso_3166_2
        $rule = new IsCountry();
        $rule->validate("", "IE", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        $rule->validate(":country", "XX", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The selected :attribute is invalid", $fails->fails[0]);

        // Test iso_3166_2 explicitly
        $rule = new IsCountry("iso_3166_2");
        $rule->validate("", "IE", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        $rule->validate(":country", "XX", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The selected :attribute is invalid", $fails->fails[0]);

        // Test country-code
        $rule = new IsCountry("country-code");
        $rule->validate("", "270", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        $rule->validate("", "9999", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The selected :attribute is invalid", $fails->fails[0]);

        // Test name
        $rule = new IsCountry("name");
        $rule->validate("", "Guyana", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        $rule->validate("", "SomeUnknownCountry", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The selected :attribute is invalid", $fails->fails[0]);
    }
}