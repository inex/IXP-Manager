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

namespace Http\Controllers\Api\V4;

use IXP\Models\IrrdbConfig;
use Tests\TestCase;

class IrrdbConfigControllerTest extends TestCase
{

    public function testStoreSourceRequired()
    {
        $this->actingAs( $this->getSuperUser() );

        // Only required field is missing
        $response = $this
            ->withHeader( 'X-Requested-With', 'XMLHttpRequest' )
            ->withHeader( 'Accept', '*/*' )
            ->post( route( "irrdb-config-api@create", [] ) )
            ->assertStatus( 422 );
        $this->assertEquals( "The source field is required.", $response->json( 'message' ) );
        $this->assertEquals( "The source field is required.", $response->json( 'errors.source.0' ) );

        // Provide only required field, succeeds
        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withHeader('Accept', '*/*')
            ->post(route("irrdb-config-api@create", [
                "source" => "TESTSOURCE"
            ]))
            ->assertStatus(200);
        $this->assertArrayHasKey("id", $response->json());

        $config = IrrdbConfig::findOrFail( $response->json( "id" ) );
        $this->assertEquals("TESTSOURCE",     $config->source);
        $this->assertEquals("whois.radb.net", $config->host);
        $this->assertNull($config->notes);

        $config->delete();

        // Can also set host and notes, and accepts - and numbers in source
        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withHeader('Accept', '*/*')
            ->post(route("irrdb-config-api@create", [
                "source" => "TESTSOURCE-222",
                "host"   => "whois.whatis.net",
                "notes"  => "got my notes",
            ]))
            ->assertStatus(200);
        $this->assertArrayHasKey("id", $response->json());

        $config = IrrdbConfig::findOrFail( $response->json( "id" ) );
        $this->assertEquals("TESTSOURCE-222",       $config->source);
        $this->assertEquals("whois.whatis.net", $config->host);
        $this->assertEquals("got my notes",     $config->notes);

        $config->delete();

        // source must be capitalized
        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withHeader('Accept', '*/*')
            ->post(route("irrdb-config-api@create", [
                "source" => "testsource",
                "host"   => "whois.whatis.net",
                "notes"  => "got my notes",
            ]))
            ->assertStatus(422);
        $this->assertEquals( "The source must only contain uppercase letters and numbers and the - character.", $response->json( 'message' ) );
        $this->assertEquals( "The source must only contain uppercase letters and numbers and the - character.", $response->json( 'errors.source.0' ) );
    }
}