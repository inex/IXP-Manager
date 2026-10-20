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

namespace Tests\Utils;

use Illuminate\Foundation\Testing\TestCase;
use IXP\Utils\FilenameFormatter;
use PHPUnit\Framework\Attributes\DataProvider;

class FilenameFormatterTest extends TestCase
{
    public static function nameTruncatedProvider(): array
    {
        return [
            'short filename' => [
                'input'    => 'report.pdf',
                'expected' => 'report.pdf',
            ],
            'exact 80-character filename with extension' => [
                'input'    => str_repeat('a', 76) . '.pdf',
                'expected' => str_repeat('a', 76) . '.pdf',
            ],
            'long filename with extension' => [
                'input'    => str_repeat('a', 100) . '.pdf',
                'expected' => str_repeat('a', 73) . '....pdf',
            ],
            'long filename with utf8 characters and extension' => [
                'input'    => 'ñandú_برنامج_español_مفاجأة_über_groß_مünchen_jongleur_façade_piñata_žlutý_kůň_kožuštek_vôňa_bløtkake_sør_trøndelag_file_name_test.pdf',
                'expected' => 'ñandú_برنامج_español_مفاجأة_über_groß_مünchen_jongleur_façade_piñata_žlut....pdf',
            ],
            'long filename with no extension' => [
                'input'    => str_repeat('b', 100),
                'expected' => str_repeat('b', 77) . '...',
            ],
            'long filename with very long extension' => [
                'input'    => str_repeat('c', 90) . '.superlongextension', // extension is 20 chars
                'expected' => str_repeat('c', 77) . '...',
            ],
            'filename with extension with multiple dots (tar.gz)' => [
                'input'    => 'very_long_archive_file_name_that_goes_on_and_on_and_needs_proper_handling_archive_v1.2.3.tar.gz',
                'expected' => 'very_long_archive_file_name_that_goes_on_and_on_and_needs_proper_handling_....gz',
            ],
        ];
    }

    #[DataProvider('nameTruncatedProvider')]
    public function testTruncate(string $input, string $expected): void
    {
        $this->assertEquals($expected, FilenameFormatter::truncate($input));
    }
}