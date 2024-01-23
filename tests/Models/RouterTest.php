<?php

namespace Tests\Models;

/*
 * Copyright (C) 2009 - 2022 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use IXP\Models\Router;

use Tests\TestCase;

/**
 * PHPUnit test class to test the Router model
 *
 * @author     Barry O'Donovan  <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Tests\Models
 * @copyright  Copyright (C) 2009 - 2022 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterTest extends TestCase
{

    public function testLastUpdatedGreaterThanSeconds()
    {
        $r = new Router();

        $this->assertNull( $r->lastUpdatedGreaterThanSeconds(10));

        $r->last_updated = Carbon::now();
        $this->assertFalse( $r->lastUpdatedGreaterThanSeconds(10));

        $r->last_updated = Carbon::now()->subMinute();
        $this->assertTrue( $r->lastUpdatedGreaterThanSeconds(10));
    }

    public function testIsUpdating()
    {
        $r = new Router();

        $this->assertNull( $r->isUpdating());

        $r->last_update_started = Carbon::now()->subMinute();
        $this->assertTrue( $r->isUpdating());

        $r->last_updated = Carbon::now()->subMinutes(2);
        $this->assertTrue( $r->isUpdating());

        $r->last_updated = $r->last_update_started;
        $this->assertFalse( $r->isUpdating());

        $r->last_updated = Carbon::now();
        $this->assertFalse( $r->isUpdating());
    }

    public function testIsUpdatingTakingLongerThanSeconds()
    {
        $r = new Router();

        $this->assertNull( $r->isUpdateTakingLongerThanSeconds(60));

        $r->last_update_started = Carbon::now()->subMinute();
        $r->last_updated = $r->last_update_started;
        $this->assertNull( $r->isUpdateTakingLongerThanSeconds(60));

        $r->last_updated = Carbon::now();
        $this->assertNull( $r->isUpdateTakingLongerThanSeconds(60));

        $r->last_update_started = Carbon::now()->subMinutes(2);
        $r->last_updated = Carbon::now()->subMinutes(3);
        $this->assertTrue( $r->isUpdateTakingLongerThanSeconds(60));

        $r->last_update_started = Carbon::now()->subMinutes(1);
        $this->assertFalse( $r->isUpdateTakingLongerThanSeconds(61));

        $r->last_update_started = Carbon::now()->subMinutes(1);
        $this->assertTrue( $r->isUpdateTakingLongerThanSeconds(59));
    }

}