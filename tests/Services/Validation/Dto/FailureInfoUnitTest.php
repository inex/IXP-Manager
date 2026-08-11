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

namespace Tests\Services\Validation\Dto;

use IXP\Services\Validation\Dto\FailureInfo;
use PHPUnit\Framework\TestCase;

class FailureInfoUnitTest extends TestCase
{
    public function testDto()
    {
        $info = new FailureInfo("SomeClass", "something happened", "file.php", 100);
        $this->assertInstanceOf(\JsonSerializable::class, $info);
        $this->assertEquals("SomeClass", $info->exception);
        $this->assertEquals("something happened", $info->message);
        $this->assertEquals("file.php", $info->file);
        $this->assertEquals(100, $info->line);
        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(["exception" => "SomeClass", "message" => "something happened", "file" => "file.php", "line" => 100], $info->jsonSerialize(), ["exception", "message", "file", "line"]);

        $exception = new \RuntimeException("a problem occurred");
        $failureInfo = FailureInfo::fromThrowable($exception);
        $this->assertEquals("RuntimeException", $failureInfo->exception);
        $this->assertEquals($exception->getMessage(), $failureInfo->message);
        $this->assertEquals($exception->getFile(), $failureInfo->file);
        $this->assertEquals($exception->getLine(), $failureInfo->line);

    }
}