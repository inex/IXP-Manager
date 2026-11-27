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

namespace IXP\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use IXP\Mail\Auth\LoginsThrottled;
use IXP\Models\User;
use IXP\Services\Auth\PasswordLockoutPolicy;

/**
 * Emails users whose logins have been throttled because of failed password attempts.
 *
 * This is done here rather than in the login request. Sending mail takes time, and it would only be
 * spent for usernames that exist, which a client could measure to learn which usernames are real.
 */
class SendLoginsThrottledNotifications extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        $users = User::query()
            ->where( 'password_fail_count', '>=', PasswordLockoutPolicy::threshold() )
            ->whereNull( 'logins_throttled_notified_at' )
            ->where( 'password_last_fail_at', '>=', PasswordLockoutPolicy::expiryCutoff() )
            ->get();

        foreach( $users as $user ) {
            // Record the notification before sending, so a failing mail server cannot cause an email a minute.
            $user->logins_throttled_notified_at = now();
            $user->save();

            if( !$user->email ) {
                continue;
            }

            Mail::to( $user->email )->send( new LoginsThrottled );
            Log::notice( "Sent logins throttled notification email to {$user->username}" );
        }
    }
}
