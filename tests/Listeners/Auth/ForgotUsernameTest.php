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
use IXP\Events\Auth\ForgotUsername as ForgotUsernameEvent;
use IXP\Listeners\Auth\ForgotUsername;
use IXP\Listeners\Auth\ForgotUsername as ForgotUsernameListener;
use IXP\Mail\Auth\ForgotUsername as ForgotUsernameMail;
use Tests\TestCase;

class ForgotUsernameTest extends TestCase
{
    public function testListener()
    {
        Mail::fake();
        Log::spy();

        $user1 = $this->getCustUser();
        $user1->email = 'forgotusernametest1@example.net';
        $user1->save();

        $user2 = $this->getCustAdminUser();
        $user2->email = 'forgotusernametest1@example.net';
        $user2->save();

        new ForgotUsername()->handle(new ForgotUsernameEvent(collect([$user1, $user2]), 'forgotusernametest1@example.net'));

        Log::shouldHaveReceived( 'notice' )
            ->with( 'Sending username reminder email to forgotusernametest1@example.net' );

        Mail::assertSent(ForgotUsernameMail::class, function( ForgotUsernameMail $mail ) use( $user1, $user2 ) {
            $mail->assertTo('forgotusernametest1@example.net');
            $this->assertCount(2, $mail->users);
            $this->assertSame($user1, $mail->users->first());
            $this->assertSame($user2, $mail->users->last());
            return $user1->id === $mail->users->first()->id &&
                $user2->id === $mail->users->last()->id
            ;
        });
    }
}