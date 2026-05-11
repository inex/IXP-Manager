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

namespace Tests\Http\Controllers\Irrdb;

use Illuminate\Support\Facades\Queue;
use IXP\Jobs\UpdateIrrdb;
use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\IrrdbConfig;
use IXP\Models\User;
use IXP\Models\VirtualInterface;
use IXP\Models\VlanInterface;
use Tests\TestCase;

class IrrdbControllerTest extends TestCase
{
    private Customer $customer;
    private User $user;
    private CustomerToUser $cust2User;
    private VirtualInterface $vi;
    private VlanInterface $vli;

    public function setUp(): void
    {
        parent::setUp();

        $ripe = IrrdbConfig::where('source', 'RIPE')->firstOrFail();

        $this->customer = Customer::create([
            'name' => 'Test Customer',
            'irrdb' => $ripe->id,
            'autsys' => 1213,
            'peeringmacro' => 'AS-TEST',
            'status' => Customer::STATUS_NORMAL,
        ] );
        $this->user = User::create();
        $this->user->custid = $this->customer->id;
        $this->user->save();
        $this->cust2User = CustomerToUser::create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'privs' => User::AUTH_CUSTUSER,
        ]);
        $this->vi = VirtualInterface::create([
            'custid' => $this->customer->id,
        ] );
        $this->vli = VlanInterface::create([
            'virtualinterfaceid' => $this->vi->id,
            'rsclient' => true,
            'irrdbfilter' => true,
        ] );
    }

    public function tearDown(): void
    {
        $this->cust2User->delete();
        $this->user->delete();
        $this->customer->irrdbUpdateLog()->delete();
        $this->customer->delete();

        unset($this->cust2User);
        unset($this->user);
        unset($this->customer);
        unset($this->vi);
        unset($this->vli);

        parent::tearDown();
    }

    public function testUpdateWhenNoData()
    {
        $this->vli->ipv4enabled = true;
        $this->vli->save();
        Queue::fake();

        $this->assertFalse(\Cache::get('updating-irrdb-asn-4-' . $this->customer->id, false));
        $this->assertFalse(\Cache::get('updated-irrdb-asn-4-' . $this->customer->id, false));

        $this->actingAs($this->user);
        $response = $this->get('irrdb/update/' . $this->customer->id . '/asn/4');
        $response->assertStatus(302);
        $response->assertRedirectToRoute('irrdb@list', [ 'cust' => $this->customer->id, 'type' => 'asn', 'protocol' => 4 ] );

        Queue::assertPushed(UpdateIrrdb::class, function (UpdateIrrdb $job) {
            return $job->customer->id === $this->customer->id &&
                $job->type === 'asn' &&
                $job->protocol === 4;
        });
        $this->assertTrue(\Cache::get('updating-irrdb-asn-4-' . $this->customer->id, false));
    }

    public function testUpdateNoJobWhenCached()
    {
        $this->vli->ipv4enabled = true;
        $this->vli->save();
        Queue::fake();

        $this->assertFalse(\Cache::get('updating-irrdb-asn-4-' . $this->customer->id, false));
        \Cache::put('updated-irrdb-asn-4-' . $this->customer->id, []);

        $this->actingAs($this->user);
        $response = $this->get('irrdb/update/' . $this->customer->id . '/asn/4');
        $response->assertStatus(302);
        $response->assertRedirectToRoute('irrdb@list', [ 'cust' => $this->customer->id, 'type' => 'asn', 'protocol' => 4 ] );

        Queue::assertNotPushed(UpdateIrrdb::class);
        $this->assertFalse(\Cache::get('updating-irrdb-asn-4-' . $this->customer->id, false));
    }

    public function testUpdateAdminCanBustCache()
    {
        $this->cust2User->privs = User::AUTH_SUPERUSER;
        $this->cust2User->save();
        $this->vli->ipv4enabled = true;
        $this->vli->save();
        Queue::fake();

        $this->assertFalse(\Cache::get('updating-irrdb-asn-4-' . $this->customer->id, false));
        \Cache::put('updated-irrdb-asn-4-' . $this->customer->id, []);

        $this->actingAs($this->user);
        $response = $this->get('irrdb/update/' . $this->customer->id . '/asn/4?reset_cache=1');
        $response->assertStatus(302);
        $response->assertRedirectToRoute('irrdb@list', [ 'cust' => $this->customer->id, 'type' => 'asn', 'protocol' => 4 ] );

        Queue::assertPushed(UpdateIrrdb::class, function (UpdateIrrdb $job) {
            return $job->customer->id === $this->customer->id &&
                $job->type === 'asn' &&
                $job->protocol === 4;
        });
        $this->assertTrue(\Cache::get('updating-irrdb-asn-4-' . $this->customer->id, false));
        $this->assertFalse(\Cache::get('updated-irrdb-asn-4-' . $this->customer->id, false));  // not there until job runs
    }

    public function testUpdateNonAdminCannotBustCache()
    {
        $this->cust2User->privs = User::AUTH_CUSTADMIN;
        $this->cust2User->save();
        $this->vli->ipv4enabled = true;
        $this->vli->save();
        Queue::fake();

        $this->assertFalse(\Cache::get('updating-irrdb-asn-4-' . $this->customer->id, false));
        \Cache::put('updated-irrdb-asn-4-' . $this->customer->id, []);

        $this->actingAs($this->user);
        $response = $this->get('irrdb/update/' . $this->customer->id . '/asn/4?reset_cache=1');
        $response->assertStatus(302);
        $response->assertRedirectToRoute('irrdb@list', [ 'cust' => $this->customer->id, 'type' => 'asn', 'protocol' => 4 ] );

        Queue::assertNotPushed(UpdateIrrdb::class);
        $this->assertFalse(\Cache::get('updating-irrdb-asn-4-' . $this->customer->id, false));
    }
}