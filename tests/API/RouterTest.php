<?php

namespace API;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


use IXP\Models\Router;
use Tests\TestCase;

/**
 * Test router api functions
 *
 *
 * Class RouterTest
 * @author     Barry O'Donovan <barry@opensolutions.ie>
 * @category   IXP
 * @package    IXP\Tests\API
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouterTest extends TestCase
{
    private const string API_KEY = 'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC9';

    private Router $r;
    private Router $rpair;
    
    public function setUp(): void {
        
        parent::setUp();
        
        // ensure we've deleted any existing router
        Router::whereIn('handle', [ 'test-router', 'test-router-pair' ] )->delete();
        
        // create a router for these tests
        $this->r = Router::forceCreate([
            'pair_id' => null,
            'vlan_id' => 1,
            'handle' => 'test-router',
            'protocol' => '4',
            'type' => '1',
            'name' => 'Test Router',
            'shortname' => 'test-router',
            'router_id' => '192.0.2.88',
            'peering_ip' => '192.0.2.88',
            'asn' => '65544',
            'software' => '1',
            'mgmt_host' => '192.0.2.89',
            'api' => null,
            'api_type' => Router::API_TYPE_NONE,
            'last_update_started' => null,
            'last_updated' => null,
            'pause_updates' => false,
        ]);
    }
    
    public function setUpPair(): void {
        
        // ensure we've deleted any existing router
        Router::where('handle', 'test-router-pair')->delete();
        
        // create a router for these tests
        $this->rpair = Router::forceCreate([
            'pair_id' => $this->r->id,
            'vlan_id' => 1,
            'handle' => 'test-router-pair',
            'protocol' => '4',
            'type' => '1',
            'name' => 'Test Router Pair',
            'shortname' => 'test-router-pair',
            'router_id' => '192.0.2.86',
            'peering_ip' => '192.0.2.86',
            'asn' => '65544',
            'software' => '1',
            'mgmt_host' => '192.0.2.87',
            'api' => null,
            'api_type' => Router::API_TYPE_NONE,
            'last_update_started' => null,
            'last_updated' => null,
            'pause_updates' => false,
        ]);
        
        $this->r->pair_id = $this->rpair->id;
        $this->r->save();
    }
    
    private function withKey(): static {
        return $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY);
    }
    
    public function tearDown(): void {
        Router::whereIn('handle', [ 'test-router', 'test-router-pair' ] )->delete();
        parent::tearDown();
    }
    
    public function testApiRouterNotExists(): void
    {
        $response = $this->withKey()->get( '/api/v4/router/updated/test-router-xxx' );
        $response->assertStatus( 404 );
    }
    
    public function testApiRouterGetLastUpdatedNulls(): void
    {
        $response = $this->withKey()->get( '/api/v4/router/updated/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNull( $response->json('last_update_started') );
        $this->assertNull( $response->json('last_update_started_unix') );
        $this->assertNull( $response->json('last_updated') );
        $this->assertNull( $response->json('last_updated_unix') );
    }
    
    public function testApiRouterGetLastUpdatedNotNulls(): void
    {
        $now = now();
        $this->r->last_update_started = $this->r->last_updated = $now;
        $this->r->save();
        
        $response = $this->withKey()->get( '/api/v4/router/updated/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNotNull( $response->json('last_update_started') );
        $this->assertNotNull( $response->json('last_update_started_unix') );
        $this->assertNotNull( $response->json('last_updated') );
        $this->assertNotNull( $response->json('last_updated_unix') );
        
        $this->assertEquals( $now->toAtomString(), $response->json('last_update_started') );
        $this->assertEquals( $now->format('U'), $response->json('last_update_started_unix') );
        $this->assertEquals( $now->toAtomString(), $response->json('last_updated') );
        $this->assertEquals( $now->format('U'), $response->json('last_updated_unix') );
    }
    public function testApiRouterGetUpdateLockOnNulls(): void
    {
        $response = $this->withKey()->post( '/api/v4/router/get-update-lock/test-router' );
        $response->assertStatus( 200 );

        $this->assertNotNull( $response->json('last_update_started') );
        $this->assertNotNull( $response->json('last_update_started_unix') );
        $this->assertIsInt( $response->json('last_update_started_unix') );
        
        $this->assertNull( $response->json('last_updated') );
        $this->assertNull( $response->json('last_updated_unix') );
        
        $this->r->refresh();
        
        $this->assertNotNull( $this->r->last_update_started );
        $this->assertNull( $this->r->last_updated );
        
        $response = $this->withKey()->post( '/api/v4/router/updated/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNotNull( $response->json('last_updated') );
        $this->assertNotNull( $response->json('last_updated_unix') );
        
        $this->r->refresh();

        $this->assertNotNull( $this->r->last_updated );
        $this->assertGreaterThanOrEqual( $this->r->last_update_started, $this->r->last_updated );
        
    }
    
    public function testApiRouterGetUpdateLockOnNotNulls(): void
    {
        // the add second also ensures the race condition mentioned in the class is avoided and tested
        $this->r->last_update_started = $this->r->last_updated = now()->addSecond();
        $this->r->save();

        $response = $this->withKey()->post( '/api/v4/router/get-update-lock/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNotNull( $response->json('last_update_started') );
        $this->assertNotNull( $response->json('last_update_started_unix') );
        $this->assertNotNull( $response->json('last_updated') );
        $this->assertNotNull( $response->json('last_updated_unix') );
        $this->assertNotEquals( $response->json('last_updated'), $response->json('last_update_started') );
        
        $this->r->refresh();
        
        $this->assertNotEquals( $this->r->last_update_started, $this->r->last_updated );
        $this->assertNotEquals( $this->r->last_update_started->format('U'), $this->r->last_updated->format('U') );
        $this->assertGreaterThan( $this->r->last_updated->format('U'), $this->r->last_update_started->format('U') );

    }
    
    public function testApiRouterReleaseLockOnNotLocked(): void
    {
        $response = $this->withKey()->post( '/api/v4/router/release-update-lock/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNull( $response->json('last_update_started') );
        $this->assertNull( $response->json('last_update_started_unix') );
        $this->assertNull( $response->json('last_updated') );
        $this->assertNull( $response->json('last_updated_unix') );
    }
    
    public function testApiRouterReleaseLockOnFirstUpdateStarted(): void
    {
        $this->r->last_update_started = now()->subMinutes( 2 );
        $this->r->save();
        
        $response = $this->withKey()->post( '/api/v4/router/release-update-lock/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNull( $response->json( 'last_update_started' ) );
        $this->assertNull( $response->json( 'last_update_started_unix' ) );
        $this->assertNull( $response->json( 'last_updated' ) );
        $this->assertNull( $response->json( 'last_updated_unix' ) );
    }
    
    public function testApiRouterReleaseLockOnSubsequentUpdateStarted(): void
    {
        $now = now()->subMinutes(2);
        $this->r->last_updated = $now;
        $this->r->last_update_started = now();
        $this->r->save();
        
        $response = $this->withKey()->post( '/api/v4/router/release-update-lock/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNotNull( $response->json('last_update_started') );
        $this->assertNotNull( $response->json('last_update_started_unix') );
        $this->assertNotNull( $response->json('last_updated') );
        $this->assertNotNull( $response->json('last_updated_unix') );
        $this->assertEquals( $response->json('last_updated'), $response->json('last_update_started') );
        
        $this->assertEquals( $now->toAtomString(), $response->json('last_update_started') );
        $this->assertEquals( $now->format('U'), $response->json('last_update_started_unix') );
        $this->assertEquals( $now->toAtomString(), $response->json('last_updated') );
        $this->assertEquals( $now->format('U'), $response->json('last_updated_unix') );
        
    }
    
    public function testApiRouterSetUpdatedFirstTime(): void
    {
        $now = now();
        $this->r->last_update_started = $now->clone()->subMinutes( 2 );
        $this->r->save();
        
        $response = $this->withKey()->post( '/api/v4/router/updated/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNotNull( $response->json( 'last_update_started' ) );
        $this->assertNotNull( $response->json( 'last_update_started_unix' ) );
        $this->assertNotNull( $response->json( 'last_updated' ) );
        $this->assertNotNull( $response->json( 'last_updated_unix' ) );
        
        $this->r->refresh();
        $this->assertEquals( $now->clone()->subMinutes( 2 )->format( 'U' ), $this->r->last_update_started->format( 'U' ) );
        $this->assertGreaterThanOrEqual( $now->format( 'U' ), $this->r->last_updated->format( 'U' ) );
        
    }
    
    public function testApiRouterSetUpdatedSubsequent(): void
    {
        $now = now();
        $this->r->last_update_started = $now;
        $this->r->last_updated = $now->clone()->subMinute();
        $this->r->save();
        
        $response = $this->withKey()->post( '/api/v4/router/updated/test-router' );
        $response->assertStatus( 200 );
        
        $this->assertNotNull( $response->json( 'last_update_started' ) );
        $this->assertNotNull( $response->json( 'last_update_started_unix' ) );
        $this->assertNotNull( $response->json( 'last_updated' ) );
        $this->assertNotNull( $response->json( 'last_updated_unix' ) );
        
        $this->r->refresh();
        $this->assertGreaterThanOrEqual( $now->format( 'U' ), $this->r->last_updated->format( 'U' ) );
    }
    
    
    public function testApiRouterPairUpdatingBlocksOnNulls(): void {
        $this->setUpPair();
        $this->rpair->last_update_started = now()->subMinutes( 2 );
        $this->rpair->save();

        $response = $this->withKey()->post( '/api/v4/router/get-update-lock/test-router' );

        $response->assertStatus( 423 );
        $this->assertEquals( 'Router not available for update', $response->getContent() );
    }
    
    public function testApiRouterPairUpdatingBlocksOnSubsequent(): void {
        $this->setUpPair();
        $this->rpair->last_updated = now()->subMinutes( 3 );
        $this->rpair->last_update_started = now()->subMinutes( 2 );
        $this->rpair->save();
        
        $response = $this->withKey()->post( '/api/v4/router/get-update-lock/test-router' );
        
        $response->assertStatus( 423 );
        $this->assertEquals( 'Router not available for update', $response->getContent() );
    }
    
    
}