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

namespace Tests\Listeners\Customer\Note;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use IXP\Events\Customer\Note\Created;
use IXP\Events\Customer\Note\Edited;
use IXP\Events\Customer\Note\Deleted;
use IXP\Listeners\Customer\Note\EmailOnChange as EmailOnChangeListener;
use IXP\Mail\Customer\Note\Changed as ChangedEmail;
use IXP\Models\CustomerNote;
use Tests\TestCase;

/**
 * This test uses event instead of directly calling handle() because the EmailOnChange listener
 * sets up multiple subscription handlers, and testing this way provides coverage of this.
 * Testing strategy could split this up in future.
 */
class EmailOnChangeTest extends TestCase
{
    public function testListenerWithSoleRecipient()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        config()->set( 'ixp_fe.customer.notes.only_send_to', 'note-ifications@ixp.local' );

        // CREATED
        $ocn = null;
        $cn = new CustomerNote();
        $cn->private = 0;
        $cn->customer_id = $user->customer->id;
        $cn->title = "IMPORTANT NOTES READ FIRST";
        $cn->note = "Note contents 1";
        $cn->save();

        $noteAuthor = $this->getSuperUser();

        event( new Created( null, $cn, $noteAuthor) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending note created email for note " . $cn->id );

        Mail::assertSent(ChangedEmail::class, function( ChangedEmail $mail ) use ( $cn ) {
            $mail->assertTo( 'note-ifications@ixp.local' );

            $this->assertNull($mail->event->oldNote());
            $this->assertSame($cn, $mail->event->note());
            return
                $mail->event->actionDescription() === 'Created' &&
                $cn->id === $mail->event->note()->id &&
                null === $mail->event->oldNote()
            ;
        });

        // EDITED
        $ocn = $cn;

        $cn = CustomerNote::findOrFail( $cn->id );
        $cn->note = "Note contents 2";
        $cn->save();

        event( new Edited( $ocn, $cn, $noteAuthor) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending note edited email for note " . $cn->id );

        Mail::assertSent(ChangedEmail::class, function( ChangedEmail $mail ) use ( $ocn, $cn ) {
            $mail->assertTo( 'note-ifications@ixp.local' );

            return
                $mail->event->actionDescription() === 'Edited' &&
                $cn->id === $mail->event->note()->id &&
                $ocn->id === $mail->event->oldNote()->id
            ;
        });


        // DELETED
        $cn = CustomerNote::findOrFail( $cn->id );
        $cn->delete();

        event( new Deleted(  null, $cn, $noteAuthor ) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending note deleted email for note " . $cn->id );

        Mail::assertSent(ChangedEmail::class, function( ChangedEmail $mail ) use ( $ocn, $cn ) {
            $mail->assertTo( 'note-ifications@ixp.local' );

            return
                $mail->event->actionDescription() === 'Deleted' &&
                $cn->id === $mail->event->note()->id &&
                null === $mail->event->oldNote()
                ;
        });
    }


    public function testListenerWithSuperuserNotifications_PreferencesNone()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        config()->set( 'ixp_fe.customer.notes.only_send_to', false );

        // CREATED
        $cn = new CustomerNote();
        $cn->private = 0;
        $cn->customer_id = $user->customer->id;
        $cn->title = "IMPORTANT NOTES READ FIRST";
        $cn->note = "Note contents 1";
        $cn->save();

        // Set global notifs
        $noteAuthor = $this->getSuperUser();
        $noteAuthor->prefs = [
            'notes' => [
                'global_notifs' => 'none',
            ]
        ];
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Log::shouldNotHaveReceived( 'notice' );

        Mail::assertNothingSent();
    }


    public function testListenerWithSuperuserNotifications_UserInvalidEmail()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        config()->set( 'ixp_fe.customer.notes.only_send_to', false );

        // CREATED
        $cn = new CustomerNote();
        $cn->private = 0;
        $cn->customer_id = $user->customer->id;
        $cn->title = "IMPORTANT NOTES READ FIRST";
        $cn->note = "Note contents 1";
        $cn->save();

        // Set global notifs
        $noteAuthor = $this->getSuperUser();
        $noteAuthor->email = 'invalid';
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Log::shouldNotHaveReceived( 'notice' );

        Mail::assertNothingSent();
    }

    public function testListenerWithSuperuserNotifications_PreferencesDefault()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        config()->set( 'ixp_fe.customer.notes.only_send_to', false );

        // CREATED
        $cn = new CustomerNote();
        $cn->private = 0;
        $cn->customer_id = $user->customer->id;
        $cn->title = "IMPORTANT NOTES READ FIRST";
        $cn->note = "Note contents 1";
        $cn->save();

        // Set global notifs
        $noteAuthor = $this->getSuperUser();
        $noteAuthor->prefs = [
            'notes' => [
                'global_notifs' => 'default',
            ]
        ];
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending note created email for note " . $cn->id );

