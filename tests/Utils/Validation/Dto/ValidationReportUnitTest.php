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
use IXP\Utils\Validation\Dto\Result;
use IXP\Utils\Validation\Dto\Software;
use IXP\Utils\Validation\Dto\ValidationReport;
use IXP\Utils\Validation\Enums\ResultType;
use PHPUnit\Framework\TestCase;

class ValidationReportUnitTest extends TestCase
{
    public function testDto()
    {
        $report = new ValidationReport(
            "Grapher Validator",
            "The Grapher validator checks grapher stuff",
            1,
            true,
            true,
            true,
            [
                new Software("PHP", "v8.4.0"),
            ],
            [
                new Result("It worked", ResultType::Info),
            ],
            FailureInfo::fromThrowable(new \RuntimeException("until it didn't"))
        );

        $this->assertInstanceOf(\JsonSerializable::class, $report);

        $this->assertEquals("Grapher Validator", $report->name);
        $this->assertEquals("The Grapher validator checks grapher stuff", $report->description);
        $this->assertEquals(1, $report->priority);
        $this->assertCount(1, $report->software);
        $this->assertCount(1, $report->results);
        $this->assertInstanceOf(FailureInfo::class, $report->failure);

        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys([
            "name" => "Grapher Validator",
            "description" => "The Grapher validator checks grapher stuff",
            "priority" => 1,
            "is_complete" => true,
            "is_failed" => true,
            "is_timedout" => true,
            "software" => $report->software,
            "results" => $report->results,
            "failure" => $report->failure,
        ], $report->jsonSerialize(), ["name", "description", "priority", "is_complete", "is_failed", "is_timedout", "software", "results", "failure"]);


        $report2 = new ValidationReport(
            "Grapher Validator",
            "The Grapher validator checks grapher stuff",
            1,
            false,
            false,
            false,
            [],
            [],
        );
        $this->assertFalse($report2->isComplete);
        $this->assertFalse($report2->isFailed);
        $this->assertFalse($report2->isTimedOut);


    }
}