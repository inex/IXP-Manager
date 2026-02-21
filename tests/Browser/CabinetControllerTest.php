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

use IXP\Models\Cabinet;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Cabinet Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CabinetControllerTest extends DuskTestCase
{
    /**
     * @throws
     */
    public function tearDown(): void
    {
        foreach( [ 'Cabinet Test', 'Cabinet Test2' ] as $name ) {
            if( $c = Cabinet::whereName( $name )->first() ) {
                $c->delete();
            }
        }

        parent::tearDown();
    }

    /**
     * Cabinet list, add, edit, remove test
     *
     * @return void
     * @throws \Throwable
     */
    public function testCabinet(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visit( route( 'rack@list' ) )
                ->assertSee( 'Racks' )
                ->assertSee( 'Cabinet 1' );

            $browser->visit( route( 'rack@create' ) )
                ->assertSee( 'Racks / Create Rack' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            // 1. test add empty inputs
            $browser->press( 'Create' )
                ->waitForLocation( route( 'rack@create' ) )
                ->assertSee( "The name field is required." )
                ->assertSee( "The locationid field is required." )
                ->assertSee( "The colocation field is required." )
                ->assertSee( "The u counts from field is required." );

            // 2. test add
            $browser->type( 'name', 'Cabinet Test' )
                ->select( 'locationid', 1 )
                ->type( 'colocation', 'test address' )
                ->type( 'type', 'test type' )
                ->type( 'height', '8' )
                ->select( 'u_counts_from', 1 )
                ->type( 'notes', 'test notes' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Create' )
                ->waitForLocation( route( 'rack@list' ) )
                ->assertSee( "Rack created." );

            $cabinet = Cabinet::whereName( 'Cabinet Test' )->first();

            // 3. test added data in database against expected values
            $this->assertInstanceOf( Cabinet::class, $cabinet );

            $this->assertEquals( 'Cabinet Test', $cabinet->name );
            $this->assertEquals( '1', $cabinet->locationid );
            $this->assertEquals( 'test address', $cabinet->colocation );
            $this->assertEquals( 'test type', $cabinet->type );
            $this->assertEquals( '8', $cabinet->height );
            $this->assertEquals( '1', $cabinet->u_counts_from );
            $this->assertEquals( 'test notes', $cabinet->notes );

            // 4. browse to edit infrastructure object:
            $browser->click( '#e2f-list-edit-' . $cabinet->id )
                ->waitForText( 'Racks / Edit Rack' );

            // 5. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'name', 'Cabinet Test' )
                ->assertSelected( 'locationid', 1 )
                ->assertInputValue( 'colocation', 'test address' )
                ->assertInputValue( 'type', 'test type' )
                ->assertInputValue( 'height', '8' )
                ->assertSelected( 'u_counts_from', 1 )
                ->assertInputValue( 'notes', 'test notes' );

            // 6. uncheck checkboxes, change selects and values, ->press('Save Changes'), assertPathIs(....)  (repeat 1)
            $browser->select( 'u_counts_from', '2' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( route( 'rack@list' ) )
                ->assertSee( "Rack updated" );


            // 7. repeat database load and database object check for new values (repeat 2)
            $cabinet->refresh();

            $this->assertEquals( 'Cabinet Test', $cabinet->name );
            $this->assertEquals( '1', $cabinet->locationid );
            $this->assertEquals( 'test address', $cabinet->colocation );
            $this->assertEquals( 'test type', $cabinet->type );
            $this->assertEquals( '8', $cabinet->height );
            $this->assertEquals( '2', $cabinet->u_counts_from );
            $this->assertEquals( 'test notes', $cabinet->notes );


            // 8. edit again and assert that all checkboxes are unchecked and assert select values are as expected
            $browser->visit( route( 'rack@edit', $cabinet->id ) )
                ->assertSee( 'Racks / Edit Rack' );

            $browser->assertInputValue( 'name', 'Cabinet Test' )
                ->assertSelected( 'locationid', 1 )
                ->assertInputValue( 'colocation', 'test address' )
                ->assertInputValue( 'type', 'test type' )
                ->assertInputValue( 'height', '8' )
                ->assertSelected( 'u_counts_from', 2 )
                ->assertInputValue( 'notes', 'test notes' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            // 9. submit with no changes and verify no changes in database
            $browser->press( 'Save Changes' )
                ->waitForLocation( route( 'rack@list' ) );


            // 10. repeat database load and database object check for new values (repeat 2)
            $cabinet->refresh();

            $this->assertEquals( 'Cabinet Test', $cabinet->name );
            $this->assertEquals( '1', $cabinet->locationid );
            $this->assertEquals( 'test address', $cabinet->colocation );
            $this->assertEquals( 'test type', $cabinet->type );
            $this->assertEquals( '8', $cabinet->height );
            $this->assertEquals( '2', $cabinet->u_counts_from );
            $this->assertEquals( 'test notes', $cabinet->notes );

            // 11. edit object
            $browser->visit( route( 'rack@edit', $cabinet->id ) )
                ->assertSee( 'Racks / Edit Rack' );

            $browser->type( 'name', 'Cabinet Test2' )
                ->type( 'colocation', 'test address2' )
                ->type( 'type', 'test type2' )
                ->type( 'height', '10' )
                ->select( 'u_counts_from', 1 )
                ->type( 'notes', 'test notes new' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( route( 'rack@list' ) );


            // 12. verify object values
            $cabinet->refresh();

            $this->assertEquals( 'Cabinet Test2', $cabinet->name );
            $this->assertEquals( '1', $cabinet->locationid );
            $this->assertEquals( 'test address2', $cabinet->colocation );
            $this->assertEquals( 'test type2', $cabinet->type );
            $this->assertEquals( '10', $cabinet->height );
            $this->assertEquals( '1', $cabinet->u_counts_from );
            $this->assertEquals( 'test notes new', $cabinet->notes );

            // 13. delete the router in the UI and verify via success message text and location
            $browser->visit( route( 'rack@list' ) )
                ->click( '#e2f-list-delete-' . $cabinet->id )
                ->waitForText( 'Do you really want to delete this rack' )
                ->press( 'Delete' );

            $browser->waitForText( 'Rack deleted.' );

            // 14. do a D2EM findOneBy and verify false/null
            $this->assertTrue( Cabinet::whereName( 'Cabinet Test2' )->doesntExist() );
        } );
    }
}