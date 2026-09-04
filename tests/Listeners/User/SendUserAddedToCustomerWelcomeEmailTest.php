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

namespace Tests\Listeners\User;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use IXP\Events\User\UserAddedToCustomer as UserAddedToCustomerEvent;
use IXP\Listeners\User\SendUserAddedToCustomerWelcomeEmail;
use IXP\Listeners\User\SendUserAddedToCustomerWelcomeEmail as SendUserAddedToCustomerWelcomeEmailListener;
use IXP\Mail\User\UserAddedToCustomer as UserAddedToCustomerMail;
use Tests\TestCase;

class SendUserAddedToCustomerWelcomeEmailTest extends TestCase
{
    public function testListener()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        new SendUserAddedToCustomerWelcomeEmail()->handle(new UserAddedToCustomerEvent($user->currentCustomerToUser));

        Log::shouldHaveReceived( 'notice' )
            ->with( "Emailing user " . $user->username . " to notify they were added to customer " . $user->currentCustomerToUser->customer->name );

        Mail::assertSent(UserAddedToCustomerMail::class, function( UserAddedToCustomerMail $mail ) use( $user ) {
            $mail->assertTo($user->email);
            $this->assertSame($user->currentCustomerToUser, $mail->c2u);
            return $mail->c2u->id === $user->currentCustomerToUser->id;
        });
    }
}