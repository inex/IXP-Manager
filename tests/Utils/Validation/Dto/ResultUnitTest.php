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

use IXP\Utils\Validation\Dto\AdditionalInfo\TextElement;
use IXP\Utils\Validation\Dto\AdditionalInfo\UrlElement;
use IXP\Utils\Validation\Dto\Result;
use IXP\Utils\Validation\Enums\ResultType;
use PHPUnit\Framework\TestCase;

class ResultUnitTest extends TestCase
{
    public function testDto()
    {
        $result = new Result("Reporting back...", ResultType::Info);
        $this->assertEquals("Reporting back...", $result->message);
        $this->assertEquals(ResultType::Info, $result->type);

        // Test passing absolute docs url
        $this->assertNull($result->docsUrl);
        $result->withDocsUrl("https://ixp.local/docs/page");
        $this->assertEquals("https://ixp.local/docs/page", $result->docsUrl);

        // Test passing absolute settings url
        $this->assertNull($result->settingsUrl);
        $result->withSettingsUrl("https://ixp.local/settings");
        $this->assertEquals("https://ixp.local/settings", $result->settingsUrl);

        // Test passing call to action
        $this->assertNull($result->callToAction);
        $result->withCallToAction("Click here to fix", "https://ixp.local/call-to-action");
        $this->assertNotNull($result->callToAction);
        $this->assertEquals("Click here to fix", $result->callToAction->text);
        $this->assertEquals("https://ixp.local/call-to-action", $result->callToAction->url);

        // Test we can pass a number of additional info element types
        $this->assertCount(0, $result->additionalInfo);

        //  - first element
        $result->addAdditionalInfoText("Hey, please check the following things out:");
        $this->assertCount(1, $result->additionalInfo);
        $this->assertInstanceOf(TextElement::class, $result->additionalInfo[0]);
        $this->assertEquals("Hey, please check the following things out:", $result->additionalInfo[0]->text);

        //  - second element
        $result->addAdditionalInfoUrl("https://google.com", "Check out this site, it's good stuff");
        $this->assertCount(2, $result->additionalInfo);
        $this->assertInstanceOf(TextElement::class, $result->additionalInfo[0]);
        $this->assertEquals("Hey, please check the following things out:", $result->additionalInfo[0]->text);

        $this->assertInstanceOf(UrlElement::class, $result->additionalInfo[1]);
        $this->assertEquals("Check out this site, it's good stuff", $result->additionalInfo[1]->text);
        $this->assertEquals("https://google.com", $result->additionalInfo[1]->url);

        //  - third element
        $customAdditionalInfo = new TextElement("some custom element");
        $result->addAdditionalInfo($customAdditionalInfo);
        $this->assertCount(3, $result->additionalInfo);
        $this->assertInstanceOf(TextElement::class, $result->additionalInfo[0]);
        $this->assertEquals("Hey, please check the following things out:", $result->additionalInfo[0]->text);

        $this->assertInstanceOf(UrlElement::class, $result->additionalInfo[1]);
        $this->assertEquals("Check out this site, it's good stuff", $result->additionalInfo[1]->text);
        $this->assertEquals("https://google.com", $result->additionalInfo[1]->url);

        $this->assertSame($customAdditionalInfo, $result->additionalInfo[2]);
        $this->assertInstanceOf(TextElement::class, $result->additionalInfo[2]);
        $this->assertEquals("some custom element", $result->additionalInfo[2]->text);

        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            [
                "message" => "Reporting back...",
                "type" => ResultType::Info,
                "docs_url" => "https://ixp.local/docs/page",
                "settings_url" => "https://ixp.local/settings",
                "call_to_action" => $result->callToAction,
                "additional_info" => [
                    $result->additionalInfo[0],
                    $result->additionalInfo[1],
                    $result->additionalInfo[2],
                ]
            ],
            $result->jsonSerialize(),
            ["message", "type", "docs_url", "settings_url", "call_to_action", "additional_info"]
        );
    }
}