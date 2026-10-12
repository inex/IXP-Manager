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

namespace Tests\Console\Validator;

use Illuminate\Support\Facades\Event;
use IXP\Models\CustomerToUser;
use IXP\Models\User;
use Tests\TestCase;

class RunCommandTest extends TestCase
{
    public function testDefaultSeverity()
    {
        $this->artisan( 'validator:run' , [])
                ->expectsOutputToContain( "Severity level: suggestion" )
                ->assertOk();
    }

    public function testSeverityParam()
    {
        $this->artisan( 'validator:run', [
                '--severity' => 'info',
        ] )
                ->expectsOutputToContain( "Severity level: info" )
                ->assertOk();

        $this->artisan( 'validator:run', [
                '--severity'    => 'WARNING',
        ] )
            ->expectsOutputToContain( "Severity level: warning")
            ->assertOk();
    }

    public function testUnknownSeverity()
    {
        $this->artisan( 'validator:run', [
                '--severity'    => 'whatisthat',
        ] )
            ->expectsOutput("Unknown severity 'whatisthat'")
            ->assertFailed();
    }
}