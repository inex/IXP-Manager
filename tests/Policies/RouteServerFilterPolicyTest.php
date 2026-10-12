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

namespace Tests\Policies;

use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\RouteServerFilter;
use IXP\Models\User;
use Tests\TestCase;

class RouteServerFilterPolicyTest extends TestCase
{
    public function testRsFilterPolicy()
    {
        $superUser = $this->getSuperUser();
        $custAdmin = $this->getCustAdminUser();
        $custUser = $this->getCustUser();

        $rsfCust1 = new RouteServerFilter();
        $rsfCust1->customer_id = $custAdmin->custid;
        $rsfCust1->save();

        $anotherUser = $this->getCustAdminUser( 'hecustadmin' );
        $rsfCust2 = new RouteServerFilter();
        $rsfCust2->customer_id = $anotherUser->custid;
        $rsfCust2->save();

        $this->assertTrue( $superUser->can( 'checkListCustomers', [ RouteServerFilter::class ] ) );
        $this->assertFalse( $custAdmin->can( 'checkListCustomers', [ RouteServerFilter::class ] ) );
        $this->assertFalse( $custUser->can( 'checkListCustomers', [ RouteServerFilter::class ] ) );

        $this->assertTrue( $superUser->can( 'update', $rsfCust1 ) );
        $this->assertTrue( $custAdmin->can( 'update', $rsfCust1 ) );
        $this->assertFalse( $custUser->can( 'update', $rsfCust1 ) );
        $this->assertTrue( $superUser->can( 'update', $rsfCust2 ) );
        $this->assertFalse( $custAdmin->can( 'update', $rsfCust2 ) );
        $this->assertFalse( $custUser->can( 'update', $rsfCust2 ) );

        $this->assertTrue( $superUser->can( 'view', $rsfCust1 ) );
        $this->assertTrue( $custAdmin->can( 'view', $rsfCust1 ) );
        $this->assertTrue( $custUser->can( 'view', $rsfCust1 ) );
        $this->assertTrue( $superUser->can( 'view', $rsfCust2 ) );
        $this->assertFalse( $custAdmin->can( 'view', $rsfCust2 ) );
        $this->assertFalse( $custUser->can( 'view', $rsfCust2 ) );

        $this->assertTrue( $superUser->can( 'toggleEnable', $rsfCust1 ) );
        $this->assertTrue( $custAdmin->can( 'toggleEnable', $rsfCust1 ) );
        $this->assertFalse( $custUser->can( 'toggleEnable', $rsfCust1 ) );
        $this->assertTrue( $superUser->can( 'toggleEnable', $rsfCust2 ) );
        $this->assertFalse( $custAdmin->can( 'toggleEnable', $rsfCust2 ) );
        $this->assertFalse( $custUser->can( 'toggleEnable', $rsfCust2 ) );

        $this->assertTrue( $superUser->can( 'changeOrder', $rsfCust1 ) );
        $this->assertTrue( $custAdmin->can( 'changeOrder', $rsfCust1 ) );
        $this->assertFalse( $custUser->can('changeOrder', $rsfCust1 ) );
        $this->assertTrue( $superUser->can( 'changeOrder', $rsfCust2 ) );
        $this->assertFalse( $custAdmin->can( 'changeOrder', $rsfCust2 ) );
        $this->assertFalse( $custUser->can( 'changeOrder', $rsfCust2 ) );

        $this->assertTrue( $superUser->can( 'delete', $rsfCust1 ) );
        $this->assertTrue( $custAdmin->can( 'delete', $rsfCust1 ) );
        $this->assertFalse( $custUser->can( 'delete', $rsfCust1 ) );
        $this->assertTrue( $superUser->can( 'delete', $rsfCust2 ) );
        $this->assertFalse( $custAdmin->can( 'delete', $rsfCust2 ) );
        $this->assertFalse( $custUser->can( 'delete', $rsfCust2 ) );

        $this->assertTrue( $superUser->can( 'listRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertTrue( $custAdmin->can( 'listRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertTrue( $custUser->can( 'listRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertFalse( $custAdmin->can( 'listRsFilters', [RouteServerFilter::class, $anotherUser->customer] ) );
        $this->assertFalse( $custUser->can( 'listRsFilters', [RouteServerFilter::class, $anotherUser->customer] ) );

        $this->assertTrue( $superUser->can( 'revertRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertTrue( $custAdmin->can( 'revertRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertFalse( $custUser->can( 'revertRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertFalse( $custAdmin->can( 'revertRsFilters', [RouteServerFilter::class, $anotherUser->customer] ) );
        $this->assertFalse( $custUser->can( 'revertRsFilters', [RouteServerFilter::class, $anotherUser->customer] ) );

        $this->assertTrue( $superUser->can( 'commitRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertTrue( $custAdmin->can( 'commitRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertFalse( $custUser->can( 'commitRsFilters', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertFalse( $custAdmin->can( 'commitRsFilters', [RouteServerFilter::class, $anotherUser->customer] ) );
        $this->assertFalse( $custUser->can( 'commitRsFilters', [RouteServerFilter::class, $anotherUser->customer] ) );

        $this->assertTrue( $superUser->can( 'createRsFilter', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertTrue( $custAdmin->can( 'createRsFilter', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertFalse( $custUser->can( 'createRsFilter', [RouteServerFilter::class, $custAdmin->customer] ) );
        $this->assertFalse( $custAdmin->can( 'createRsFilter', [RouteServerFilter::class, $anotherUser->customer] ) );
        $this->assertFalse( $custUser->can( 'createRsFilter', [RouteServerFilter::class, $anotherUser->customer] ) );

        $rsfCust1->delete();
        $rsfCust2->delete();
    }

    /**
     * Test that we can't mess with filters if theres no VlanInterface with rsclient=true
     * Admin's can because of the before rule
     */
    public function testIsNotRsClient()
    {
        $customer = new Customer();
        $customer->save();

        $user = new User();
        $user->custid = $customer->id;
        $user->save();

        $c2u = new CustomerToUser();
        $c2u->customer_id = $user->custid;
        $c2u->user_id = $user->id;
        $c2u->privs = User::AUTH_CUSTADMIN;
        $c2u->save();

        $rsf = new RouteServerFilter();
        $rsf->customer_id = $user->custid;
        $rsf->save();

        $superUser = $this->getSuperUser();

        $this->assertTrue( $superUser->can( 'update', $rsf ) );
        $this->assertFalse( $user->can( 'update', $rsf ) );

        $this->assertTrue( $superUser->can( 'view', $rsf ) );
        $this->assertFalse( $user->can( 'view', $rsf ) );

        $this->assertTrue( $superUser->can( 'toggleEnable', $rsf ) );
        $this->assertFalse( $user->can( 'toggleEnable', $rsf ) );

        $this->assertTrue( $superUser->can( 'changeOrder', $rsf ) );
        $this->assertFalse( $user->can( 'changeOrder', $rsf ) );

        $this->assertTrue( $superUser->can( 'delete', $rsf ) );
        $this->assertFalse( $user->can( 'delete', $rsf ) );

        $rsf->delete();
        $c2u->delete();
        $user->delete();
        $customer->delete();
    }
}