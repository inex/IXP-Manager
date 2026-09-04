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

namespace Tests\Listeners\Customer;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use IXP\Events\Customer\BillingDetailsChanged as BillingDetailsChangedEvent;
use IXP\Listeners\Customer\BillingDetailsChanged;
use IXP\Mail\Customer\BillingDetailsChanged as BillingDetailsChangedMail;
use IXP\Models\Customer;
use Tests\TestCase;

class BillingDetailsChangedTest extends TestCase
{
    public function testListener()
    {
        Mail::fake();

        $user = $this->getCustUser();

        $ocbd = $user->customer->companyBillingDetail;
        $ocbd->billingEmail = "oldemail@example.net";
        $ocbd->billingTelephone = "+11111111";
        $ocbd->save();

        $cbd = $user->customer->companyBillingDetail()->first();
        $cbd->billingEmail = "newemail@example.net";
        $cbd->billingTelephone = "+12222222";
        $cbd->save();

        config()->set( 'ixp_fe.customer.billing_updates_notify', 'billing-notifications@ixp.local' );

        Log::spy();

        new BillingDetailsChanged()->handle( new BillingDetailsChangedEvent( $ocbd, $cbd ) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending Billing Details Changed email regarding customer [" . $user->customer->id . "|" . $user->customer->name . "] " );

        Mail::assertSent(BillingDetailsChangedMail::class, function( BillingDetailsChangedMail $mail ) use( $ocbd, $cbd ) {
            $mail->assertTo( 'billing-notifications@ixp.local' );

            $this->assertSame($ocbd, $mail->ocbd);
            $this->assertSame($cbd, $mail->cbd);
            return $ocbd->id === $mail->ocbd->id &&
                $cbd->id === $mail->cbd->id
            ;
        });
    }

    public function testListener_NoNotificationsEmail()
    {
        Mail::fake();

        $user = $this->getCustUser();

        $ocbd = $user->customer->companyBillingDetail;
        $ocbd->billingEmail = "oldemail@example.net";
        $ocbd->billingTelephone = "+11111111";
        $ocbd->save();

        $cbd = $user->customer->companyBillingDetail()->first();
        $cbd->billingEmail = "newemail@example.net";
        $cbd->billingTelephone = "+12222222";
        $cbd->save();

        config()->set( 'ixp_fe.customer.billing_updates_notify', false ); // matches default in config

        new BillingDetailsChanged()->handle( new BillingDetailsChangedEvent( $ocbd, $cbd ) );

        Mail::assertNothingSent();
    }


    public function testListener_NoNotificationsIfResold()
    {
        Mail::fake();

        $user = $this->getCustUser();

        $ocbd = $user->customer->companyBillingDetail;
        $ocbd->billingEmail = "oldemail@example.net";
        $ocbd->billingTelephone = "+11111111";
        $ocbd->save();

        $cbd = $user->customer->companyBillingDetail()->first();
        $cbd->billingEmail = "newemail@example.net";
        $cbd->billingTelephone = "+12222222";
        $cbd->save();

        $customer = $user->customer;
        $customer->reseller = Customer::where('name', 'HEAnet')->first()->id;
        $customer->save();

        config()->set( 'ixp_fe.customer.billing_updates_notify', 'billing-notifications@ixp.local' );

        new BillingDetailsChanged()->handle( new BillingDetailsChangedEvent( $ocbd, $cbd ) );

        Mail::assertNothingSent();
    }
}