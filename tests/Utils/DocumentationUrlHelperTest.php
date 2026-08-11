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

declare(strict_types = 1);

namespace Tests\Utils;

use Tests\TestCase;

class DocumentationUrlHelperTest extends TestCase
{
    public function testUrl(): void
    {
        $this->assertEquals(
            "https://docs.ixpmanager.org/999.9/some-page",
            documentation_url("some-page")
        );

        $this->assertEquals(
            "https://docs.ixpmanager.org/999.9/some-page",
            documentation_url("/some-page")
        );

        config()->set('ixp_fe.documentation.base_url', "http://localhost:8000");

        $this->assertEquals(
            "http://localhost:8000/some-page",
            documentation_url("some-page")
        );
    }
}