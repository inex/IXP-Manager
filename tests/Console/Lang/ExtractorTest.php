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

use IXP\Utils\Lang\Extractor;

use Tests\TestCase;

/**
 * Tests for the translatable string extractor.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    Tests\Console\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ExtractorTest extends TestCase
{
    /**
     * Files created by fixture(); removed in tearDown().
     *
     * @var string[]
     */
    private array $tmp = [];

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        foreach( $this->tmp as $file ) {
            @unlink( $file );
        }

        $this->tmp = [];

        parent::tearDown();
    }

    /**
     * Write a temporary source file.
     *
     * @param   string  $contents
     * @param   string  $extension
     *
     * @return string
     */
    private function fixture( string $contents, string $extension = '.foil.php' ): string
    {
        $file = tempnam( sys_get_temp_dir(), 'ixplang' );
        @unlink( $file );
        $file .= $extension;

        file_put_contents( $file, $contents );
        $this->tmp[] = $file;

        return $file;
    }

    /**
     * @param   string  $contents
     * @param   string  $extension
     *
     * @return string[]
     */
    private function extract( string $contents, string $extension = '.foil.php' ): array
    {
        return array_keys( ( new Extractor() )->scan( [ $this->fixture( $contents, $extension ) ] )->keys() );
    }

    /**
     * The basic shapes a Foil template uses.
     */
    public function testExtractsLiteralStrings(): void
    {
        $keys = $this->extract( <<<'PHP'
            <div>
                <?= __( 'Peering Manager' ) ?>
                <?= __("Double quoted") ?>
                <?php echo trans( 'Via trans' ); ?>
                <?= __( 'With args', [ 'a' => $b ] ) ?>
            </div>
            PHP );

        sort( $keys );

        $this->assertSame( [ 'Double quoted', 'Peering Manager', 'Via trans', 'With args' ], $keys );
    }

    /**
     * A `__()` inside a comment or inside another string is not a call site.
     * This is why the extractor tokenises rather than pattern matching.
     */
    public function testIgnoresCommentsAndStrings(): void
    {
        $keys = $this->extract( <<<'PHP'
            <?php
                // __( 'In a line comment' )
                # __( 'In a hash comment' )
                /* __( 'In a block comment' ) */
                /** __( 'In a doc comment' ) */
                $a = 'text with __( \'In a string\' ) inside';
                $b = __( 'Real' );
            PHP );

        $this->assertSame( [ 'Real' ], $keys );
    }

    /**
     * Only the global helpers are translation calls.
     */
    public function testIgnoresMethodAndStaticCalls(): void
    {
        $keys = $this->extract( <<<'PHP'
            <?php
                $obj->__( 'Method call' );
                $obj?->trans( 'Nullsafe method call' );
                Foo::trans( 'Static call' );
                function trans( 'Not really valid but must not match' ) {}
                $c = __( 'Real' );
            PHP );

        $this->assertSame( [ 'Real' ], $keys );
    }

    /**
     * A concatenated or variable key cannot be handed to a translator, so it
     * is reported separately rather than silently half-extracted.
     */
    public function testConcatenatedAndVariableKeysAreReportedAsDynamic(): void
    {
        $file = $this->fixture( <<<'PHP'
            <?php
                __( 'Prefix ' . $suffix );
                __( $wholeThing );
                __( 'Fine' );
            PHP );

        $e = ( new Extractor() )->scan( [ $file ] );

        $this->assertSame( [ 'Fine' ], array_keys( $e->keys() ) );
        $this->assertCount( 2, $e->dynamic() );
    }

    /**
     * Blade's echo and @lang constructs are not PHP to the tokeniser, so they
     * are rewritten first.
     */
    public function testExtractsFromBlade(): void
    {
        $keys = $this->extract( <<<'BLADE'
            <p>{{ __( 'In double braces' ) }}</p>
            <p>{!! __( 'In raw braces' ) !!}</p>
            <p>@lang( 'Via the lang directive' )</p>
            <p>@lang('With a ) paren in the string')</p>
            <?php echo __( 'In a php block' ); ?>
            BLADE, '.blade.php' );

        sort( $keys );

        $this->assertSame( [
            'In a php block',
            'In double braces',
            'In raw braces',
            'Via the lang directive',
            'With a ) paren in the string',
        ], $keys );
    }

    /**
     * Escapes in the literal must be resolved - the key at run time is the
     * string's value, not its source form.
     */
    public function testUnescapesLiterals(): void
    {
        $keys = $this->extract( <<<'PHP'
            <?php
                __( 'It\'s here' );
                __( "A \"quoted\" word" );
            PHP );

        sort( $keys );

        $this->assertSame( [ 'A "quoted" word', "It's here" ], $keys );
    }

    /**
     * Every call site is recorded, so the export can tell a translator where a
     * string appears.
     */
    public function testRecordsEveryCallSiteWithLineNumbers(): void
    {
        $file = $this->fixture( "<?php\n__( 'Twice' );\n\n__( 'Twice' );\n" );

        $sites = ( new Extractor() )->scan( [ $file ] )->keys()[ 'Twice' ];

        $this->assertCount( 2, $sites );
        $this->assertSame( 2, $sites[ 0 ][ 'line' ] );
        $this->assertSame( 4, $sites[ 1 ][ 'line' ] );
    }

    /**
     * Placeholders must be surfaced so they can be protected on import.
     */
    public function testFindsPlaceholders(): void
    {
        $this->assertSame( [ 'language' ], Extractor::placeholders( 'Use the default (:language)' ) );
        $this->assertSame( [ 'Customer' ], Extractor::placeholders( ':Customer Details' ) );
        $this->assertSame( [ 'a', 'b' ], Extractor::placeholders( ':a and :b' ) );
        $this->assertSame( [], Extractor::placeholders( 'No placeholders here' ) );

        // a bare colon, and a time, are not placeholders
        $this->assertSame( [], Extractor::placeholders( 'Ports: 24' ) );
    }
}