        Mail::assertSent( ChangedEmail::class, function( ChangedEmail $mail ) use ( $cn ) {
            return
                $mail->event->actionDescription() === 'Created' &&
                $cn->id === $mail->event->note()->id &&
                null === $mail->event->oldNote() &&
                count($mail->to) === 1 &&
                $mail->to[0]['address'] === $this->getSuperUser()->email &&
                $mail->to[0]['name'] === $this->getSuperUser()->username
                ;
        } );
    }


    public function testListenerWithSuperuserNotifications_PreferencesAll()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        config()->set( 'ixp_fe.customer.notes.only_send_to', false );

        // CREATED
        $cn = new CustomerNote();
        $cn->private = 0;
        $cn->customer_id = $user->customer->id;
        $cn->title = "IMPORTANT NOTES READ FIRST";
        $cn->note = "Note contents 1";
        $cn->save();

        // Set global notifs
        $noteAuthor = $this->getSuperUser();
        $noteAuthor->prefs = [
            'notes' => [
                'global_notifs' => 'all',
            ]
        ];
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending note created email for note " . $cn->id );

        Mail::assertSent( ChangedEmail::class, function( ChangedEmail $mail ) use ( $cn ) {
            return
                $mail->event->actionDescription() === 'Created' &&
                $cn->id === $mail->event->note()->id &&
                null === $mail->event->oldNote() &&
                count($mail->to) === 1 &&
                $mail->to[0]['address'] === $this->getSuperUser()->email &&
                $mail->to[0]['name'] === $this->getSuperUser()->username
                ;
        } );
    }


    public function testListenerWithSuperuserNotifications_PreferencesNotSet()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        config()->set( 'ixp_fe.customer.notes.only_send_to', false );

        // CREATED
        $cn = new CustomerNote();
        $cn->private = 0;
        $cn->customer_id = $user->customer->id;
        $cn->title = "IMPORTANT NOTES READ FIRST";
        $cn->note = "Note contents 1";
        $cn->save();

        // Set global notifs
        $noteAuthor = $this->getSuperUser();
        $noteAuthor->prefs = [
            'notes' => [
            ]
        ];
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending note created email for note " . $cn->id );

        Mail::assertSent( ChangedEmail::class, function( ChangedEmail $mail ) use ( $cn ) {
            return
                $mail->event->actionDescription() === 'Created' &&
                $cn->id === $mail->event->note()->id &&
                null === $mail->event->oldNote() &&
                count($mail->to) === 1 &&
                $mail->to[0]['address'] === $this->getSuperUser()->email &&
                $mail->to[0]['name'] === $this->getSuperUser()->username
                ;
        } );
    }


    public function testListenerWithSuperuserNotifications_PreferencesWatchedCustomerOnly()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        config()->set( 'ixp_fe.customer.notes.only_send_to', false );

        // CREATED
        $cn = new CustomerNote();
        $cn->private = 0;
        $cn->customer_id = $user->customer->id;
        $cn->title = "IMPORTANT NOTES READ FIRST";
        $cn->note = "Note contents 1";
        $cn->save();

        // Set global notifs
        $noteAuthor = $this->getSuperUser();
        $noteAuthor->prefs = [
            'notes' => [
                'global_notifs' => 'watched',
                'customer_watching' => [$user->customer->id => 1],
            ]
        ];
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending note created email for note " . $cn->id );

        Mail::assertSentCount(1);
        Mail::assertSent( ChangedEmail::class, function( ChangedEmail $mail ) use ( $cn ) {
            return
                $mail->event->actionDescription() === 'Created' &&
                $cn->id === $mail->event->note()->id &&
                null === $mail->event->oldNote() &&
                count($mail->to) === 1 &&
                $mail->to[0]['address'] === $this->getSuperUser()->email &&
                $mail->to[0]['name'] === $this->getSuperUser()->username
                ;
        } );


        // Now unsubscribe from that customer..
        $noteAuthor->prefs = [
            'notes' => [
                'global_notifs' => 'watched',
                'customer_watching' => [],
            ]
        ];
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Mail::assertSentCount(1);

    }

    public function testListenerWithSuperuserNotifications_PreferencesWatchedNoteOnly()
    {
        Mail::fake();
        Log::spy();

        $user = $this->getCustUser();

        config()->set( 'ixp_fe.customer.notes.only_send_to', false );

        // CREATED
        $cn = new CustomerNote();
        $cn->private = 0;
        $cn->customer_id = $user->customer->id;
        $cn->title = "IMPORTANT NOTES READ FIRST";
        $cn->note = "Note contents 1";
        $cn->save();

        // Set global notifs
        $noteAuthor = $this->getSuperUser();
        $noteAuthor->prefs = [
            'notes' => [
                'global_notifs' => 'watched',
                'note_watching' => [$cn->id => 1],
            ]
        ];
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Log::shouldHaveReceived( 'notice' )
            ->with( "Sending note created email for note " . $cn->id );

        Mail::assertSentCount(1);
        Mail::assertSent( ChangedEmail::class, function( ChangedEmail $mail ) use ( $cn ) {
            return
                $mail->event->actionDescription() === 'Created' &&
                $cn->id === $mail->event->note()->id &&
                null === $mail->event->oldNote() &&
                count($mail->to) === 1 &&
                $mail->to[0]['address'] === $this->getSuperUser()->email &&
                $mail->to[0]['name'] === $this->getSuperUser()->username
                ;
        } );

        // Now unsubscribe from that note..
        $noteAuthor->prefs = [
            'notes' => [
                'global_notifs' => 'watched',
                'note_watching' => [],
            ]
        ];
        $noteAuthor->save();

        event( new Created( null, $cn, $noteAuthor ) );

        Mail::assertSentCount(1);
    }
}