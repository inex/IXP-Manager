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
    protected string $testFile = '.env.test';
    protected DotEnvWriter $writer;

    /**
     * Utility function to get a .env.test file content and variable list
     *
     */
    public function testReader(): void
    {
        $this->testFile = base_path($this->testFile);
        $this->writer = new DotEnvWriter($this->testFile);
        $variables = $this->writer->getAll();

        info("Variables:\n".var_export($variables,1));
        $this->assertIsArray($variables);

    }

}
