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

namespace Tests\Http\Controllers;

use IXP\Models\ApiKey;
use IXP\Models\Customer;
use IXP\Models\User;
use Tests\TestCase;

class ApiKeyControllerTest extends TestCase
{
    public function testUpdate()
    {
        $customer = Customer::create();
        $customer->name = "Test Customer/Member";
        $customer->type = Customer::TYPE_FULL;
        $customer->status = Customer::STATUS_NORMAL;
        $customer->save();

        $user1 = User::create();
        $user1->username = "API Test User";
        $user1->custid = $customer->id;
        $user1->save();
        $user1->customerToUser()->create( [
            'customer_id' => $customer->id,
            'privs'       => User::AUTH_CUSTADMIN
        ] );

        $user2 = User::create();
        $user2->username = "API Test User 2";
        $user2->custid = $customer->id;
        $user2->save();
        $user2->customerToUser()->create( [
            'customer_id' => $customer->id,
            'privs'       => User::AUTH_CUSTADMIN
        ] );

        try {
            $apikey = new ApiKey();
            $apikey->api_key = "randomapikey";
            $apikey->description = "original description";
            $apikey->expires = now()->addMonth()->format("d-m-Y");
            $apikey->user_id = $user1->id;
            $apikey->save();

            $this->actingAs( $user1 );
            $this->put( route( "api-key@update", ['id' => $apikey->id] ), [
                "description" => "updated description",
                "expires" => $apikey->expires,
            ] )
                ->assertStatus( 302 )
                ->assertRedirect( route( "api-key@list" ) );
            $apikey->refresh();
            $this->assertEquals("updated description", $apikey->description);

            $this->actingAs( $user2 );
            $this->put( route( "api-key@update", ['id' => $apikey->id] ), [
                "description" => "updated description 2",
                "expires" => $apikey->expires,
            ] )
                ->assertStatus(403);

            $apikey->refresh();
            $this->assertEquals("updated description", $apikey->description);
        } finally {
            $user1->customerToUser()->delete();
            $user2->customerToUser()->delete();
            $user1->apiKeys()->delete();
            $user1->delete();
            $user2->delete();
            $customer->delete();
        }
    }
}