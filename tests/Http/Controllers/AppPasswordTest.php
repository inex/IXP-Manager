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

namespace Http\Controllers;

use Illuminate\Support\Facades\Hash;
use IXP\Models\AppPassword;
use IXP\Models\Customer;
use IXP\Models\User;
use Tests\TestCase;

class AppPasswordTest extends TestCase
{
    public function testUpdate()
    {
        $customer = Customer::create();
        $customer->name = "Test Customer/Member";
        $customer->type = Customer::TYPE_FULL;
        $customer->status = Customer::STATUS_NORMAL;
        $customer->save();

        $user1 = User::create();
        $user1->username = "App Password Test User";
        $user1->custid = $customer->id;
        $user1->save();
        $user1->customerToUser()->create( [
            'customer_id' => $customer->id,
            'privs'       => User::AUTH_SUPERUSER
        ] );

        $user2 = User::create();
        $user2->username = "App Password Test User 2";
        $user2->custid = $customer->id;
        $user2->save();
        $user2->customerToUser()->create( [
            'customer_id' => $customer->id,
            'privs'       => User::AUTH_SUPERUSER
        ] );

        try {
            $appPassword = new AppPassword();
            $appPassword->password = Hash::make("password");
            $appPassword->description = "some app password description";
            $appPassword->expires = now()->addMonth()->format("d-m-Y");
            $appPassword->user_id = $user1->id;
            $appPassword->save();

            $this->actingAs( $user1 );
            $this->put( route( "app-password@update", ['id' => $appPassword->id] ), [
                "description" => "updated app password description",
                "expires" => $appPassword->expires,
            ] )
                ->assertStatus( 302 )
                ->assertRedirect( route( "app-password@list" ) );

            $appPassword->refresh();
            $this->assertEquals("updated app password description", $appPassword->description);

            $this->actingAs( $user2 );
            $this->put( route( "app-password@update", ['id' => $appPassword->id] ), [
                "description" => "updated app password description 2",
                "expires" => $appPassword->expires,
            ] )
                ->assertStatus(403);

            $appPassword->refresh();
            $this->assertEquals("updated app password description", $appPassword->description);
        } finally {
            $user1->customerToUser()->delete();
            $user2->customerToUser()->delete();
            $user1->appPasswords()->delete();
            $user1->delete();
            $user2->delete();
            $customer->delete();
        }
    }
}