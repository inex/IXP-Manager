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

namespace IXP\Listeners\Customer;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use IXP\Events\Customer\BillingDetailsChanged as BillingDetailsChangedEvent;

use IXP\Mail\Customer\BillingDetailsChanged as BillingDetailsChangedMailable;

final class BillingDetailsChanged
{
    public function handle( BillingDetailsChangedEvent $e ): void
    {
        if( !config( 'ixp_fe.customer.billing_updates_notify' ) || $e->ocbd->customer->resellerObject()->exists() ) {
            return;
        }

        Mail::to( config( 'ixp_fe.customer.billing_updates_notify' ) )->send( new BillingDetailsChangedMailable( $e->ocbd, $e->cbd ) );
        Log::notice("Sending Billing Details Changed email regarding customer [" . $e->ocbd->customer->id . "|" . $e->ocbd->customer->name . "] ");
    }
}