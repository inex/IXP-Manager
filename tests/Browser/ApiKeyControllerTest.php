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

use Str;

use Carbon\Carbon;

use IXP\Models\ApiKey;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test Apikey Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ApiKeyControllerTest extends DuskTestCase
{
    public function tearDown(): void
    {
        if( $key = ApiKey::where( 'description','Temporally Test API Key' ) ) {
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

            // check the key in the database is listed
            $browser->visit( '/api-key/list' )
                ->assertSee( 'API Keys' )
                ->assertSee( 'iqLw1OF50aPU' );

            // 1. test add some inputs
            $browser->visit( '/api-key/create' )
                ->assertSee( 'Create API Key' )
                ->type( "description", "API Key" )
                ->type( "expires", now()->addMonth()->format("d-m-Y") )
                ->press( 'Create' )
                ->waitForLocation( '/api-key/list' )
                ->assertSee( "API Key created" )
                ->assertSee( "API key created:" );

            $apiKey = ApiKey::latest()->first();

            // 2. Check the api key
            $this->assertInstanceOf( ApiKey::class, $apiKey );

            $element = $browser->element(".alert-dismissible code");
            $this->assertNotNull($element, "could not locate a code section inside an alert - did we change how we show it to the user?");
            $locatedApiKey = $element->getText();

            $browser->assertSee( "API key created: " . $locatedApiKey );

            // 3. Edit API key
            $browser->click( '#e2f-list-edit-' . $apiKey->id )
                ->waitForText( 'Edit API Key' )
                ->assertInputValue( 'description', 'API Key' )
                ->type( "description", "Temporally Test API Key" )
                ->press( "Save Changes" )
                ->waitForLocation( '/api-key/list' )
                ->assertSee( "API Key updated" );

            $apiKey->refresh();

            // 4. Check Value

            // work around locale issues:
            $expectedExpires = now()->addMonth()->format( "Y-m-d" );

            if( $expectedExpires === Carbon::parse( $apiKey->expires )->format( "Y-m-d" ) ) {
                $db = Carbon::parse( $apiKey->expires )->format( "Y-m-d" );
            } else {
                $db = Carbon::parse( $apiKey->expires )->format( "Y-d-m" );
            }

            $this->assertEquals( $expectedExpires, $db );
            $this->assertEquals( 'Temporally Test API Key', $apiKey->description );

            // 5. Delete API KEY
            $browser->click( "#e2f-list-delete-" . $apiKey->id )
                ->waitForText( 'Do you really want to delete this API key' )
                ->press( 'Delete' )
                ->waitForLocation( '/api-key/list' )
                ->assertSee( "API Key deleted" )
                ->assertDontSee( $apiKey->token_identifier );

            $this->assertTrue( ApiKey::whereId( $apiKey->id )->doesntExist() );
        } );
    }
}