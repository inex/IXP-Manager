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

use IXP\Models\IrrdbConfig;
use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * IRRDB Config Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Laszlo Kiss <laszlo@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IrrdbConfigControllerTest extends DuskTestCase
{
    /**
     * @throws
     */
    public function tearDown(): void
    {
        foreach( [ 'TEST1', 'TEST1,TEST2' ] as $source ) {
            if( $c = IrrdbConfig::where( 'source', $source )->first() ) {
                $c->delete();
            }
        }

        parent::tearDown();
    }

    /**
     * IRRDB Config list, add, edit, remove test
     *
     * @return void
     * @throws \Throwable
     */
    public function testIRRDBConfig(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin' );

            $browser->visit( '/irrdb-config/list' )
                ->assertSee( 'IRRDB Sources' );

            $browser->visit( '/irrdb-config/create' )
                ->assertSee( 'IRRDB Sources / Create IRRDB Source' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            // 1. test add empty inputs
            $browser->press( 'Create' )
                ->waitForLocation( '/irrdb-config/create' )
                ->assertSee( "The host field is required." )
                ->assertSee( "The source field is required." );

            // 2. test add
            $browser->type( 'host', 'whois.radb.net' )
                ->type( 'source', 'TEST1' )
                ->type( 'notes', 'TEST1 Query from RADB Database' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Create' )
                ->waitForLocation( '/irrdb-config/list' )
                ->assertSee( "IRRDB Source created." );

            $irrdbConfig = IrrdbConfig::where( 'source', 'TEST1' )->first();

            // 3. test added data in database against expected values
            $this->assertInstanceOf( IrrdbConfig::class, $irrdbConfig );

            $this->assertEquals( 'whois.radb.net', $irrdbConfig->host );
            $this->assertEquals( 'TEST1', $irrdbConfig->source );
            $this->assertEquals( 'TEST1 Query from RADB Database', $irrdbConfig->notes );

            // 4. browse to edit infrastructure object:
            $browser->click( '#e2f-list-edit-' . $irrdbConfig->id )
                ->waitForText( 'IRRDB Sources / Edit IRRDB Source' );

            // 5. test that form contains settings as above using assertChecked(), assertNotChecked(), assertSelected(), assertInputValue, ...
            $browser->assertInputValue( 'host', 'whois.radb.net' )
                ->assertInputValue( 'source', 'TEST1' )
                ->assertInputValue( 'notes', 'TEST1 Query from RADB Database' );
            
            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            // 6. submit with no changes and verify no changes in database
            $browser->press( 'Save Changes' )
                ->waitForLocation( '/irrdb-config/list' )
                ->assertSee('IRRDB Source updated.');


            // 7. repeat database load and database object check for new values (repeat 2)
            $irrdbConfig->refresh();

            $this->assertEquals( 'whois.radb.net', $irrdbConfig->host );
            $this->assertEquals( 'TEST1', $irrdbConfig->source );
            $this->assertEquals( 'TEST1 Query from RADB Database', $irrdbConfig->notes );

            // 8. edit object
            $browser->visit( '/irrdb-config/edit/' . $irrdbConfig->id )
                ->assertSee( 'IRRDB Sources / Edit IRRDB Source' );

            $browser->type( 'host', 'whois.radb.net' )
                ->type( 'source', 'TEST1,TEST2' )
                ->type( 'notes', 'TEST1+TEST2 Query from RADB Database' );

            $browser->driver->executeScript( 'window.scrollTo(0, 3000);' );

            $browser->press( 'Save Changes' )
                ->waitForLocation( '/irrdb-config/list' )
                ->assertSee('IRRDB Source updated.');


            // 12. verify object values
            $irrdbConfig->refresh();

            $this->assertEquals( 'whois.radb.net', $irrdbConfig->host );
            $this->assertEquals( 'TEST1,TEST2', $irrdbConfig->source );
            $this->assertEquals( 'TEST1+TEST2 Query from RADB Database', $irrdbConfig->notes );

            // 13. delete the router in the UI and verify via success message text and location
            $browser->visit( '/irrdb-config/list/' )
                ->click( '#e2f-list-delete-' . $irrdbConfig->id )
                ->waitForText( 'Do you really want to delete this an IRRDB Sources?' )
                ->press( 'Delete' );

            $browser->waitForText( 'IRRDB Source deleted.' );

            // 14. do a D2EM findOneBy and verify false/null
            $this->assertTrue( IrrdbConfig::where( 'source', 'TEST1,TEST2' )->doesntExist() );
        } );
    }
}