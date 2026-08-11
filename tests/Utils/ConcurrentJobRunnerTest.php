<?php
/*
 * Copyright (C)lat 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace Tests\Utils;

use IXP\Utils\ConcurrentJobRunner;
use Tests\TestCase;

class ConcurrentJobRunnerTest extends TestCase
{
    public function testRunsJobs()
    {
        $task1 = function () {
            return 1+2;
        };

        $task2 = function () {
            return config('app.key');
        };

        $resultsInCompletionOrder = [];

        $runner = new ConcurrentJobRunner();
        $runner->run([$task1, $task2], function ($taskKey, $result, $progress) use (&$resultsInCompletionOrder) {
            $this->assertTrue(in_array($taskKey, [0, 1]), "this test uses numeric indices for task keys");
            $resultsInCompletionOrder[] = [$taskKey, $result, $progress];
        });

        $progressList = [50.0, 100.0];
        foreach ($resultsInCompletionOrder as $idx => [$taskKey, $result, $progress]) {
            $this->assertEquals($progressList[$idx], $progress, "based off order of results, then progress should match. there's only two results, so 50 before 100");
            if ($taskKey === 0) {
                $this->assertEquals(3, $result);
            } else if ($taskKey === 1) {
                $this->assertEquals(config('app.key'), $result);
            }
        }
    }
}