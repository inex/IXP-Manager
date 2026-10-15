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

namespace Tests\Jobs;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use IXP\Jobs\SendAppPasswordExpiryReminders;
use IXP\Mail\AppPassword\ExpiringSoon;
use IXP\Models\AppPassword;
use IXP\Models\User;
use Tests\TestCase;

class SendAppPasswordExpiryRemindersTest extends TestCase
{
    public function testSendsReminderForPasswordsExpiringInFourteenDays(): void
    {
        Mail::fake();

        Carbon::setTestNow('2026-06-17 10:00:00');

        $user = User::create();
        $random = uniqid('reminder-user-', true);
        $user->name = 'Reminder User';
        $user->username = $random;
        $user->email = $random . '@example.net';
        $user->save();

        $password = AppPassword::forceCreate([
            'user_id' => $user->id,
            'password' => 'hash-value',
            'expires' => Carbon::today()->addDays(14)->setTime(12, 0, 0),
            'description' => 'API token',
        ]);

        ( new SendAppPasswordExpiryReminders() )->handle();

        Mail::assertSent(ExpiringSoon::class, function( Mailable $mail ) use( $user ) {
            return $mail->hasTo($user->email);
        });

        $password->delete();
        $user->delete();

    }

    public function testDoesNotSendReminderForNonMatchingExpiryDate(): void
    {
        Mail::fake();

        Carbon::setTestNow('2026-06-17 10:00:00');

        $user = User::create();
        $random = uniqid('no-reminder-user-', true);
        $user->name = 'No Reminder User';
        $user->username = $random;
        $user->email = $random . '@example.net';
        $user->save();

        $password = AppPassword::forceCreate([
            'user_id' => $user->id,
            'password' => 'hash-value',
            'expires' => Carbon::today()->addDays(13)->setTime(12, 0, 0),
            'description' => 'CLI token',
        ]);

        ( new SendAppPasswordExpiryReminders() )->handle();

        Mail::assertNothingSent();
        
        $password->delete();
        $user->delete();
    }
}
