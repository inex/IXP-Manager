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

namespace Tests\Utils\BgpTools;

use IXP\Exceptions\GeneralException;
use IXP\Utils\BgpTools\CsvReader;

/**
 * CsvReaderTest
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class CsvReaderTest extends \Tests\TestCase
{
    public function testReadSuccessfully()
    {
        $data = $this->getSampleAsnCsv();
        $reader = new CsvReader();
        $generator = $reader->read($data, ['asn', 'name', 'class', 'cc']);
        $contents = [];
        foreach ($generator as $row) {
            $contents[] = $row;
        }
        $this->assertCount( 4, $contents );

        [$asn, $name, $class, $cc] = $contents[0];
        $this->assertEquals("AS10", $asn);
        $this->assertEquals("CSNET Coordination and Information Center (CSNET-CIC)", $name);
        $this->assertEquals("Unknown", $class);
        $this->assertEquals("US", $cc);

        [$asn, $name, $class, $cc] = $contents[1];
        $this->assertEquals("AS100", $asn);
        $this->assertEquals("FMC Central Engineering Laboratories", $name);
        $this->assertEquals("Unknown", $class);
        $this->assertEquals("US", $cc);

        [$asn, $name, $class, $cc] = $contents[2];
        $this->assertEquals("AS1000", $asn);
        $this->assertEquals("CORIX NETWORKS", $name);
        $this->assertEquals("Unknown", $class);
        $this->assertEquals("US", $cc);

        [$asn, $name, $class, $cc] = $contents[3];
        $this->assertEquals("AS10000", $asn);
        $this->assertEquals("Nagasaki Cable Media Inc.", $name);
        $this->assertEquals("Eyeball", $class);
        $this->assertEquals("JP", $cc);
    }

    public function testFileMissingHeaders()
    {
        $data = $this->getSampleAsnCsv();
        $reader = new CsvReader();

        // Generator must be iterated over before code runs.
        $generator = $reader->read($data, ['asn', 'name', 'class', 'cc', 'thisisntthere']);
        $this->expectException(GeneralException::class);
        $this->expectExceptionMessage("CSV file has less columns than expected!");
        $generator->next();
    }

    public function testFileWrongHeaders()
    {
        $data = $this->getSampleAsnCsv();
        $reader = new CsvReader();

        // Generator must be iterated over before code runs.
        $generator = $reader->read($data, ['this', 'is', 'completely', 'different']);
        $this->expectException(GeneralException::class);
        $this->expectExceptionMessage("CSV file headers do not match what was expected!");
        $generator->next();
    }

    public function testHeadersParamNotRequired()
    {
        $data = $this->getSampleAsnCsv();
        $reader = new CsvReader();
        $generator = $reader->read($data);
        $contents = [];
        foreach ($generator as $row) {
            $contents[] = $row;
        }
        $this->assertCount( 4, $contents );
    }

    private function getSampleAsnCsv(): string
    {
        return file_get_contents( base_path( 'data/ci/known-good/sample-asns.csv' ) );
    }
}