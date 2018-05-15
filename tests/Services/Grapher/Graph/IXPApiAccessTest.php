<?php

namespace Tests\Services\Grapher\Graph;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Config, D2EM;

use Entities\User as UserEntity;


/**
 * Test access restrictions for IXP graphs
 *
 * Class IXPAccessTest
 * @package Tests\Services\Grapher\Graph
 */
class IXPApiAccessTest extends Access
{
//    public function setUp() {
////        Config::set( 'grapher.backend', 'dummy' );
//    }


    /**
     * Test access restrictions for public api access
     * @return void
     */
    public function testApiPublicAccess()
    {
        // this should be the default
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(200);

        // force the default
        Config::set( 'grapher.access.ixp', '0' );
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions for verious non-public access settings
     * @return void
     */
    public function testApiNonPublicAccess()
    {
        Config::set( 'grapher.access.ixp', '1' );
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        Config::set( 'grapher.access.ixp', '2' );
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        Config::set( 'grapher.access.ixp', '3' );
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        Config::set( 'grapher.access.ixp', 'blah' );
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        Config::set( 'grapher.access.ixp', null );
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(403);
    }

    /**
     * Test access restrictions requiring minimum user of CustUser (privs=1) for api access
     * @return void
     */
    public function testApiCustUserAccess()
    {
        Config::set( 'grapher.access.ixp', '1' );

        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getSuperUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions requiring minimum logged in user of CustAdmin (privs=2) for web access
     * @return void
     */
    public function testWebCustAdminAccess()
    {
        Config::set( 'grapher.access.ixp', '2' );
        
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getSuperUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions requiring logged in superuser (privs=3) for web access
     * @return void
     */
    public function testWebSuperuserAccess()
    {
        Config::set( 'grapher.access.ixp', '3' );
        $response = $this->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getSuperUser() )->get('/grapher/ixp?id=1');
        $response->assertStatus(200);
    }


}
