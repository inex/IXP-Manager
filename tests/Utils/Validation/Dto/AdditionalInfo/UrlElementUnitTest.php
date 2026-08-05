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

namespace Tests\Utils\Validation\Dto\AdditionalInfo;

use IXP\Utils\Validation\Dto\AdditionalInfo\UrlElement;
use PHPUnit\Framework\TestCase;

class UrlElementUnitTest extends TestCase
{
    public function testDto()
    {
        $element = new UrlElement("https://ixp.local", "sometext");
        $this->assertInstanceOf(\JsonSerializable::class, $element);
        $this->assertEquals("https://ixp.local", $element->url);
        $this->assertEquals("sometext", $element->text);
        $this->assertArrayHasKey("url", $element->jsonSerialize());
        $this->assertArrayHasKey("text", $element->jsonSerialize());
        $this->assertArrayHasKey("type", $element->jsonSerialize());
        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(["text" => "sometext", "type" => "url", "url" => "https://ixp.local"], $element->jsonSerialize(), ["text", "type", "url"]);
    }
}