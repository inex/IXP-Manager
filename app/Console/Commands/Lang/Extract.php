<?php

namespace IXP\Console\Commands\Lang;

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

use IXP\Console\Commands\Command as IXPCommand;

use IXP\Utils\Lang\{
    Extractor,
    Scope
};

/**
 * Extract translatable strings from the source tree.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Extract extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:extract
        {--json= : write the extracted keys, with provenance, to this file as JSON}
        {--list : list every key found}
        {--dynamic : list call sites whose first argument is not a literal string}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find the translatable strings (__() / trans() / @lang()) in the source tree';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $files = Scope::files();
        $e     = ( new Extractor() )->scan( $files )->withConfiguredNouns();
        $keys  = $e->keys();

        if( $this->option( 'list' ) ) {
            foreach( $keys as $key => $sites ) {
                $this->line( sprintf( '%s   <fg=gray>%s:%d%s</>',
                    $key,
                    Scope::relative( $sites[ 0 ][ 'file' ] ),
                    $sites[ 0 ][ 'line' ],
                    count( $sites ) > 1 ? ' (+' . ( count( $sites ) - 1 ) . ' more)' : ''
                ) );
            }
        }

        if( $this->option( 'dynamic' ) ) {
            foreach( $e->dynamic() as $d ) {
                $this->line( sprintf( '<fg=yellow>%s:%d</> %s() with a non-literal key',
                    Scope::relative( $d[ 'file' ] ), $d[ 'line' ], $d[ 'function' ] ) );
            }
        }

        if( $path = $this->option( 'json' ) ) {
            $out = [];

            foreach( $keys as $key => $sites ) {
                $out[] = [
                    'key'          => $key,
                    'context'      => Scope::context( $sites[ 0 ][ 'file' ] ),
                    'placeholders' => Extractor::placeholders( $key ),
                    'sites'        => array_map( static fn( array $s ): string
                        => Scope::relative( $s[ 'file' ] ) . ':' . $s[ 'line' ], $sites ),
                ];
            }

            file_put_contents( $path, json_encode( $out,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" );

            $this->info( "Wrote {$path}" );
        }

        $this->line( sprintf( 'Scanned %d files; found %d translatable strings at %d call sites.',
            count( $files ), count( $keys ), array_sum( array_map( 'count', $keys ) ) ) );

        if( $dynamic = count( $e->dynamic() ) ) {
            $this->warn( sprintf(
                '%d call site%s pass a non-literal key and cannot be extracted (see --dynamic).',
                $dynamic, $dynamic === 1 ? '' : 's'
            ) );
        }

        return self::SUCCESS;
    }
}
