<?php

namespace Tests\Browser;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use IXP\Models\CustomerEquipment;
use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Customer Equipment Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerEquipmentControllerTest extends DuskTestCase
{
    /**
     * @throws
     */
    public function tearDown(): void
    {
        foreach( [ 'Colocated Equipment #1', 'Colocated Equipment #2' ] as $name ) {
            if( $c = CustomerEquipment::whereName( $name )->first() ) {
                $c->delete();
            }
        }

        parent::tearDown();
    }

    /**
     * Customer Equipment list, add, edit, remove test
     *
     * @return void
     * @throws \Throwable
     */
    public function testColocatedEquipment(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visit( route( 'cust-kit@list' ) )
                ->assertSee( 'Colocated Equipment' );

            $browser->visit( route( 'cust-kit@create' ) )
                ->assertSee( 'Colocated Equipment / Create Colocated Equipment' );

            // 1. test add empty inputs
            $browser->press( 'Create' )
                ->waitForLocation( route( 'cust-kit@create' ) )
                ->assertSee( "The name field is required." )
                ->assertSee( "The custid field is required." )
                ->assertSee( "The cabinetid field is required." );

            // 2. test add
            $browser->type( 'name', 'Colocated Equipment #1' )
                ->select( 'custid', 1 )
                ->select( 'cabinetid', 1 )
                ->type( 'descr', 'Test Description' );

            $browser->press( 'Create' )
                ->waitForLocation( route( 'cust-kit@list' ) )
                ->assertSee( "Colocated Equipment created." );

            $colocatedEquipment = CustomerEquipment::whereName( 'Colocated Equipment #1' )->first();

            // 3. test added data in database against expected values
            $this->assertInstanceOf( CustomerEquipment::class, $colocatedEquipment );

            $this->assertEquals( 'Colocated Equipment #1', $colocatedEquipment->name );
            $this->assertEquals( '1', $colocatedEquipment->custid );
            $this->assertEquals( '1', $colocatedEquipment->cabinetid );
            $this->assertEquals( 'Test Description', $colocatedEquipment->descr );

            // 4. browse to edit infrastructure object:
            $browser->click( '#e2f-list-edit-' . $colocatedEquipment->id )
                ->waitForText( 'Colocated Equipment / Edit Colocated Equipment' );

            // 5. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'name', 'Colocated Equipment #1' )
                ->assertSelected( 'custid', 1 )
                ->assertSelected( 'cabinetid', 1 )
                ->assertInputValue( 'descr', 'Test Description' );

            // 6. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'custid', '2' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( route( 'cust-kit@list' ) )
                ->assertSee( "Colocated Equipment updated" );


            // 7. repeat database load and database object check for new values (repeat 2)
            $colocatedEquipment->refresh();

            $this->assertEquals( 'Colocated Equipment #1', $colocatedEquipment->name );
            $this->assertEquals( '2', $colocatedEquipment->custid );
            $this->assertEquals( '1', $colocatedEquipment->cabinetid );
            $this->assertEquals( 'Test Description', $colocatedEquipment->descr );


            // 8. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( route( 'cust-kit@edit', $colocatedEquipment->id ) )
                ->waitForText( 'Colocated Equipment / Edit Colocated Equipment' );

            $browser->assertInputValue( 'name', 'Colocated Equipment #1' )
                ->assertSelected( 'custid', 2 )
                ->assertSelected( 'cabinetid', 1 )
                ->assertInputValue( 'descr', 'Test Description' );

            // 9. submit with no changes and verify no changes in database
            $browser->press( 'Save Changes' )
                ->waitForLocation( route( 'cust-kit@list' ) );


            // 10. repeat database load and database object check for new values (repeat 2)
            $colocatedEquipment->refresh();

            $this->assertEquals( 'Colocated Equipment #1', $colocatedEquipment->name );
            $this->assertEquals( '2', $colocatedEquipment->custid );
            $this->assertEquals( '1', $colocatedEquipment->cabinetid );
            $this->assertEquals( 'Test Description', $colocatedEquipment->descr );

            // 11. edit object
            $browser->visit( route( 'cust-kit@edit', $colocatedEquipment->id ) )
                ->assertSee( 'Colocated Equipment / Edit Colocated Equipment' );

            $browser->type( 'name', 'Colocated Equipment #2' )
                ->select( 'custid', 3 )
                ->type( 'descr', 'Test Description new' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( route( 'cust-kit@list' ) );


            // 12. verify object values
            $colocatedEquipment->refresh();

            $this->assertEquals( 'Colocated Equipment #2', $colocatedEquipment->name );
            $this->assertEquals( '3', $colocatedEquipment->custid );
            $this->assertEquals( '1', $colocatedEquipment->cabinetid );
            $this->assertEquals( 'Test Description new', $colocatedEquipment->descr );

            // 13. delete the router in the UI and verify via success message text and location
            $browser->visit( route( 'cust-kit@list' ) )
                ->click( '#e2f-list-delete-' . $colocatedEquipment->id )
                ->waitForText( 'Do you really want to delete this colocated equipment?' )
                ->press( 'Delete' );

            $browser->waitForText( 'Colocated Equipment deleted.' );

            // 14. do a D2EM findOneBy and verify false/null
            $this->assertTrue( CustomerEquipment::whereName( 'Colocated Equipment #2' )->doesntExist() );
        } );
    }
}