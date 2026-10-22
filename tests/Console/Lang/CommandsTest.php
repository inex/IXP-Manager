<?php

namespace Tests\Console\Lang;

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

use IXP\Utils\Lang\Catalogue;

use Tests\TestCase;

/**
 * Tests for the lang:* console commands.
 *
 * These run against the real source tree, so they assert on behaviour that
 * does not depend on how much of the application has been translated yet.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    Tests\Console\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CommandsTest extends TestCase
{
    /**
     * A locale that will never be a real one.
     */
    private const string LOCALE = 'zz-testing';

    /**
     * @var string[]
     */
    private array $tmp = [];

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        @unlink( Catalogue::path( self::LOCALE ) );

        foreach( $this->tmp as $file ) {
            @unlink( $file );
        }

        $this->tmp = [];

        parent::tearDown();
    }

    /**
     * @param   string  $extension
     *
     * @return string
     */
    private function tmpFile( string $extension ): string
    {
        $file = tempnam( sys_get_temp_dir(), 'ixplang' );
        @unlink( $file );
        $file .= $extension;

        $this->tmp[] = $file;

        return $file;
    }

    /**
     * lang:extract must find the strings this feature itself introduced.
     */
    public function testExtractFindsKnownStrings(): void
    {
        $json = $this->tmpFile( '.json' );

        $this->artisan( 'lang:extract', [ '--json' => $json ] )->assertSuccessful();

        $keys = array_column( json_decode( (string)file_get_contents( $json ), true ), 'key' );

        $this->assertContains( 'Set Language', $keys );
        $this->assertContains( 'Use the default (:language)', $keys );
    }

    /**
     * An entirely untranslated locale must fail the audit - that is what makes
     * it usable as a CI gate - and must pass with --no-fail.
     */
    public function testAuditFailsForAnUntranslatedLocale(): void
    {
        $this->artisan( 'lang:audit', [ 'locale' => self::LOCALE ] )->assertFailed();
        $this->artisan( 'lang:audit', [ 'locale' => self::LOCALE, '--no-fail' => true ] )->assertSuccessful();
    }

    /**
     * A translation that drops a :placeholder is an error even though the
     * string is "translated" - at run time it would render the literal text
     * instead of the value.
     */
    public function testAuditFailsOnPlaceholderMismatch(): void
    {
        $c = Catalogue::load( self::LOCALE );
        $c->set( 'Use the default (:language)', 'Utiliser la langue par defaut' );
        $c->save();

        $this->artisan( 'lang:audit', [ 'locale' => self::LOCALE, '--placeholders-only' => true ] )
            ->expectsOutputToContain( 'Placeholder mismatch' )
            ->assertFailed();
    }

    /**
     * Export then import must round trip a translation into the catalogue.
     */
    public function testExportImportRoundTrip(): void
    {
        $csv = $this->tmpFile( '.csv' );

        $this->artisan( 'lang:export', [
            'locale'    => self::LOCALE,
            '--output'  => $csv,
            '--format'  => 'csv',
        ] )->assertSuccessful();

        $this->assertFileExists( $csv );

        // fill in one translation, as a translator would
        $rows = array_map( 'str_getcsv', file( $csv ) );
        $out  = fopen( $csv, 'wb' );

        foreach( $rows as $row ) {
            if( ( $row[ 0 ] ?? '' ) === 'Set Language' ) {
                $row[ 2 ] = 'Définir la langue';
            }

            fputcsv( $out, $row, escape: '' );
        }

        fclose( $out );

        $this->artisan( 'lang:import', [ 'file' => $csv, 'locale' => self::LOCALE ] )->assertSuccessful();

        $this->assertSame( 'Définir la langue', Catalogue::load( self::LOCALE )->get( 'Set Language' ) );
    }

    /**
     * A bad row must abort the whole import, so the catalogue is never left
     * half updated.
     */
    public function testImportRejectsPlaceholderMismatchAndWritesNothing(): void
    {
        $csv = $this->tmpFile( '.csv' );

        $fh = fopen( $csv, 'wb' );
        fputcsv( $fh, [ 'key', 'english', 'french', 'context', 'screen', 'placeholders', 'notes', 'status' ], escape: '' );
        fputcsv( $fh, [ 'Set Language', 'Set Language', 'Définir la langue', '', '', '', '', '' ], escape: '' );
        fputcsv( $fh, [ 'Use the default (:language)', '', 'Utiliser la langue par defaut', '', '', '', '', '' ], escape: '' );
        fclose( $fh );

        $this->artisan( 'lang:import', [ 'file' => $csv, 'locale' => self::LOCALE ] )->assertFailed();

        // nothing at all should have been written - not even the good row
        $this->assertNull( Catalogue::load( self::LOCALE )->get( 'Set Language' ) );

        // ... but --force takes the good row and skips the bad one
        $this->artisan( 'lang:import', [ 'file' => $csv, 'locale' => self::LOCALE, '--force' => true ] )
            ->assertSuccessful();

        $c = Catalogue::load( self::LOCALE );

        $this->assertSame( 'Définir la langue', $c->get( 'Set Language' ) );
        $this->assertNull( $c->get( 'Use the default (:language)' ) );
    }

    /**
     * A key the code no longer uses must be skipped rather than reintroduced.
     */
    public function testImportSkipsKeysNotInTheCode(): void
    {
        $csv = $this->tmpFile( '.csv' );

        $fh = fopen( $csv, 'wb' );
        fputcsv( $fh, [ 'key', 'english', 'french' ], escape: '' );
        fputcsv( $fh, [ 'A String The Code Does Not Contain', '', 'Une traduction', ], escape: '' );
        fclose( $fh );

        $this->artisan( 'lang:import', [ 'file' => $csv, 'locale' => self::LOCALE ] )->assertSuccessful();

        $this->assertNull( Catalogue::load( self::LOCALE )->get( 'A String The Code Does Not Contain' ) );
    }

    /**
     * --dry-run must report but write nothing.
     */
    public function testImportDryRunWritesNothing(): void
    {
        $csv = $this->tmpFile( '.csv' );

        $fh = fopen( $csv, 'wb' );
        fputcsv( $fh, [ 'key', 'english', 'french' ], escape: '' );
        fputcsv( $fh, [ 'Set Language', '', 'Définir la langue' ], escape: '' );
        fclose( $fh );

        $this->artisan( 'lang:import', [ 'file' => $csv, 'locale' => self::LOCALE, '--dry-run' => true ] )
            ->assertSuccessful();

        $this->assertFileDoesNotExist( Catalogue::path( self::LOCALE ) );
    }
}
