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

use IXP\Rules\IPv4Cidr;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class IPv4CidrTest extends TestCase
{

    public static function acceptedInput(): array
    {
        return [
            ['192.0.2.0/24'],
            ['192.0.2.0/32'],
            ['192.0.2.0/0'],
        ];
    }

    public static function rejectedInput(): array
    {
        return [
            # missing /
            [''],
            ['192.0.2.0'],

            # one separator means two parts
            ['192.0.2.0/2/2'],

            # mask is an int
            ['192.0.2.0/'],
            ['192.0.2.0/a'],
            ['192.0.2.0/2a'],

            # mask between 0 and 32
            ['192.0.2.0/-1'],
            ['192.0.2.0/33'],

            # an ipv4 address
            ['catsanddogs/32'],
            ['00000/32'],

            ['fd12:3456:7890:1::1/32'],

            # unusual or non-standard ips
            ['0177.0.0.1/24'],
            ['0x7f000001/24'],
            ['127.0.0.0x1/24'],
            ['::ffff:192.168.1.1/24'],
        ];
    }

    #[DataProvider('rejectedInput')]
    public function testRejected(string $input)
    {
        $rule = new IPv4Cidr();
        $this->assertFalse($rule->passes('', $input));
    }

    #[DataProvider('acceptedInput')]
    public function testAccepted(string $input)
    {
        $rule = new IPv4Cidr();
        $this->assertTrue($rule->passes('', $input));
    }

    public function testMessage()
    {
        $rule = new IPv4Cidr();
        $this->assertEquals('Invalid IPv4 address in CIDR format (e.g. 192.0.2.0/24).', $rule->message());
    }
}