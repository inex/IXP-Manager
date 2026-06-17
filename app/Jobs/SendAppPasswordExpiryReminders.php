<?php

namespace IXP\Jobs;

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

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use IXP\Mail\AppPassword\ExpiringSoon;
use IXP\Models\AppPassword;

class SendAppPasswordExpiryReminders extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $targetDate = Carbon::today()->addDays(14)->startOfDay();

        /** @var \Illuminate\Database\Eloquent\Collection<int, AppPassword> $passwords */
        $passwords = AppPassword::query()
            ->with('user')
            ->whereDate('expires', '>=', $targetDate )
            ->whereDate('expires', '<=', $targetDate->endOfDay() )
            ->get();

        if( $passwords->isEmpty() ) {
            return;
        }

        foreach( $passwords->groupBy('user_id') as $userPasswords ) {
            $user = $userPasswords->first()?->user;

            if( !$user || !$user->email ) {
                continue;
            }

            Mail::to( $user->email )->send( new ExpiringSoon( $user, $userPasswords ) );
        }
    }
}
