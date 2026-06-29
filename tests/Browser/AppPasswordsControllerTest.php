<?php

namespace Tests\Browser;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Illuminate\Support\Facades\Hash;
use IXP\Models\AppPassword;
use Str;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test Apikey Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class AppPasswordsControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
        if( $key = AppPassword::where( 'description','Temporary app password' ) ) {
            $key->delete();
        }

        parent::tearDown();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws
     */
    public function test(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visit( '/admin/app-password/list' )
                ->assertSee( 'Application-Specific Passwords' )
                ->assertSee( 'No app password exists.' );

            // 1. test add empty inputs - descriptino is required
            $browser->visit( '/admin/app-password/create' )
                ->assertSee( 'Create Application-Specific Password' )
                ->press( 'Create' )
                ->waitForText("The description field is required");

            $browser->type("description", "App password")
                ->press('Create')
                ->waitForLocation( '/admin/app-password/list' )
                ->assertSee( "Application-Specific Password created." )
                ->assertSee( "NB: this is the only time you will be able to see this password." );

            // 2. Fetch the app password as displayed on the page, use that to assert contents of AppPassword
            $element = $browser->element(".alert-dismissible code");
            $this->assertNotNull($element, "could not locate a code section inside an alert - did we change how we show it to the user?");

            $locatedAppPassword = $element->getText();
            $browser->assertSee( "App password created: " . $locatedAppPassword );

            $appPassword = AppPassword::latest()->first();
            $this->assertInstanceOf( AppPassword::class, $appPassword );
            $this->assertEquals("App password", $appPassword->description );
            if ($appPassword->salt != null) {
                // salted sha256
                $this->assertTrue(hash_equals( hash("sha256", $locatedAppPassword . $appPassword->salt ), $appPassword->password ) );
            } else {
                // password hash construction
                $this->assertTrue(Hash::check( $locatedAppPassword, $appPassword->password ) );
            }

            // 3. Edit app password
            $browser->click( '#e2f-list-edit-' . $appPassword->id )
                ->waitForText( 'Edit Application-Specific Password' )
                ->assertInputValue( 'description', 'App password' )
                ->type( "description", "Temporary app password" )
                ->press( "Save Changes" )
                ->waitForLocation( '/admin/app-password/list' )
                ->assertSee( "Application-Specific Password updated." );

            $appPassword->refresh();

            // 4. Check Value
            $this->assertEquals( "Temporary app password", $appPassword->description );

            // 9. Delete API KEY
            $browser->click( "#e2f-list-delete-" . $appPassword->id )
                ->waitForText( 'Do you really want to delete this app password?' )
                ->press( 'Delete' )
                ->waitForLocation( '/admin/app-password/list' )
                ->assertSee( "Application-Specific Password deleted." )
                ->assertDontSee( $appPassword->description );

            $this->assertTrue( AppPassword::whereId( $appPassword->id )->doesntExist() );
        } );
    }
}