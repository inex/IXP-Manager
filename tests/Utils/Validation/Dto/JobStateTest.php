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

use Carbon\Carbon;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Utils\Validation\Backend;
use IXP\Utils\Validation\Dto\JobState;
use IXP\Utils\Validation\Validators\Grapher as GrapherValidator;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class JobStateTest extends TestCase
{
    public function testGetCacheKey()
    {
        $this->assertEquals("validation:job:key", JobState::getCacheKey("key"));

        $uuid = (string) Uuid::uuid4();
        $this->assertEquals("validation:job:" . $uuid, JobState::getCacheKey($uuid));
    }

    public function testDto()
    {
        Carbon::setTestNow('2026-08-05 14:00:00');

        $uuid = (string) Uuid::uuid4();
        $now = Carbon::now()->getTimestamp();

        $jobState = JobState::create($uuid);
        $this->assertEquals($now, $jobState->startedAt);
        $this->assertNull($jobState->finishedAt);
        $this->assertEquals(0, $jobState->progress);
        $this->assertCount(0, $jobState->runners);
        $this->assertTrue($jobState->jsonSerialize()['complete'], "tests every.. and there are none.. so technically true?!");

        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            [
                "started" => $now,
                "finished" => null,
                "progress" => 0,
                "complete" => true, // again a special case, as zero runners
                "validations" => [],
            ],
            $jobState->jsonSerialize(),
            ["started", "finished", "progress", "complete", "validations"],
        );

        Carbon::setTestNow('2026-08-05 14:05:00');
        $later = Carbon::now()->getTimestamp();

        $jobState->finalizeCompletedJob();
        $this->assertEquals($later, $jobState->finishedAt);
        $this->assertEquals(100, $jobState->progress);

        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            [
                "started" => $now,
                "finished" => $later,
                "progress" => 100,
                "complete" => true, // again a special case, as zero runners
                "validations" => [],
            ],
            $jobState->jsonSerialize(),
            ["started", "finished", "progress", "complete", "validations"],
        );
    }

    public function testMarkTestTimedOut()
    {
        Carbon::setTestNow('2026-08-05 14:00:00');

        $uuid = (string) Uuid::uuid4();
        $now = Carbon::now()->getTimestamp();

        $runner = new Backend(GrapherValidator::class);
        $jobState = JobState::create($uuid, [$runner]);

        // Test initial state
        $this->assertFalse($runner->isTimedOut());
        $this->assertCount(1, $jobState->runners);
        $this->assertFalse($jobState->jsonSerialize()['complete']);

        // Mark test timed out, what happens?
        $jobState->markTaskTimedOut(0);
        $this->assertTrue($runner->isTimedOut());
        $this->assertFalse($runner->isComplete(), "this is documented, so test the behavior");
        $this->assertTrue($jobState->jsonSerialize()['complete']);

        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            [
                "started" => $now,
                "finished" => null, // only finalizeCompletedJob updates this
                "progress" => 0,    // only finalizeCompletedJob or recordValidatorResults updates this
                "complete" => true,
                "validations" => [$jobState->runners[0]->toReport()],
            ],
            $jobState->jsonSerialize(),
            ["started", "finished", "progress", "complete", "validations"],
        );

    }

    public function testMarkTestFailed()
    {
        Carbon::setTestNow('2026-08-05 14:00:00');

        $uuid = (string) Uuid::uuid4();
        $now = Carbon::now()->getTimestamp();

        $runner = new Backend(GrapherValidator::class);
        $jobState = JobState::create($uuid, [$runner]);

        // Test initial state
        $this->assertCount(1, $jobState->runners);
        $this->assertFalse($jobState->jsonSerialize()['complete']);

        $this->assertFalse($runner->isFailed());
        $this->assertNull($runner->getFailureInfo());
        $this->assertFalse($runner->isComplete());

        // Mark test failed and see what happens
        $jobState->markTestFailed(0, new \RuntimeException("unexpected!!!"));
        $this->assertTrue($jobState->jsonSerialize()['complete']);

        $this->assertTrue($runner->isFailed());
        $this->assertNotNull($runner->getFailureInfo());
        $this->assertTrue($runner->isComplete());

        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            [
                "started" => $now,
                "finished" => null, // only finalizeCompletedJob updates this
                "progress" => 0,    // only finalizeCompletedJob or recordValidatorResults updates this
                "complete" => true,
                "validations" => [$jobState->runners[0]->toReport()],
            ],
            $jobState->jsonSerialize(),
            ["started", "finished", "progress", "complete", "validations"],
        );
    }


    public function testRecordValidatorResults()
    {
        Carbon::setTestNow('2026-08-05 14:00:00');

        $uuid = (string) Uuid::uuid4();
        $now = Carbon::now()->getTimestamp();

        $anonValidator = new class implements Validator {
            public function getName(): string
            {
                return "test validator";
            }

            public function getDescription(): string
            {
                return "testing the things";
            }

            public function getPriority(): int
            {
                return 100;
            }

            public function run( ValidationBackend $backend ): void
            {
                $backend->software("PHP", "v8.4.0");
                $backend->info("Tests passed");
            }
        };

        $runner = new Backend($anonValidator::class);
        $jobState = JobState::create($uuid, [$runner]);

        // Test initial state
        $this->assertCount(1, $jobState->runners);

        $this->assertFalse($jobState->jsonSerialize()['complete']);
        $this->assertFalse($runner->isFailed());
        $this->assertFalse($runner->isComplete());
        $this->assertFalse($runner->isTimedOut());
        $this->assertNull($runner->getFailureInfo());

        // Ideally would unserialize(serialize()) but cannot since it's an anonymous object
        // Pretend they came from the same place
        $different = new Backend($anonValidator::class);
        $different->run();

        // Record validator results
        $jobState->recordValidatorResults(0, $different, 100.0);
        $runner = $jobState->runners[0]; // reread this, why not.

        $this->assertTrue($jobState->jsonSerialize()['complete']);
        $this->assertFalse($runner->isFailed());
        $this->assertTrue($runner->isComplete());
        $this->assertFalse($runner->isTimedOut());
        $this->assertNull($runner->getFailureInfo());


        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            [
                "started" => $now,
                "finished" => null,   // only finalizeCompletedJob updates this
                "progress" => 100.0,
                "complete" => true,
                "validations" => [$jobState->runners[0]->toReport()],
            ],
            $jobState->jsonSerialize(),
            ["started", "finished", "progress", "complete", "validations"],
        );

    }
}