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

use RuntimeException;

use Tests\TestCase;

/**
 * Tests for the JSON translation catalogue.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    Tests\Console\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CatalogueTest extends TestCase
{
    /**
     * A locale that will never be a real one, so a stray file cannot affect
     * the running application.
     */
    private const string LOCALE = 'zz-testing';

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        @unlink( Catalogue::path( self::LOCALE ) );

        parent::tearDown();
    }

    /**
     * A locale with no file is simply empty, not an error.
     */
    public function testMissingFileIsAnEmptyCatalogue(): void
    {
        $c = Catalogue::load( self::LOCALE );

        $this->assertSame( [], $c->all() );
        $this->assertFalse( $c->has( 'anything' ) );
        $this->assertNull( $c->get( 'anything' ) );
    }

    /**
     * Round trip, and the file must be readable by a human: sorted, and with
     * accented characters left alone rather than \u-escaped.
     */
    public function testSavesSortedAndUnescaped(): void
    {
        $c = Catalogue::load( self::LOCALE );
        $c->set( 'Zebra', 'Zèbre' );
        $c->set( 'Apple', 'Pomme' );
        $path = $c->save();

        $raw = (string)file_get_contents( $path );

        $this->assertStringContainsString( 'Zèbre', $raw );
        $this->assertLessThan( strpos( $raw, 'Zebra' ), strpos( $raw, 'Apple' ) );

        $this->assertSame( [ 'Apple' => 'Pomme', 'Zebra' => 'Zèbre' ], Catalogue::load( self::LOCALE )->all() );
    }

    /**
     * An empty translation means "not translated" and must not be written,
     * otherwise the string would render blank rather than falling back to
     * English.
     */
    public function testEmptyTranslationRemovesTheKey(): void
    {
        $c = Catalogue::load( self::LOCALE );
        $c->set( 'One', 'Un' );
        $c->set( 'One', '   ' );

        $this->assertFalse( $c->has( 'One' ) );
        $this->assertSame( [], $c->all() );
    }

    /**
     * Pruning drops exactly the keys that are no longer used.
     */
    public function testPruneRemovesOnlyUnknownKeys(): void
    {
        $c = Catalogue::load( self::LOCALE );
        $c->set( 'Keep', 'Garder' );
        $c->set( 'Drop', 'Jeter' );

        $removed = $c->prune( [ 'Keep' ] );

        $this->assertSame( [ 'Drop' ], $removed );
        $this->assertSame( [ 'Keep' => 'Garder' ], $c->all() );
    }

    /**
     * A corrupt catalogue must fail loudly rather than silently discarding
     * every translation in it.
     */
    public function testInvalidJsonThrows(): void
    {
        file_put_contents( Catalogue::path( self::LOCALE ), '{ this is not json' );

        $this->expectException( RuntimeException::class );

        Catalogue::load( self::LOCALE );
    }
}
