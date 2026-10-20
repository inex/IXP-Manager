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

namespace Tests\Jobs;

use IXP\Contracts\IrrQuerier;
use IXP\Jobs\UpdateIrrdb;
use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\IrrdbAsn;
use IXP\Models\IrrdbConfig;
use IXP\Models\IrrdbPrefix;
use IXP\Models\User;
use IXP\Models\VirtualInterface;
use IXP\Models\VlanInterface;
use IXP\Utils\Bgpq3;
use Mockery;
use Tests\TestCase;

class UpdateIrrdbTest extends TestCase
{
    private IrrdbConfig $irrdbConfig;
    private Customer $customer;
    private User $user;
    private CustomerToUser $cust2User;
    private VirtualInterface $vi;
    private VlanInterface $vli;

    public function setUp(): void
    {
        parent::setUp();

        $this->irrdbConfig = IrrdbConfig::where('source', 'RIPE')->firstOrFail();

        $this->customer = Customer::create([
            'name' => 'Test Customer',
            'irrdb' => $this->irrdbConfig->id,
            'autsys' => 1213,
            'status' => Customer::STATUS_NORMAL,
        ] );
        $this->user = User::create();
        $this->user->custid = $this->customer->id;
        $this->user->save();
        $this->cust2User = CustomerToUser::forceCreate([
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

    public function testAsnJob()
    {
        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $asns = json_decode(file_get_contents("data/ci/known-good/bgpq3/heanet-asns.json"), true)['pl'];

        $this->assertEquals(0, IrrdbAsn::where('customer_id', $this->customer->id)->where('protocol', 4)->count());
        $this->assertEquals(0, IrrdbAsn::where('customer_id', $this->customer->id)->where('protocol', 6)->count());

        $bgp = Mockery::mock(IrrQuerier::class);
        $bgp->expects('setWhois')->with($this->irrdbConfig->host)->times(2);
        $bgp->expects('setSources')->with($this->irrdbConfig->source)->times(2);
        $bgp->expects('getAsnList')->with('as1213', 4)->andReturn($asns);
        $bgp->expects('getAsnList')->with('as1213', 6)->andReturn($asns);
        $job = new UpdateIrrdb($this->customer, 'asn', 4);
        $job->handle($bgp);

        $this->assertEquals(count($asns), IrrdbAsn::where('customer_id', $this->customer->id)->where('protocol', 4)->count());
        $this->assertEquals(count($asns), IrrdbAsn::where('customer_id', $this->customer->id)->where('protocol', 6)->count());

        $jobResult = \Cache::get( 'updated-irrdb-'  . $job->type . '-' . $job->protocol . '-' . $this->customer->id);
        $this->assertArrayHasKey('wiped', $jobResult);
        $this->assertArrayHasKey('v4', $jobResult);
        $this->assertArrayHasKey('v6', $jobResult);
        $this->assertArrayHasKey('netTime', $jobResult);
        $this->assertArrayHasKey('dbTime', $jobResult);
        $this->assertArrayHasKey('procTime', $jobResult);
        $this->assertArrayHasKey('msg', $jobResult);
        $this->assertArrayHasKey('found_at', $jobResult);
    }

    public function testPrefixJob()
    {
        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $prefixesV4 = array_column(json_decode(file_get_contents("data/ci/known-good/bgpq3/heanet-prefixes-v4.json"), true)['pl'], 'prefix');
        $prefixesV6 = array_column(json_decode(file_get_contents("data/ci/known-good/bgpq3/heanet-prefixes-v6.json"), true)['pl'], 'prefix');

        $this->assertEquals(0, IrrdbPrefix::where('customer_id', $this->customer->id)->where('protocol', 4)->count());
        $this->assertEquals(0, IrrdbPrefix::where('customer_id', $this->customer->id)->where('protocol', 6)->count());

        $bgp = Mockery::mock(IrrQuerier::class);
        $bgp->expects('setWhois')->with($this->irrdbConfig->host)->times(2);
        $bgp->expects('setSources')->with($this->irrdbConfig->source)->times(2);
        $bgp->expects('getPrefixList')->with('as1213', 4)->andReturn($prefixesV4);
        $bgp->expects('getPrefixList')->with('as1213', 6)->andReturn($prefixesV6);
        $job = new UpdateIrrdb($this->customer, 'prefix', 4);
        $job->handle($bgp);

        $this->assertEquals(count($prefixesV4), IrrdbPrefix::where('customer_id', $this->customer->id)->where('protocol', 4)->count());
        $this->assertEquals(count($prefixesV6), IrrdbPrefix::where('customer_id', $this->customer->id)->where('protocol', 6)->count());

        $jobResult = \Cache::get( 'updated-irrdb-'  . $job->type . '-' . $job->protocol . '-' . $this->customer->id);
        $this->assertArrayHasKey('wiped', $jobResult);
        $this->assertArrayHasKey('v4', $jobResult);
        $this->assertArrayHasKey('v6', $jobResult);
        $this->assertArrayHasKey('netTime', $jobResult);
        $this->assertArrayHasKey('dbTime', $jobResult);
        $this->assertArrayHasKey('procTime', $jobResult);
        $this->assertArrayHasKey('msg', $jobResult);
        $this->assertArrayHasKey('found_at', $jobResult);
    }
}