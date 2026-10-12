<?php
/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace Tests\Models;

use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function testCurrentCustomerToUserRelationship()
    {
        $user = User::create();
        $user->name = 'testuser';
        $user->disabled = false;
        $user->save();

        $customer1 = Customer::create( [
            'name' => 'Test Customer',
            'status' => Customer::STATUS_NORMAL,
            'type' => Customer::TYPE_FULL,
        ] );
        $c2u1 = CustomerToUser::forceCreate( [
            'user_id' => $user->id,
            'customer_id' => $customer1->id,
            'privs' => User::AUTH_CUSTADMIN,
        ] );


        $customer2 = Customer::create( [
            'name' => 'Test Customer',
            'status' => Customer::STATUS_NORMAL,
            'type' => Customer::TYPE_INTERNAL,
        ] );
        $c2u2 = CustomerToUser::forceCreate( [
            'user_id' => $user->id,
            'customer_id' => $customer2->id,
            'privs' => User::AUTH_SUPERUSER,
        ] );

        // Test association with Customer 1
        $user->custid = $customer1->id;
        $this->assertFalse($user->relationLoaded('currentCustomerToUser'), "The relation wasn't loaded yet");

        $user->save();
        $this->assertFalse($user->relationLoaded('currentCustomerToUser'), "The relation wasn't loaded yet");

        // We do our first read on this next line:
        $this->assertInstanceOf(CustomerToUser::class, $user->currentCustomerToUser, "Here we do the first read");
        $this->assertTrue($user->relationLoaded('currentCustomerToUser'), "We did our first read, so relation is now loaded");
        $this->assertUserAssociatedWithC2U($c2u1, $user);


        // Test association with Customer 2, along with refresh
        $user->custid = $customer2->id;
        $this->assertTrue($user->relationLoaded('currentCustomerToUser'), "We haven't saved our changes, relation is still loaded and referring to customer1/c2u1");
        $this->assertUserAssociatedWithC2U($c2u1, $user);

        $user->save();
        $this->assertFalse($user->relationLoaded('currentCustomerToUser'), "We've saved our changes, our save hook unloaded the relation, preventing a stale read");

        // We do our re-read on this next line:
        $this->assertInstanceOf(CustomerToUser::class, $user->currentCustomerToUser, "Here we do the fresh read");
        $this->assertTrue($user->relationLoaded('currentCustomerToUser'), "We did our re-read, so relation is now loaded");
        $this->assertUserAssociatedWithC2U($c2u2, $user);

        // For completeness, test refresh, although it won't change anything. Our save hook prevented the stale read after the save.
        $user->refresh();
        $this->assertUserAssociatedWithC2U($c2u2, $user);


        // Test effect of nulling custid
        $user->custid = null;
        $this->assertTrue($user->relationLoaded('currentCustomerToUser'), "We haven't saved our changes, relation is still loaded and referring to customer2/c2u2");
        $this->assertUserAssociatedWithC2U($c2u2, $user);

        $user->save();
        $this->assertFalse($user->relationLoaded('currentCustomerToUser'), "We've saved our changes, our save hook unloaded the relation, preventing a stale read");

        // We do our re-read on this next line:
        $this->assertNull($user->currentCustomerToUser, "Here we do the fresh read");
        $this->assertNull($user->privs());

        // For completeness, test refresh, although it won't change anything. Our save hook prevented the stale read after the save.
        $user->refresh();
        $this->assertNull($user->currentCustomerToUser, "The refresh didn't change anything, our save hook prevented the stale read after the save.");
        $this->assertNull($user->privs());
    }

    private function assertUserAssociatedWithC2U(CustomerToUser $expected, User $user): void
    {
        $c2u = $user->currentCustomerToUser;
        $this->assertEquals($expected->id, $c2u->id);
        $this->assertEquals($expected->privs, $c2u->privs);
        $this->assertEquals($expected->privs, $user->privs());
    }
}