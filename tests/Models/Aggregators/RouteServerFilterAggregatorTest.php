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

namespace Tests\Models\Aggregators;

use IXP\Models\Aggregators\RouteServerFilterAggregator;
use IXP\Models\RouteServerFilter;
use IXP\Models\RouteServerFilterProd;
use IXP\Models\Vlan;
use Tests\TestCase;

class RouteServerFilterAggregatorTest extends TestCase
{
    public function testInSync()
    {
        $user = $this->getCustAdminUser();
        $customer = $user->customer;

        $peer = $this->getCustAdminUser('hecustadmin')->customer;

        $this->assertCount(0, RouteServerFilter::whereCustomerId($customer->id)->get());
        $this->assertCount(0, RouteServerFilterProd::whereCustomerId($customer->id)->get());

        $rsf1 = new RouteServerFilter();
        $rsf1->customer_id = $customer->id;
        $rsf1->peer_id = $peer->id;
        $rsf1->vlan_id = Vlan::find(1)->id;
        $rsf1->protocol = 4;
        $rsf1->action_advertise = RouteServerFilter::AS_IS;
        $rsf1->action_receive = RouteServerFilter::AS_IS;
        $rsf1->save();

        $this->assertCount(1, RouteServerFilter::whereCustomerId($customer->id)->get());
        $this->assertCount(0, RouteServerFilterProd::whereCustomerId($customer->id)->get());
        $this->assertFalse(RouteServerFilterAggregator::inSync($customer));

        // add staging rule to prod
        RouteServerFilterAggregator::commit($customer);
        $this->assertCount(1, RouteServerFilter::whereCustomerId($customer->id)->get());
        $this->assertCount(1, RouteServerFilterProd::whereCustomerId($customer->id)->get());

        $this->assertTrue(RouteServerFilterAggregator::inSync($customer));

        // test deleting staged rule and reverting
        $rsf1->delete();
        $this->assertFalse(RouteServerFilterAggregator::inSync($customer));
        RouteServerFilterAggregator::revert($customer);
        $this->assertTrue(RouteServerFilterAggregator::inSync($customer));

        // make a change to staging and check commit
        $rsf1 = RouteServerFilter::whereCustomerId($customer->id)->first();
        $rsf1->peer_id = null;
        $rsf1->save();

        $prsf1 = RouteServerFilterProd::whereCustomerId($customer->id)->first();
        $this->assertEquals($peer->id, $prsf1->peer_id);

        $this->assertFalse(RouteServerFilterAggregator::inSync($customer));
        RouteServerFilterAggregator::commit($customer);
        $this->assertTrue(RouteServerFilterAggregator::inSync($customer));

        $prsf1 = RouteServerFilterProd::whereCustomerId($customer->id)->first();
        $this->assertNull($prsf1->peer_id);

        RouteServerFilter::whereCustomerId($customer->id)->delete();
        RouteServerFilterProd::whereCustomerId($customer->id)->delete();
    }
}