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

use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use IXP\Listeners\Auth\LoginSuccessful;
use IXP\Models\CustomerToUser;
use Tests\TestCase;

class LoginSuccessfulTest extends TestCase
{
    private function resetLoginHistory(CustomerToUser $c2u): void
    {
        // Reset CustomerToUser login values for assertions later
        $c2u->last_login_date = null;
        $c2u->last_login_via = null;
        $c2u->last_login_from = null;
        $c2u->save();

        // Remove UserLoginHistories for assertions later
        $c2u->userLoginHistories()->delete();
    }

    public function testListener()
    {
        Carbon::setTestNow( Carbon::now() );
        $now = Carbon::getTestNow();

        $user = $this->getCustUser();
        $this->resetLoginHistory( $user->currentCustomerToUser );

        Log::spy();

        new LoginSuccessful()->handle( new Login( 'web', $user, false) );

        // Log is always written
        Log::shouldHaveReceived( 'notice' )
            ->once()
            ->with( 'Login successful for user "' . $user->username. '" from IP 127.0.0.1.' )
        ;

        $c2u = $user->currentCustomerToUser;
        $c2u->refresh();

        $this->assertEquals($now->timestamp, $c2u->last_login_date->timestamp);
        $this->assertEquals('Login', $c2u->last_login_via);
        $this->assertEquals('127.0.0.1', $c2u->last_login_from);

        $this->assertCount(1, $user->currentCustomerToUser->userLoginHistories);

        $history = $user->currentCustomerToUser->userLoginHistories[0];
        $this->assertEquals($c2u->id, $history->customer_to_user_id);
        $this->assertEquals($now->timestamp, $history->at->timestamp);
        $this->assertEquals('Login', $history->via);
        $this->assertEquals('127.0.0.1', $history->ip);
    }

    public function testListenerLoginHistoryDisabled()
    {
        Carbon::setTestNow( Carbon::now() );
        $now = Carbon::getTestNow();

        $user = $this->getCustUser();
        $this->resetLoginHistory( $user->currentCustomerToUser );

        // Disable UserLoginHistory recording
        config()->set('ixp_fe.login_history.enabled', false);

        Log::spy();

        new LoginSuccessful()->handle( new Login( 'web', $user, false) );

        // Log is always written
        Log::shouldHaveReceived( 'notice' )
            ->once()
            ->with( 'Login successful for user "' . $user->username . '" from IP 127.0.0.1.' );

        // CustomerToUser continues to be updated
        $c2u = $user->currentCustomerToUser;
        $c2u->refresh();
        $this->assertEquals($now->timestamp, $c2u->last_login_date->timestamp);
        $this->assertEquals('Login', $c2u->last_login_via);
        $this->assertEquals('127.0.0.1', $c2u->last_login_from);

        // Configuration setting is respected, no UserLoginHistory
        $this->assertCount(0, $user->currentCustomerToUser->userLoginHistories);
    }

    public function testListenerNoLogsDuringSwitchFrom()
    {
        $user = $this->getCustUser();
        $this->resetLoginHistory( $user->currentCustomerToUser );

        session()->put('switched_user_from', $this->getSuperUser()->id);

        Log::spy();
        new LoginSuccessful()->handle( new Login( 'web', $user, false) );
        Log::shouldHaveReceived( 'notice' )
            ->once()
            ->with( 'Login successful for user "' . $user->username . '" from IP 127.0.0.1.' );

        $c2u = $user->currentCustomerToUser;
        $c2u->refresh();

        // Since switch from key was set, these aren't touched
        $this->assertNull($c2u->last_login_date);
        $this->assertNull($c2u->last_login_via);
        $this->assertNull($c2u->last_login_from);

        // And no login history will be created, even though config is still enabled
        $this->assertTrue(config('ixp_fe.login_history.enabled'));
        $this->assertCount(0, $user->currentCustomerToUser->userLoginHistories);
    }
}