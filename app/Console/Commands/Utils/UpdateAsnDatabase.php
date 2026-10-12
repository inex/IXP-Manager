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

namespace IXP\Console\Commands\Utils;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use IXP\Console\Commands\Command as IXPCommand;
use IXP\Exceptions\GeneralException;
use IXP\Models\Asn;
use IXP\Utils\BgpTools\FileFetcher;
use IXP\Utils\BgpTools\CsvReader;

/**
 * UpdateAsnDatabase refreshes a local store of ASN information from the
 * BGPTools ASN database.
 */
class UpdateAsnDatabase extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "utils:asn-update
        {file? : HTTP file or file system path. Defaults to BGPTools ASN database.}
    ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the ASN database table.';

    /**
     * Location of the BGPTools ASN database.
     * @var string
     */
    private string $bgpToolsUrl = 'https://bgp.tools/asns.csv';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle( FileFetcher $fetcher, CsvReader $reader): int
    {
        /** @var string $file */
        $file = $this->argument( 'file' ) ?? $this->bgpToolsUrl;

        $this->checkFile( $file );

        try {
            $asnsGenerator = $reader->read( $fetcher->fetch( $file ), [ "asn", "name", "class", "cc" ] );

            DB::transaction(function() use ($asnsGenerator) {
                DB::table('asns')->delete();

                $counter = 0;
                foreach ( $asnsGenerator as [ $asn, $name, $class, $cc ] ) {
                    if ( $counter++ % 1000 === 0 && $this->isVerbosityVerbose() ) {
                        $this->output->write( '.' );
                    }

                    // Strip off 'AS' from the value
                    $asNumber = substr( $asn, 2 );

                    Asn::create( [
                        'asn'           => $asNumber,
                        'name'          => $name,
                        'class'         => $class,
                        'country_code'  => $cc,
                    ] );
                }

                if ( $this->isVerbosityVerbose() ) {
                    $this->isVerbosityVerbose() && $this->output->write( '.', newline: true );
                }
            });

        } catch (ConnectionException $e) {
            $this->fail( "Connection failed: " . $e->getMessage() );
        } catch (GeneralException $e) {
            $this->fail( $e->getMessage() );
        }

        return 0;
    }

    private function checkFile( string $file ): void
    {
        if ( !( str_starts_with( $file, 'http://' ) || str_starts_with( $file, 'https://' ) ) ) {
            // Sanity check path if it's not HTTP
            if( !file_exists( $file ) ) {
                $this->fail( "File does not exist, or is not a file" );
            } else if( !is_file( $file ) ) {
                $this->fail( "File is not a regular file" );
            }
        }
    }
}