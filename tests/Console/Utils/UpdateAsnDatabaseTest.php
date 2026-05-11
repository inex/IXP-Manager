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

namespace Tests\Console\Utils;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use IXP\Exceptions\GeneralException;
use IXP\Models\Asn;
use IXP\Utils\BgpTools\FileFetcher;
use Tests\TestCase;

/**
 * UpdateAsnDatabaseTest
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class UpdateAsnDatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Asn::truncate();
        parent::tearDown();
    }

    public function testFreshFromFile()
    {
        // let's ensure we're starting with a clean slate
        Asn::truncate();
        
        
        $this->assertEquals(0, Asn::count());
        $this->artisan('utils:asn-update -v ' . $this->getSampleFilePath())
            ->assertExitCode(0)
            ->expectsOutput('.')
        ;
        $this->assertEquals(4, Asn::count());

        $asn10 = Asn::where('asn', '=', 10)->first();
        $this->assertEquals( "CSNET Coordination and Information Center (CSNET-CIC)", $asn10->name);
        $this->assertEquals( "Unknown", $asn10->class);
        $this->assertEquals( "US", $asn10->country_code);

        $asn100 = Asn::find(100);
        $this->assertEquals( "FMC Central Engineering Laboratories", $asn100->name);
        $this->assertEquals( "Unknown", $asn100->class);
        $this->assertEquals( "US", $asn100->country_code);

        $asn10000 = Asn::find(10000);
        $this->assertEquals( "Nagasaki Cable Media Inc.", $asn10000->name);
        $this->assertEquals( "Eyeball", $asn10000->class);
        $this->assertEquals( "JP", $asn10000->country_code);
    }

    public function testUnknownFile()
    {
        $this->assertEquals(0, Asn::count());
        $this->artisan('utils:asn-update /some/unknown/file')
            ->assertExitCode(1)
            ->expectsOutputToContain('File does not exist, or is not a file');
        $this->assertEquals(0, Asn::count());
    }

    public function testNotAFile()
    {
        $this->assertEquals(0, Asn::count());
        $this->artisan('utils:asn-update /')
            ->assertExitCode(1)
            ->expectsOutputToContain('File is not a regular file');
        $this->assertEquals(0, Asn::count());
    }


    public function testFreshFromHttpDefault()
    {
        Http::fake([
            'https://bgp.tools/asns.csv' => $this->getSampleAsnCsv(),
        ]);

        $this->assertEquals(0, Asn::count());
        $this->artisan('utils:asn-update')->assertExitCode(0);
        $this->assertEquals(4, Asn::count());

        $asn10 = Asn::where('asn', '=', 10)->first();
        $this->assertEquals( "CSNET Coordination and Information Center (CSNET-CIC)", $asn10->name);
        $this->assertEquals( "Unknown", $asn10->class);
        $this->assertEquals( "US", $asn10->country_code);

        $asn100 = Asn::find(100);
        $this->assertEquals( "FMC Central Engineering Laboratories", $asn100->name);
        $this->assertEquals( "Unknown", $asn100->class);
        $this->assertEquals( "US", $asn100->country_code);

        $asn10000 = Asn::find(10000);
        $this->assertEquals( "Nagasaki Cable Media Inc.", $asn10000->name);
        $this->assertEquals( "Eyeball", $asn10000->class);
        $this->assertEquals( "JP", $asn10000->country_code);
    }

    public function testFreshFromHttp()
    {
        Http::fake([
            'https://some.mirror/asns.csv' => $this->getSampleAsnCsv(),
        ]);

        $this->assertEquals(0, Asn::count());
        $this->artisan('utils:asn-update https://some.mirror/asns.csv')->assertExitCode(0);
        $this->assertEquals(4, Asn::count());

        $asn10 = Asn::where('asn', '=', 10)->first();
        $this->assertEquals( "CSNET Coordination and Information Center (CSNET-CIC)", $asn10->name);
        $this->assertEquals( "Unknown", $asn10->class);
        $this->assertEquals( "US", $asn10->country_code);

        $asn100 = Asn::find(100);
        $this->assertEquals( "FMC Central Engineering Laboratories", $asn100->name);
        $this->assertEquals( "Unknown", $asn100->class);
        $this->assertEquals( "US", $asn100->country_code);

        $asn10000 = Asn::find(10000);
        $this->assertEquals( "Nagasaki Cable Media Inc.", $asn10000->name);
        $this->assertEquals( "Eyeball", $asn10000->class);
        $this->assertEquals( "JP", $asn10000->country_code);
    }


    public function testDeletesOldContents()
    {
        Asn::create(['asn' => 2128, 'name' => 'INEX Internet Neutral Exchange Association Company Limited By Guarantee, IE', 'class' => 'Unknown', 'country_code' => 'IE']);

        $this->assertEquals(1, Asn::count());

        $this->artisan('utils:asn-update ' . $this->getSampleFilePath())->assertExitCode(0);
        $this->assertEquals(4, Asn::count());
        $this->assertNull(Asn::find(2128));
    }

    public function testConnectionException()
    {
        $this->mock(FileFetcher::class, function ($mock) {
            $mock->expects('fetch')->andThrow(new ConnectionException('Failed to resolve IPV4 address for URI'));
        });

        $this->artisan('utils:asn-update https://bgp.tools/asns.csv')
            ->assertExitCode(1)
            ->expectsOutputToContain('Connection failed: Failed to resolve IPV4 address for URI');
    }

    public function testGeneralException()
    {
        $this->mock(FileFetcher::class, function ($mock) {
            $mock->expects('fetch')->andThrow(new GeneralException('HTTP error [404] while fetching file:'));
        });
        $this->artisan('utils:asn-update https://bgp.tools/asns.csv')
            ->assertExitCode(1)
            ->expectsOutputToContain('HTTP error [404] while fetching file:');
    }

    private function getSampleAsnCsv(): string
    {
        return file_get_contents(base_path('/data/ci/known-good/sample-asns.csv'));
    }

    private function getSampleFilePath(): string
    {
        return base_path('/data/ci/known-good/sample-asns.csv');
    }
}