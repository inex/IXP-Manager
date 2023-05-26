<?php

namespace Tests\Services\Grapher\Graph\Access\Web;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Config;

use Tests\Services\Grapher\Graph\Access\Access;

/**
 * Test access restrictions for Members graphs
 *
 * Class MembersAccessTest
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Services\Grapher\Graph\Access\Web
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class MembersWebAccessTest extends Access
{
    /**
     * Test access restrictions for public web access
     *
     * @return void
     */
    public function testApiPublicAccess(): void
    {
        // this should be the default
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        // force the default
        Config::set( 'grapher.access.customer', 'own_graphs_only' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        // force public access
        Config::set( 'grapher.access.customer', 0 );
        $response = $this->get('/statistics/members');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions for various non-public access settings
     *
     * @return void
     */
    public function testWebNonPublicAccess(): void
    {
        Config::set( 'grapher.access.customer', '1' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        Config::set( 'grapher.access.customer', '2' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        Config::set( 'grapher.access.customer', '3' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        Config::set( 'grapher.access.customer', 'blah' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        Config::set( 'grapher.access.customer', null );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);
    }

    /**
     * Test access restrictions requiring own_graphs_only logged in user
     *
     * @return void
     */
    public function testWebOwnUserCustUserAccess(): void
    {
        Config::set( 'grapher.access.customer', 'own_graphs_only' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        // a customer user
        $response = $this->actingAs( $this->getCustUser( 'hecustuser' ) )->get('/statistics/members');
        $response->assertStatus(403);

        // a customer admin user
        $response = $this->actingAs( $this->getCustAdminUser( 'hecustadmin' ) )->get('/statistics/members');
        $response->assertStatus(403);

        // another customer user
        $response = $this->actingAs( $this->getCustUser( 'imcustuser' ) )->get('/statistics/members');
        $response->assertStatus(403);

        // another customer admin user
        $response = $this->actingAs( $this->getCustAdminUser( 'imcustadmin' ) )->get('/statistics/members');
        $response->assertStatus(403);

        // superadmin
        $response = $this->actingAs( $this->getSuperUser() )->get('/statistics/members');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions requiring minimum logged in user of CustUser (privs=1) for web access
     *
     * @return void
     */
    public function testWebCustUserAccess(): void
    {
        Config::set( 'grapher.access.customer', '1' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustUser() )->get('/statistics/members');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/statistics/members');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getSuperUser() )->get('/statistics/members');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions requiring minimum logged in user of CustAdmin (privs=2) for web access
     *
     * @return void
     */
    public function testWebCustAdminAccess(): void
    {
        Config::set( 'grapher.access.customer', '2' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustUser() )->get('/statistics/members');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/statistics/members');
        $response->assertStatus(200);

        $response = $this->actingAs( $this->getSuperUser() )->get('/statistics/members');
        $response->assertStatus(200);
    }

    /**
     * Test access restrictions requiring logged in superuser (privs=3) for web access
     *
     * @return void
     */
    public function testWebSuperuserAccess(): void
    {
        Config::set( 'grapher.access.customer', '3' );
        $response = $this->get('/statistics/members');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustUser() )->get('/statistics/members');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getCustAdminUser() )->get('/statistics/members');
        $response->assertStatus(403);

        $response = $this->actingAs( $this->getSuperUser() )->get('/statistics/members');
        $response->assertStatus(200);
    }
}
