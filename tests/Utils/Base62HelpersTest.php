<?php

namespace Tests\Utils;

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

use Tests\TestCase;

class Base62HelpersTest extends TestCase
{
    public function testBase62EncodeKnownValuesAndDecodeRoundTrip(): void
    {
        $known = [
            0      => '000000',
            1      => '000001',
            61     => '00000Z',
            62     => '000010',
            3843   => '0000ZZ',
            238327 => '000ZZZ',
        ];

        foreach( $known as $int => $base62 ) {
            $this->assertEquals( $base62, base62_encode( $int ) );
        }

        foreach( [ 0, 1, 10, 61, 62, 12345, 999999 ] as $int ) {
            $this->assertEquals( $int, base62_decode( base62_encode( $int ) ) );
        }
    }
}
