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

namespace Tests\Utils\Whois;

use IXP\Utils\Whois\WhoisResolver;
use Illuminate\Foundation\Testing\TestCase;

class WhoisLiveTest extends TestCase
{
    public function testPrefixWithMask()
    {
        $whois = app(WhoisResolver::class)->get('prefix');
        $result = $whois->whois('8.8.8.8/32');
        $this->assertStringContainsString('route:          8.8.8.0/24', $result);
        $this->assertStringContainsString('origin:         AS15169',    $result);
        $this->assertStringContainsString('descr:          Google',     $result);
    }

    public function testPrefixWithoutMask()
    {
        $whois = app(WhoisResolver::class)->get('prefix');

        $result = $whois->whois('8.8.8.8');
        $this->assertStringContainsString('route:          8.8.8.0/24', $result);
        $this->assertStringContainsString('origin:         AS15169',    $result);
        $this->assertStringContainsString('descr:          Google',     $result);
    }

    public function testPrefixWithoutMaskTrailingSlash()
    {
        // This test shows what happens when a trailing slash is left on, when network mask is omitted.
        $whois = app(WhoisResolver::class)->get('prefix');
        $result = $whois->whois('8.8.8.8/');
        $this->assertEquals("%  No entries found for the selected source(s).\n\n\n", $result);
    }

    public function testAsn()
    {
        $whois = app(WhoisResolver::class)->get('asn2');
        $result = $whois->whois('AS2128');
        $this->assertEquals("AS Name\nINEX Internet Neutral Exchange Association Company Limited By Guarantee, IE\n", $result);
    }
}