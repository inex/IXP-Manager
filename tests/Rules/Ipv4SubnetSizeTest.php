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
use IXP\Rules\Ipv4SubnetSize;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class Ipv4SubnetSizeTest extends TestCase
{

    public static function acceptedInput(): array
    {
        return [
            [24, '192.0.2.0/24'],
            [24, '192.0.2.0/23'],
            [24, '192.0.2.0/0'],
            [0, '192.0.2.0/0'],
        ];
    }

    public static function rejectedInput(): array
    {
        return [
            # missing /
            [24, '192.0.2.0/25'],
            [22, '192.0.2.0/23'],
            [20, '192.0.2.0/21'],
            [20, '192.0.2.0/'],
            [20, '192.0.2.0/a'],
        ];
    }

    #[DataProvider('rejectedInput')]
    public function testRejected(int $minSubnetSize, string $input)
    {
        config()->set('ixp.irrdb.min_v4_subnet_size', $minSubnetSize);

        $rule = new Ipv4SubnetSize();
        $this->assertFalse($rule->passes('', $input));
    }

    #[DataProvider('acceptedInput')]
    public function testAccepted(int $minSubnetSize, string $input)
    {
        config()->set('ixp.irrdb.min_v4_subnet_size', $minSubnetSize);

        $rule = new Ipv4SubnetSize();
        $this->assertTrue($rule->passes('', $input));
    }

    public function testMessage(): void
    {
        $rule = new Ipv4SubnetSize();

        config()->set('ixp.irrdb.min_v4_subnet_size', 10);
        $this->assertEquals('Invalid subnet, must be minimum 10', $rule->message());

        config()->set('ixp.irrdb.min_v4_subnet_size', 19);
        $this->assertEquals('Invalid subnet, must be minimum 19', $rule->message());
    }
}