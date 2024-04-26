<?php

namespace Tests\Browser;

/*
 * Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\Vendor;
use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Vendor Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VendorControllerTest extends DuskTestCase
{
    /**
     * @throws
     */
    public function tearDown(): void
    {
        foreach( [ 'Vendor Company', 'Vendor Company2' ] as $name ) {
            if( $c = Vendor::whereName( $name )->first() ) {
                $c->delete();
            }
        }

        parent::tearDown();
    }

    /**
     * Vendor list, add, edit, remove test
     *
     * @return void
     * @throws \Throwable
     */
    public function testVendor(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->assertPathIs( '/admin' );

            $browser->visit( '/vendor/list' )
                ->assertSee( 'Vendors' );

            $browser->visit( '/vendor/create' )
                ->assertSee( 'Vendors / Create Vendor' );

            // 1. test add empty inputs
            $browser->press( 'Create' )
                ->assertPathIs( '/vendor/create' )
                ->assertSee( "The name field is required." )
                ->assertSee( "The shortname field is required." );

            // 2. test add
            $browser->type( 'name', 'Vendor Company' )
                ->type( 'shortname', 'VendorCo' )
                ->type( 'bundle_name', 'Vendor Bundle' );

            $browser->press( 'Create' )
                ->assertPathIs( '/vendor/list' )
                ->assertSee( "Vendor created." );

            $vendor = Vendor::whereName( 'Vendor Company' )->first();

            // 3. test added data in database against expected values
            $this->assertInstanceOf( Vendor::class, $vendor );

            $this->assertEquals( 'Vendor Company', $vendor->name );
            $this->assertEquals( 'VendorCo', $vendor->shortname );
            $this->assertEquals( 'Vendor Bundle', $vendor->bundle_name );

            // 4. browse to edit infrastructure object:
            $browser->click( '#e2f-list-edit-' . $vendor->id )
                ->assertSee( 'Vendors / Edit Vendor' );

            // 5. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'name', 'Vendor Company' )
                ->assertInputValue( 'shortname', 'VendorCo' )
                ->assertInputValue( 'bundle_name', 'Vendor Bundle' );

            // 6. submit with no changes and verify no changes in database
            $browser->press( 'Save Changes' )
                ->assertPathIs( '/vendor/list' );

            // 7. repeat database load and database object check for new values (repeat 2)
            $vendor->refresh();

            $this->assertEquals( 'Vendor Company', $vendor->name );
            $this->assertEquals( 'VendorCo', $vendor->shortname );
            $this->assertEquals( 'Vendor Bundle', $vendor->bundle_name );

            // 8. edit object
            $browser->visit( '/vendor/edit/' . $vendor->id )
                ->assertSee( 'Vendors / Edit Vendor' );

            $browser->type( 'name', 'Vendor Company2' )
                ->type( 'shortname', 'VendorCo2' )
                ->type( 'bundle_name', 'Vendor Bundle2' );

            $browser->press( 'Save Changes' )
                ->assertPathIs( '/vendor/list' );

            // 9. verify object values
            $vendor->refresh();

            $this->assertEquals( 'Vendor Company2', $vendor->name );
            $this->assertEquals( 'VendorCo2', $vendor->shortname );
            $this->assertEquals( 'Vendor Bundle2', $vendor->bundle_name );

            // 10. delete the router in the UI and verify via success message text and location
            $browser->visit( '/vendor/list/' )
                ->click( '#e2f-list-delete-' . $vendor->id )
                ->waitForText( 'Do you really want to delete this a vendor?' )
                ->press( 'Delete' );

            $browser->assertSee( 'Vendor deleted.' );

            // 11. do a D2EM findOneBy and verify false/null
            $this->assertTrue( Vendor::whereName( 'Vendor Company2' )->doesntExist() );
        } );
    }
}