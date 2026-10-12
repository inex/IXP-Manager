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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests\Listeners\Auth;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;
use IXP\Listeners\Auth\LoginFailed;
use IXP\Models\User;
use Tests\TestCase;

class LoginFailedTest extends TestCase
{
    public function testListener()
    {
        $user1 = User::create();
        $user1->name = 'Reminder User';
        $user1->username = "failedlogin";
        $user1->email = 'failedlogin@example.net';
        $user1->save();

        Log::spy();

        new LoginFailed()->handle( new Failed( 'web', $user1, [ 'username' => 'failedlogin', 'password' => 'incorrect' ] ) );

        Log::shouldHaveReceived( 'warning' )
            ->once()
            ->with( 'Login failed for user [failedlogin] from IP [127.0.0.1]' )
        ;
    }
}