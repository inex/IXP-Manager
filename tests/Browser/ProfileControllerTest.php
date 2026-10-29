<?php
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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests\Browser;

use IXP\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfileControllerTest extends DuskTestCase
{
    /**
     * Tests member notes settings for super user
     */
    public function testMemberNotesSuperUser(): void
    {
        $this->browse( function( Browser $browser ): void {
            $user = User::where('username', 'travis')->first();

            $browser->resize( 1600, 1400 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->click('#my-account')
                ->click('#profile')
                ->waitForLocation( route('profile@edit') )
                ->assertSee("My Profile")
                ->assertSee(ucfirst(config('ixp_fe.lang.customer.one') . " Notes"));

            $this->assertNull($user->prefs, "DB seed user has no prefs");
            $browser->assertChecked('#notify-none', "The controller populates form with 'none' selected if no preferences");
            $browser->assertNotChecked('#notify-watched');
            $browser->assertNotChecked('#notify-all');

            // Select: all
            $browser->click('#notify-all')
                ->press('Set Notification Preference')
                ->waitForLocation( route('profile@edit') )
                ->assertSee('Notification preference updated.');

            $user->refresh();
            $this->assertUserGlobalNotifs( 'all', $user );

            // Select: none
            $browser->click('#notify-none')
                ->press('Set Notification Preference')
                ->waitForLocation( route('profile@edit') )
                ->assertSee('Notification preference updated.');

            $user->refresh();
            $this->assertUserGlobalNotifs( 'none', $user );

            $browser->assertChecked('#notify-none');

            // Select: watched
            $browser->click('#notify-watched')
                ->press('Set Notification Preference')
                ->waitForLocation( route('profile@edit') )
                ->assertSee('Notification preference updated.');

            $user->refresh();
            $this->assertUserGlobalNotifs( 'watched', $user );
        } );
    }

    private function assertUserGlobalNotifs( string $preference , User $user ): void
    {
        $this->assertNotNull( $user->prefs );
        $this->assertArrayHasKey( 'notes', $user->prefs );
        $this->assertArrayHasKey( 'global_notifs', $user->prefs['notes'] );
        $this->assertEquals($preference, $user->prefs['notes']['global_notifs'] );
    }
}