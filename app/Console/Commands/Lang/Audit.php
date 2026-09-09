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

/**
 * Report the drift between the strings in the code and a translation
 * catalogue.
 *
 * This is the safety net for upgrades. After merging a new upstream release,
 * run this to see which strings are new or changed (they will render in
 * English until translated) and which translations are now orphaned because
 * upstream changed or removed the English.
 *
 * Exits non-zero when there are untranslated strings, so it can be used as a
 * CI gate.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Console\Commands\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Audit extends IXPCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:audit
        {locale=fr : the locale to audit}
        {--show-missing : list every untranslated string}
        {--show-orphaned : list every translation no longer used by the code}
        {--placeholders-only : only fail on placeholder mismatches, not on untranslated strings}
        {--no-fail : always exit 0, just report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report untranslated, orphaned and broken strings for a locale';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $locale     = (string)$this->argument( 'locale' );
        $catalogue  = Catalogue::load( $locale );
        $extracted  = ( new Extractor() )->scan( Scope::files() )->keys();

        $missing = $orphaned = $broken = [];

        foreach( $extracted as $key => $sites ) {
            if( !$catalogue->has( $key ) ) {
                $missing[ $key ] = $sites;
                continue;
            }

            // a translation that drops or invents a :placeholder will render
            // the literal text instead of the value, so treat it as an error
            $want = Extractor::placeholders( $key );
            $got  = Extractor::placeholders( (string)$catalogue->get( $key ) );

            sort( $want );
            sort( $got );

            if( $want !== $got ) {
                $broken[ $key ] = [ 'expected' => $want, 'found' => $got ];
            }
        }

        foreach( array_keys( $catalogue->all() ) as $key ) {
            if( !isset( $extracted[ $key ] ) ) {
                $orphaned[] = $key;
            }
        }

        $translated = count( $extracted ) - count( $missing );

        $this->line( '' );
        $this->line( sprintf( '  <options=bold>Locale %s</>', $locale ) );
        $this->line( sprintf( '  %-28s %d', 'strings in the code',  count( $extracted ) ) );
        $this->line( sprintf( '  %-28s %d (%s)', 'translated', $translated,
            count( $extracted ) ? round( $translated / count( $extracted ) * 100 ) . '%' : '-' ) );
        $this->line( sprintf( '  %-28s %d', 'untranslated', count( $missing ) ) );
        $this->line( sprintf( '  %-28s %d', 'orphaned translations', count( $orphaned ) ) );
        $this->line( sprintf( '  %-28s %d', 'placeholder mismatches', count( $broken ) ) );
        $this->line( '' );

        if( $missing && $this->option( 'show-missing' ) ) {
            $this->line( '<options=bold>Untranslated:</>' );

            foreach( $missing as $key => $sites ) {
                $this->line( sprintf( '  <fg=yellow>%s</>  <fg=gray>%s:%d</>', $key,
                    Scope::relative( $sites[ 0 ][ 'file' ] ), $sites[ 0 ][ 'line' ] ) );
            }

            $this->line( '' );
        }

        if( $orphaned && $this->option( 'show-orphaned' ) ) {
            $this->line( '<options=bold>Orphaned (no longer in the code - upstream may have reworded it):</>' );

            foreach( $orphaned as $key ) {
                $this->line( '  <fg=gray>' . $key . '</>' );
            }

            $this->line( '' );
        }

        foreach( $broken as $key => $p ) {
            $this->error( sprintf( 'Placeholder mismatch for "%s": expected %s, translation has %s',
                $key,
                $p[ 'expected' ] ? ':' . implode( ', :', $p[ 'expected' ] ) : 'none',
                $p[ 'found' ]    ? ':' . implode( ', :', $p[ 'found' ] )    : 'none'
            ) );
        }

        if( $this->option( 'no-fail' ) ) {
            return self::SUCCESS;
        }

        if( $broken ) {
            return self::FAILURE;
        }

        if( $missing && !$this->option( 'placeholders-only' ) ) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
