<?php

namespace Tests\Services;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Services\DotEnvWriter;
use Tests\TestCase;

/**
 * DotEnvWriterTest
 *
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DotEnvWriterTest extends TestCase
{
    protected string $originalFile = '.env.example';
    protected string $testFile = '.env.test';

    protected DotEnvWriter $writer;

    /**
     * Utility function to get a .env.test file content and variable list
     *
     */
    public function testReader(): void
    {
        $originalFile = base_path($this->originalFile);
        $testFile = base_path($this->testFile);

        @unlink($testFile);
        copy($originalFile, $testFile);

        $this->writer = new DotEnvWriter($testFile);
        $variables = $this->writer->getAll();

        $this->assertIsArray($variables);
    }

    /**
     * Utility function to set variables to the .env.test file
     *
     */
    public function testSetVariables(): void
    {
        $testFile = base_path($this->testFile);
        $this->writer = new DotEnvWriter($testFile);
        $this->writer->set("APP_LOG","daily","not showing description");
        $this->writer->set("TEST_KEY","Test value","It is a test description");
        $this->writer->enable("MAIL_PORT");
        $this->writer->disable("APP_KEY");
        $this->writer->delete("GRAPHER_BACKEND_MRTG_DBTYPE",true);
        $variables = $this->writer->getAll();

        $mail = $this->writer->get("MAIL_PORT");
        $app = $this->writer->get("APP_KEY");
        $log = $this->writer->get("APP_LOG");
        $logDescription = $log - 1;
        $test = $this->writer->get("TEST_KEY");
        $testDescription = $test - 1;
        $graph = $this->writer->get("GRAPHER_BACKEND_MRTG_DBTYPE");

        $this->assertIsArray($variables);

        $this->assertTrue($variables[$mail]["status"]);
        $this->assertTrue($variables[$mail]["changed"]);
        $this->assertFalse($variables[$app]["status"]);
        $this->assertTrue($variables[$app]["changed"]);
        $this->assertEquals("daily",$variables[$log]["value"]);
        $this->assertTrue($variables[$log]["changed"]);
        $this->assertNotEquals("# not showing description",$variables[$logDescription]["value"]);
        $this->assertEquals("Test value",$variables[$test]["value"]);
        $this->assertTrue($variables[$test]["changed"]);
        $this->assertNull($variables[$testDescription]["key"]);
        $this->assertEquals("# It is a test description",$variables[$testDescription]["value"]);
        $this->assertFalse($variables[$testDescription]["status"]);
        $this->assertTrue($variables[$testDescription]["changed"]);
        $this->assertFalse($graph);
    }

    /**
     * Utility function to write the .env.test file content from the variables
     *
     */
    public function testWrite(): void
    {
        $testFile = base_path($this->testFile);
        $this->writer = new DotEnvWriter($testFile);

        $this->writer->set("APP_LOG","daily","not showing description");
        $this->writer->enable("MAIL_PORT");
        $this->writer->disable("APP_KEY");
        $this->writer->delete("GRAPHER_BACKEND_MRTG_DBTYPE",true);

        $this->writer->set("TEST_KEY","Test value","It is a test 'description' with /slashes\ and \"quotes\"");

        $this->writer->write();

        // reload file
        $_newEnv = new DotEnvWriter($testFile);
        $variables = $_newEnv->getAll();

        $this->assertIsArray($variables);

        $log = $_newEnv->get("APP_LOG");
        $logDescription = $log - 1;
        $mail = $_newEnv->get("MAIL_PORT");
        $app = $_newEnv->get("APP_KEY");
        $test = $_newEnv->get("TEST_KEY");
        $testDescription = $test - 1;
        $graph = $_newEnv->get("GRAPHER_BACKEND_MRTG_DBTYPE");

        $this->assertEquals("daily",$variables[$log]["value"]);
        $this->assertNotEquals("# not showing description",$variables[$logDescription]["value"]);
        $this->assertTrue($variables[$mail]["status"]);
        $this->assertFalse($variables[$app]["status"]);
        $this->assertEquals("Test value",$variables[$test]["value"]);
        $this->assertNull($variables[$testDescription]["key"]);
        $this->assertEquals("# It is a test 'description' with /slashes\ and \"quotes\"",$variables[$testDescription]["value"]);
        $this->assertFalse($variables[$testDescription]["status"]);
        $this->assertFalse($graph);
    }
}
