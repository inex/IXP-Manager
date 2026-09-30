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

use IXP\Rules\Ipv6SubnetSize;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\LiteTestCase;

class Ipv6SubnetSizeTest extends LiteTestCase
{

    public static function acceptedInput(): array
    {
        return [
            [24, 'fd12:3456:7890:1::1/24'],
            [24, 'fd12:3456:7890:1::1/23'],
            [24, 'fd12:3456:7890:1::1/0'],
            [0, 'fd12:3456:7890:1::1/0'],
        ];
    }

    public static function rejectedInput(): array
    {
        return [
            # missing /
            [24, 'fd12:3456:7890:1::1/25'],
            [22, 'fd12:3456:7890:1::1/23'],
            [20, 'fd12:3456:7890:1::1/21'],
            [20, 'fd12:3456:7890:1::1/'],
            [20, 'fd12:3456:7890:1::1/a'],
        ];
    }

    #[DataProvider('rejectedInput')]
    public function testRejected(int $minSubnetSize, string $input)
    {
        config()->set('ixp.irrdb.min_v6_subnet_size', $minSubnetSize);

        $rule = new Ipv6SubnetSize();
        $this->assertFalse($rule->passes('', $input));
    }

    #[DataProvider('acceptedInput')]
    public function testAccepted(int $minSubnetSize, string $input)
    {
        config()->set('ixp.irrdb.min_v6_subnet_size', $minSubnetSize);

        $rule = new Ipv6SubnetSize();
        $this->assertTrue($rule->passes('', $input));
    }

    public function testMessage(): void
    {
        $rule = new Ipv6SubnetSize();

        config()->set('ixp.irrdb.min_v6_subnet_size', 10);
        $this->assertEquals('Invalid subnet, must be minimum 10', $rule->message());

        config()->set('ixp.irrdb.min_v6_subnet_size', 19);
        $this->assertEquals('Invalid subnet, must be minimum 19', $rule->message());
    }
}