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

namespace Tasks\Irrdb;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use IXP\Contracts\IrrQuerier;
use IXP\Models\Customer;
use IXP\Models\IrrdbPrefix;
use IXP\Models\IrrdbConfig;
use IXP\Models\IrrdbUpdateLog;
use IXP\Models\VirtualInterface;
use IXP\Models\VlanInterface;
use IXP\Tasks\Irrdb\UpdatePrefixDb;
use Mockery;
use Tests\TestCase;

class UpdatePrefixDbTest extends TestCase
{
    private Customer $customer;
    private VirtualInterface $vi;
    private VlanInterface $vli;

    /**
     * A cache of prefixes loaded from prefix files.
     * Prefixes are keyed by protocol.
     * [4 => [...4 prefixes list...], 6 => [...v6 prefixes list...]]
     * @var array
     */
    private array $prefixesCache = [];

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

    private function getPrefixesForProtocol( $protocol ): array
    {
        return IrrdbPrefix::where( 'customer_id', $this->customer->id )->where( 'protocol', $protocol )->pluck('prefix')->toArray();
    }

    public function getCachedPrefixesForProtocol( $protocol ): ?array
    {
        return Cache::get('irrdb:prefix:ipv' . $protocol . ':' . $this->customer->asMacro($protocol) );
    }

    /**
     * This returns a list of prefixes from the known good prefixes file.
     * $n is the number of prefixes to return. If null, all prefixes are returned.
     * @param int $protocol
     * @param int|null $n
     * @return []
     * @throws \JsonException
     */
    private function getSomePrefixesForProtocol( int $protocol , ?int $n = null ): array
    {
        if ( !array_key_exists( $protocol, $this->prefixesCache ) ) {
            if ( !( $json = file_get_contents( "data/ci/known-good/bgpq3/heanet-prefixes-v" . $protocol . ".json" ) ) ) {
                throw new \RuntimeException("Missing known-good prefixes file");
            }
            $this->prefixesCache[$protocol] = json_decode( $json, false, flags: JSON_THROW_ON_ERROR );
        }

        if (null === $n) {
            $n = count( $this->prefixesCache[$protocol] );
        }

        return array_column( array_slice( $this->prefixesCache[ $protocol ]->pl, 0, $n ), 'prefix' );
    }

