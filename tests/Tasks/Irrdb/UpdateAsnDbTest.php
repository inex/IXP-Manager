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

namespace Tests\Tasks\Irrdb;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use IXP\Models\Customer;
use IXP\Models\IrrdbAsn;
use IXP\Models\IrrdbConfig;
use IXP\Models\IrrdbUpdateLog;
use IXP\Models\VirtualInterface;
use IXP\Models\VlanInterface;
use IXP\Tasks\Irrdb\UpdateAsnDb;
use IXP\Contracts\IrrQuerier;
use Mockery;
use Tests\TestCase;

class UpdateAsnDbTest extends TestCase
{
    private Customer $customer;
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
        ] );
        $this->vi = VirtualInterface::create([
            'custid' => $this->customer->id,
        ] );
        $this->vli = VlanInterface::create([
            'virtualinterfaceid' => $this->vi->id,
            'rsclient' => true,
        ] );
    }

    public function tearDown(): void
    {
        $this->customer->irrdbUpdateLog()->delete();
        $this->customer->delete();

        unset($this->customer);
        unset($this->vi);
        unset($this->vli);

        parent::tearDown();
    }

    private function getAsnsForProtocol( $protocol ): array
    {
        return IrrdbAsn::where( 'customer_id', $this->customer->id )->where( 'protocol', $protocol )->orderBy('asn')->pluck('asn')->toArray();
    }
    public function getCachedAsnsForProtocol( $protocol ): ?array
    {
        return Cache::get('irrdb:asn:ipv' . $protocol . ':' . $this->customer->asMacro($protocol));
    }

    /**
     * Tests initial update, then a refresh when ASNs are removed, and a refresh when a new ASN is added.
     * @return void
     * @throws \IXP\Exceptions\ConfigurationException
     */
    public function testAsnUpdateBothProtocols()
    {
        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $testBegin = Carbon::now();
        $testTime1 = $testBegin->copy();
        Carbon::setTestNow($testTime1);

        $this->assertEquals( 0, IrrdbAsn::where( 'customer_id', $this->customer->id )->count() );
        $this->assertNull( IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first() );

        // Test initial save imports 6 ASNs for both protocols
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )  ->with( 'whois.radb.net' )->andReturnSelf()->times( 6 ); // 2 protocols to update, performed 3 times
        $bgpq->expects( 'setSources' )->with( 'RIPE' )          ->andReturnSelf()->times( 6 ); // 2 protocols to update, performed 3 times
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )     ->andReturn([ 112, 1213, 1921, 2128, 2850, 42310 ] );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 6 )     ->andReturn([ 112, 1213, 1921, 2128, 2850, 42310 ] );

        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(6, $result['v4']['count']);
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $result['v4']['new']);
        $this->assertEquals([], $result['v4']['stale']);
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(6, $result['v6']['count']);
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $result['v6']['new']);
        $this->assertEquals([], $result['v6']['stale']);
        $this->assertTrue($result['v6']['dbUpdated']);

        // IrrdbAsn's now contains the irrdb results
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol( 4 ) );
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol( 6 ) );

        // IrrdbUpdateLog reflects updates to asn_v4 and asn_v6
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime1->timestamp, $updateLog->asn_v4->timestamp );
        $this->assertEquals($testTime1->timestamp, $updateLog->asn_v6->timestamp );
        $this->assertNull( $updateLog->prefix_v4 );
        $this->assertNull( $updateLog->prefix_v6 );

        // the cached asn's were updated for both protocols
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getCachedAsnsForProtocol(4 ) );
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getCachedAsnsForProtocol(6 ) );

        // Test removal of ASNs
        $testTime2 = $testBegin->copy()->addMinutes(1);
        Carbon::setTestNow($testTime2);
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )->andReturn( [ 112, 1213, 1921, 2128, ] );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 6 )->andReturn( [ 112, 1213, 1921, 2128, ] );
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(4, $result['v4']['count']);
        $this->assertEquals([ ], $result['v4']['new']);
        $this->assertEquals([ 2850, 42310 ], array_column($result['v4']['stale'], 'asn'));
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(4, $result['v6']['count']);
        $this->assertEquals([ ], $result['v6']['new']);
        $this->assertEquals([2850, 42310 ], array_column($result['v6']['stale'], 'asn'));
        $this->assertTrue($result['v6']['dbUpdated']);

        // IrrdbAsn's reduced by 2
        $this->assertEquals([ 112, 1213, 1921, 2128 ], $this->getAsnsForProtocol( 4 ) );
        $this->assertEquals([ 112, 1213, 1921, 2128 ], $this->getAsnsForProtocol( 6 ) );

        // IrrdbUpdateLog reflects the new time
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals( $testTime2->timestamp, $updateLog->asn_v4->timestamp );
        $this->assertEquals( $testTime2->timestamp, $updateLog->asn_v6->timestamp );
        $this->assertNull( $updateLog->prefix_v4 );
        $this->assertNull( $updateLog->prefix_v6 );

        // the cached asn's were updated for both protocols
        $this->assertEquals( [ 112, 1213, 1921, 2128 ], $this->getCachedAsnsForProtocol(4 ) );
        $this->assertEquals( [ 112, 1213, 1921, 2128 ], $this->getCachedAsnsForProtocol(6 ) );

        // Test when a new ASN is added
        $testTime3 = $testBegin->copy()->addMinutes( 2 );
        Carbon::setTestNow( $testTime3 );

        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )->andReturn( [ 112, 1213, 1921, 2128, 9999, ] );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 6 )->andReturn( [ 112, 1213, 1921, 2128, 9999, ] );
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(5, $result['v4']['count']);
        $this->assertEquals([ 9999 ], $result['v4']['new']);
        $this->assertEquals([], $result['v4']['stale']);
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(5, $result['v6']['count']);
        $this->assertEquals([ 9999 ], $result['v6']['new']);
        $this->assertEquals([], $result['v6']['stale']);
        $this->assertTrue($result['v6']['dbUpdated']);

        // IrrdbAsns contains the new ASN
        $this->assertEquals( [ 112, 1213, 1921, 2128, 9999 ], $this->getAsnsForProtocol( 4 ) );
        $this->assertEquals( [ 112, 1213, 1921, 2128, 9999 ], $this->getAsnsForProtocol( 6 ) );

        // IrrdbUpdateLog is updated since last run
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals( $testTime3->timestamp, $updateLog->asn_v4->timestamp );
        $this->assertEquals( $testTime3->timestamp, $updateLog->asn_v6->timestamp );
        $this->assertNull( $updateLog->prefix_v4 );
        $this->assertNull( $updateLog->prefix_v6 );

        // the cached asn's were updated for both protocols
        $this->assertEquals( [ 112, 1213, 1921, 2128, 9999 ], $this->getCachedAsnsForProtocol( 4 ) );
        $this->assertEquals( [ 112, 1213, 1921, 2128, 9999 ], $this->getCachedAsnsForProtocol( 6 ) );
    }

    /**
     * This test checks our safety net, where we abort the task if IRRDB returns no records.
     *
     * @return void
     * @throws \IXP\Exceptions\ConfigurationException
     */
    public function testDontWipeIfNoIrrdbEntries()
    {
        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $testBegin = Carbon::now();
        $testTime1 = $testBegin->copy();
        Carbon::setTestNow($testTime1);

        $this->assertEquals( 0, IrrdbAsn::where( 'customer_id', $this->customer->id )->count() );
        $this->assertNull( IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first() );

        // Test initial save
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )->with( 'whois.radb.net' )->andReturnSelf()->times(4 );
        $bgpq->expects( 'setSources' )->with( 'RIPE' )->andReturnSelf()->times(4 );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )->andReturn( [ 112, 1213, 1921, 2128, 2850, 42310 ] );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 6 )->andReturn( [ 112, 1213, 1921, 2128, 2850, 42310 ] );
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(6, $result['v4']['count']);
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310], $result['v4']['new']);
        $this->assertEquals([], $result['v4']['stale']);
        $this->assertTrue( $result['v4']['dbUpdated']);
        $this->assertEquals(6, $result['v6']['count']);
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310], $result['v6']['new']);
        $this->assertEquals([], $result['v6']['stale']);
        $this->assertTrue( $result['v6']['dbUpdated'] );

        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol( 4 ) );
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol( 6 ) );

        // Both protocols updated
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime1->timestamp, $updateLog->asn_v4->timestamp );
        $this->assertEquals($testTime1->timestamp, $updateLog->asn_v6->timestamp );


        // Test IRRDB returning zero records for IPV6
        $testTime2 = $testBegin->copy()->addMinutes(1);
        Carbon::setTestNow($testTime2);

        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )->andReturn( [112, 1213, 1921, 2128, 2850, 42310] );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 6 )->andReturn( [] );
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(6, $result['v4']['count']);
        $this->assertEquals([], $result['v4']['new']);
        $this->assertEquals([], $result['v4']['stale']);
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(0, $result['v6']['count']);
        $this->assertEquals([], $result['v6']['new']);
        $this->assertEquals([], $result['v6']['stale']);
        $this->assertFalse($result['v6']['dbUpdated']);
        $this->assertEquals("IRRDB asn: Test Customer has a non-zero asn count for IPv6 in the database but BGPQ3 returned none. Please examine manually. No databases changes made for this customer.", $result['msg']);

        // No change to the original, because IRRDB query returned empty set.
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol( 4 ) );
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol( 6 ) );

        // Also no change to update log.
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals( $testTime2->timestamp, $updateLog->asn_v4->timestamp );
        $this->assertEquals( $testTime1->timestamp, $updateLog->asn_v6->timestamp );

    }

    /**
     * The task only refreshes ASNs for a certain protocol if the VLI has that protocol enabled.
     * The test starts with both protocols are enabled, then tests what happens when IPV6 is disabled
     * When that happens, IrrdbASN's will be empty for v6, and the cache for v6 should be cleared.
     * @return void
     * @throws \IXP\Exceptions\ConfigurationException
     */
    public function testOnlyActiveProtocolsAreTracked()
    {
        // Starts with both, we'll disable one
        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $testBegin = Carbon::now();
        $testTime1 = $testBegin->copy();
        Carbon::setTestNow($testTime1);

        // Test initial save of 6 ASNs
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )->with( 'whois.radb.net' )->andReturnSelf()->times( 3 );
        $bgpq->expects( 'setSources' )->with( 'RIPE' )->andReturnSelf()->times( 3 );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )->andReturn( [ 112, 1213, 1921, 2128, 2850, 42310 ] );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 6 )->andReturn( [ 112, 1213, 1921, 2128, 2850, 42310 ] );
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $result = $task->update();

        // DB asns
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol(4 ) );
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol(6 ) );

        // Cached ASNs
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getCachedAsnsForProtocol(4 ) );
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getCachedAsnsForProtocol(6 ) );

        // Both protocols updated
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime1->timestamp, $updateLog->asn_v4->timestamp );
        $this->assertEquals($testTime1->timestamp, $updateLog->asn_v6->timestamp );

        // Disable IPV6
        $this->vli->ipv6enabled = false;
        $this->vli->save();

        // Test update - no change to returned records
        $testTime2 = $testBegin->copy()->addMinutes(1);
        Carbon::setTestNow($testTime2);

        // BGPQ3 only called for v4.
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )->andReturn( [112, 1213, 1921, 2128, 2850 ] );
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $task->update();

        // IPV4 IrrdbASNs has one less
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850 ], $this->getAsnsForProtocol( 4 ) );
        // IPV6 IrrdbAsns is now empty.
        $this->assertEquals( [ ], $this->getAsnsForProtocol( 6 ) );

        // Cached ASNs - IPV4 updated, IPV6 entry deleted
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850 ], $this->getCachedAsnsForProtocol(4 ) );
        $this->assertEquals( null, $this->getCachedAsnsForProtocol(6 ) );

        // IrrdbUpdateLog for IPV4 is more recent than IPV6
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime2->timestamp, $updateLog->asn_v4->timestamp );
        $this->assertEquals($testTime1->timestamp, $updateLog->asn_v6->timestamp );
    }


    /**
     * The task only refreshes ASNs for a certain protocol if the VLI has that protocol enabled.
     * The test starts with both protocols are enabled, then tests what happens when IPV6 is disabled
     * When that happens, IrrdbASN's will be empty for v6, and the cache for v6 should be cleared.
     * @return void
     * @throws \IXP\Exceptions\ConfigurationException
     */
    public function testMalformedAsnsRemoved()
    {
        // Starts with both, we'll disable one
        $this->vli->ipv4enabled = true;
        $this->vli->save();

        $testBegin = Carbon::now();
        $testTime1 = $testBegin->copy();
        Carbon::setTestNow($testTime1);

        // Test initial save of 6 ASNs
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )->with( 'whois.radb.net' )->andReturnSelf()->times( 2 );
        $bgpq->expects( 'setSources' )->with( 'RIPE' )->andReturnSelf()->times( 2 );
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )->andReturn( [ 112, 1213, 1921, 2128, 2850, 42310 ] );
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $task->update();

        // DB + cached match
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getAsnsForProtocol(4 ) );
        $this->assertEquals( [ 112, 1213, 1921, 2128, 2850, 42310 ], $this->getCachedAsnsForProtocol( 4 ) );

        // IrrdbUpdateLog updated
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals( $testTime1->timestamp, $updateLog->asn_v4->timestamp );

        // Test IRRDB returning a negative ASN for some reason (should be filtered by validate)
        $testTime2 = $testBegin->copy()->addMinutes(1);
        Carbon::setTestNow( $testTime2 );

        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )->andReturn( [112, 1213, 1921, 2020, 2128, 2850, -9999 ] );
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $task->update();

        // -9999 isn't in IrrdbAsns
        $this->assertEquals( [ 112, 1213, 1921, 2020, 2128, 2850 ], $this->getAsnsForProtocol( 4 ) );

        // Cached ASNs - -9999 not there either
        $this->assertEquals( [ 112, 1213, 1921, 2020, 2128, 2850 ], $this->getCachedAsnsForProtocol(4 ) );

        // IrrdbUpdateLog was updated
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals ($testTime2->timestamp, $updateLog->asn_v4->timestamp );
    }

    /**
     * The task can be scoped to only the provided protocols, even if both are enabled on the VLI
     * @return void
     * @throws \IXP\Exceptions\ConfigurationException
     */
    public function testCanRestrictProtocols()
    {
        $bgpq = Mockery::mock(IrrQuerier::class);
        $task = new UpdateAsnDb( $bgpq, $this->customer );
        $this->assertEquals( [ 4, 6 ], $task->protocols() );

        $task = new UpdateAsnDb( $bgpq, $this->customer , [4]);
        $this->assertEquals( [ 4 ], $task->protocols() );

        $task = new UpdateAsnDb( $bgpq, $this->customer , [6]);
        $this->assertEquals( [ 6 ], $task->protocols() );

        $task = new UpdateAsnDb( $bgpq, $this->customer , [4, 6]);
        $this->assertEquals( [ 4, 6 ], $task->protocols() );

        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $testBegin = Carbon::now();
        $testTime1 = $testBegin->copy();
        Carbon::setTestNow($testTime1);

        $this->assertEquals( 0, IrrdbAsn::where( 'customer_id', $this->customer->id )->count() );
        $this->assertNull( IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first() );

        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )  ->with( 'whois.radb.net' )->andReturnSelf()->times( 1 ); // 2 protocols to update, performed 3 times
        $bgpq->expects( 'setSources' )->with( 'RIPE' )          ->andReturnSelf()->times( 1 ); // 2 protocols to update, performed 3 times
        $bgpq->expects( 'getAsnList' )->with( 'AS-TEST', 4 )     ->andReturn([ 112, 1213, 1921, 2128, 2850, 42310 ] );

        $task = new UpdateAsnDb( $bgpq, $this->customer, [4] );
        $result = $task->update();
        $this->assertEquals(6, $result['v4']['count']);
        $this->assertEquals([ 112, 1213, 1921, 2128, 2850, 42310 ], $result['v4']['new']);
        $this->assertEquals([], $result['v4']['stale']);
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(0, $result['v6']['count']);
        $this->assertEquals([], $result['v6']['new']);
        $this->assertEquals([], $result['v6']['stale']);
        $this->assertFalse($result['v6']['dbUpdated']);

    }
}