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

namespace Tests\Api;

use Carbon\Carbon;
use IXP\Models\Infrastructure;
use IXP\Models\Router;
use IXP\Models\TaskLastRun;
use IXP\Models\Vlan;
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
class NagiosControllerTest extends TestCase
{
    #[\Override]
    public function tearDown(): void
    {
        TaskLastRun::truncate();
        parent::tearDown();
    }

    public function testCustomersVlanNotFound(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/customers/999999/4")
            ->assertStatus(404);
    }

    public function testCustomersProtocolUnknown(): void
    {
        $vlan1 = Vlan::whereId(1)->firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/customers/{$vlan1->id}/9000/default")
            ->assertStatus(404)
            ->assertJson(['message' => "Unknown protocol"]);
    }

    public function testCustomersTemplateNotFound(): void
    {
        $vlan1 = Vlan::whereId(1)->firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/customers/{$vlan1->id}/4/unknowntemplate")
            ->assertStatus(404)
            ->assertJson(['message' => "Unknown template"]);
    }

    public function testCustomers(): void
    {
        $vlan1 = Vlan::whereId(1)->firstOrFail();
        $vlan2 = Vlan::whereId(2)->firstOrFail();

        $this->assertCount(0, TaskLastRun::all());
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/customers/{$vlan1->id}/4")
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.customers.default');

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_CUSTOMERS,
            'parameters->vlan' => $vlan1->id,
            'parameters->protocol' => 4,
            'parameters->template' => 'api/v4/nagios/customers/default',
        ]);

        // behaviour with parameters
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/customers/{$vlan1->id}/6")
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.customers.default');

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_CUSTOMERS,
            'parameters->vlan' => $vlan1->id,
            'parameters->protocol' => 6,
            'parameters->template' => 'api/v4/nagios/customers/default',
        ]);

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/customers/{$vlan2->id}/6")
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.customers.default');

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_CUSTOMERS,
            'parameters->vlan' => $vlan2->id,
            'parameters->protocol' => 6,
            'parameters->template' => 'api/v4/nagios/customers/default',
        ]);
    }

    public function testSwitchesInfraNotFound(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/switches/999999")
            ->assertStatus(404);
    }

    public function testSwitchesTemplateNotFound(): void
    {
        $infrastructure1 = Infrastructure::whereId(1)->firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/switches/{$infrastructure1->id}/unknowntemplate")
            ->assertStatus(404)
            ->assertJson(['message' => "Unknown template"]);
    }

    public function testSwitches(): void
    {
        $infrastructure1 = Infrastructure::whereId(1)->firstOrFail();
        $infrastructure2 = Infrastructure::whereId(2)->firstOrFail();

        $this->assertCount(0, TaskLastRun::all());
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/switches/{$infrastructure1->id}")
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.switches.default');

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_SWITCHES,
            'parameters->infrastructure' => $infrastructure1->id,
            'parameters->template' => 'api/v4/nagios/switches/default',
        ]);

        // behaviour with parameters
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/switches/{$infrastructure2->id}")
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.switches.default');

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_SWITCHES,
            'parameters->infrastructure' => $infrastructure2->id,
            'parameters->template' => 'api/v4/nagios/switches/default',
        ]);
    }

    public function testBirdseyeDaemonsVlanNotFound(): void
    {
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/birdseye-daemons/default/999999")
            ->assertStatus(404);
    }

    public function testBirdseyeDaemonsTemplateNotFound(): void
    {
        $vlan1 = Vlan::whereId(1)->firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/birdseye-daemons/unknowntemplate/{$vlan1->id}")
            ->assertStatus(404)
            ->assertJson(['message' => "Unknown template"]);
    }

    public function testBirdseyeDaemons(): void
    {
        $this->assertCount(0, TaskLastRun::all());
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/birdseye-daemons")
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.birdseye-daemons.default');

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_BIRDSEYE_DAEMONS,
            'parameters->vlan' => null,
            'parameters->template' => 'api/v4/nagios/birdseye-daemons/default',
        ]);

        // with vlan param
        $vlan1 = Vlan::whereId(1)->firstOrFail();
        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get("admin/api/v4/nagios/birdseye-daemons/default/" . $vlan1->id)
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.birdseye-daemons.default');

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_BIRDSEYE_DAEMONS,
            'parameters->vlan' => $vlan1->id,
            'parameters->template' => 'api/v4/nagios/birdseye-daemons/default',
        ]);
    }

    public function testBirdseyeDaemonsNoBirdsEyeRouters(): void
    {
        // with vlan param
        $vlan1 = Vlan::whereId(1)->firstOrFail();

        $birdsEyeRouterIds = Router::where('api_type', Router::API_TYPE_BIRDSEYE)
            ->where('vlan_id', $vlan1->id)
            ->get('id')->pluck('id')->toArray();

        Router::whereIn('id', $birdsEyeRouterIds)->update(['api_type' => Router::API_TYPE_OTHER]);

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/birdseye-daemons/default/" . $vlan1->id)
            ->assertJson(['message' => "No routers for the provided VLAN ID / Bird's Eye API type."])
            ->assertNotFound();

        Router::whereIn('id', $birdsEyeRouterIds)->update(['api_type' => Router::API_TYPE_BIRDSEYE]);
    }

    public function testBirdseyeBgpSessionsTemplateNotFound(): void
    {
        $vlan1 = Vlan::whereId(1)->firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/birdseye-bgp-sessions/{$vlan1->id}/4/" . Router::TYPE_ROUTE_SERVER . "/unknowntemplate")
            ->assertStatus(404)
            ->assertJson( [ 'message' => "Unknown template" ] );
    }

    public function testBirdseyeBgpSessionsUnknownProtocol(): void
    {
        $vlan1 = Vlan::whereId(1)->firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/birdseye-bgp-sessions/{$vlan1->id}/9999/" . Router::TYPE_ROUTE_SERVER)
            ->assertStatus(404)
            ->assertJson( [ 'message' => "Unknown protocol" ] );
    }

    public function testBirdseyeBgpSessionsUnknownRouterType(): void
    {
        $vlan1 = Vlan::whereId(1)->firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/birdseye-bgp-sessions/{$vlan1->id}/4/12345")
            ->assertStatus(404)
            ->assertJson( [ 'message' => "Unknown router type" ] );
    }

    public function testBirdseyeBgpSessionsNoBirdsEyeRouters(): void
    {
        // with vlan param
        $vlan1 = Vlan::whereId(1)->firstOrFail();

        $birdsEyeRouterIds = Router::where('api_type', Router::API_TYPE_BIRDSEYE)
            ->where('vlan_id', $vlan1->id)
            ->get('id')->pluck('id')->toArray();

        Router::whereIn('id', $birdsEyeRouterIds)->update(['api_type' => Router::API_TYPE_OTHER]);

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/birdseye-bgp-sessions/" . $vlan1->id . "/4/" . Router::TYPE_ROUTE_SERVER)
            ->assertJson(['message' => "No suitable router(s) found."])
            ->assertNotFound();

        Router::whereIn('id', $birdsEyeRouterIds)->update(['api_type' => Router::API_TYPE_BIRDSEYE]);
    }

    public function testBirdseyeBgpSessions(): void
    {
        $vlan2 = Vlan::whereId(2)->firstOrFail();

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/birdseye-bgp-sessions/{$vlan2->id}/4/" . Router::TYPE_ROUTE_SERVER)
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.birdseye-bgp-sessions.default')
        ;

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_BIRDSEYE_BGP_SESSIONS,
            'parameters->vlan' => $vlan2->id,
            'parameters->protocol' => 4,
            'parameters->type' => Router::TYPE_ROUTE_SERVER,
            'parameters->template' => 'api/v4/nagios/birdseye-bgp-sessions/default',
        ]);

        $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->withHeader("accept", "application/json")
            ->get("admin/api/v4/nagios/birdseye-bgp-sessions/{$vlan2->id}/6/" . Router::TYPE_ROUTE_SERVER . "/default")
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=utf-8')
            ->assertViewIs('api.v4.nagios.birdseye-bgp-sessions.default')
        ;

        $this->assertDatabaseHas('task_last_run', [
            'task_key' => TaskLastRun::NAGIOS_BIRDSEYE_BGP_SESSIONS,
            'parameters->vlan' => $vlan2->id,
            'parameters->protocol' => 6,
            'parameters->type' => Router::TYPE_ROUTE_SERVER,
            'parameters->template' => 'api/v4/nagios/birdseye-bgp-sessions/default',
        ]);
    }

}