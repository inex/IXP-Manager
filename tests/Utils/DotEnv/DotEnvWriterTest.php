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

use IXP\Utils\DotEnv\DotEnvWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @package IXP\Utils\DotEnv
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
final class DotEnvWriterTest extends TestCase
{
    /**
     * Parser generator, with optional content.
     */
    private function makeWriter(?array $settings = null): DotEnvWriter
    {
        return new DotEnvWriter($settings);
    }

    public function testEmptySettings(): void
    {
        $w = $this->makeWriter();
        $this->assertNull($w->generateContent());

        $w = $this->makeWriter([]);
        $this->assertNull($w->generateContent());
    }

    public static function parsableSettingsProvider(): array
    {

        // KEY=
        // KEY=VALUE
        // KEY=VALUE # COMMENT
        // KEY= # COMMENT


        return [
            [ 'TEST_VAR', 'VALUE', null, "TEST_VAR=VALUE\n" ],
            [ 'TEST_VAR', true, null, "TEST_VAR=true\n" ],
            [ 'TEST_VAR', false, null, "TEST_VAR=false\n" ],
            [ 'TEST_VAR', 1000, null, "TEST_VAR=1000\n" ],
            [ 'TEST_VAR', '"there once was"', null, "TEST_VAR=\"there once was\"\n" ],
            [ 'TEST_VAR', '"there once was    "', null, "TEST_VAR=\"there once was    \"\n" ],

            [ 'TEST_VAR', 'VALUE', "a comment", "TEST_VAR=VALUE # a comment\n" ],
            [ 'TEST_VAR', true, "## a comment", "TEST_VAR=true ### a comment\n" ],
            [ 'TEST_VAR', false, '##', "TEST_VAR=false ###\n" ],
            [ 'TEST_VAR', null, null, "TEST_VAR=\n" ],
            [ 'TEST_VAR', '', null, "TEST_VAR=\n" ],
            [ 'TEST_VAR', null, 'test', "TEST_VAR= # test\n" ],

            [ null, null, "", "#\n" ],
            //[ null, null, "a comment", "# a comment\n" ],
            [ null, null, "a comment", "# a comment\n" ],
            [ null, null, "## a comment", "### a comment\n" ],
            [ null, null, " ## a comment", "# ## a comment\n" ],
            [ null, null, "################################", "#################################\n" ],

            [ null, null, null, "\n" ],
        ];
    }

    #[DataProvider('parsableSettingsProvider')]
    public function testParsableSettings( ?string $key, string|bool|int|null $value, ?string $comment, string $expected ): void
    {
        $w = $this->makeWriter()
            ->setSettings( [ [ 'key' => $key, 'value' => $value, 'comment' => $comment ] ] );

        $this->assertEquals( $expected, $w->generateContent() );
    }

    public function testMultipleParsableSettings(): void
    {
        $w = $this->makeWriter()
            ->setSettings( [
                [ 'key' => null, 'value' => null, 'comment' => '###########################################################' ],
                [ 'key' => null, 'value' => null, 'comment' => '###########################################################' ],
                [ 'key' => null, 'value' => null, 'comment' => '###' ],
                [ 'key' => null, 'value' => null, 'comment' => '### test settings' ],
                [ 'key' => null, 'value' => null, 'comment' => '###' ],
                [ 'key' => null, 'value' => null, 'comment' => null ],
                [ 'key' => null, 'value' => null, 'comment' => 'Test variable and value' ],
                [ 'key' => 'VAR1', 'value' => 'value1', 'comment' => null ],
                [ 'key' => null, 'value' => null, 'comment' => null ],
                [ 'key' => 'VAR2', 'value' => '"a second value"', 'comment' => null ],
                [ 'key' => 'VAR2_ENABLED', 'value' => true, 'comment' => 'enabled by default' ],
                [ 'key' => 'VAR2_PORT', 'value' => 8080, 'comment' => 'port to listen on' ],
                [ 'key' => null, 'value' => null, 'comment' => null ],
                [ 'key' => null, 'value' => null, 'comment' => '###' ],
                [ 'key' => null, 'value' => null, 'comment' => '###########################################################' ],
            ]);

        $expected = <<<EOF
############################################################
############################################################
####
#### test settings
####

# Test variable and value
VAR1=value1

VAR2="a second value"
VAR2_ENABLED=true # enabled by default
VAR2_PORT=8080 # port to listen on

####
############################################################

EOF;


        $this->assertEquals( $expected, $w->generateContent() );
    }

}
