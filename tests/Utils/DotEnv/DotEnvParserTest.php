<?php
declare(strict_types=1);

namespace Tests\Utils\DotEnv;

/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use IXP\Exceptions\Utils\DotEnvParserException;
use IXP\Utils\DotEnv\DotEnvParser;

/**
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP\Utils\DotEnv
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
final class DotEnvParserTest extends TestCase
{
    /**
     * Parser generator, with optional content.
     */
    private function makeParser(?string $content = null): DotEnvParser
    {
        return new DotEnvParser($content);
    }

    public function testContentAndSetContent(): void
    {
        $p = $this->makeParser('FOO=bar');
        $this->assertSame('FOO=bar', $p->content());

        $p->setContent('BAZ=qux');
        $this->assertSame('BAZ=qux', $p->content());
    }

    public function testStripQuotesRemovesMatchingQuotesOnly(): void
    {
        $p = $this->makeParser();

        $this->assertSame('"hello goodbye"', $p->stripQuotes('"hello goodbye"'));
        $this->assertSame("'hello goodbye'", $p->stripQuotes("'hello goodbye'"));
        $this->assertSame('"\'hello goodbye\'"', $p->stripQuotes("\"'hello goodbye'\""));
        $this->assertSame('\'"hello goodbye"\'', $p->stripQuotes("'\"hello goodbye\"'"));

        // Mismatched quotes should remain unchanged
        $this->assertSame('\'hello goodbye"', $p->stripQuotes('\'hello goodbye"'));

        // Inner quotes are preserved
        $this->assertSame('"he\'llo goodbye"', $p->stripQuotes('"he\'llo goodbye"'));
        $this->assertSame('"he\'llo goo\\"dbye"', $p->stripQuotes('"he\'llo goo\\"dbye"'));
    }

    public function testParseValueStripsQuotes(): void
    {
        $p = $this->makeParser();

        $this->assertSame('"hello goodbye"', $p->parseValue('"hello goodbye"'));
        $this->assertSame('"hello goodbye"', $p->parseValue("'hello goodbye'"));
        $this->assertSame('"\'hello goodbye\'"', $p->parseValue("\"'hello goodbye'\""));

        // Inner quotes are preserved
        $this->assertSame('"he\'llo goodbye"', $p->parseValue('"he\'llo goodbye"'));
        $this->assertSame('"he\'llo goo\\"dbye"', $p->parseValue('"he\'llo goo\\"dbye"'));
    }

    public function testParseBooleanValueTypes(): void
    {
        $p = $this->makeParser();

        // phpDotEnv can interpret 1 as bool, but we also need 1 as an integer,
        // so will use integer as a priority
        
        $this->assertNotTrue( '1' );
        $this->assertTrue( $p->parseValue('true') );
        $this->assertTrue( $p->parseValue('True') );
        $this->assertTrue( $p->parseValue('TRUE') );
        $this->assertTrue( $p->parseValue('yes') );
        // int: $this->assertTrue( $p->parseValue('1') );
        $this->assertTrue( $p->parseValue('On') );
        $this->assertTrue( $p->parseValue('on') );

        $this->assertNotFalse( '0' );
        $this->assertFalse( $p->parseValue('false') );
        $this->assertFalse( $p->parseValue('False') );
        $this->assertFalse( $p->parseValue('FALSE') );
        $this->assertFalse( $p->parseValue('no') );
        // int: $this->assertFalse( $p->parseValue('0') );
        $this->assertFalse( $p->parseValue('Off') );
        $this->assertFalse( $p->parseValue('off') );
    }

    public function testParseIntValueTypes(): void
    {
        $p = $this->makeParser();

        $this->assertIsNotInt( '1' );

        // boolean 0/1 are parsed as ints, not booleans
        $this->assertIsInt( $p->parseValue('0') );
        $this->assertIsInt( $p->parseValue('1') );

        $this->assertIsInt( $p->parseValue('02') );
        $this->assertEquals( 2, $p->parseValue('02') );
        $this->assertIsInt( $p->parseValue('-02') );
        $this->assertEquals( -2, $p->parseValue('-02') );

        $this->assertIsInt( $p->parseValue('2') );
        $this->assertEquals( 2, $p->parseValue('2') );

        $this->assertIsInt( $p->parseValue('102') );
        $this->assertEquals( 102, $p->parseValue('102') );

        $this->assertIsInt( $p->parseValue('222') );
        $this->assertEquals( 222, $p->parseValue('222') );

        $this->assertIsInt( $p->parseValue('-2') );
        $this->assertEquals( -2, $p->parseValue('-2') );

        $this->assertIsInt( $p->parseValue('-2342') );
        $this->assertEquals( -2342, $p->parseValue('-2342') );

    }


    /**
     * @throws DotEnvParserException
     */
    public function testParseContentBlankCommentLines(): void
    {
        foreach( [ "#\n", "#\r\n", "#\r", "#\n\r", "#\r\n\r", "#   \n", "#  \r\n", "#     \r", "#\t   \n\r", "#   \t\r\n\r" ] as $line ) {
            $p = $this->makeParser()
                ->setContent( $line )
                ->parse();

            $this->assertEquals(
                [ 0 => [
                    "key"     => null,
                    "value"   => null,
                    "comment" => "",
                ] ],
                $p->settings()
            );
        }
    }

    /**
     * @throws DotEnvParserException
     */
    public function testParseContentCommentLines(): void
    {
        foreach(
                [
                    "# comment \n" => "comment",
                    "# comment comment \r\n" => "comment comment",
                    "# this is at !! comment   \r" => "this is at !! comment",
                    "# hey \$ho yolo\tthhe\n\r" => "hey \$ho yolo\tthhe",
                    "#    thesis yo\r\n\r" => "thesis yo",
                    "#  yes, sisko was the best star trek captain! \n" => "yes, sisko was the best star trek captain!",
                    "#     no, it was't kirk. or picard. <====\r\n" => "no, it was't kirk. or picard. <====",
                    "#yes the defiant WAS a cool ship     \r" => "yes the defiant WAS a cool ship",
                    "#\tncc1701\t   \n\r" => "ncc1701",
                ] as $line => $expected ) {


            $p = $this->makeParser()
                ->setContent( $line )
                ->parse();

            $this->assertEquals(
                [ 0 => [
                    "key"     => null,
                    "value"   => null,
                    "comment" => $expected,
                ] ],
                $p->settings()
            );
        }
    }

    public static function unparsableValuesProvider(): array
    {
        return [
            [ "=jkkjk"], [ "  =jkkfewfew" ], [ "THT_JKHK= hjkhke" ], [ "TEST =kdjfdf" ],
            [ "TEST=\${OTHER_VAR}"], [ "TEST=\"there was \${SOME_OTHER_VAR} also\""], [ "TEST_VAR='\${OTHER_VAR}'"], [ "TEST=\"aa\${OTHER_VAR}aa\" # comment" ],
            [ "kjjdf k e\n" ], [ "=kjjdf\n" ]
        ];
    }

    #[DataProvider('unparsableValuesProvider')]
    public function testUnparsable( string $line ): void
    {
        $p = $this->makeParser()
            ->setContent( $line );

        $this->expectException( DotEnvParserException::class);
        $p->parse();
    }

    public function testDuplicateKeys(): void
    {
        $content = <<<EOF
            FOO=bar
            BAZ="qux quux"
            NAME="John Doe" # person
            ENABLED=true
            FOO=bar
            DISABLED=false

            EOF;

        $p = $this->makeParser()
            ->setContent( $content );

        $this->expectException( DotEnvParserException::class);
        $p->parse();
    }

    /**
     * @throws DotEnvParserException
     */
    public function testBlankLines(): void
    {
        // leading and trailing blank lines should be removed, but those in the middle should not
        $content = implode("\n", [
            '',
            '',
            '',
            'FOO=bar',
            '',
            '',
            '',
            'BAZ="qux quux"',
            '',
            'NAME="John Doe" # person',
            '',
            '',
            '# just a comment line',
            '',
            '',
        ]);

        $p = $this->makeParser($content)
                ->parse();

        $blankLine = [
            "key"     => null,
            "value"   => null,
            "comment" => null,
        ];

        $this->assertCount( 10, $p->settings() );
        $this->assertNotEquals( $blankLine, $p->settings()[0] );
        $this->assertEquals( $blankLine, $p->settings()[1] );
        $this->assertEquals( $blankLine, $p->settings()[2] );
        $this->assertEquals( $blankLine, $p->settings()[3] );
        $this->assertNotEquals( $blankLine, $p->settings()[4] );
        $this->assertEquals( $blankLine, $p->settings()[5] );
        $this->assertNotEquals( $blankLine, $p->settings()[6] );
        $this->assertEquals( $blankLine, $p->settings()[7] );
        $this->assertEquals( $blankLine, $p->settings()[8] );
    }


    public static function parsableKeyValueProvider(): array
    {

        // KEY=
        // KEY=VALUE
        // KEY=VALUE # COMMENT
        // KEY= # COMMENT


        return [
            [ "TEST=\n", "TEST", '', null ],
            [ "TEST=    \n", "TEST", '', null ],
            [ "TEST= \t \t  \n", "TEST", '', null ],
            [ "TEST=qwerty\n", "TEST", "qwerty", null ],
            [ "TEST_VAR=\"there once was a \"\n", "TEST_VAR", '"there once was a "', null ],
            [ "TEST_VAR=\"there once was a \"    # comment  \n", "TEST_VAR", '"there once was a "', "comment" ],
            [ "TEST=\"true false something else\"\n", "TEST", '"true false something else"', null ],
            [ "TEST_VAR=\"there once was a \"   ### comment\n", "TEST_VAR", '"there once was a "', "## comment" ],
            [ "APP_KEY=\"base64:01234567899876543210abcdefghijjihgfedcba123=\"", "APP_KEY", '"base64:01234567899876543210abcdefghijjihgfedcba123="', null ],
        ];
    }

    /**
     * @throws DotEnvParserException
     */
    #[DataProvider('parsableKeyValueProvider')]
    public function testKeyValueCommentParser( string $line, string $key, ?string $value, ?string $comment ): void
    {
        $p = $this->makeParser()
            ->setContent( $line )
            ->parse();

        $this->assertEquals(
            [ 0 => [
                "key"     => $key,
                "value"   => $value,
                "comment" => $comment,
            ] ],
            $p->settings()
        );
    }


}
