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

namespace Tests\Utils\Validation\Dto;

use IXP\Utils\Validation\Dto\FailureInfo;
use IXP\Utils\Validation\Dto\Software;
use PHPUnit\Framework\TestCase;

class SoftwareUnitTest extends TestCase
{
    public function testDto()
    {
        $info = new Software("PHP", "v8.4.0");
        $this->assertInstanceOf(\JsonSerializable::class, $info);
        $this->assertEquals("PHP", $info->name);
        $this->assertEquals("v8.4.0", $info->version);

        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(["name" => "PHP", "version" => "v8.4.0"], $info->jsonSerialize(), ["name", "version"]);

    }
}