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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Produce a spreadsheet for a human translator.
 *
 * One row per translatable string, grouped into a worksheet per area of the
 * site so a translator can work through it a screen at a time. Only the
 * `french` column is theirs to edit; `lang:import` reads it back.
 *
 * Because translations are keyed on the English source string, a reworded
 * string upstream appears as a new (untranslated) key and the old translation
 * becomes orphaned. So that the previous work is not simply thrown away, each
 * untranslated row carries the translation of the closest orphaned key as a
 * starting suggestion.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Export extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:export
        {locale=fr : the locale to export}
        {--o|output= : file to write (default: lang/exports/<locale>-<date>.<ext>)}
        {--format= : xlsx (default, needs phpoffice/phpspreadsheet) or csv}
        {--untranslated : export only the strings that still need translating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export a locale to a spreadsheet for a translator';

    /**
     * Column headings.
     *
     * @var string[]
     */
    private const array HEADINGS = [
        'key', 'english', 'french', 'context', 'screen', 'placeholders', 'notes', 'status',
    ];

    /**
     * Below this similarity a previous translation is not worth suggesting.
     */
    private const int SUGGESTION_THRESHOLD = 80;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $locale    = (string)$this->argument( 'locale' );
        $catalogue = Catalogue::load( $locale );
        $extracted = ( new Extractor() )->scan( Scope::files() )->keys();

        $orphaned = array_diff_key( $catalogue->all(), $extracted );
        $rows     = [];

        foreach( $extracted as $key => $sites ) {
            $translated = $catalogue->has( $key );

            if( $translated && $this->option( 'untranslated' ) ) {
                continue;
            }

            $notes = [];

            if( !$translated && ( $suggestion = $this->suggest( $key, $orphaned ) ) ) {
                $notes[] = 'Previously, for very similar English, we had: "' . $suggestion . '"';
            }

            if( $placeholders = Extractor::placeholders( $key ) ) {
                $notes[] = 'Keep :' . implode( ' and :', $placeholders ) . ' exactly as written - '
                    . ( count( $placeholders ) === 1 ? 'it is' : 'they are' ) . ' replaced with a real value at run time.';
            }

            $rows[] = [
                'key'          => $key,
                'english'      => $key,
                'french'       => $catalogue->get( $key ) ?? '',
                'context'      => Scope::context( $sites[ 0 ][ 'file' ] ),
                'screen'       => Scope::relative( $sites[ 0 ][ 'file' ] )
                                    . ( count( $sites ) > 1 ? ' (+' . ( count( $sites ) - 1 ) . ' more)' : '' ),
                'placeholders' => implode( ' ', array_map( static fn( $p ) => ':' . $p, $placeholders ) ),
                'notes'        => implode( ' ', $notes ),
                'status'       => $translated ? 'translated' : 'new',
            ];
        }

        if( !$rows ) {
            $this->info( 'Nothing to export - every string is translated.' );
            return self::SUCCESS;
        }

        $format = (string)( $this->option( 'format' ) ?: ( class_exists( Spreadsheet::class ) ? 'xlsx' : 'csv' ) );

        if( $format === 'xlsx' && !class_exists( Spreadsheet::class ) ) {
            $this->error( 'xlsx output needs phpoffice/phpspreadsheet: composer require --dev phpoffice/phpspreadsheet' );
            $this->line( 'Alternatively export CSV with --format=csv' );
            return self::FAILURE;
        }

        $path = (string)( $this->option( 'output' )
            ?: base_path( 'lang/exports/' . $locale . '-' . date( 'Y-m-d' ) . '.' . $format ) );

        if( !is_dir( $dir = dirname( $path ) ) ) {
            mkdir( $dir, 0755, true );
        }

        $format === 'csv' ? $this->writeCsv( $path, $rows ) : $this->writeXlsx( $path, $rows, $locale );

        $new = count( array_filter( $rows, static fn( array $r ): bool => $r[ 'status' ] === 'new' ) );

        $this->info( sprintf( 'Wrote %s - %d row%s (%d still to translate, ~%d words).',
            $path, count( $rows ), count( $rows ) === 1 ? '' : 's', $new,
            array_sum( array_map( static fn( array $r ): int
                => $r[ 'status' ] === 'new' ? str_word_count( $r[ 'english' ] ) : 0, $rows ) )
        ) );

        if( $orphaned ) {
            $this->warn( sprintf(
                '%d translation%s in lang/%s.json no longer match any string in the code. '
                    . 'Run `php artisan lang:audit %s --show-orphaned` to see them.',
                count( $orphaned ), count( $orphaned ) === 1 ? '' : 's', $locale, $locale
            ) );
        }

        return self::SUCCESS;
    }

    /**
     * The translation of the orphaned key most similar to $key, if any is
     * close enough to be worth offering.
     *
     * @param   string  $key
     * @param   array   $orphaned
     *
     * @return string|null
     */
    private function suggest( string $key, array $orphaned ): ?string
    {
        $best      = null;
        $bestScore = 0.0;

        foreach( $orphaned as $old => $translation ) {
            similar_text( $key, (string)$old, $percent );

            if( $percent > $bestScore ) {
                $bestScore = $percent;
                $best      = (string)$translation;
            }
        }

        return $bestScore >= self::SUGGESTION_THRESHOLD ? $best : null;
    }

    /**
     * @param   string  $path
     * @param   array   $rows
     *
     * @return void
     */
    private function writeCsv( string $path, array $rows ): void
    {
        $fh = fopen( $path, 'wb' );

        // BOM so that Excel opens UTF-8 correctly
        fwrite( $fh, "\xEF\xBB\xBF" );
        fputcsv( $fh, self::HEADINGS, escape: '' );

        foreach( $rows as $row ) {
            fputcsv( $fh, array_values( $row ), escape: '' );
        }

        fclose( $fh );
    }

    /**
     * @param   string  $path
     * @param   array   $rows
     * @param   string  $locale
     *
     * @return void
     */
    private function writeXlsx( string $path, array $rows, string $locale ): void
    {
        $book = new Spreadsheet();
        $book->removeSheetByIndex( 0 );

        // group by context so the translator can work area by area
        $byContext = [];

        foreach( $rows as $row ) {
            $byContext[ $row[ 'context' ] ][] = $row;
        }

        ksort( $byContext );

        foreach( $byContext as $context => $contextRows ) {
            $sheet = $book->createSheet();
            $sheet->setTitle( $this->sheetTitle( $context ) );

            $sheet->fromArray( self::HEADINGS, null, 'A1' );

            $r = 2;

            foreach( $contextRows as $row ) {
                $sheet->fromArray( array_values( $row ), null, 'A' . $r );
                // long strings must not be interpreted as formulas or numbers
                $sheet->getCell( 'A' . $r )->setValueExplicit( $row[ 'key' ],
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING );
                $sheet->getCell( 'C' . $r )->setValueExplicit( $row[ 'french' ],
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING );
                $r++;
            }

            $last = $r - 1;

            $sheet->getStyle( 'A1:H1' )->getFont()->setBold( true );
            $sheet->freezePane( 'A2' );

            foreach( [ 'A' => 55, 'B' => 55, 'C' => 55, 'D' => 20, 'E' => 40, 'F' => 16, 'G' => 60, 'H' => 12 ] as $col => $w ) {
                $sheet->getColumnDimension( $col )->setWidth( $w );
            }

            $sheet->getStyle( 'A2:C' . $last )->getAlignment()
                ->setWrapText( true )->setVertical( Alignment::VERTICAL_TOP );
            $sheet->getStyle( 'G2:G' . $last )->getAlignment()
                ->setWrapText( true )->setVertical( Alignment::VERTICAL_TOP );

            // highlight the one column the translator edits
            $sheet->getStyle( 'C1:C' . $last )->getFill()
                ->setFillType( Fill::FILL_SOLID )
                ->getStartColor()->setARGB( 'FFFFF6D5' );

            // and grey out the ones they should not
            $sheet->getStyle( 'A1:B' . $last )->getFill()
                ->setFillType( Fill::FILL_SOLID )
                ->getStartColor()->setARGB( 'FFF2F2F2' );

            $sheet->setAutoFilter( 'A1:H' . $last );
        }

        $book->setActiveSheetIndex( 0 );

        ( new Xlsx( $book ) )->save( $path );
    }

    /**
     * A worksheet name Excel will accept: 31 characters, none of \ / ? * [ ] :
     *
     * @param   string  $context
     *
     * @return string
     */
    private function sheetTitle( string $context ): string
    {
        return substr( str_replace( [ '\\', '/', '?', '*', '[', ']', ':' ], '-', $context ), 0, 31 );
    }
}
