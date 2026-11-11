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

namespace Tests\Api;

use IXP\Models\Asn;
use IXP\Services\PeeringDb;
use IXP\Utils\Whois\Whois;
use IXP\Utils\Whois\WhoisResolver;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class WhoisControllerTest extends TestCase
{
    public function testAsnLookup(): void
    {
        Asn::create(['asn' => 2128, 'name' => 'INEX Internet Neutral Exchange Association Company Limited By Guarantee, IE', 'class' => 'Unknown', 'country_code' => 'IE']);

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/aut-num/2128' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $expecting  = "Number  : 2128\n";
        $expecting .= "Name    : INEX Internet Neutral Exchange Association Company Limited By Guarantee, IE\n";
        $expecting .= "Class:  : Unknown\n";
        $expecting .= "Country : IE\n";
        $this->assertEquals($expecting, $response->getContent());
    }

    public function testAsnNotFound(): void
    {
        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/aut-num/99999999' );
        $response->assertStatus( 404 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $this->assertEquals("ASN not found in store", $response->getContent());
    }

    public function testPrefix(): void
    {
        $this->mock(WhoisResolver::class, function (MockInterface $mock) {
            $mock->expects('get')->with('prefix')
                ->andReturn(
                    Mockery::mock(Whois::class, function (MockInterface $mock) {
                        $mock->expects('whois')->with('8.8.8.8/32')
                            ->andReturn(file_get_contents('data/ci/known-good/whois-prefix.8.8.8.8.txt'));
                    })
                );
        });

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/prefix-whois/8.8.8.8/32' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $this->assertEquals(file_get_contents('data/ci/known-good/whois-prefix.8.8.8.8.txt'), $response->getContent());
    }

    public function testPrefixWithoutMask(): void
    {
        $this->mock(WhoisResolver::class, function (MockInterface $mock) {
            $mock->expects('get')->with('prefix')
                ->andReturn(
                    Mockery::mock(Whois::class, function (MockInterface $mock) {
                        $mock->expects('whois')->with('8.8.8.8')
                            ->andReturn(file_get_contents('data/ci/known-good/whois-prefix.8.8.8.8.txt'));
                    })
                );
        });

        $this->post(  "login", [
            "username" => "travis",
            "password" => "travisci",
        ] );

        $response = $this->get( '/api/v4/prefix-whois/8.8.8.8' );
        $response->assertStatus( 200 );
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $this->assertEquals(file_get_contents('data/ci/known-good/whois-prefix.8.8.8.8.txt'), $response->getContent());
    }

}