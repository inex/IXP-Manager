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

namespace Tests\Listeners\Layer2Address;

use Illuminate\Support\Facades\Mail;
use IXP\Events\Layer2Address\Added;
use IXP\Events\Layer2Address\Deleted;
use IXP\Listeners\Layer2Address\Changed;
use IXP\Mail\Layer2Address\ChangedMail;
use IXP\Models\Layer2Address;
use Tests\TestCase;

class ChangedTest extends TestCase
{
    public function testConfiguredToNotNotifyOnSuperUserChange(): void
    {
        Mail::fake();

        $vlanInterface = $this->getCustAdminUser()->customer->vlanInterfaces()->first();

        $l2a = new Layer2Address();
        $l2a->vlanInterface()->associate( $vlanInterface );
        $l2a->mac = "42:42:42:42:42:42";
        $l2a->save();

        config()->set('ixp_fe.layer2-addresses.email_on_superuser_change', false);
        config()->set('ixp_fe.layer2-addresses.email_on_customer_change', false);

        new Changed()->handle(new Added($l2a, $this->getSuperUser()));

        Mail::assertNothingSent();
    }

    public function testConfiguredToNotNotifyOnCustomerChange(): void
    {
        Mail::fake();

        $user = $this->getCustAdminUser();

        $vlanInterface = $user->customer->vlanInterfaces()->first();

        config()->set('ixp_fe.layer2-addresses.email_on_superuser_change', false);
        config()->set('ixp_fe.layer2-addresses.email_on_customer_change', false);

        new Changed()->handle(new Deleted("42:42:42:42:42:42", $vlanInterface, $user));

        Mail::assertNothingSent();
    }

    public function testConfiguredToNotifyOnSuperuserChange(): void
    {
        Mail::fake();

        $vlanInterface = $this->getCustAdminUser()->customer->vlanInterfaces()->first();

        $l2a = new Layer2Address();
        $l2a->vlanInterface()->associate( $vlanInterface );
        $l2a->mac = "42:42:42:42:42:42";
        $l2a->save();

        config()->set('ixp_fe.layer2-addresses.email_on_change_dest', 'notifications@ixp.local');
        config()->set('ixp_fe.layer2-addresses.email_on_superuser_change', true);
        config()->set('ixp_fe.layer2-addresses.email_on_customer_change', false);

        $event = new Added($l2a, $this->getSuperUser());
        new Changed()->handle($event);

        Mail::assertSent(ChangedMail::class, function (ChangedMail $mail) use ($event) {
            return $mail->hasTo( 'notifications@ixp.local' ) &&
                $mail->event === $event
            ;
        });
    }

    public function testConfiguredToNotifyOnCustomerChange(): void
    {
        Mail::fake();

        $user = $this->getCustAdminUser();

        $vlanInterface = $user->customer->vlanInterfaces()->first();

        config()->set('ixp_fe.layer2-addresses.email_on_change_dest', 'notifications@ixp.local');
        config()->set('ixp_fe.layer2-addresses.email_on_superuser_change', true);
        config()->set('ixp_fe.layer2-addresses.email_on_customer_change', false);

        $event = new Deleted("42:42:42:42:42:42", $vlanInterface, $user);
        new Changed()->handle($event);

        Mail::assertSent(ChangedMail::class, function (ChangedMail $mail) use ($event) {
            return $mail->hasTo( 'notifications@ixp.local' ) &&
                $mail->event === $event
            ;
        });
    }

    public function testDontEmailOnSupervisorChange(): void
    {
        Mail::fake();

        $vlanInterface = $this->getCustAdminUser()->customer->vlanInterfaces()->first();

        $l2a = new Layer2Address();
        $l2a->vlanInterface()->associate( $vlanInterface );
        $l2a->mac = "42:42:42:42:42:42";
        $l2a->save();

        config()->set('ixp_fe.layer2-addresses.email_on_change_dest', 'notifications@ixp.local');
        config()->set('ixp_fe.layer2-addresses.email_on_superuser_change', false);
        config()->set('ixp_fe.layer2-addresses.email_on_customer_change', true);

        new Changed()->handle(new Added($l2a, $this->getSuperUser()));

        Mail::assertNothingSent();
    }

}