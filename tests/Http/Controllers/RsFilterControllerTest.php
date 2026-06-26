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

namespace Http\Controllers;

use IXP\Models\RouteServerFilter;
use IXP\Models\User;
use IXP\Policies\CustomerPolicy;
use IXP\Policies\RouteServerFilterPolicy;
use Tests\Policies\RouteServerFilterPolicyTest;
use Tests\TestCase;
use Tests\Trait\ModifiesEnv;

class RsFilterControllerTest extends TestCase
{
    use ModifiesEnv;

    public function setUp(): void
    {
        $this->overrideEnv(["IXP_FE_FRONTEND_DISABLED_RS_FILTERS" => false]);
        parent::setUp();
    }

    public function testPolicyListCustomersSuperUserAllowed()
    {
        // hits policy
        $this
            ->actingAs( $this->getSuperUser() )
            ->get( 'admin/rs-filters/list-customers' )
            ->assertStatus( 200 )
            ->assertSee( "Route Server Filtering :: Customers with Filters" );
    }

    public function testPolicyListCustomers()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'checkListCustomers' )->times(2)->andReturn( false );
        });

        // Disable middleware to check the policy now
        $this->withoutMiddleware();

        $this->actingAs( $this->getCustAdminUser() )
            ->get( 'admin/rs-filters/list-customers' )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );

        $this->actingAs( $this->getCustUser() )
            ->get( 'admin/rs-filters/list-customers' )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );
    }

    public function testPolicyListForCustomer()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'listRsFilters' )->once()->andReturn( false );
        });

        $this->actingAs( $user = $this->getCustUser() )
            ->get( 'rs-filtering/' . $user->customer->id )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );
    }

    public function testPolicyRevert()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'revertRsFilters' )->once()->andReturn( false );
        });

        // Can create RsFilter for own customer
        $this->actingAs( $user = $this->getCustAdminUser() )
            ->post( 'rs-filter/revert/' . $user->customer->id )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );
    }

    public function testPolicyCommit()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'commitRsFilters' )->once()->andReturn( false );
        });

        // Can create RsFilter for own customer
        $this->actingAs( $user = $this->getCustAdminUser() )
            ->post( 'rs-filter/commit/' . $user->customer->id )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );
    }

    public function testPolicyCreate()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'createRsFilter' )->once()->andReturn( false );
        });

        $this->actingAs( $user = $this->getCustUser() )
            ->get(  'rs-filter/create/' . $user->customer->id )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );
    }

    public function testPolicyStore()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'createRsFilter' )->once()->andReturn( false );
        });

        // Can create RsFilter for own customer
        $this->actingAs( $user = $this->getCustAdminUser() )
            ->post( 'rs-filter/store', [
                'peer_id' => "0",
                'vlan_id' => "0",
                "protocol" => "",
                "advertised_prefix_val" => "",
                "action_advertise" => "AS_IS",
                "received_prefix_val" => "",
                "action_receive" => "AS_IS",
                "custid" => $user->custid,
            ] )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );
    }

    public function testPolicyEdit()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'update' )->once()->andReturn( false );
        });
        $user = $this->getCustAdminUser();

        $rsf = new RouteServerFilter();
        $rsf->customer_id = $user->custid;
        $rsf->save();

        // Can create RsFilter for own customer
        $this->actingAs( $user )
            ->get( 'rs-filter/edit/' . $rsf->id )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );

        $rsf->delete();
    }

    public function testPolicyUpdate()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'update' )->once()->andReturn( false );
        });
        $user = $this->getCustAdminUser();

        $rsf = new RouteServerFilter();
        $rsf->customer_id = $user->custid;
        $rsf->save();

        // Can create RsFilter for own customer
        $this->actingAs( $user )
            ->put( 'rs-filter/update/' . $rsf->id , [
                'peer_id' => "0",
                'vlan_id' => "0",
                "protocol" => "",
                "advertised_prefix_val" => "",
                "action_advertise" => "AS_IS",
                "received_prefix_val" => "",
                "action_receive" => "AS_IS",
                "custid" => $user->custid,
            ])
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );

        $rsf->delete();
    }

    public function testPolicyView()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'view' )->once()->andReturn( false );
        });
        $user = $this->getCustAdminUser();

        $rsf = new RouteServerFilter();
        $rsf->customer_id = $user->custid;
        $rsf->save();

        // Can create RsFilter for own customer
        $this->actingAs( $user )
            ->get( 'rs-filter/view/' . $rsf->id )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );

        $rsf->delete();
    }

    public function testPolicyToggleEnable()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'toggleEnable' )->once()->andReturn( false );
        });
        $user = $this->getCustAdminUser();

        $rsf = new RouteServerFilter();
        $rsf->customer_id = $user->custid;
        $rsf->save();

        // Can create RsFilter for own customer
        $this->actingAs( $user )
            ->get( 'rs-filter/toggle-enable/' . $rsf->id . '/0' )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );

        $rsf->delete();
    }

    public function testPolicyChangeOrder()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function ( $mock ) {
            $mock->expects( 'changeOrder' )->once()->andReturn( false );
        });
        $user = $this->getCustAdminUser();

        $rsf = new RouteServerFilter();
        $rsf->customer_id = $user->custid;
        $rsf->order_by = 0;
        $rsf->save();

        // Can create RsFilter for own customer
        $this->actingAs( $user )
            ->get( 'rs-filter/change-order/' . $rsf->id . '/0' )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );

        $rsf->delete();
    }

    public function testPolicyDelete()
    {
        // We unit test policy logic already - just check that the correct policy / method is used for authorization
        $this->partialMock( RouteServerFilterPolicy::class, function( $mock ) {
            $mock->expects( 'delete' )->once()->andReturn( false );
        });
        $user = $this->getCustAdminUser();

        $rsf = new RouteServerFilter();
        $rsf->customer_id = $user->custid;
        $rsf->order_by = 0;
        $rsf->save();

        // Can create RsFilter for own customer
        $this->actingAs( $user )
            ->delete( 'rs-filter/delete/' . $rsf->id )
            ->assertStatus( 403 )
            ->assertSee( "This action is unauthorized" );

        $rsf->delete();
    }
}