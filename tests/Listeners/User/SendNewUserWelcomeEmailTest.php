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
use IXP\Events\User\UserCreated as UserCreatedEvent;
use IXP\Listeners\User\SendNewUserWelcomeEmail;
use IXP\Mail\User\UserCreated as UserCreatedMail;
use Tests\TestCase;

class SendNewUserWelcomeEmailTest extends TestCase
{
    public function testListener()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        new SendNewUserWelcomeEmail()->handle(new UserCreatedEvent($user));

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending new user welcome email to " . $user->username . " (" . $user->name . ")" );

        Mail::assertSent(UserCreatedMail::class, function( UserCreatedMail $mail ) use( $user ) {
            $mail->assertTo($user->email);
            $this->assertSame($user, $mail->user);
            return $mail->user->id === $user->id;
        });
    }
}