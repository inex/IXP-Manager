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

namespace Tests\Console\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * ExpungeApiKeysTest
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 */
class ExpungeApiKeysTest extends TestCase
{
    protected function tearDown(): void
    {
        DB::table('api_keys')->whereIn('api_key', [
            'phpunit-expunge-old',
            'phpunit-expunge-recent',
            'phpunit-expunge-no-expiry',
            'phpunit-expunge-future',
        ])->delete();

        Carbon::setTestNow();

        parent::tearDown();
    }

    public function testExpungesOnlyApiKeysExpiredMoreThan28DaysAgo()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-25 12:00:00'));
        $userId = DB::table('user')->value('id');

        $this->assertNotNull($userId);

        DB::table('api_keys')->insert([
            [
                'user_id' => $userId,
                'api_key' => 'phpunit-expunge-old',
                'expires' => Carbon::now()->subDays(29),
                'allowed_ips' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $userId,
                'api_key' => 'phpunit-expunge-recent',
                'expires' => Carbon::now()->subDays(27),
                'allowed_ips' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $userId,
                'api_key' => 'phpunit-expunge-future',
                'expires' => Carbon::now()->addDays(1),
                'allowed_ips' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $this->artisan('utils:expunge-api-keys')
            ->assertExitCode(0);

        $this->assertEquals(0, DB::table('api_keys')->where('api_key', 'phpunit-expunge-old')->count());
        $this->assertEquals(1, DB::table('api_keys')->where('api_key', 'phpunit-expunge-recent')->count());
        $this->assertEquals(1, DB::table('api_keys')->where('api_key', 'phpunit-expunge-future')->count());
    }
}
