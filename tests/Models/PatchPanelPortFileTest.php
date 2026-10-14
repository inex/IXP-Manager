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

namespace Tests\Models;

use Illuminate\Foundation\Testing\TestCase;
use IXP\Models\PatchPanelPortFile;

class PatchPanelPortFileTest extends TestCase
{
    public function testTruncateSupportsLongWithNoExtension(): void
    {
        $model = new PatchPanelPortFile();
        $model->name = str_repeat('A', 100);
        $this->assertEquals(str_repeat('A', 77) . "...", $model->nameTruncated());
    }
    public function testTruncateReturnsFinalExtension(): void
    {
        $model = new PatchPanelPortFile();
        $model->name = str_repeat('A', 100) . ".tar.gz";
        $this->assertEquals(str_repeat('A', 74) . "....gz", $model->nameTruncated());
    }

    public function testTruncateReturnsUtf8SafeOutput(): void
    {
        $model = new PatchPanelPortFile();
        $model->name = str_repeat('é', 100) . '.pdf';
        $this->assertEquals(str_repeat('é', 73) . "....pdf", $model->nameTruncated());
    }
}