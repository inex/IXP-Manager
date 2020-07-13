<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace Tests\Browser;

use D2EM;

use IXP\Models\SwitchPort;
use Tests\DuskTestCase;

use Laravel\Dusk\Browser;

use Entities\{
    SwitchPort              as SwitchPortEntity
};

class SwitchPortControllerTest extends DuskTestCase
{
    /**
     * Test the switch port (add, edit, delete)
     *
     * @return void
     *
     * @throws
     */
    public function testSwitchPort(): void
    {
        $this->browse( function ( Browser $browser ) {

            $browser->resize( 1600, 1200 )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->assertPathIs( '/admin' )
                ->visit( '/switch-port/list' );

            /**
             * Test view Switch information
             */
            $sps = SwitchPort::getFeList( (object)[] );

            $sp = SwitchPort::whereId( reset($sps )[ "id" ] )->get()->first();

            $this->assertInstanceOf( SwitchPort::class, $sp );

            $browser->press( "#e2f-list-view-" . $sp->id )
                ->assertSee( "Details for switch port: " . $sp->switcher->name . " :: " . $sp->name . " (DB ID: " . $sp->id . ")" );

            $browser->press( "#e2f-list-a" )
            ->assertPathIs( "/switch-port/list" );


            /**
             * Test add Switch port form
             */

            $browser->press( "#create-switch-port" )
                ->assertSee( "Create Switch Port" );

            // Fill the form with new value
            $browser->select(   'switchid', 2   )
                    ->select(   'type',     1   )
                    ->type(     'numfirst', '1' )
                    ->type(     'numports', '5' )
                    ->type(     'prefix',   'travistest%d')
                    ->click( "#generate-btn" )
                    ->click( "#btn-submit" )
                    ->assertPathIs( "/switch-port/list" )
                    ->assertSee( "Switch Port created" );

                $newSp = SwitchPort::whereName( 'travistest1' )->get()->first();

                // test added data in database against expected values
                $this->assertInstanceOf( SwitchPort::class, $newSp );

                $this->assertEquals( "travistest1",     $newSp->name );
                $this->assertEquals( 2,                 $newSp->switchid );
                $this->assertEquals( 1,                 $newSp->type );
                $this->assertEquals( true,              $newSp->active );
                $this->assertEquals( null,              $newSp->ifIndex );
                $this->assertEquals( null,              $newSp->ifName );
                $this->assertEquals( null,              $newSp->ifAlias );
                $this->assertEquals( null,              $newSp->ifHighSpeed );
                $this->assertEquals( null,              $newSp->ifMtu );
                $this->assertEquals( null,              $newSp->ifPhysAddress );
                $this->assertEquals( null,              $newSp->ifAdminStatus );
                $this->assertEquals( null,              $newSp->ifOperStatus );
                $this->assertEquals( null,              $newSp->ifLastChange );
                $this->assertEquals( null,              $newSp->lastSnmpPoll );
                $this->assertEquals( null,              $newSp->lagIfIndex );
                $this->assertEquals( null,              $newSp->mauType );
                $this->assertEquals( null,              $newSp->mauState );
                $this->assertEquals( null,              $newSp->mauAvailability );
                $this->assertEquals( null,              $newSp->mauJacktype );
                $this->assertEquals( null,              $newSp->mauAutoNegSupported );
                $this->assertEquals( null,              $newSp->mauAutoNegAdminState );



            /**
             * Test edit Switch port form
             */
                $browser->press( "#e2f-list-edit-" . $newSp->id )
                    ->assertSee( "Edit Switch" );

                // test that form is filled with all and the correct object informations
                $browser->assertSelected(   'switchid',     2 )
                        ->assertInputValue( 'name',         "travistest1" )
                        ->assertSelected(   'type',         1 )
                        ->assertChecked(    'active' );


                // submit unchanged form
                $browser->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port updated" );

                $newSp->refresh();

                $this->assertInstanceOf( SwitchPort::class, $newSp );

                $this->assertEquals( "travistest1",     $newSp->name );
                $this->assertEquals( 2,                 $newSp->switchid );
                $this->assertEquals( 1,                 $newSp->type );
                $this->assertEquals( true,              $newSp->active );
                $this->assertEquals( null,              $newSp->ifIndex );
                $this->assertEquals( null,              $newSp->ifName );
                $this->assertEquals( null,              $newSp->ifAlias );
                $this->assertEquals( null,              $newSp->ifHighSpeed );
                $this->assertEquals( null,              $newSp->ifMtu );
                $this->assertEquals( null,              $newSp->ifPhysAddress );
                $this->assertEquals( null,              $newSp->ifAdminStatus );
                $this->assertEquals( null,              $newSp->ifOperStatus );
                $this->assertEquals( null,              $newSp->ifLastChange );
                $this->assertEquals( null,              $newSp->lastSnmpPoll );
                $this->assertEquals( null,              $newSp->lagIfIndex );
                $this->assertEquals( null,              $newSp->mauType );
                $this->assertEquals( null,              $newSp->mauState );
                $this->assertEquals( null,              $newSp->mauAvailability );
                $this->assertEquals( null,              $newSp->mauJacktype );
                $this->assertEquals( null,              $newSp->mauAutoNegSupported );
                $this->assertEquals( null,              $newSp->gmauAutoNegAdminState );

                $browser->press( "#e2f-list-edit-" . $newSp->id )
                    ->assertSee( "Edit Switch" );

                // test that form is filled with all and the correct object informations
                $browser->assertSelected(   'switchid',     2 )
                        ->assertInputValue( 'name',         "travistest1" )
                        ->assertSelected(   'type',         1 )
                        ->assertChecked(    'active' );

                // Fill the form with new value
                $browser->select(   'switchid', 2 )
                        ->type(     'name',     'travistest6' )
                        ->select(   'type',     2 )
                        ->uncheck(  'active' )
                        ->press(    'Save Changes')
                        ->assertPathIs('/switch-port/list')
                        ->assertSee( "Switch Port updated" );


                $browser->press( "#e2f-list-edit-" . $newSp->id )
                        ->assertSee( "Edit Switch Port" );

                $newSp->refresh();

                $this->assertInstanceOf( SwitchPort::class, $newSp );

                $this->assertEquals( "travistest6",     $newSp->name );
                $this->assertEquals( 2,                 $newSp->switchid );
                $this->assertEquals( 2,                 $newSp->type );
                $this->assertEquals( false,             $newSp->active );
                $this->assertEquals( null,              $newSp->ifIndex );
                $this->assertEquals( null,              $newSp->ifName );
                $this->assertEquals( null,              $newSp->ifAlias );
                $this->assertEquals( null,              $newSp->ifHighSpeed );
                $this->assertEquals( null,              $newSp->ifMtu );
                $this->assertEquals( null,              $newSp->ifPhysAddress );
                $this->assertEquals( null,              $newSp->ifAdminStatus );
                $this->assertEquals( null,              $newSp->ifOperStatus );
                $this->assertEquals( null,              $newSp->ifLastChange );
                $this->assertEquals( null,              $newSp->lastSnmpPoll );
                $this->assertEquals( null,              $newSp->lagIfIndex );
                $this->assertEquals( null,              $newSp->mauType );
                $this->assertEquals( null,              $newSp->mauState );
                $this->assertEquals( null,              $newSp->mauAvailability );
                $this->assertEquals( null,              $newSp->mauJacktype );
                $this->assertEquals( null,              $newSp->mauAutoNegSupported );
                $this->assertEquals( null,              $newSp->mauAutoNegAdminState );


                // test that form is filled with all and the correct object informations
                $browser->assertSelected(   'switchid',     2 )
                        ->assertInputValue( 'name',         "travistest6" )
                        ->assertSelected(   'type',         2 )
                        ->assertNotChecked(    'active' );

                // Test the checkbox (checked)
                $browser->check(  'active' )
                    ->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port updated" );

                $newSp->refresh();

                $this->assertEquals( true,             $newSp->active );


                $browser->press( "#e2f-list-edit-" . $newSp->id )
                    ->assertSee( "Edit Switch Port" );

                // Test the checkbox (unchecked)
                $browser->uncheck(  'active' )
                    ->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port updated" );

                // refresh the object
                $newSp->refresh();

                // check that the attribute is false (unchecked checkbox)
                $this->assertEquals( false,             $newSp->active );



            $browser->press( "#e2f-list-edit-" . $newSp->id )
                ->assertSee( "Edit Switch Port" );


                // Test the select
                $browser->select(  'type', 3 )
                    ->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port updated" );

                $newSp->refresh();

                $this->assertEquals( 3,             $newSp->type );


                $browser->press( "#e2f-list-edit-" . $newSp->id )
                    ->assertSee( "Edit Switch Port" );

                // Test the select
                $browser->select(  'type', 4 )
                    ->press(    'Save Changes')
                    ->assertPathIs('/switch-port/list')
                    ->assertSee( "Switch Port updated" );

                 $newSp->refresh();

                $this->assertEquals( 4,             $newSp->type );

            /**
             * Test delete Switch port
             */
            $browser->press( "#e2f-list-delete-" . $newSp->id )
                ->waitForText( "Delete Switch Port" )
                ->press( "Delete" )
                ->assertSee( "Switch Port deleted." );
        });
    }
}