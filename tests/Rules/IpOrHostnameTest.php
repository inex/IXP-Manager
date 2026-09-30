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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests\Rules;

use IXP\Rules\IpOrHostname;
use Tests\LiteTestCase;
use Tests\TestUtils\ValidationErrorLogger;

class IpOrHostnameTest extends LiteTestCase
{
    public function testIpv4(): void
    {
        $rule = new IpOrHostname();
        // local
        $rule->validate( "", "127.0.0.1", ( $fails = new ValidationErrorLogger )( ... ) );
        $this->assertCount( 0, $fails->fails );

        // public
        $rule->validate( "", "8.8.8.8", ( $fails = new ValidationErrorLogger )( ... ) );
        $this->assertCount( 0, $fails->fails );

        // private
        $rule->validate( "", "10.0.2.2", ( $fails = new ValidationErrorLogger )( ... ) );
        $this->assertCount( 0, $fails->fails );

        // invalid
        $rule->validate("", "192..0.1", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);
    }

    public function testIpv6(): void
    {
        $rule = new IpOrHostname();

        // local
        $rule->validate("", "0000:0000:0000:0000:0000:0000:0000:0001", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        // public (shorthand)
        $rule->validate("", "2001:4860:4860::8888", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        // private (long form)
        $rule->validate("", "fd12:3456:789a:0001:0000:0000:0000:0005", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        // invalid
        $rule->validate("", "ghij:3456:789a:0001:0000:0000:0000:0005", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

    }

    public function testIp(): void
    {
        $rule = new IpOrHostname();

        // rejects empty value
        $rule->validate("", "", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

        // control characters
        $rule->validate("", "hostname\".com", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

        $rule->validate("", "hostname\\.com", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

        $rule->validate("", "hostname\0.com", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

        $rule->validate("", "hostname\n.com", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

        $rule->validate("", "hostname\r.com", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

        // non-alphanum at start or end
        $rule->validate("", "-hostname.com", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

        $rule->validate("", "hostname-.com", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);

        // too long
        $rule->validate("", str_repeat("a", 64) . ".com", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals( "The :attribute must be a valid IP address or hostname.", $fails->fails[0]);
    }
}