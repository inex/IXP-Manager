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

namespace Tests\Services\Validation;

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Services\Validation\Backend;
use IXP\Services\Validation\Dto\FailureInfo;
use IXP\Services\Validation\Enums\ResultType;
use Mockery;
use Tests\TestCase;

class BackendTest extends TestCase
{
    public function testValidationBackend()
    {
        $validator = Mockery::mock(Validator::class);
        $backend = new Backend($validator::class);
        $this->assertEmpty($backend->getResults());
        $this->assertEmpty($backend->getSoftware());

        $backend->software("MySQL", "v1.0.0");
        $backend->software("pdo-mysql", "v8.4.0");
        $backend->debug("debug message!");
        $backend->info("some informational message");
        $backend->suggestion("why not try");
        $backend->warning("something isn't right");
        $backend->error("we got a big problem");

        $this->assertCount(2, $backend->getSoftware());
        $this->assertEquals("MySQL", $backend->getSoftware()[0]->name);
        $this->assertEquals("v1.0.0", $backend->getSoftware()[0]->version);

        $this->assertEquals("pdo-mysql", $backend->getSoftware()[1]->name);
        $this->assertEquals("v8.4.0", $backend->getSoftware()[1]->version);

        $this->assertCount(5, $backend->getResults());
        $this->assertEquals("debug message!", $backend->getResults()[0]->message);
        $this->assertEquals(ResultType::Debug, $backend->getResults()[0]->type);

        $this->assertEquals("some informational message", $backend->getResults()[1]->message);
        $this->assertEquals(ResultType::Info, $backend->getResults()[1]->type);

        $this->assertEquals("why not try", $backend->getResults()[2]->message);
        $this->assertEquals(ResultType::Suggestion, $backend->getResults()[2]->type);

        $this->assertEquals("something isn't right", $backend->getResults()[3]->message);
        $this->assertEquals(ResultType::Warning, $backend->getResults()[3]->type);

        $this->assertEquals("we got a big problem", $backend->getResults()[4]->message);
        $this->assertEquals(ResultType::Error, $backend->getResults()[4]->type);
    }

    public function testValidationRunnerOk()
    {
        $validator = new class implements Validator {
            public function getDescription(): string
            {
                return "desc";
            }
            public function getName(): string
            {
                return "name";
            }
            public function getPriority(): int
            {
                return 1;
            }
            public function run( ValidationBackend $backend ): void
            {

            }
        };

        $backend = new Backend($validator::class);
        $this->assertInstanceOf($validator::class, $backend->getValidator());

        $this->assertFalse($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
        $backend->run();
        $this->assertTrue($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
    }

    public function testValidationRunnerFails()
    {
        $validator = new class implements Validator {
            public function getDescription(): string
            {
                return "desc";
            }
            public function getName(): string
            {
                return "name";
            }
            public function getPriority(): int
            {
                return 1;
            }
            public function run( ValidationBackend $backend ): void
            {
                throw new \RuntimeException("unexpected exception!");
            }
        };

        $backend = new Backend($validator::class);
        $this->assertInstanceOf($validator::class, $backend->getValidator());

        $this->assertFalse($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
        $backend->run();
        $this->assertTrue($backend->isComplete(), "failure marks validation as complete");
        $this->assertTrue($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertInstanceOf(FailureInfo::class, $backend->getFailureInfo());

        $failure = $backend->getFailureInfo();
        $this->assertEquals("unexpected exception!", $failure->message);
        $this->assertEquals(\RuntimeException::class, $failure->exception);
        $this->assertNotEquals(0, $failure->line);
        $this->assertEquals(__FILE__, $failure->file);
    }

    public function testValidationRunnerMarkTimedOut()
    {
        $validator = new class implements Validator {
            public function getDescription(): string
            {
                return "desc";
            }
            public function getName(): string
            {
                return "name";
            }
            public function getPriority(): int
            {
                return 1;
            }
            public function run( ValidationBackend $backend ): void
            {
            }
        };

        $backend = new Backend($validator::class);
        $this->assertInstanceOf($validator::class, $backend->getValidator());

        $this->assertFalse($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
        $backend->markTimedOut();
        $this->assertFalse($backend->isComplete(), "timeout doesn't mark test as complete");
        $this->assertFalse($backend->isFailed());
        $this->assertTrue($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
    }
}