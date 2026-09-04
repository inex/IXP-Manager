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
use IXP\Events\Auth\PasswordReset as PasswordResetEvent;
use IXP\Listeners\Auth\PasswordReset;
use IXP\Listeners\Auth\PasswordReset as PasswordResetListener;
use IXP\Mail\Auth\PasswordReset as PasswordResetMail;
use IXP\Models\User;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    public function testListener()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        new PasswordReset()->handle(new PasswordResetEvent($user));

        Log::shouldHaveReceived( 'notice' )
            ->with( $user->username . '(' . $user->name . ') reset their password' );

        Mail::assertSent(PasswordResetMail::class, function( PasswordResetMail $mail ) use( $user ) {
            $this->assertSame($user, $mail->user);
            return $user->id === $mail->user->id;
        });
    }
}