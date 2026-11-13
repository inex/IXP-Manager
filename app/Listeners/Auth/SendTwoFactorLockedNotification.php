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

namespace IXP\Listeners\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use IXP\Events\Auth\TwoFactorLocked;
use IXP\Mail\Auth\TwoFactorLocked as TwoFactorLockedMailable;

/**
 * SendTwoFactorLockedNotification
 * Handles TwoFactorLockout events - sends email to user about the lockout
 */
final class SendTwoFactorLockedNotification
{
    public function handle( TwoFactorLocked $e ): void
    {
        // We should refer to the cust admin the user is not a superuser, and is only attached to one customer.
        if (!$e->user->isSuperUser() && $e->user->customers->count() === 1) {
            $referToCustAdmin = $e->user->customers->first();
        } else {
            $referToCustAdmin = null;
        }

        Mail::to( $e->user->email )->send( new TwoFactorLockedMailable( $referToCustAdmin ) );
        Log::warning( "Two factor credential locked for {$e->user->username} (using client IP: " . ixp_get_client_ip() . ") due to too many failed attempts. Sending email notification." );
    }
}