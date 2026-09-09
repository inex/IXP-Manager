<?php

namespace Tests\Browser;

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\User;

use Laravel\Dusk\Browser;

use Tests\DuskTestCase;

/**
 * Browser test for the per-user language preference.
 *
 * The mechanism itself is covered deterministically in
 * tests/Http/ProfileLocaleTest.php. This adds the end to end path a real user
 * takes: choose a language on the profile page, see the interface change, and
 * change it back.
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    Tests\Browser
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class LocaleTest extends DuskTestCase
{
    /**
     * Leave no preference behind - the rest of the browser suite asserts on
     * English and shares this database.
     */
    protected function tearDown(): void
    {
        if( $u = User::whereUsername( 'imcustadmin' )->first() ) {
            $u->setLocale( null );
            $u->save();
        }

        parent::tearDown();
    }

    /**
     * Choose French on the profile page, confirm the interface is French, and
     * change back.
     */
    public function testUserCanChooseTheirLanguage(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser->resize( 1600, 1200 )
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'imcustadmin' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/dashboard' );

            // English to start with. The submit is an <input type=submit>, so
            // its label lives in the value attribute rather than in the text.
            $browser->visit( '/profile' )
                ->waitFor( '#locale' )
                ->assertSourceHas( 'lang="en"' )
                ->assertSourceHas( 'value="Set Language"' )
                ->assertSee( 'Logout' );

            // choose French - press() resolves an input[type=submit] by value
            $browser->select( 'locale', 'fr' )
                ->press( 'Set Language' )
                ->waitForLocation( '/profile' )
                ->waitFor( '#locale' );

            $browser->assertSourceHas( 'lang="fr"' )
                ->assertSourceHas( 'value="Définir la langue"' )
                ->assertSourceMissing( 'value="Set Language"' )
                // the chrome follows too, not just the form that changed it
                ->assertSee( 'Déconnexion' )
                ->assertDontSee( 'Logout' );

            // and it persists to another page
            $browser->visit( '/dashboard' )
                ->waitForText( 'Accueil' )
                ->assertSourceHas( 'lang="fr"' );

            // change back
            $browser->visit( '/profile' )
                ->waitFor( '#locale' )
                ->select( 'locale', 'en' )
                ->press( 'Définir la langue' )
                ->waitForLocation( '/profile' )
                ->waitFor( '#locale' );

            $browser->assertSourceHas( 'lang="en"' )
                ->assertSourceHas( 'value="Set Language"' )
                ->assertSee( 'Logout' );
        });
    }
}
