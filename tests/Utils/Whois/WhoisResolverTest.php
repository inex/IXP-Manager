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

use IXP\Exceptions\Utils\Whois\WhoisException;
use IXP\Utils\Whois\Whois;
use IXP\Utils\Whois\WhoisResolver;
use Illuminate\Foundation\Testing\TestCase;

class WhoisResolverTest extends TestCase
{
    public function testGetUnknown()
    {
        $resolver = new WhoisResolver();
        $this->expectException(WhoisException::class);
        $this->expectExceptionMessage("Configuration not found for whois server 'unknown'");
        $resolver->get('unknown');
    }

    public function testGet()
    {
        $resolver = new WhoisResolver();
        $whois = $resolver->get('asn');
        $this->assertEquals(config("ixp_api.whois.asn.host"), $whois->host());

        $whois = $resolver->get('asn2');
        $this->assertEquals(config("ixp_api.whois.asn2.host"), $whois->host());

        $whois = $resolver->get('prefix');
        $this->assertEquals(config("ixp_api.whois.prefix.host"), $whois->host());
    }
}