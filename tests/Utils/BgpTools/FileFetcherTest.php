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

namespace Tests\Utils\BgpTools;

use Illuminate\Http\Client\Request;
use IXP\Exceptions\GeneralException;
use IXP\Utils\BgpTools\FileFetcher;
use Tests\TestCase;

/**
 * FileFetcherTest
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class FileFetcherTest extends TestCase
{
    public function testMissingFileError()
    {
        $fetcher = new FileFetcher();
        $this->expectException( GeneralException::class );
        $this->expectExceptionMessage( "Could not fetch file: /some/unknown/file" );

        // error suppression is required here or PHPUnit will capture the
        // warning emitted by file_get_contents and throw an \ErrorException
        @$fetcher->fetch( "/some/unknown/file" );
    }

    public function testFetch()
    {
        $fetcher = new FileFetcher();
        $data = $fetcher->fetch( $this->getSampleFilePath() );
        $this->assertEquals( $this->getSampleAsnCsv(), $data );
    }

    public function testFetchHttp()
    {
        \Http::fake(
            [ 'https://bgp.tools/asns.csv' => $this->getSampleAsnCsv() ]
        );

        $fetcher = new FileFetcher();
        $data = $fetcher->fetch( 'https://bgp.tools/asns.csv' );
        $this->assertEquals( $this->getSampleAsnCsv(), $data );

        // Check custom User Agent is sent per bgp.tools requirements
        \Http::assertSent(function ( Request $request ) {
            $this->assertCount( 1, $request->header('User-Agent') );
            $this->assertEquals( 'IXP-Manager', $request->header( 'User-Agent' )[0] );
            return 'IXP-Manager' === $request->header( 'User-Agent' )[0];
        });
    }

    public function testFetchHttp404()
    {
        \Http::fake( [
            'https://bgp.tools/asns.csv' => \Http::response('', 404)
        ] );

        $fetcher = new FileFetcher();
        $this->expectException( GeneralException::class );
        $this->expectExceptionMessage( "HTTP error [404] while fetching file: " );
        $fetcher->fetch( 'https://bgp.tools/asns.csv' );
    }

    private function getSampleFilePath(): string
    {
        return base_path( 'data/ci/known-good/sample-asns.csv' );
    }

    private function getSampleAsnCsv(): string
    {
        return file_get_contents( base_path( 'data/ci/known-good/sample-asns.csv' ) );
    }
}