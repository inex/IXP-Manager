<?php

namespace Tests\Api\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Tests\TestCase;

/**
 * Test the provisioning API authentication chain.
 *
 * This deliberately tests the scaffolding rather than any business logic: if the route
 * group, the API key middleware and the superuser privilege assertion do not line up, every
 * later endpoint fails the same way. Proving it once here keeps those failures legible.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PingTest extends TestCase
{
    private const string URL = '/admin/api/v4/provisioning/ping';

    /**
     * A superuser API key is accepted and the key's owner is resolved.
     */
    public function testPingWithSuperuserKey(): void
    {
        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_SUPERUSER )
            ->get( self::URL );

        $response->assertStatus( 200 );
        $response->assertJson( [ 'pong' => true ] );

        // the API key middleware logs the key's owner in, so the controller sees a user:
        $this->assertNotEmpty( $response->json( 'user' ) );
    }

    /**
     * Without a key the request is rejected outright - and, crucially, not redirected to
     * the login form, which a machine caller could not follow.
     */
    public function testPingWithoutKeyIsUnauthorised(): void
    {
        $response = $this->get( self::URL );

        $response->assertStatus( 401 );
    }

    /**
     * A valid key belonging to a non-superuser must not reach the provisioning API.
     */
    public function testPingWithCustAdminKeyIsForbidden(): void
    {
        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_CUSTADMIN )
            ->get( self::URL );

        $response->assertStatus( 403 );
    }

    /**
     * The same, for an ordinary customer user.
     */
    public function testPingWithCustUserKeyIsForbidden(): void
    {
        $response = $this->withHeader( 'X-IXP-Manager-API-Key', self::API_KEY_CUSTUSER )
            ->get( self::URL );

        $response->assertStatus( 403 );
    }
}
