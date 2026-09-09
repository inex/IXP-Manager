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
    Catalogue,
    Extractor,
    Scope
};

use PhpOffice\PhpSpreadsheet\IOFactory;

use RuntimeException;

/**
 * Read a translated spreadsheet back into lang/<locale>.json
 *
 * Rows are validated before anything is written: a translation that loses or
 * invents a :placeholder is rejected, because at run time that renders the
 * literal text instead of the value. Keys that no longer exist in the code are
 * reported and skipped.
 *
 * Nothing is written unless every row passes, so a bad spreadsheet cannot
 * leave the catalogue half updated. Use --force to import anyway, skipping
 * only the bad rows.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Import extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:import
        {file : the completed spreadsheet (.xlsx or .csv)}
        {locale=fr : the locale to write}
        {--prune : also remove translations whose key is no longer in the code}
        {--force : import valid rows even if some rows fail validation}
        {--dry-run : report what would change and write nothing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a translated spreadsheet into lang/<locale>.json';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $file   = (string)$this->argument( 'file' );
        $locale = (string)$this->argument( 'locale' );

        if( !is_readable( $file ) ) {
            $this->error( "Cannot read {$file}" );
            return self::FAILURE;
        }

        try {
            $rows = $this->read( $file );
        } catch( RuntimeException $e ) {
            $this->error( $e->getMessage() );
            return self::FAILURE;
        }

        if( !$rows ) {
            $this->error( 'No rows found. Is the first row the column headings written by lang:export?' );
            return self::FAILURE;
        }

        $catalogue = Catalogue::load( $locale );
        $extracted = ( new Extractor() )->scan( Scope::files() )->withConfiguredNouns()->keys();

        // A couple of upstream strings end in a space. Spreadsheet editors trim
        // cells, and so does this importer, so match a trimmed key back to the
        // exact source string rather than discarding the row.
        $byTrimmed = [];

        foreach( array_keys( $extracted ) as $codeKey ) {
            $byTrimmed[ trim( $codeKey ) ] ??= $codeKey;
        }

        $problems = $unknown = [];
        $accepted = [];

        foreach( $rows as $n => $row ) {
            $key    = trim( (string)( $row[ 'key' ] ?? '' ) );
            $french = trim( (string)( $row[ 'french' ] ?? '' ) );

            if( $key === '' ) {
                continue;
            }

            if( $french === '' ) {
                continue;
            }

            if( !isset( $extracted[ $key ] ) ) {
                if( isset( $byTrimmed[ $key ] ) ) {
                    $key = $byTrimmed[ $key ];
                } else {
                    $unknown[] = $key;
                    continue;
                }
            }

            $want = Extractor::placeholderNames( $key );
            $got  = Extractor::placeholderNames( $french );

            if( $want !== $got ) {
                $problems[] = sprintf( 'row %d: "%s" expects %s but the translation has %s',
                    $n,
                    mb_strimwidth( $key, 0, 60, '...' ),
                    $want ? ':' . implode( ', :', $want ) : 'no placeholders',
                    $got  ? ':' . implode( ', :', $got )  : 'none'
                );
                continue;
            }

            $accepted[ $key ] = $french;
        }

        foreach( $problems as $problem ) {
            $this->error( $problem );
        }

        if( $problems && !$this->option( 'force' ) ) {
            $this->line( '' );
            $this->error( sprintf( 'Nothing was written: %d row%s failed validation. '
                . 'Fix the spreadsheet, or re-run with --force to import the rest.',
                count( $problems ), count( $problems ) === 1 ? '' : 's' ) );
            return self::FAILURE;
        }

        if( $unknown ) {
            $this->warn( sprintf( '%d row%s skipped: the English no longer appears in the code '
                . '(upstream probably reworded it).', count( $unknown ), count( $unknown ) === 1 ? ' was' : 's were' ) );

            foreach( array_slice( $unknown, 0, 10 ) as $key ) {
                $this->line( '  <fg=gray>' . mb_strimwidth( $key, 0, 90, '...' ) . '</>' );
            }

            if( count( $unknown ) > 10 ) {
                $this->line( '  <fg=gray>... and ' . ( count( $unknown ) - 10 ) . ' more</>' );
            }
        }

        $added = $changed = 0;

        foreach( $accepted as $key => $french ) {
            $existing = $catalogue->get( $key );

            if( $existing === null ) {
                $added++;
            } elseif( $existing !== $french ) {
                $changed++;
            }

            $catalogue->set( $key, $french );
        }

        $pruned = $this->option( 'prune' ) ? $catalogue->prune( array_keys( $extracted ) ) : [];

        if( $this->option( 'dry-run' ) ) {
            $this->line( '' );
            $this->info( sprintf( 'Dry run - would add %d, update %d, remove %d. Nothing written.',
                $added, $changed, count( $pruned ) ) );
            return self::SUCCESS;
        }

        $path = $catalogue->save();

        $this->line( '' );
        $this->info( sprintf( 'Wrote %s - %d added, %d updated%s.',
            $path, $added, $changed,
            $pruned ? ', ' . count( $pruned ) . ' orphaned removed' : '' ) );

        return self::SUCCESS;
    }

    /**
     * Read the spreadsheet into a list of heading => value maps.
     *
     * @param   string  $file
     *
     * @return array
     *
     * @throws RuntimeException
     */
    private function read( string $file ): array
    {
        return str_ends_with( strtolower( $file ), '.csv' )
            ? $this->readCsv( $file )
            : $this->readSpreadsheet( $file );
    }

    /**
     * @param   string  $file
     *
     * @return array
     */
    private function readCsv( string $file ): array
    {
        $fh   = fopen( $file, 'rb' );
        $rows = [];
        $head = null;
        $n    = 0;

        while( ( $line = fgetcsv( $fh, escape: '' ) ) !== false ) {
            $n++;

            if( $head === null ) {
                // strip a UTF-8 BOM from the first heading
                $line[ 0 ] = preg_replace( '/^\xEF\xBB\xBF/', '', (string)$line[ 0 ] );
                $head      = array_map( static fn( $h ): string => strtolower( trim( (string)$h ) ), $line );
                continue;
            }

            $rows[ $n ] = $this->combine( $head, $line );
        }

        fclose( $fh );

        return $rows;
    }

    /**
     * Read every worksheet of an xlsx (lang:export writes one per context).
     *
     * @param   string  $file
     *
     * @return array
     *
     * @throws RuntimeException
     */
    private function readSpreadsheet( string $file ): array
    {
        if( !class_exists( IOFactory::class ) ) {
            throw new RuntimeException(
                'Reading xlsx needs phpoffice/phpspreadsheet: composer require --dev phpoffice/phpspreadsheet'
                . ' (or ask for the translation back as .csv)'
            );
        }

        $reader = IOFactory::createReaderForFile( $file );
        $reader->setReadDataOnly( true );

        $book = $reader->load( $file );
        $rows = [];
        $n    = 0;

        foreach( $book->getAllSheets() as $sheet ) {
            $head = null;

            foreach( $sheet->toArray( null, true, false, false ) as $line ) {
                $n++;

                if( $head === null ) {
                    $head = array_map( static fn( $h ): string => strtolower( trim( (string)$h ) ), $line );
                    continue;
                }

                $rows[ $n ] = $this->combine( $head, $line );
            }
        }

        return $rows;
    }

    /**
     * Zip a heading row and a data row, tolerating ragged rows.
     *
     * @param   array  $head
     * @param   array  $line
     *
     * @return array
     */
    private function combine( array $head, array $line ): array
    {
        $row = [];

        foreach( $head as $i => $name ) {
            $row[ $name ] = $line[ $i ] ?? '';
        }

        return $row;
    }
}
