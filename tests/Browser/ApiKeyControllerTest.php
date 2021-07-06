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
        if( $key = ApiKey::find( 5 ) ) {
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
        $this->browse( function ( Browser $browser ) {
            $browser->resize( 1600,1200 )
                ->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->assertPathIs( '/admin' );

            $browser->visit( '/api-key/list' )
                ->assertSee( 'API Keys' )
                ->assertSee( 'Syy4R8...' );

            // 1. test add empty inputs
            $browser->visit( '/api-key/create' )
                ->assertSee( 'Create API Key' )
                ->press('Create')
                ->assertPathIs('/api-key/list')
                ->assertSee( "API Key created" )
                ->assertSee( "API key created:" );


            $apiKey = ApiKey::find( 5 );

            // 2. Check the api key
            $this->assertInstanceOf( ApiKey::class, $apiKey );

            $browser->assertSee( Str::limit( $apiKey->apiKey , 6 ) );

            // 3. Edit API key
            $browser->click( '#e2f-list-edit-' . $apiKey->id )
                ->assertSee( 'Edit API Key' )
                ->assertInputValue('apiKey',    $keyLimited =   Str::limit( $apiKey->apiKey , 6 ) )
                ->assertInputValue('description', '')
                ->assertInputValue('expires', '')
                ->assertDisabled('apiKey' )
                ->type( "description" , "description test" )
                ->type("expires", now()->addYear()->startOfMonth()->format( "d-m-Y" ) )
                ->press( "Save Changes" )
                ->assertPathIs('/api-key/list')
                ->assertSee( "API Key updated" );

            $apiKey->refresh();

            // 4. Check Value
            $this->assertEquals(            $keyLimited,              Str::limit( $apiKey->apiKey , 6 ) );

            // work around locale issues:
            $now = now()->addYear()->startOfMonth()->format( "Y-m-d" );

            if( $now === Carbon::parse($apiKey->expires)->format( "Y-m-d" ) ) {
                $db = Carbon::parse($apiKey->expires)->format( "Y-m-d" );
            } else {
                $db = Carbon::parse($apiKey->expires)->format( "Y-d-m" );
            }

            $this->assertEquals( $db, $now );
            $this->assertEquals( 'description test',        $apiKey->description );

            // 5. Enter wrong password to see the not limited API KEY
            $browser->type( "pass" , "wrongPass" )
                    ->press( "Submit" )
                    ->assertPathIs( "/api-key/list-show-keys" )
                    ->assertSee( "Incorrect password entered" );

            // 6. Enter good password to see the not limited API KEY
            $browser->type( "pass" , 'travisci' )
                ->press( "Submit" )
                ->assertPathIs( "/api-key/list-show-keys" )
                ->assertSee( "API keys are visible for this request only" )
                ->assertSee( $apiKey->apiKey );

            // 7. Check that the API Key are restricted again
            $browser->visit( '/api-key/list' )
                    ->assertSee( $keyLimited );

            // 8. Delete API KEY
            $browser->click( "#e2f-list-delete-" . $apiKey->id )
                ->waitForText( 'Do you really want to delete this API key' )
                ->press( 'Delete' )
                ->assertPathIs('/api-key/list' )
                ->assertSee( "API Key deleted" )
                ->assertDontSee( $keyLimited );

            $this->assertTrue( ApiKey::whereId( 5 )->doesntExist() );
        });
    }
}