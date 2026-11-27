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

namespace Tests\Listeners\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use IXP\Events\Auth\TwoFactorLocked;
use IXP\Mail\Auth\TwoFactorLocked as TwoFactorLockedMail;
use IXP\Listeners\Auth\SendTwoFactorLockedNotification;
use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\User;
use Tests\TestCase;

class SendTwoFactorLockedNotificationTest extends TestCase
{
    public function testListenerCustUserOneCustomer(): void
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        new SendTwoFactorLockedNotification()->handle(new TwoFactorLocked($user));

        $this->app->terminate();

        Log::shouldHaveReceived( 'warning' )
            ->with( "Two factor credential locked for {$user->username} (using client IP: " . ixp_get_client_ip() . ") due to too many failed attempts. Sending email notification." );

        Mail::assertSent(TwoFactorLockedMail::class, function( TwoFactorLockedMail $mail ) use ( $user ) {
            $mail->assertTo($user->email);
            return $mail->referToCustAdmin->id === $user->customers()->first()->id;
        });
    }

    public function testListenerCustUserMultipleCustomers(): void
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        $newCustomer = new Customer();
        $newCustomer->save();
        $c2u = CustomerToUser::forceCreate([
            'user_id' => $user->id,
            'customer_id' => $newCustomer->id,
            'privs' => User::AUTH_CUSTUSER,
        ]);

        new SendTwoFactorLockedNotification()->handle(new TwoFactorLocked($user));

        $this->app->terminate();

        Log::shouldHaveReceived( 'warning' )
            ->with( "Two factor credential locked for {$user->username} (using client IP: " . ixp_get_client_ip() . ") due to too many failed attempts. Sending email notification." );

        Mail::assertSent(TwoFactorLockedMail::class, function( TwoFactorLockedMail $mail ) use ( $user ) {
            $mail->assertTo($user->email);
            return is_null($mail->referToCustAdmin);
        });
    }

    public function testListenerSuperUserOneCustomer(): void
    {
        Mail::fake();
        Log::spy();

        $user = $this->getSuperUser();

        new SendTwoFactorLockedNotification()->handle(new TwoFactorLocked($user));

        $this->app->terminate();

        Log::shouldHaveReceived( 'warning' )
            ->with( "Two factor credential locked for {$user->username} (using client IP: " . ixp_get_client_ip() . ") due to too many failed attempts. Sending email notification." );

        Mail::assertSent(TwoFactorLockedMail::class, function( TwoFactorLockedMail $mail ) use ( $user ) {
            $mail->assertTo($user->email);
            return is_null($mail->referToCustAdmin);
        });
    }
}