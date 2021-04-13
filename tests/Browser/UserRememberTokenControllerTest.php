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

use Auth ;

use IXP\Models\{
    User,
    UserRememberToken
};

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Test UserRememberToken Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Tests\Browser
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserRememberTokenControllerTest extends DuskTestCase
{

    /**
     * A Dusk test example.
     *
     * @return void
     *
     * @throws
     */
    public function testAdd(): void
    {
        $this->browse( function ( Browser $browser, Browser $browser2 ) {
            $user = User::whereUsername('travis' )->first();
            $cookieName = Auth::getRecallerName();

            $browser->resize( 1600,1200 )
                    ->visit('/logout')
                    ->visit('/login')
                    ->type('username', $user->username )
                    ->type('password', 'travisci' )
                    ->press('#login-btn' )
                    ->assertPathIs( '/admin' );

            /**
             * Check that the remember cookie and DB entry is not existing as we didn't checked the remember me checkbox
             */
            $listUrt = UserRememberToken::whereUserId( $user->id )->get();

            $this->assertEquals( 0, $listUrt->count() );

            /**
             * Login checking the checkbox remember me
             */
            $browser->resize( 1600,1200 )
                    ->visit('/logout')
                    ->visit('/login')
                    ->type('username', 'travis' )
                    ->type('password', 'travisci' )
                    ->check( '#remember-me')
                    ->press('#login-btn' )
                    ->assertPathIs( '/admin' );

            /**
             * Check that the remember cookie and DB entry is existing as we checked the remember me checkbox
             */
            $browser->assertHasCookie( $cookieName );

            $listUrt = UserRememberToken::whereUserId( $user->id )->get();
            $this->assertEquals( 1, $listUrt->count() );

            /**
             * Open a new browser and login
             */
            $browser2->resize( 1600,1200 )
                ->visit('/logout')
                ->visit('/login')
                ->type('username', 'travis' )
                ->type('password', 'travisci' )
                ->check( '#remember-me')
                ->press('#login-btn' )
                ->assertPathIs( '/admin' );

            /**
             * Check that the remember cookie exist in the new browser
             */
            $browser2->assertHasCookie( $cookieName );

            /**
             * Check that the user has 2 active sessions
             */
            $listUrt = UserRememberToken::whereUserId( $user->id )->get();
            $this->assertEquals( 2, $listUrt->count() );

            /**
             * Delete an active session
             */
            $browser->click('#my-account')
                    ->click("#active-sessions")
                    ->assertPathIs('/active-sessions/list');

            // Get the last user remember token for the user
            $lastUrt = UserRememberToken::whereUserId( $user->id )->orderBy( 'id', 'DESC' )->first();

            $browser->press("#d2f-list-delete-" . $lastUrt->id)
                ->waitForText( 'Do you really want to delete this active login session?' )
                ->press('Delete')
                ->assertPathIs('/active-sessions/list' )
                ->assertSee( 'Active Login Session deleted.' );

            $listUrt = UserRememberToken::whereUserId( $user->id )->get();
            $this->assertEquals( 1, $listUrt->count() );

            /**
             * Refresh the second browser and check that we have been logged out
             */
            $browser2->refresh()
                    ->assertPathIs( '/login' );

            /**
             * Refresh the first browser and check that we are still logged in
             */
            $browser->refresh()
                ->assertPathIs( '/active-sessions/list' );


            /**
             * Delete the user remember token left for the user and check that the user is loggued out
             */
            // Get the user remember token left for the user
            $urt = UserRememberToken::whereUserId( $user->id )->first();

            $browser->press("#d2f-list-delete-" . $urt->id )
                ->waitForText( 'Do you really want to delete this active login session?' )
                ->press('Delete')
                ->assertPathIs('/login' )
                ->assertSee( 'You have been logged out.' );
        });
    }
}