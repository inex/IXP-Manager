<?php

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, D2EM;

use Entities\{
    User                as UserEntity,
    UserRememberToken   as UserRememberTokenEntity,
};

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class UserRememberTokenControllerTest extends DuskTestCase
{

    /**
     * A Dusk test example.
     *
     * @return void
     * @throws
     */
    public function testAdd()
    {

        $this->browse(function ( Browser $browser, Browser $browser2 ) {

            /** @var UserEntity $user */
            $user = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'username' => 'travis'  ] );

            $cookieName = Auth::getRecallerName();

            $browser->resize( 1600,1200 )
                    ->visit('/logout')
                    ->visit('/login')
                    ->type('username', $user->getUsername() )
                    ->type('password', 'travisci' )
                    ->press('#login-btn' )
                    ->assertPathIs( '/admin' );

            /**
             * Check that the remember cookie and DB entry is not existing as we didnt checked the remember me checkbox
             */
            $browser->assertCookieMissing( $cookieName, false );

            $listUrt = D2EM::getRepository( UserRememberTokenEntity::class )->findBy( [ 'User' => $user->getId()  ] );

            $this->assertEquals( 0, count( $listUrt ) );

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

            $listUrt = D2EM::getRepository( UserRememberTokenEntity::class )->findBy( [ 'User' => $user->getId()  ] );

            $this->assertEquals( 1, count( $listUrt ) );



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
            $listUrt = D2EM::getRepository( UserRememberTokenEntity::class )->findBy( [ 'User' => $user->getId()  ] );

            $this->assertEquals( 2, count( $listUrt ) );

            /**
             * Delete an active session
             */
            $browser->click('#my-account')
                    ->click("#active-sessions")
                    ->assertPathIs('/active-sessions/list');

            // Get the last user remember token for the user
            $lastUrt = D2EM::getRepository( UserRememberTokenEntity::class )->findOneBy( [ 'User' => $user->getId() ], [ 'id' => 'DESC' ] );

            $browser->press("#d2f-list-delete-" . $lastUrt->getId() )
                ->waitForText( 'Do you really want to delete this active login session?' )
                ->press('Delete')
                ->assertPathIs('/active-sessions/list' )
                ->assertSee( 'Active Login Session deleted.' );


            D2EM::refresh( $lastUrt );

            $listUrt = D2EM::getRepository( UserRememberTokenEntity::class )->findBy( [ 'User' => $user->getId()  ] );

            $this->assertEquals( 1, count( $listUrt ) );

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
            $urt = D2EM::getRepository( UserRememberTokenEntity::class )->findOneBy( [ 'User' => $user->getId() ] );

            $browser->press("#d2f-list-delete-" . $urt->getId() )
                ->waitForText( 'Do you really want to delete this active login session?' )
                ->press('Delete')
                ->assertPathIs('/login' )
                ->assertSee( 'You have been logged out.' );
        });


    }

}
