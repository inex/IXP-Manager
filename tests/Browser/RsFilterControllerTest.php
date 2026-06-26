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

namespace Tests\Browser;

use IXP\Models\Customer;
use IXP\Models\RouteServerFilter;
use IXP\Models\RouteServerFilterProd;
use IXP\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Trait\ModifiesEnv;

class RsFilterControllerTest extends DuskTestCase
{
    use ModifiesEnv;

    #[\Override]
    public function setUp(): void
    {
        $this->overrideEnv( [ 'IXP_FE_FRONTEND_DISABLED_RS_FILTERS' => 'false' ] );
        $this->awaitArtisanEnvReload();
        parent::setUp();
    }

    #[\Override]
    public function tearDown(): void
    {
        $user = User::whereUsername( 'imcustadmin' )->firstOrFail();
        $user->customer->routeServerFilters()->delete();
        $user->customer->routeServerFiltersInProduction()->delete();
        parent::tearDown();
    }

    public function testSuperUser()
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visit( '/admin/rs-filters/list-customers' )
                ->assertSee( 'Route Server Filtering :: Customers with Filters' );

            // can manage customers rs-filters
            $user = User::whereUsername( 'imcustadmin' )->firstOrFail();
            $customer = $user->customer;

            $this->testRsFiltersAsCustomer($browser, $customer);
        } );
    }

    public function testCustAdmin()
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' );

            // admin middleware, not policy unfortunately.
            $browser->visit( '/admin/rs-filters/list-customers' )
                ->assertSee( 'Insufficient permissions' );


            // denied access to other customers resources
            $differentUser = User::whereUsername( 'hecustadmin' )->firstOrFail();

            // Can't see other customers filters. CustomerPolicy::listRsFilters
            $browser->visit( '/rs-filtering/' . $differentUser->custid )
                ->assertSee( '403' )
                ->assertSee( "THIS ACTION IS UNAUTHORIZED" );

            // Can't visit create for other users. CustomerPolicy::listRsFilters
            $browser->visit( '/rs-filter/create/' . $differentUser->custid )
                ->assertSee( '403' )
                ->assertSee( "THIS ACTION IS UNAUTHORIZED" );

            // can manage own customers rs-filters
            $user = User::whereUsername( 'imcustadmin' )->firstOrFail();
            $customer = $user->customer;

            $this->testRsFiltersAsCustomer($browser, $customer);
        } );
    }


    private function testRsFiltersAsCustomer(Browser $browser, Customer $customer): void
    {
        // Can see own RsFilters. CustomerPolicy::listRsFilters
        $browser->visit( '/rs-filtering/' . $customer->id )
            ->assertSee( "Route Server Filtering" );

        $this->assertCount( 0, RouteServerFilter::whereCustomerId( $customer->id )->get() );
        $this->assertCount( 0, RouteServerFilterProd::whereCustomerId( $customer->id )->get() );

        /**
         * Test creating a RS Filter
         */
        // Create an RS Filter for our customer. CustomerPolicy::createRsFilter
        $browser->visit( '/rs-filter/create/' . $customer->id )
            ->press( 'Create' )
            ->waitForLocation( '/rs-filtering/' . $customer->id )
            ->assertSee( "Route Server Filter created" )
            ->assertSee( "Advertise As Is" )
            ->assertSee( "Your filters are not in sync with our production configuration" );

        // Assert staged rule table has 1 row
        $this->assertCount( 1, $browser->elements( "#staged-table-list tbody tr" ) );

        /**
         * Test reverting a staged change
         */
        // Click Revert on the warning box, and confirm. No more staged changes, nothing in production.
        // CustomerPolicy::revertRsFilters
        $browser->click( "#form-revert #submit-revert" )
            ->waitForText( 'Are you sure you want to revert your changes?' )
            ->click( ".submit-revert-confirm" )
            ->waitForLocation( "/rs-filtering/" . $customer->id )
            ->assertSee( "Staged changes reverted." )
            ->assertSee( "You have no filters in production." );

        $this->assertCount( 0, $browser->elements( "#staged-table-list tbody tr" ) );

        /**
         * Test committing a staged change to production
         */
        // Go to Create page, create filter. CustomerPolicy::createRsFilter
        $browser->visit( '/rs-filter/create/' . $customer->id )
            ->press( 'Create' )
            ->waitForLocation( '/rs-filtering/' . $customer->id )
            ->assertSee( "Route Server Filter created" )
            ->assertSee( "Advertise As Is" )
            ->assertSee( "Your filters are not in sync with our production configuration" );

        // One row in staged rules table
        $this->assertCount( 1, $browser->elements( "#staged-table-list tbody tr" ) );

        // Click Commit on the warning box, and confirm. No more staged changes, 1 rule in production.
        // CustomerPolicy::commitRsFilter
        $browser->press( "#form-commit #submit-commit" )
            ->waitForText( 'Are you sure you want to commit your changes to production?' )
            ->click( ".submit-commit-confirm" )
            ->waitForLocation( "/rs-filtering/" . $customer->id )
            ->assertSee( "Staged changes commited. There is no information available as to how often the route servers are updated." )
            ->assertSee( "Your filters are in sync with our production configuration." );

        $this->assertCount( 0, $browser->elements( "#staged-table-list tbody tr" ) );
        $this->assertCount( 1, $browser->elements( "#production-table-list tbody tr" ) );

        $this->assertStagedMatchesProduction($customer);

        /**
         * Test deleting an RS filter
         */
        // Get that filter we just created, and click delete button. Confirm delete. RouteServerFilterPolicy::delete
        $rsFilter = RouteServerFilter::whereCustomerId( $customer->id )->orderBy( 'id', 'desc' )->first();
        $browser->click( '#delete-rsf-' . $rsFilter->id )
            ->waitForText( "Do you want to delete this route server filter?" )
            ->press( ".delete-rsf-confirm" )
            ->waitForLocation( "/rs-filtering/" . $customer->id )
            ->assertSee( "Route server filter deleted." )
            ->assertSee( "Your filters are not in sync with our production configuration. You can continue editing or:" )
            ->assertSee( "Commit now to remove all filters from production." );
        $this->assertNull( RouteServerFilter::whereId( $rsFilter->id )->first() );

        /**
         * Commit the delete, now no staged or production rules
         */
        // Commit changes, confirm action - now there's no staged or production rules. CustomerPolicy::commitRsFilters
        $browser->press( "#form-commit #submit-commit" )
            ->waitForText( 'Are you sure you want to commit your changes to production?' )
            ->click( ".submit-commit-confirm" )
            ->waitForLocation( "/rs-filtering/" . $customer->id )
            ->assertSee( "Staged changes commited. There is no information available as to how often the route servers are updated." )
            ->assertSee( "You have no filters in production." )
            ->assertSee( "No route server filters have been defined." );

        $this->assertCount( 0, $browser->elements( "#staged-table-list tbody tr" ) );
        $this->assertCount( 0, $browser->elements( "#production-table-list tbody tr" ) );

        $this->assertStagedMatchesProduction($customer);

        /**
         * Create RS filter and edit it
         */
        $browser->visit( '/rs-filter/create/' . $customer->id )
            ->assertSee( 'Create Route Server Filter' )
            ->press( 'Create' )
            ->waitForLocation( '/rs-filtering/' . $customer->id )
            ->assertSee( "Route Server Filter created" )
            ->assertSee( "Advertise As Is" )
            ->assertSee( "Your filters are not in sync with our production configuration" );

        // Get that filter we just created, and click the edit button
        $rsFilter = RouteServerFilter::whereCustomerId( $customer->id )->orderBy( 'id', 'desc' )->first();
        $this->assertEquals( RouteServerFilter::AS_IS, $rsFilter->action_receive );
        $this->assertEquals( RouteServerFilter::AS_IS, $rsFilter->action_advertise );

        // RouteServerFilterPolicy::edit
        $browser->click( '#edit-rsf-' . $rsFilter->id )
            ->waitForLocation( "/rs-filter/edit/" . $rsFilter->id )
            ->assertSee( "Edit Route Server Filter" )
            ->select( 'action_advertise', RouteServerFilter::PREPEND_ONCE )
            ->select( 'action_receive', RouteServerFilter::PREPEND_ONCE )
            ->press( "Save Changes" )
            ->waitForLocation( "/rs-filtering/" . $customer->id )
            ->assertSee( "Route Server Filter updated" );

        $rsFilter->refresh();
        $this->assertEquals( RouteServerFilter::PREPEND_ONCE, $rsFilter->action_receive );
        $this->assertEquals( RouteServerFilter::PREPEND_ONCE, $rsFilter->action_advertise );

        // View RSF, and can return to list. RouteServerFilterPolicy::view.
        $browser->click( '#view-rsf-' . $rsFilter->id )
            ->waitForLocation( "/rs-filter/view/" . $rsFilter->id )
            ->assertSee( "Route Server Filter / " . $rsFilter->id )
            ->element( "#list-customer-rsf" )
            ->click();
        $browser
            ->waitForLocation( "/rs-filtering/" . $customer->id )
            ->assertSee( "Route Server Filtering" );

        // View RSF, and can go to create form. Already tested policy, just testing nav.
        $browser->visit( "/rs-filter/view/" . $rsFilter->id )
            ->element( "#create-rsf" )
            ->click();
        $browser
            ->waitForLocation( "/rs-filter/create/" . $customer->id )
            ->assertSee( 'Create Route Server Filter' );

        // View RSF, and can go to edit form. Already tested policy, just testing nav.
        $browser->visit( "/rs-filter/view/" . $rsFilter->id )
            ->element( "#edit-rsf" )
            ->click();
        $browser
            ->waitForLocation( "/rs-filter/edit/" . $rsFilter->id )
            ->assertSee( 'Edit Route Server Filter' );


        // Create another RSF
        $browser->visit( "/rs-filter/create/" . $customer->id )
            ->assertSee( 'Create Route Server Filter' )
            ->select( "peer_id", "PCH DNS" )
            ->select( "protocol", "4" )
            ->select( "vlan_id", "1" )
            ->select( "action_advertise", "PREPEND_THRICE" )
            ->select( "action_receive", "PREPEND_THRICE" )
            ->press( 'Create' )
            ->waitForLocation( '/rs-filtering/' . $customer->id )
            ->assertSee( "Route Server Filter created" );

        // Keep order of route server filters 'before', and swap array keys as we change order to test
        // change order
        $before = RouteServerFilter::whereCustomerId( $customer->id )->get()->all();
        $this->assertEquals( 1, $before[ 0 ]->order_by );
        $this->assertEquals( 2, $before[ 1 ]->order_by );

        // Top and bottom elements are disabled
        $this->assertTrue( in_array( "disabled", explode( " ", $browser->element( "#change-rsf-order-up-" . $before[ 0 ]->id )->getAttribute( "class" ) ) ) );
        $this->assertTrue( in_array( "disabled", explode( " ", $browser->element( "#change-rsf-order-down-" . $before[ 1 ]->id )->getAttribute( "class" ) ) ) );

        // Move RSF down. Essentially swap the two RSF's.
        $browser->click( "#change-rsf-order-down-" . $rsFilter->id )
            ->waitForLocation( '/rs-filtering/' . $customer->id )
            ->assertSee( "Route server filter moved down" );

        $after = RouteServerFilter::whereCustomerId( $customer->id )->get()->all();
        $this->assertEquals( $before[ 0 ]->id, $after[ 1 ]->id );
        $this->assertEquals( 2, $after[ 1 ]->order_by );
        $this->assertEquals( $before[ 1 ]->id, $after[ 0 ]->id );
        $this->assertEquals( 1, $after[ 0 ]->order_by );

        // Top and bottom elements are disabled
        $this->assertTrue( in_array( "disabled", explode( " ", $browser->element( "#change-rsf-order-up-" . $before[ 1 ]->id )->getAttribute( "class" ) ) ) );
        $this->assertTrue( in_array( "disabled", explode( " ", $browser->element( "#change-rsf-order-down-" . $before[ 0 ]->id )->getAttribute( "class" ) ) ) );

        // Move RSF back up. Essentially swap the two RSF's.
        $browser->click( "#change-rsf-order-up-" . $rsFilter->id )
            ->waitForLocation( '/rs-filtering/' . $customer->id )
            ->assertSee( "Route server filter moved up" );

        $after = RouteServerFilter::whereCustomerId( $customer->id )->get()->all();
        $this->assertEquals( $before[ 0 ]->id, $after[ 0 ]->id );
        $this->assertEquals( 1, $after[ 0 ]->order_by );
        $this->assertEquals( $before[ 1 ]->id, $after[ 1 ]->id );
        $this->assertEquals( 2, $after[ 1 ]->order_by );

        // Top and bottom elements are disabled
        $this->assertTrue( in_array( "disabled", explode( " ", $browser->element( "#change-rsf-order-up-" . $before[ 0 ]->id )->getAttribute( "class" ) ) ) );
        $this->assertTrue( in_array( "disabled", explode( " ", $browser->element( "#change-rsf-order-down-" . $before[ 1 ]->id )->getAttribute( "class" ) ) ) );

        $rsFilter->refresh();
        $this->assertEquals( 1, $rsFilter->enabled );

        // Toggle a RSF to disable
        $browser->click( "#toggle-rsf-" . $rsFilter->id )
            ->waitForLocation( '/rs-filtering/' . $customer->id )
            ->assertSee( "Route server filter disabled" );

        $rsFilter->refresh();
        $this->assertEquals( 0, $rsFilter->enabled );

        $browser->press( "#form-commit #submit-commit" )
            ->waitForText( 'Are you sure you want to commit your changes to production?' )
            ->click( ".submit-commit-confirm" )
            ->waitForLocation( "/rs-filtering/" . $customer->id )
            ->assertSee( "Staged changes commited. There is no information available as to how often the route servers are updated." )
            ->assertSee( "Your filters are in sync with our production configuration." );

        $this->assertStagedMatchesProduction($customer);

    }

    private function assertStagedMatchesProduction(Customer $customer): void
    {
        $listStaged = RouteServerFilter::whereCustomerId($customer->id)->get();
        $listProd = RouteServerFilterProd::whereCustomerId($customer->id)->get();
        $this->assertCount(count($listStaged), $listProd);

        for ($i = 0; $i < count($listProd); $i++) {
            $this->assertEquals($listStaged[$i]->customer_id, $listProd[$i]->customer_id);
            $this->assertEquals($listStaged[$i]->peer_id, $listProd[$i]->peer_id);
            $this->assertEquals($listStaged[$i]->vlan_id, $listProd[$i]->vlan_id);
            $this->assertEquals($listStaged[$i]->received_prefix, $listProd[$i]->received_prefix);
            $this->assertEquals($listStaged[$i]->advertised_prefix, $listProd[$i]->advertised_prefix);
            $this->assertEquals($listStaged[$i]->protocol, $listProd[$i]->protocol);
            $this->assertEquals($listStaged[$i]->action_advertise, $listProd[$i]->action_advertise);
            $this->assertEquals($listStaged[$i]->action_receive, $listProd[$i]->action_receive);
            $this->assertEquals($listStaged[$i]->enabled, $listProd[$i]->enabled);
            $this->assertEquals($listStaged[$i]->order_by, $listProd[$i]->order_by);
        }
    }

    public function testCustUser()
    {
        $this->browse( function( Browser $browser ) {
            // Create an RSF as custadmin so we have something to see
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' );
            $customer = User::where( 'username', 'imcustadmin' )->first()->customer;
            // Create another RSF
            $browser->visit( "/rs-filter/create/" . $customer->id )
                ->assertSee( 'Create Route Server Filter' )
                ->select( "peer_id", "PCH DNS" )
                ->select( "protocol", "4" )
                ->select( "vlan_id", "1" )
                ->select( "action_advertise", "PREPEND_THRICE" )
                ->select( "action_receive", "PREPEND_THRICE" )
                ->press( 'Create' )
                ->waitForLocation( '/rs-filtering/' . $customer->id )
                ->assertSee( "Route Server Filter created" );

            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'imcustuser' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' );

            // admin middleware, not policy unfortunately.
            $browser->visit( '/admin/rs-filters/list-customers' )
                ->assertSee( 'Insufficient permissions' );

            // denied access to other customers resources
            $differentUser = User::whereUsername( 'hecustuser' )->firstOrFail();
            // Can't see other customers filters. CustomerPolicy::listRsFilters
            $browser->visit( '/rs-filtering/' . $differentUser->custid )
                ->assertSee( '403' )
                ->assertSee( "THIS ACTION IS UNAUTHORIZED" );
            // Can't visit create for other users. CustomerPolicy::listRsFilters
            $browser->visit( '/rs-filter/create/' . $differentUser->custid )
                ->assertSee( '403' )
                ->assertSee( "THIS ACTION IS UNAUTHORIZED" );

            // read only access to own customers rs-filters
            $user = User::whereUsername( 'imcustuser' )->firstOrFail();
            $customer = $user->customer;

            // Can see own RsFilters. CustomerPolicy::listRsFilters
            $browser->visit( '/rs-filtering/' . $customer->id )
                ->assertSee( "Route Server Filtering" );

            // Assert staged rule table has 1 row
            $this->assertCount( 1, $browser->elements( "#staged-table-list tbody tr" ) );

            /**
             * Test creating a RS Filter is forbidden
             */
            // Create an RS Filter for our customer. CustomerPolicy::createRsFilter
            $browser->visit( '/rs-filter/create/' . $customer->id )
                ->assertSee( '403' )
                ->assertSee( "THIS ACTION IS UNAUTHORIZED" )
                ->visit( "/rs-filtering/" . $customer->id );

            $rsFilter = $customer->routeServerFilters()->first();

            // View RSF, and can return to list. RouteServerFilterPolicy::view.
            $browser->click( '#view-rsf-' . $rsFilter->id )
                ->waitForLocation( "/rs-filter/view/" . $rsFilter->id )
                ->assertSee( "Route Server Filter / " . $rsFilter->id )
                ->element( "#list-customer-rsf" )
                ->click();
            $browser
                ->waitForLocation( "/rs-filtering/" . $customer->id )
                ->assertSee( "Route Server Filtering" );

        } );
    }
}