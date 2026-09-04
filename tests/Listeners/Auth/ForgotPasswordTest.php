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

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use IXP\Events\Auth\ForgotPassword as ForgotPasswordEvent;
use IXP\Listeners\Auth\ForgotPassword;
use IXP\Listeners\Auth\ForgotPassword as ForgotPasswordListener;
use IXP\Mail\Auth\ForgotPassword as ForgotPasswordMail;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    public function testListener()
    {
        Mail::fake();
        Log::spy();

        $token = (string) Uuid::uuid4();

        $user = $this->getCustUser();

        new ForgotPassword()->handle(new ForgotPasswordEvent($token, $user));

        Log::shouldHaveReceived( 'notice' )
            ->with( $user->username . " has forgot their password - sending email with password reset link" );

        Mail::assertSent(ForgotPasswordMail::class, function( ForgotPasswordMail $mail ) use( $user, $token ) {
            $mail->assertTo($user->email);
            $this->assertSame($user, $mail->user);
            $this->assertEquals($token, $mail->token);
            return $mail->user->id === $user->id &&
                $mail->token === $token;
        });
    }
}