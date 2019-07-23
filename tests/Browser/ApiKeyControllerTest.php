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

use Entities\{
    ApiKey as ApiKeyEntity
};

use Str;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ApiKeyControllerTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function test()
    {

        $this->browse(function (Browser $browser) {
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
            $browser->visit( '/api-key/add' )
                ->assertSee( 'Add API Key' )
                ->press('Add')
                ->assertPathIs('/api-key/list')
                ->assertSee( "API Key added" )
                ->assertSee( "API key created:" );

            /** @var ApiKeyEntity $apiKey */
            $apiKey = D2EM::getRepository( ApiKeyEntity::class )->findOneBy( [ 'id' => 5 ] );

            // 2. Check the api key
            $this->assertInstanceOf( ApiKeyEntity::class, $apiKey );

            $browser->assertSee( Str::limit( $apiKey->getApiKey() , 6 ) );

            // 3. Edit API key
            $browser->click( '#d2f-list-edit-' . $apiKey->getId() )
                ->assertSee( 'Edit API Key' )
                ->assertInputValue('key',    $keyLimited =   Str::limit( $apiKey->getApiKey() , 6 ) )
                ->assertInputValue('description', '')
                ->assertInputValue('expires', '')
                ->assertDisabled('key' )
                ->type( "description" , "description test" )
                ->type("expires", "20/07/2019" )
                ->press( "Save Changes" )
                ->assertPathIs('/api-key/list')
                ->assertSee( "API Key edited" );

            D2EM::refresh( $apiKey );

            // 4. Check Value
            $this->assertEquals(            $keyLimited,              Str::limit( $apiKey->getApiKey() , 6 ) );
            $this->assertEquals( '2019-07-20',              $apiKey->getExpires()->format( "Y-m-d" ) );
            $this->assertEquals( 'description test',        $apiKey->getDescription() );

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
                ->assertSee( $apiKey->getApiKey() );

            // 7. Check that the API Key are restricted again
            $browser->visit( '/api-key/list' )
                    ->assertSee( $keyLimited );

            // 8. Delete API KEY
            $browser->click( "#d2f-list-delete-" . $apiKey->getId() )
                ->waitForText( 'Do you really want to delete this API key' )
                ->press( 'Delete' )
                ->assertPathIs('/api-key/list' )
                ->assertSee( "API Key deleted" )
                ->assertDontSee( $keyLimited );

            $this->assertNull( D2EM::getRepository( ApiKeyEntity::class )->findOneBy( [ 'id' => 5 ] ) );

        });

    }
}
