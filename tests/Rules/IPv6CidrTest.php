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

use IXP\Rules\IPv6Cidr;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class IPv6CidrTest extends TestCase
{

    public static function acceptedInput(): array
    {
        return [
            ['fd12:3456:7890:1::1/24'],
            ['fd12:3456:7890:1::1/32'],
            ['fd12:3456:7890:1::1/128'],
            ['fd12:3456:7890:1::1/0'],
            ['2001:0db8:85a3:0000:0000:8a2e:0370:7334/0'],
            ['2001:db8:85a3::8a2e:370:7334/0'],
            ['::1/0'],
            ['::/0'],
        ];
    }

    public static function rejectedInput(): array
    {
        return [
            # missing /
            [''],
            ['fd12:3456:7890:1::1'],

            # one separator means two parts
            ['fd12:3456:7890:1::1/2/2'],

            # mask is an int
            ['fd12:3456:7890:1::1/'],
            ['fd12:3456:7890:1::1/a'],
            ['fd12:3456:7890:1::1/2a'],

            # mask between 0 and 128
            ['fd12:3456:7890:1::1/-1'],
            ['fd12:3456:7890:1::1/129'],

            # an ipv4 address
            ['catsanddogs/32'],
            ['00000/32'],

            ['192.0.2.0/32'],
            # unusual or nonstandard
            ['2001::85a3::7334/32'],
        ];
    }

    #[DataProvider('rejectedInput')]
    public function testRejected(string $input)
    {
        $rule = new IPv6Cidr();
        $this->assertFalse($rule->passes('', $input));
    }

    #[DataProvider('acceptedInput')]
    public function testAccepted(string $input)
    {
        $rule = new IPv6Cidr();
        $this->assertTrue($rule->passes('', $input));
    }

    public function testMessage()
    {
        $rule = new IPv6Cidr();
        $this->assertEquals('Invalid IPv6 address in CIDR format (e.g. 2001:db8:10::/48).', $rule->message());
    }
}