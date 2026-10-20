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
use IXP\Rules\IsPhone;
use Tests\TestUtils\ValidationErrorLogger;

class IsPhoneTest extends TestCase
{
    public function testRule()
    {
        $rule = new IsPhone();

        // Acceptable phone numbers with different use of chars: space, +, (, ), and -
        $rule->validate("", '+353 20 912 2000', ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        $rule->validate("", '+353 (0) 20 912 2000', ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        $rule->validate("", '020-912-2000', ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        $rule->validate("", '020.912.2000', ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        $rule->validate("", '0209122000', ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        // 7 digits is shortest allowed phone number
        $rule->validate("", "1234567", ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        // 25 digits is longest allowed phone number
        $rule->validate("", "0123456789012345678901234", ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        // we do accept nonsense symbols within the whitelist, as long as there is 4 digits
        $rule->validate("", '+() -..-1234567', ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        // unicode numbers allowed
        $rule->validate("", "+٢٠ ١٢ ٣٤٥٦ ٧٨٩", ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        $rule->validate("", "+۹۸ ۲۱ ۱۲۳۴ ۵۶۷۸", ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        $rule->validate("", "+९१ ९८७६५ ४३२१०", ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals([], $fails->fails);

        // failures:

        // pass something that isn't a string
        $rule->validate("", null, ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals(["The :attribute must be a valid phone number string"], $fails->fails);

        // not numbers
        $rule->validate("", "notallowed", ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals(["The :attribute format is invalid. Allowed characters besides digits are: +, -, (, ), ."], $fails->fails);

        // min 4 digits
        $rule->validate("", '+()-----()', ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals(["The :attribute must contain at least 7 digits."], $fails->fails);

        // the plus has to be at the start of the string
        $rule->validate("", "1234+", ($fails = new ValidationErrorLogger)(...));
        $this->assertEquals(["The :attribute format is invalid. Allowed characters besides digits are: +, -, (, ), ."], $fails->fails);
    }
}