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

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Support\Carbon;
use IXP\Models\Cabinet;
use IXP\Models\PatchPanel;
use IXP\Models\PatchPanelPort;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PatchPanelControllerTest extends DuskTestCase
{

    /**
     * Test Patch Panel create/list/edit
     *
     * @return void
     */
    public function testAddRemoveAndEdit(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            // Go to patch panel list page
            $browser->visit( route( "patch-panel@list" ) )
                ->waitForText( "Patch Panels (Active Only)" )
                ->assertSee( "Show Inactive" )
            ;

            // No patch panels in test DB so far
            $initialRows = $browser->elements( '#patch-panel-list tbody tr' );
            $this->assertDatabaseCount(PatchPanel::class, 0);
            $this->assertCount( 0, $initialRows );

            $this->assertDatabaseCount(Cabinet::class, 1);

            // Page won't have any patch panels in table, will warn as much and have a create link
            $browser->assertSee("No active patch panels exist.")
                ->assertSee("Create one...");

            // Go to patch panel create page
            $browser
                ->clickLink("Create one...")
                ->waitForLocation( route("patch-panel@create" ) )
                ->assertSee( "Patch Panels / Create" )
                ->pause(200)
            ;

            $today = Carbon::today()->format('Y-m-d');

            // JS magic afoot on the create page... Test for the side effect. Then reset everything and test how we'd like.
            $this->assertEquals(
                "PP Fac1 Rack1Test-F1-R1-A",
                $browser
                    ->type("name", "PP Fac1 Rack1")
                    ->type("colo_reference", "Test-F1-R1-A")
                    ->inputValue("colo_reference")
            );

            // Reset that so we can proceed with the test
            $browser->clear("colo_reference")
                ->clear("name");

            // Enter colo_reference before name so we avoid the messing around, check they are as we expect
            $browser
                ->type("colo_reference", "Test-F1-R1-A")
                ->type("name", "PP Fac1 Rack1")
            ;
            $this->assertEquals("Test-F1-R1-A", $browser->inputValue("colo_reference"));
            $this->assertEquals("PP Fac1 Rack1", $browser->inputValue("name"));

            // Fill out rest of Create Patch Panel form, and submit.
            $browser->select("cabinet_id", 1)
                ->select("mounted_at", PatchPanel::MOUNTED_AT_FRONT)
                ->type("u_position", "2")
                ->select("cable_type", PatchPanel::CABLE_TYPE_SMF)
                ->select("connector_type", PatchPanel::CONNECTOR_TYPE_LC)
                ->type("numberOfPorts", '24')
                ->type("port_prefix", "test_prefix")
                ->select("chargeable", PatchPanelPort::CHARGEABLE_YES)
                ->press("Today")
                ->select("colo_pp_type", PatchPanel::COLO_PP_TYPE_SIMPLEX)
                ->assertChecked('active')
                ->press("Create")
                ->waitForText("Patch Panel Port - PP Fac1 Rack1")
            ;

            // Test particulars in the database
            $pp = PatchPanel::orderBy('id', 'desc')->limit(1)->first();
            $this->assertEquals("PP Fac1 Rack1", $pp->name);
            $this->assertEquals("Test-F1-R1-A", $pp->colo_reference);
            $this->assertEquals(1, $pp->cabinet_id);
            $this->assertEquals(PatchPanel::MOUNTED_AT_FRONT, $pp->mounted_at);
            $this->assertEquals(2, $pp->u_position);
            $this->assertEquals(PatchPanel::CABLE_TYPE_SMF, $pp->cable_type);
            $this->assertEquals(PatchPanel::CONNECTOR_TYPE_LC, $pp->connector_type);
            $this->assertEquals(24, $pp->patchPanelPorts()->count());
            $this->assertEquals("test_prefix", $pp->port_prefix);
            $this->assertEquals(PatchPanelPort::CHARGEABLE_YES, $pp->chargeable);
            $this->assertEquals($today, $pp->installation_date);
            $this->assertEquals(PatchPanel::COLO_PP_TYPE_SIMPLEX, $pp->colo_pp_type);
            $this->assertEquals(1, $pp->active);


            // Now go to Patch Panel edit, mark it inactive.
            $browser
                ->assertRouteIs('patch-panel-port@list-for-patch-panel', ['pp' => $pp])
                ->visit(route('patch-panel@edit' , [ 'pp' => $pp->id ]))
                ->assertSee("Patch Panels / Edit: " . $pp->name)
                ->assertChecked("active")
                ->uncheck("active")
                ->press("Save Changes");

            // Once redirected, our Patch Panel name has [Inactive] after it.
            $browser
                ->waitForRoute('patch-panel-port@list-for-patch-panel', ['pp' => $pp])
                ->assertSee( "Patch Panel Port" )
                ->assertSee( " - " .$pp->name . " [Inactive]" )
                ->assertSee( "Ports for " . $pp->name . " (Colo Ref: " . $pp->colo_reference . ")" )
            ;

            // Go and change the state of a Patch Panel Port to Connected. This is not available, so
            // changing Patch Panel active field will not be allowed.
            $ppp1 = $pp->patchPanelPorts()->first();

            $browser->visit(route("patch-panel-port@edit", [ 'ppp' => $ppp1->id ]))
                ->select("state", PatchPanelPort::STATE_CONNECTED)
                ->press("Save Changes")
                ->waitForRoute('patch-panel-port@list-for-patch-panel', ['pp' => $pp])
            ;
            // Frontend echoes the change we just made
            $stateText = $browser->text( '#table-ppp tbody tr:first-child td:nth-child(7) span' );
            $this->assertEquals(PatchPanelPort::$STATES[PatchPanelPort::STATE_CONNECTED], $stateText);

            // Try to make the Patch Panel active - we won't be allowed.
            $browser
                ->visit(route('patch-panel@edit' , [ 'pp' => $pp->id ]))
                ->assertSee("Patch Panels / Edit: " . $pp->name)
                ->assertNotChecked("active")
                ->check("active")
                ->press("Save Changes")
                ->waitForRoute('patch-panel@edit' , [ 'pp' => $pp->id ])
                ->assertSee( 'To make a patch panel active, all ports must be available for use.' )
                ->assertNotChecked("active")
            ;

            // Go back and change the port state to Available.
            $browser->visit(route("patch-panel-port@edit", [ 'ppp' => $ppp1->id ]))
                ->select("state", PatchPanelPort::STATE_AVAILABLE)
                ->press("Save Changes")
                ->waitForRoute('patch-panel-port@list-for-patch-panel', ['pp' => $pp])
            ;
            // Frontend echoes the change we just made
            $stateText = $browser->text( '#table-ppp tbody tr:first-child td:nth-child(7) span' );
            $this->assertEquals(PatchPanelPort::$STATES[PatchPanelPort::STATE_AVAILABLE], $stateText);

            // And now we can reactivate the Patch Panel.
            $browser
                ->visit( route( 'patch-panel@edit' , [ 'pp' => $pp->id ] ) )
                ->assertSee( "Patch Panels / Edit: " . $pp->name )
                ->assertNotChecked( "active" )
                ->check( "active" )
                ->press( "Save Changes" )
                ->waitForRoute( 'patch-panel-port@list-for-patch-panel', [ 'pp' => $pp ] )
                ->assertSee( "Patch Panel Port" )
                ->assertSee( " - " .$pp->name )
                ->assertDontSee( "[Inactive]" )
            ;

            //$this->assertDatabaseCount(PatchPanel::class, 1);

        } );
    }
}