    /**
     * Tests initial update, then tests when some prefixes are removed from IRRDB results, and when new prefixes are added
     * @return void
     * @throws \IXP\Exceptions\ConfigurationException
     */
    public function testPrefixUpdateBothProtocols()
    {

        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $testBegin = Carbon::now();
        $testTime1 = $testBegin->copy();
        Carbon::setTestNow($testTime1);

        $this->assertEquals( 0, IrrdbPrefix::where( 'customer_id', $this->customer->id )->count() );
        $this->assertNull( IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first() );

        // Test initial save imports 5 prefixes for both protocols
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )  ->with( 'whois.radb.net' )->andReturnSelf()->times( 6 ); // 2 protocols to update, performed 3 times
        $bgpq->expects( 'setSources' )->with( 'RIPE' )          ->andReturnSelf()->times( 6 ); // 2 protocols to update, performed 3 times
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 ) ->andReturn( $this->getSomePrefixesForProtocol(4, 5) );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 6 ) ->andReturn( $this->getSomePrefixesForProtocol(6, 5) );

        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(5, $result['v4']['count']);
        $this->assertEquals( $this->getSomePrefixesForProtocol(4, 5), $result['v4']['new']);
        $this->assertEquals([], $result['v4']['stale']);
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(5, $result['v6']['count']);
        $this->assertEquals( $this->getSomePrefixesForProtocol(6, 5), $result['v6']['new']);
        $this->assertEquals([], $result['v6']['stale']);
        $this->assertTrue($result['v6']['dbUpdated']);

        // IrrdbPrefix's now contains the 5 irrdb results
        $this->assertEquals($this->getSomePrefixesForProtocol(4, 5), $this->getPrefixesForProtocol( 4 ) );
        $this->assertEquals($this->getSomePrefixesForProtocol(6, 5), $this->getPrefixesForProtocol( 6 ) );

        // IrrdbUpdateLog reflects updates to prefix fields
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime1->timestamp, $updateLog->prefix_v4->timestamp );
        $this->assertEquals($testTime1->timestamp, $updateLog->prefix_v6->timestamp );
        $this->assertNull( $updateLog->asn_v4 );
        $this->assertNull( $updateLog->asn_v6 );

        // the cached prefixes were stored for both protocols
        $this->assertEquals( $this->getSomePrefixesForProtocol(4, 5), $this->getCachedPrefixesForProtocol(4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol(6, 5), $this->getCachedPrefixesForProtocol(6 ) );

        // Test removal of 2 prefixes
        $testTime2 = $testBegin->copy()->addMinutes(1);
        Carbon::setTestNow($testTime2);
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 )->andReturn( $this->getSomePrefixesForProtocol(4, 3) );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 6 )->andReturn( $this->getSomePrefixesForProtocol(6, 3) );
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(3, $result['v4']['count']);
        $this->assertEquals( [], $result['v4']['new']);
        $this->assertEquals( [ '77.72.72.0/21', '77.72.72.0/22' ], array_column($result['v4']['stale'], 'prefix'));
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(3, $result['v6']['count']);
        $this->assertEquals( [], $result['v6']['new']);
        $this->assertEquals( ['2001:67c:1bc::/48', '2001:67c:10b8::/48'], array_column($result['v6']['stale'], 'prefix'));
        $this->assertTrue($result['v6']['dbUpdated']);

        // IrrdbPrefix's reduced by 2
        $this->assertEquals( $this->getSomePrefixesForProtocol(4, 3), $this->getPrefixesForProtocol( 4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol(6, 3), $this->getPrefixesForProtocol( 6 ) );

        // IrrdbUpdateLog reflects the new time
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals( $testTime2->timestamp, $updateLog->prefix_v4->timestamp );
        $this->assertEquals( $testTime2->timestamp, $updateLog->prefix_v6->timestamp );
        $this->assertNull( $updateLog->asn_v4 );
        $this->assertNull( $updateLog->asn_v6 );

        // the cached prefixes were updated for both protocols
        $this->assertEquals( $this->getSomePrefixesForProtocol(4, 3), $this->getCachedPrefixesForProtocol(4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol(6, 3), $this->getCachedPrefixesForProtocol(6 ) );

        // Test when a new prefix is added
        $testTime3 = $testBegin->copy()->addMinutes( 2 );
        Carbon::setTestNow( $testTime3 );

        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 )->andReturn( $this->getSomePrefixesForProtocol(4, 4 ) );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 6 )->andReturn( $this->getSomePrefixesForProtocol(6, 4 ) );
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(4, $result['v4']['count']);
        $this->assertEquals( ['77.72.72.0/21',], $result['v4']['new']);
        $this->assertEquals( [], array_column($result['v4']['stale'], 'prefix'));
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(4, $result['v6']['count']);
        $this->assertEquals( ['2001:67c:1bc::/48',], $result['v6']['new']);
        $this->assertEquals( [], array_column($result['v6']['stale'], 'prefix'));
        $this->assertTrue($result['v6']['dbUpdated']);

        // IrrdbPrefixes - both have an extra prefix after update
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 4 ), $this->getPrefixesForProtocol( 4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 4 ), $this->getPrefixesForProtocol( 6 ) );

        // IrrdbUpdateLog is updated since last run
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime3->timestamp, $updateLog->prefix_v4->timestamp );
        $this->assertEquals($testTime3->timestamp, $updateLog->prefix_v6->timestamp );
        $this->assertNull( $updateLog->asn_v4 );
        $this->assertNull( $updateLog->asn_v6 );

        // the cached prefixes were updated for both protocols
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 4 ), $this->getCachedPrefixesForProtocol( 4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 4 ), $this->getCachedPrefixesForProtocol( 6 ) );
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

        $this->assertEquals( 0, IrrdbPrefix::where( 'customer_id', $this->customer->id )->count() );
        $this->assertNull( IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first() );

        // Test initial save
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )->with( 'whois.radb.net' )->andReturnSelf()->times(4 );
        $bgpq->expects( 'setSources' )->with( 'RIPE' )->andReturnSelf()->times(4 );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 )->andReturn( $this->getSomePrefixesForProtocol( 4, 6 ) );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 6 )->andReturn( $this->getSomePrefixesForProtocol( 6, 6 ) );
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $task->update();

        $this->assertEquals($this->getSomePrefixesForProtocol( 4, 6 ), $this->getPrefixesForProtocol(4 ) );
        $this->assertEquals($this->getSomePrefixesForProtocol( 6, 6 ), $this->getPrefixesForProtocol(6 ) );

        // Both protocols updated
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime1->timestamp, $updateLog->prefix_v4->timestamp );
        $this->assertEquals($testTime1->timestamp, $updateLog->prefix_v6->timestamp );


        // Test IRRDB returning zero records for IPV6
        $testTime2 = $testBegin->copy()->addMinutes(1);
        Carbon::setTestNow($testTime2);

        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 )->andReturn( $this->getSomePrefixesForProtocol( 4, 6 ) );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 6 )->andReturn( [] );
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $result = $task->update();
        $this->assertEquals(6, $result['v4']['count']);
        $this->assertEquals( [] , $result['v4']['new']);
        $this->assertEquals([], $result['v4']['stale']);
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(0, $result['v6']['count']);
        $this->assertEquals( [], $result['v6']['new']);
        $this->assertEquals([], $result['v6']['stale']);
        $this->assertFalse($result['v6']['dbUpdated']);
        $this->assertEquals("IRRDB prefix: Test Customer has a non-zero prefix count for IPv6 in the database but BGPQ3 returned none. Please examine manually. No databases changes made for this customer.", $result['msg']);

        // No change to the original, because IRRDB query returned empty set.
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 6 ), $this->getPrefixesForProtocol( 4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 6 ), $this->getPrefixesForProtocol( 6 ) );

        // Also no change to update log for V6
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals( $testTime2->timestamp, $updateLog->prefix_v4->timestamp );  # no change but still did routine
        $this->assertEquals( $testTime1->timestamp, $updateLog->prefix_v6->timestamp );
    }

    /**
     * The task only refreshes prefixes for a certain protocol if the VLI has that protocol enabled.
     * The test starts with both protocols are enabled, then tests what happens when IPV6 is disabled
     * When that happens, IrrdbPrefixes's will be empty for v6, and the cache for v6 should be cleared.
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

        // Test initial save of 6 prefixes
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )->with( 'whois.radb.net' )->andReturnSelf()->times( 3 );
        $bgpq->expects( 'setSources' )->with( 'RIPE' )->andReturnSelf()->times( 3 );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 )->andReturn( $this->getSomePrefixesForProtocol( 4, 6 ) );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 6 )->andReturn( $this->getSomePrefixesForProtocol( 6, 6 ) );
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $task->update();

        // DB prefixes
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 6 ), $this->getPrefixesForProtocol(4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 6 ), $this->getPrefixesForProtocol(6 ) );

        // Cached prefixes
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 6 ), $this->getCachedPrefixesForProtocol(4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 6 ), $this->getCachedPrefixesForProtocol(6 ) );

        // Both protocols updated
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime1->timestamp, $updateLog->prefix_v4->timestamp );
        $this->assertEquals($testTime1->timestamp, $updateLog->prefix_v6->timestamp );

        // Disable IPV6
        $this->vli->ipv6enabled = false;
        $this->vli->save();

        // Test an update, no change to returned results
        $testTime2 = $testBegin->copy()->addMinutes(1);
        Carbon::setTestNow($testTime2);

        // BGPQ3 only called for v4.
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 )->andReturn( $this->getSomePrefixesForProtocol( 4, 6 ) );
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $task->update();

        // IPV4 IrrdbPrefixes has one less
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 6 ), $this->getPrefixesForProtocol( 4 ) );
        // IPV6 IrrdbPrefixs is now empty.
        $this->assertEquals( [ ], $this->getPrefixesForProtocol( 6 ) );

        // Cached prefixes - IPV4 updated, IPV6 entry deleted
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 6 ), $this->getCachedPrefixesForProtocol(4 ) );
        $this->assertEquals( null, $this->getCachedPrefixesForProtocol(6 ) );

        // IrrdbUpdateLog for IPV4 is more recent than IPV6
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals($testTime2->timestamp, $updateLog->prefix_v4->timestamp );
        $this->assertEquals($testTime1->timestamp, $updateLog->prefix_v6->timestamp );
    }


    /**
     * The task only refreshes prefixes for a certain protocol if the VLI has that protocol enabled.
     * The test starts with both protocols are enabled, then tests what happens when IPV6 is disabled
     * When that happens, IrrdbPrefix's will be empty for v6, and the cache for v6 should be cleared.
     * @return void
     * @throws \IXP\Exceptions\ConfigurationException
     */
    public function testMalformedPrefixesRemoved()
    {
        // Starts with both, we'll disable one
        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $testBegin = Carbon::now();
        $testTime1 = $testBegin->copy();
        Carbon::setTestNow($testTime1);

        // Test initial save of 6 prefixes
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )->with( 'whois.radb.net' )->andReturnSelf()->times( 4 );
        $bgpq->expects( 'setSources' )->with( 'RIPE' )->andReturnSelf()->times( 4 );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 )->andReturn( $this->getSomePrefixesForProtocol( 4, 6 ) );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 6 )->andReturn( $this->getSomePrefixesForProtocol( 6, 6 ) );
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $task->update();

        // DB + cached match
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 6 ), $this->getPrefixesForProtocol(4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 6 ), $this->getPrefixesForProtocol(6 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 6 ), $this->getCachedPrefixesForProtocol( 4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 6 ), $this->getCachedPrefixesForProtocol( 6 ) );

        // IrrdbUpdateLog updated
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals( $testTime1->timestamp, $updateLog->prefix_v4->timestamp );

        // Test IRRDB returning an invalid for some reason (should be filtered by validate)
        $testTime2 = $testBegin->copy()->addMinutes(1);
        Carbon::setTestNow( $testTime2 );

        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 )->andReturn( array_merge($this->getSomePrefixesForProtocol( 4, 10 ), ['invalid']) );
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 6 )->andReturn( array_merge($this->getSomePrefixesForProtocol( 6, 10 ), ['invalid']) );
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $task->update();

        // 'invalid' isn't in IrrdbPrefixs or cached for v4
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 10 ), $this->getPrefixesForProtocol( 4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 4, 10 ), $this->getCachedPrefixesForProtocol( 4 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 10 ), $this->getPrefixesForProtocol( 6 ) );
        $this->assertEquals( $this->getSomePrefixesForProtocol( 6, 10 ), $this->getCachedPrefixesForProtocol( 6 ) );

        // IrrdbUpdateLog was updated
        $updateLog = IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first();
        $this->assertNotNull( $updateLog );
        $this->assertEquals ($testTime2->timestamp, $updateLog->prefix_v4->timestamp );
    }

    /**
     *  The task can be scoped to only the provided protocols, even if both are enabled on the VLI
     */
    public function testCanSetProtocols()
    {
        $bgpq = Mockery::mock(IrrQuerier::class);
        $task = new UpdatePrefixDb( $bgpq, $this->customer );
        $this->assertEquals( [ 4, 6 ], $task->protocols() );

        $task = new UpdatePrefixDb( $bgpq, $this->customer , [4]);
        $this->assertEquals( [ 4 ], $task->protocols() );

        $task = new UpdatePrefixDb( $bgpq, $this->customer , [6]);
        $this->assertEquals( [ 6 ], $task->protocols() );

        $task = new UpdatePrefixDb( $bgpq, $this->customer , [4, 6]);
        $this->assertEquals( [ 4, 6 ], $task->protocols() );

        $this->vli->ipv4enabled = true;
        $this->vli->ipv6enabled = true;
        $this->vli->save();

        $testBegin = Carbon::now();
        $testTime1 = $testBegin->copy();
        Carbon::setTestNow($testTime1);

        $this->assertEquals( 0, IrrdbPrefix::where( 'customer_id', $this->customer->id )->count() );
        $this->assertNull( IrrdbUpdateLog::where( 'cust_id' , $this->customer->id )->first() );

        // Test initial save imports 5 prefixes for both protocols
        $bgpq = Mockery::mock(IrrQuerier::class);
        $bgpq->expects( 'setWhois' )  ->with( 'whois.radb.net' )->andReturnSelf()->times( 1 ); // 2 protocols to update, performed 3 times
        $bgpq->expects( 'setSources' )->with( 'RIPE' )          ->andReturnSelf()->times( 1 ); // 2 protocols to update, performed 3 times
        $bgpq->expects( 'getPrefixList' )->with( 'AS-TEST', 4 ) ->andReturn( $this->getSomePrefixesForProtocol(4, 5) );

        $task = new UpdatePrefixDb( $bgpq, $this->customer , [4] );
        $result = $task->update();
        $this->assertEquals(5, $result['v4']['count']);
        $this->assertEquals( $this->getSomePrefixesForProtocol(4, 5), $result['v4']['new']);
        $this->assertEquals( [], $result['v4']['stale']);
        $this->assertTrue($result['v4']['dbUpdated']);
        $this->assertEquals(0, $result['v6']['count']);
        $this->assertEquals( [], $result['v6']['new']);
        $this->assertEquals( [], $result['v6']['stale']);
        $this->assertFalse($result['v6']['dbUpdated']);
    }
}